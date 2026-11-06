<?php

namespace App\Http\Controllers;

use App\Models\AssessmentEligibility;
use App\Models\AssessmentReminder;
use App\Models\CourseProgress;
use App\Models\StaffProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LeaderEvaluationController extends Controller
{
    /**
     * Bộ tiêu chuẩn năng lực theo vị trí công việc, dùng khi trưởng ca xếp lịch đánh giá.
     */
    private const STANDARDS = [
        'STD_BARISTA' => 'Tiêu chuẩn năng lực Barista',
        'STD_CASHIER' => 'Tiêu chuẩn năng lực Thu ngân',
        'STD_SERVER' => 'Tiêu chuẩn năng lực Phục vụ',
    ];

    private function staffQuery(): Builder
    {
        $this->authorizeLeader();

        return User::query()->ledBy(Auth::user());
    }

    /**
     * Chỉ trưởng ca mới được dùng các chức năng đánh giá năng lực, nhắc việc và ghi nhận lỗi.
     */
    private function authorizeLeader(): void
    {
        abort_unless(Auth::user()?->isLeader(), 403, 'Chức năng chỉ dành cho trưởng ca.');
    }

    /**
     * Mã các bản đánh giá lại gắn với vòng đào tạo lại.
     */
    private function reassessmentIds(): array
    {
        return DB::table('retraining_rounds')->whereNotNull('assessment_id')->pluck('assessment_id')->all();
    }

    /**
     * Bản đánh giá lại chỉ do trưởng ca được quản lý phân công chấm (có thể khác ca);
     * các bản đánh giá khác do trưởng ca cùng cửa hàng, cùng ca chấm.
     */
    private function canScore(object $assessment, array $reassessmentIds): bool
    {
        if (in_array($assessment->id, $reassessmentIds)) {
            return (int) $assessment->NguoiDanhGia_id === (int) Auth::id();
        }

        return $this->staffQuery()->where('id', $assessment->user_id)->exists();
    }

    /**
     * Trang xếp lịch và chấm điểm của trưởng ca.
     */
    public function index(): View
    {
        $this->authorizeLeader();
        $staffs = $this->staffQuery()->get();
        foreach ($staffs as $person) {
            $person->eligibility = app(AssessmentEligibility::class)->initial($person);
        }

        // Dùng cùng chu kỳ đánh giá và trạng thái làm việc với quản lý cửa hàng.
        $dueStaffs = app(AssessmentReminder::class)->decorate($staffs)->where('needs_reminder', true);

        $standards = collect(self::STANDARDS)->map(fn (string $name, string $code) => (object) ['MaCTDT' => $code, 'TenCTDT' => $name])->values();

        // Mỗi nhân viên: giữ mọi lượt còn cần xử lý, nếu không có thì chỉ giữ lượt gần nhất.
        // Gồm cả bản đánh giá lại được phân công cho trưởng ca này dù nhân viên thuộc ca khác.
        $reassessmentIds = $this->reassessmentIds();
        $schedules = DB::table('danh_gia_nang_luc')
            ->join('users', 'danh_gia_nang_luc.user_id', '=', 'users.id')
            ->select(
                'danh_gia_nang_luc.id',
                'danh_gia_nang_luc.user_id',
                'danh_gia_nang_luc.NguoiDanhGia_id',
                'users.name as staff_name',
                'danh_gia_nang_luc.TieuChuan',
                'danh_gia_nang_luc.MaCTDT',
                'danh_gia_nang_luc.NgayHen',
                'danh_gia_nang_luc.TrangThai',
                'danh_gia_nang_luc.Diem',
                'danh_gia_nang_luc.ChiTietDeXuat',
                'danh_gia_nang_luc.ly_do_tra_lai'
            )
            ->where(fn ($query) => $query->whereIn('danh_gia_nang_luc.user_id', $staffs->modelKeys())
                ->orWhere(fn ($query) => $query->whereIn('danh_gia_nang_luc.id', $reassessmentIds)
                    ->where('danh_gia_nang_luc.NguoiDanhGia_id', Auth::id())))
            ->whereIn('danh_gia_nang_luc.TrangThai', ['ChoDanhGia', 'ChoPheDuyet', 'DaDuyet', 'HoanThanh', 'TraLai'])
            ->orderBy('danh_gia_nang_luc.NgayHen', 'asc')
            ->get();
        foreach ($schedules as $item) {
            $item->can_score = ! in_array($item->id, $reassessmentIds) || (int) $item->NguoiDanhGia_id === (int) Auth::id();
        }

        $schedules = $schedules->groupBy('user_id')->flatMap(function ($history) {
            $pending = $history->whereIn('TrangThai', ['ChoDanhGia', 'ChoPheDuyet', 'TraLai']);

            return $pending->isNotEmpty() ? $pending : $history->sortBy([['NgayHen', 'desc'], ['id', 'desc']])->take(1);
        })->sortBy('NgayHen')->values();

        $proposalOwners = User::when(Auth::user()->cua_hang_id, fn ($query, $storeId) => $query->where('cua_hang_id', $storeId))
            ->orderBy('name')->get(['id', 'name']);
        $proposalCourses = DB::table('chuong_trinh_dao_tao')->orderBy('MaCTDT')->get(['MaCTDT', 'TenCTDT']);
        $proposalLessons = DB::table('bai_hoc')->orderBy('FK_MaCTDT')->orderBy('ThuTu')->get(['MaBaiHoc', 'FK_MaCTDT', 'TenBaiHoc']);

        $page = request()->routeIs('leader.scoring') ? 'scoring' : 'schedule';

        return view('leader.dashboard', compact('page', 'staffs', 'standards', 'schedules', 'dueStaffs', 'proposalOwners', 'proposalCourses', 'proposalLessons'));
    }

    /**
     * Xếp lịch đánh giá năng lực cho một nhân viên đủ điều kiện và gửi thông báo cho nhân viên đó.
     */
    public function createSchedule(Request $request): RedirectResponse
    {
        $this->authorizeLeader();
        $data = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'MaCTDT' => ['required', Rule::in(array_keys(self::STANDARDS))],
            'NgayDanhGia' => 'required|date',
            'note' => 'nullable|string|max:2000',
        ], [
            'user_id.required' => 'Vui lòng chọn nhân viên.',
            'user_id.exists' => 'Nhân viên được chọn không hợp lệ.',
            'MaCTDT.required' => 'Vui lòng chọn tiêu chuẩn đánh giá.',
            'NgayDanhGia.required' => 'Vui lòng chọn thời gian đánh giá.',
        ]);

        $standard = self::STANDARDS[$data['MaCTDT']];
        $message = "Bạn có lịch đánh giá năng lực theo '$standard' vào lúc ".Carbon::parse($data['NgayDanhGia'])->format('H:i d/m/Y').'.';
        if (trim($data['note'] ?? '') !== '') {
            $message .= "\n\nLời dặn từ Trưởng ca: ".trim($data['note']);
        }

        return DB::transaction(function () use ($data, $standard, $message): RedirectResponse {
            $person = $this->staffQuery()->where('id', $data['user_id'])->lockForUpdate()->first();
            if (! $person) {
                return back()->withErrors(['user_id' => 'Chỉ xếp lịch cho nhân viên đang làm việc trong phạm vi ca / cửa hàng.'])->withInput();
            }
            if (DB::table('danh_gia_nang_luc')->where('user_id', $person->id)->whereIn('TrangThai', ['ChoDanhGia', 'ChoPheDuyet', 'TraLai'])->exists()) {
                return back()->withErrors(['user_id' => 'Nhân viên đã có lịch đánh giá hoặc kết quả đang xử lý. Vui lòng xử lý lượt hiện tại trước.'])->withInput();
            }
            $eligibility = app(AssessmentEligibility::class)->initial($person);
            if (! $eligibility['ready']) {
                return back()->withErrors(['user_id' => 'Chưa đủ điều kiện đánh giá: '.implode(' ', $eligibility['missing'])])->withInput();
            }

            DB::table('danh_gia_nang_luc')->insert([
                'user_id' => $person->id, 'NguoiDanhGia_id' => Auth::id(), 'TieuChuan' => $standard,
                'NgayHen' => $data['NgayDanhGia'], 'TrangThai' => 'ChoDanhGia', 'created_at' => now(), 'updated_at' => now(),
            ]);
            DB::table('notifications')->insert([
                'user_id' => $person->id, 'title' => 'Lịch đánh giá năng lực mới từ trưởng ca', 'message' => $message,
                'type' => 'schedule', 'is_read' => 0, 'created_at' => now(), 'updated_at' => now(),
            ]);

            return back()->with('success', 'Đã xếp lịch và phát thông báo tới nhân viên thành công!');
        });
    }

    /**
     * Gửi thông báo nhắc việc (UC-TC08) cho một nhân viên hoặc toàn bộ nhân viên trong phạm vi phụ trách.
     */
    public function sendNotification(Request $request): RedirectResponse
    {
        $this->authorizeLeader();
        $scopeIds = $this->staffQuery()->pluck('id')->all();
        $data = $request->validate([
            'user_id' => ['required', Rule::in(array_merge(['all'], array_map('strval', $scopeIds)))],
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
        ], [
            'user_id.required' => 'Vui lòng chọn người nhận thông báo.',
            'user_id.in' => 'Chỉ gửi thông báo cho nhân viên thuộc phạm vi phụ trách.',
        ]);

        $targetUserIds = $data['user_id'] === 'all' ? $scopeIds : [(int) $data['user_id']];
        $now = now();
        DB::table('notifications')->insert(array_map(fn (int $userId) => [
            'user_id' => $userId, 'title' => trim($data['title']), 'message' => trim($data['message']),
            'type' => 'general', 'is_read' => 0, 'created_at' => $now, 'updated_at' => $now,
        ], $targetUserIds));

        return back()->with('success', $data['user_id'] === 'all'
            ? 'Đã gửi thông báo tới '.count($targetUserIds).' nhân viên trong phạm vi phụ trách.'
            : 'Đã gửi thông báo cho nhân viên thành công!');
    }

    /**
     * Ghi nhận lỗi nghiệp vụ (UC-TC09): lưu vào loi_nghiep_vu và gửi nhắc nhở cho nhân viên.
     */
    public function recordIncident(Request $request): RedirectResponse
    {
        $this->authorizeLeader();
        $data = $request->validate([
            'user_id' => ['required', Rule::in($this->staffQuery()->pluck('id')->all())],
            'loai_loi' => ['required', 'string', 'max:100'],
            'muc_do' => ['required', Rule::in(['Nhe', 'TrungBinh', 'NghiemTrong'])],
            'thoi_diem' => ['nullable', 'date', 'before_or_equal:now'],
            'mo_ta' => ['required', 'string', 'max:2000'],
        ], [
            'user_id.required' => 'Vui lòng chọn nhân viên.',
            'user_id.in' => 'Chỉ ghi nhận lỗi cho nhân viên thuộc phạm vi phụ trách.',
        ]);

        DB::transaction(function () use ($data): void {
            DB::table('loi_nghiep_vu')->insert([
                'user_id' => $data['user_id'], 'nguoi_ghi_nhan_id' => Auth::id(), 'loai_loi' => $data['loai_loi'],
                'muc_do' => $data['muc_do'],
                'mo_ta' => trim($data['mo_ta']), 'thoi_diem_xay_ra' => $data['thoi_diem'] ?? now(),
                'chi_nhanh_id' => Auth::user()->cua_hang_id, 'created_at' => now(), 'updated_at' => now(),
            ]);
            DB::table('notifications')->insert([
                'user_id' => $data['user_id'], 'title' => 'Nhắc nhở: '.$data['loai_loi'], 'message' => trim($data['mo_ta']),
                'type' => 'warning', 'is_read' => 0, 'created_at' => now(), 'updated_at' => now(),
            ]);
        });

        return back()->with('success', 'Đã ghi nhận lỗi nghiệp vụ và gửi nhắc nhở cho nhân viên.');
    }

    /**
     * Lưu điểm năm tiêu chí, tính kết quả (đạt khi tổng >= 80 và mọi tiêu chí >= 70) và gửi quản lý phê duyệt.
     */
    public function saveScore(Request $request): RedirectResponse
    {
        $this->authorizeLeader();
        $request->validate([
            'assessment_id' => 'required|integer|exists:danh_gia_nang_luc,id',
            'diem_nghiep_vu' => 'required|numeric|min:0|max:100',
            'diem_ky_nang' => 'required|numeric|min:0|max:100',
            'diem_quy_trinh' => 'required|numeric|min:0|max:100',
            'diem_phuc_vu' => 'required|numeric|min:0|max:100',
            'diem_tac_phong' => 'required|numeric|min:0|max:100',
            'comment' => 'nullable|string|max:5000',
            'proposals' => 'nullable|array',
            'proposals.*' => 'required|distinct|in:daotaobosung,hoclaibaihoc,theodoica,danhgialai',
        ]);

        $nv = (float) $request->diem_nghiep_vu;
        $kn = (float) $request->diem_ky_nang;
        $qt = (float) $request->diem_quy_trinh;
        $pv = (float) $request->diem_phuc_vu;
        $tp = (float) $request->diem_tac_phong;

        // Trọng số: Nghiệp vụ 20%, Kỹ năng 30%, Quy trình 20%, Phục vụ 15%, Tác phong 15%
        $diemTong = round(($nv * 0.20) + ($kn * 0.30) + ($qt * 0.20) + ($pv * 0.15) + ($tp * 0.15), 1);
        $allCriteriaPassed = ($nv >= 70 && $kn >= 70 && $qt >= 70 && $pv >= 70 && $tp >= 70);
        $isPassed = ($diemTong >= 80) && $allCriteriaPassed;

        $proposalDetails = [];
        if (! $isPassed) {
            $request->validate(['proposals' => 'required|array|min:1'], [
                'proposals.required' => 'Nhân viên chưa đạt: vui lòng chọn ít nhất một đề xuất xử lý.',
            ]);
            $rules = [];
            foreach ($request->input('proposals', []) as $proposal) {
                $prefix = 'proposal_details.'.$proposal.'.';
                $rules['proposal_details.'.$proposal] = 'required|array';
                switch ($proposal) {
                    case 'daotaobosung':
                        $rules[$prefix.'skill'] = 'required|string|max:1000';
                        $rules[$prefix.'instructions'] = 'required|string|max:3000';
                        $rules[$prefix.'owner_id'] = 'required|integer|exists:users,id';
                        break;
                    case 'hoclaibaihoc':
                        $rules[$prefix.'course_id'] = 'required|integer|exists:chuong_trinh_dao_tao,MaCTDT';
                        $rules[$prefix.'lesson_ids'] = 'required|array|min:1';
                        $rules[$prefix.'lesson_ids.*'] = [
                            'required', 'integer', 'distinct',
                            Rule::exists('bai_hoc', 'MaBaiHoc')->where('FK_MaCTDT', $request->input($prefix.'course_id')),
                        ];
                        break;
                    case 'theodoica':
                        $rules[$prefix.'behavior'] = 'required|string|max:3000';
                        $rules[$prefix.'shift_count'] = 'nullable|required_without:'.$prefix.'until_date|integer|min:1|max:90';
                        $rules[$prefix.'until_date'] = 'nullable|required_without:'.$prefix.'shift_count|date_format:Y-m-d|after_or_equal:today';
                        break;
                    case 'danhgialai':
                        $rules[$prefix.'assessment_date'] = 'required|date_format:Y-m-d|after_or_equal:today';
                        $rules[$prefix.'assessor_id'] = 'required|integer|exists:users,id';
                        $rules[$prefix.'criteria'] = 'required|array|min:1';
                        $rules[$prefix.'criteria.*'] = 'required|distinct|in:nghiep_vu,ky_nang,quy_trinh,phuc_vu,tac_phong';
                        break;
                }
            }
            $fieldLabels = [
                'skill' => 'kỹ năng cần cải thiện', 'instructions' => 'nội dung hướng dẫn',
                'owner_id' => 'người phụ trách', 'course_id' => 'khóa học cần học lại',
                'lesson_ids' => 'bài học cần học lại', 'lesson_ids.*' => 'bài học đã chọn',
                'behavior' => 'hành vi cần quan sát', 'shift_count' => 'số ca theo dõi',
                'until_date' => 'ngày kết thúc theo dõi', 'assessment_date' => 'ngày đánh giá lại',
                'assessor_id' => 'người đánh giá lại', 'criteria' => 'tiêu chí cần kiểm tra lại',
                'criteria.*' => 'tiêu chí đã chọn',
            ];
            $attributes = [];
            foreach ($rules as $field => $rule) {
                $attributes[$field] = $fieldLabels[preg_replace('/^proposal_details\.[^.]+\./', '', $field)] ?? 'nội dung đề xuất';
            }
            $validatedDetails = $request->validate($rules, [
                'required' => 'Vui lòng nhập :attribute.',
                'required_without' => 'Vui lòng nhập số ca hoặc ngày kết thúc theo dõi.',
                'exists' => ':attribute không hợp lệ hoặc không thuộc khóa học đã chọn.',
                'after_or_equal' => ':attribute phải từ hôm nay trở đi.',
                'in' => ':attribute không hợp lệ.',
                'max' => ':attribute vượt quá giới hạn cho phép (:max).',
                'min' => ':attribute phải có ít nhất :min.',
            ], $attributes);
            $allowedFields = [
                'daotaobosung' => ['skill', 'instructions', 'owner_id'],
                'hoclaibaihoc' => ['course_id', 'lesson_ids'],
                'theodoica' => ['behavior', 'shift_count', 'until_date'],
                'danhgialai' => ['assessment_date', 'assessor_id', 'criteria'],
            ];
            foreach ($request->input('proposals') as $proposal) {
                $proposalDetails[$proposal] = array_intersect_key(
                    $validatedDetails['proposal_details'][$proposal], array_flip($allowedFields[$proposal])
                );
            }
        }

        $assessment = DB::table('danh_gia_nang_luc')->find($request->assessment_id);
        abort_unless($this->canScore($assessment, $this->reassessmentIds()), 403, 'Bạn không được phân công chấm đánh giá này.');

        return DB::transaction(function () use ($request, $isPassed, $diemTong, $nv, $kn, $qt, $pv, $tp, $proposalDetails): RedirectResponse {
            $assessment = DB::table('danh_gia_nang_luc')->where('id', $request->assessment_id)->lockForUpdate()->first();
            if (! in_array($assessment->TrangThai, ['ChoDanhGia', 'TraLai'])) {
                return back()->with('error', 'Đánh giá đang chờ duyệt hoặc đã duyệt. Vui lòng tạo lượt đánh giá mới.');
            }
            $now = now();
            DB::table('danh_gia_nang_luc')->where('id', $assessment->id)->update([
                'Diem' => $diemTong,
                'Diem_NghiepVu' => $nv,
                'Diem_KyNang' => $kn,
                'Diem_QuyTrinh' => $qt,
                'Diem_PhucVu' => $pv,
                'Diem_TacPhong' => $tp,
                'ChiTietDiem' => json_encode(['nghiep_vu' => $nv, 'ky_nang' => $kn, 'quy_trinh' => $qt, 'phuc_vu' => $pv, 'tac_phong' => $tp]),
                'IsPassed' => $isPassed ? 1 : 0,
                'NgayDanhGia' => $now,
                'NhanXet' => $request->comment,
                'TrangThai' => 'ChoPheDuyet',
                'DeXuatXuLy' => $isPassed ? null : json_encode($request->input('proposals'), JSON_UNESCAPED_UNICODE),
                'ChiTietDeXuat' => $isPassed ? null : json_encode([
                    'source' => 'Trưởng ca đề xuất', 'proposed_by' => Auth::id(),
                    'status' => 'ChoPheDuyet', 'details' => $proposalDetails,
                ], JSON_UNESCAPED_UNICODE),
                'nguoi_duyet_id' => null,
                'ngay_duyet' => null,
                'ly_do_tra_lai' => null,
                'updated_at' => $now,
            ]);

            $employee = DB::table('users')->where('id', $assessment->user_id)->first(['name', 'cua_hang_id']);
            $managerIds = DB::table('users')->where('cua_hang_id', $employee->cua_hang_id)
                ->whereRaw('LOWER(TRIM(ChucVu)) = ?', [User::MANAGER_POSITION])->pluck('id');
            foreach ($managerIds as $managerId) {
                DB::table('notifications')->insertOrIgnore([
                    'user_id' => $managerId, 'title' => 'Đánh giá chờ phê duyệt',
                    'message' => 'Trưởng ca đã gửi đánh giá năng lực của nhân viên '.$employee->name.'. Vui lòng xem tại mục Duyệt đánh giá.',
                    'type' => 'training', 'is_read' => 0, 'created_at' => $now, 'updated_at' => $now,
                    'event_key' => 'pending-review-'.$managerId.'-'.$assessment->id.'-'.$now->toDateTimeString(),
                ]);
            }

            return back()->with('success', $isPassed
                ? "Đã gửi kết quả đánh giá ĐẠT ({$diemTong}/100 điểm) cho Quản lý cửa hàng phê duyệt. Nhân viên nhận kết quả sau khi được duyệt."
                : "Đã gửi đề xuất xử lý cho nhân viên CHƯA ĐẠT ({$diemTong}/100 điểm) tới Quản lý cửa hàng!");
        });
    }

    /**
     * Danh sách nhân viên trong ca kèm tiến độ học, điểm kiểm tra và kết quả đánh giá gần nhất.
     */
    public function staffList(): View
    {
        $this->authorizeLeader();
        $truongCa = Auth::user();

        $staffs = $this->staffQuery()->get();

        $latestQuizResults = DB::table('ket_qua_kiem_tra')
            ->whereIn('user_id', $staffs->modelKeys())
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get()
            ->unique('user_id')
            ->keyBy('user_id');

        $latestAssessments = DB::table('danh_gia_nang_luc')
            ->whereIn('user_id', $staffs->modelKeys())
            ->whereIn('TrangThai', ['ChoPheDuyet', 'DaDuyet', 'HoanThanh'])
            ->whereNotNull('Diem')
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->get()
            ->unique('user_id')
            ->keyBy('user_id');

        $coursePercentages = app(CourseProgress::class)->coursePercentages($staffs);
        foreach ($staffs as $staff) {
            $percentages = $coursePercentages->get($staff->id, collect());
            $totalCoursesCount = $percentages->count();
            $completedCoursesCount = $percentages->filter(fn ($value) => $value >= 100)->count();
            $staff->overall_progress = round($percentages->avg() ?? 0, 1);

            $staff->completed_courses_count = $completedCoursesCount;
            $staff->total_courses_count = $totalCoursesCount;

            $quizResult = $latestQuizResults->get($staff->id);
            $staff->theory_score = $quizResult ? round((float) $quizResult->Diem, 1) : null;

            $assessment = $latestAssessments->get($staff->id);
            $staff->assessment_score = $assessment ? round((float) $assessment->Diem, 1) : null;
            $staff->assessment_status = match (true) {
                ! $assessment => null,
                $assessment->TrangThai === 'ChoPheDuyet' => 'Chờ duyệt',
                $assessment->IsPassed === null => 'Đã duyệt',
                default => $assessment->IsPassed ? 'Đạt' : 'Chưa đạt',
            };
        }

        $totalStaffs = $staffs->count();

        $dueCount = app(AssessmentReminder::class)->decorate($staffs)->where('needs_reminder', true)->count();

        $avgProgress = $totalStaffs > 0 ? round($staffs->avg('overall_progress') ?? 0, 1) : 0;

        return view('leader.staff_list', compact(
            'truongCa',
            'staffs',
            'totalStaffs',
            'dueCount',
            'avgProgress'
        ));
    }

    /**
     * Hồ sơ chi tiết của một nhân viên thuộc ca trưởng ca phụ trách.
     */
    public function staffProfile(int $id): View
    {
        $this->authorizeLeader();
        $staff = $this->staffQuery()->findOrFail($id);

        return view('leader.staff_profile', ['staff' => $staff] + app(StaffProfile::class)->build($staff));
    }
}
