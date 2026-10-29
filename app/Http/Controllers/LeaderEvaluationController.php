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
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class LeaderEvaluationController extends Controller
{
    private function staffQuery(): Builder
    {
        $this->authorizeLeader();

        return User::query()->ledBy(Auth::user());
    }

    /**
     * Chỉ trưởng ca mới được dùng các chức năng sát hạch, nhắc việc và ghi nhận lỗi.
     */
    private function authorizeLeader(): void
    {
        abort_unless(Auth::user()?->isLeader(), 403, 'Chức năng chỉ dành cho trưởng ca.');
    }

    /**
     * Danh sách Bộ tiêu chuẩn năng lực chuẩn hóa theo vị trí công việc
     */
    private function getStandardList()
    {
        return collect([
            (object) ['MaCTDT' => 'STD_BARISTA', 'TenCTDT' => 'Tiêu chuẩn năng lực Barista'],
            (object) ['MaCTDT' => 'STD_CASHIER', 'TenCTDT' => 'Tiêu chuẩn năng lực Thu ngân'],
            (object) ['MaCTDT' => 'STD_SERVER',  'TenCTDT' => 'Tiêu chuẩn năng lực Phục vụ'],
        ]);
    }

    /**
     * Hiển thị trang Dashboard cho Leader (/leader/dashboard)
     */
    public function index(): View
    {
        $this->authorizeLeader();
        $currentUserId = Auth::id();

        // 1. Tất cả danh sách nhân viên
        $staffs = $this->staffQuery()->get();
        foreach ($staffs as $person) {
            $person->eligibility = app(AssessmentEligibility::class)->initial($person);
        }

        // Dùng cùng chu kỳ đánh giá và trạng thái làm việc với quản lý cửa hàng.
        $dueStaffs = app(AssessmentReminder::class)->decorate($staffs)->where('needs_reminder', true);

        // 3. Bộ tiêu chuẩn năng lực vị trí
        $standards = $this->getStandardList();

        // 4. Danh sách Lịch sát hạch
        $schedules = DB::table('danh_gia_nang_luc')
            ->join('users', 'danh_gia_nang_luc.user_id', '=', 'users.id')
            ->select(
                'danh_gia_nang_luc.id',
                'danh_gia_nang_luc.user_id',
                'users.name as staff_name',
                'danh_gia_nang_luc.TieuChuan as tiêu_chuẩn',
                'danh_gia_nang_luc.MaCTDT',
                'danh_gia_nang_luc.NgayHen',
                'danh_gia_nang_luc.TrangThai',
                'danh_gia_nang_luc.Diem',
                'danh_gia_nang_luc.ChiTietDeXuat'
            )
            ->whereIn('danh_gia_nang_luc.user_id', $staffs->modelKeys())
            ->whereIn('danh_gia_nang_luc.TrangThai', ['ChoDanhGia', 'ChoPheDuyet', 'DaDuyet', 'HoanThanh', 'TraLai'])
            ->orderBy('danh_gia_nang_luc.NgayHen', 'asc')
            ->get();

        $schedules = $schedules->groupBy('user_id')->flatMap(function ($history) {
            $pending = $history->whereIn('TrangThai', ['ChoDanhGia', 'ChoPheDuyet', 'TraLai']);

            // Keep every actionable schedule; completed history belongs in the staff profile.
            return $pending->isNotEmpty() ? $pending : $history->sortBy([['NgayHen', 'desc'], ['id', 'desc']])->take(1);
        })->sortBy('NgayHen')->values();

        $schedules = $schedules->groupBy('user_id')->flatMap(function ($history) {
            $pending = $history->whereIn('TrangThai', ['ChoDanhGia', 'ChoPheDuyet', 'TraLai']);

            // Keep every actionable schedule; completed history belongs in the staff profile.
            return $pending->isNotEmpty() ? $pending : $history->sortByDesc('NgayHen')->sortByDesc('id')->take(1);
        })->sortBy('NgayHen')->values();

        if (Schema::hasColumn('danh_gia_nang_luc', 'ly_do_tra_lai')) {
            $reasons = DB::table('danh_gia_nang_luc')->whereIn('id', $schedules->pluck('id'))->pluck('ly_do_tra_lai', 'id');
            foreach ($schedules as $schedule) {
                $schedule->ly_do_tra_lai = $reasons->get($schedule->id);
            }
        }
        $proposalOwners = User::orderBy('name')->get(['id', 'name']);
        if (auth()->user()?->cua_hang_id && Schema::hasColumn('users', 'cua_hang_id')) {
            $proposalOwners = User::where('cua_hang_id', auth()->user()->cua_hang_id)->orderBy('name')->get(['id', 'name']);
        }
        $proposalCourses = DB::table('chuong_trinh_dao_tao')->orderBy('MaCTDT')->get(['MaCTDT', 'TenCTDT']);
        $proposalLessons = DB::table('bai_hoc')->orderBy('FK_MaCTDT')->orderBy('ThuTu')->get(['MaBaiHoc', 'FK_MaCTDT', 'TenBaiHoc']);

        $page = request()->routeIs('leader.scoring') ? 'scoring' : 'schedule';

        return view('leader.dashboard', compact('page', 'staffs', 'standards', 'schedules', 'dueStaffs', 'proposalOwners', 'proposalCourses', 'proposalLessons'));
    }

    /**
     * Tạo lịch sát hạch mới cho 1 NHÂN VIÊN CỤ THỂ & TỰ ĐỘNG BẮN THÔNG BÁO (type = schedule)
     * (Bắt buộc chọn 1 nhân viên, không gửi hàng loạt)
     */
    public function createSchedule(Request $request): RedirectResponse
    {
        $this->authorizeLeader();
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'MaCTDT' => 'required',
            'NgayDanhGia' => 'required',
            'note' => 'nullable|string|max:2000',
        ], [
            'user_id.required' => 'Vui lòng chọn nhân viên.',
            'user_id.exists' => 'Nhân viên được chọn không hợp lệ.',
            'MaCTDT.required' => 'Vui lòng chọn tiêu chuẩn đánh giá.',
            'NgayDanhGia.required' => 'Vui lòng chọn thời gian đánh giá.',
        ]);

        $standardMap = [
            'STD_BARISTA' => 'Tiêu chuẩn năng lực Barista',
            'STD_CASHIER' => 'Tiêu chuẩn năng lực Thu ngân',
            'STD_SERVER' => 'Tiêu chuẩn năng lực Phục vụ',
        ];

        $tieuChuanTen = $standardMap[$request->MaCTDT] ?? ($request->MaCTDT ?: 'Tiêu chuẩn năng lực Barista');
        $maCTDT_Value = is_numeric($request->MaCTDT) ? $request->MaCTDT : null;
        $ghiChuText = $request->input('note') ?? $request->input('ghi_chu') ?? $request->input('comment');

        // Tạo nội dung thông báo chuẩn hóa
        $messageContent = "Bạn có lịch sát hạch '$tieuChuanTen' vào lúc ".Carbon::parse($request->NgayDanhGia)->format('H:i d/m/Y').'.';
        if (! empty(trim($ghiChuText))) {
            $messageContent .= "\n\nLời dặn từ Trưởng ca: ".trim($ghiChuText);
        }

        DB::beginTransaction();
        try {
            $person = $this->staffQuery()->where('id', $request->user_id)->lockForUpdate()->first();
            if (! $person) {
                DB::rollBack();

                return back()->withErrors(['user_id' => 'Chỉ xếp lịch cho nhân viên đang làm việc trong phạm vi ca / cửa hàng.'])->withInput();
            }
            if (DB::table('danh_gia_nang_luc')->where('user_id', $person->id)->whereIn('TrangThai', ['ChoDanhGia', 'ChoPheDuyet', 'TraLai'])->exists()) {
                DB::rollBack();

                return back()->withErrors(['user_id' => 'Nhân viên đã có lịch đánh giá hoặc kết quả đang xử lý. Vui lòng xử lý lượt hiện tại trước.'])->withInput();
            }
            $now = now();
            $eligibility = app(AssessmentEligibility::class)->initial($person);
            if (! $eligibility['ready']) {
                DB::rollBack();

                return back()->withErrors(['user_id' => 'Chưa đủ điều kiện đánh giá: '.implode(' ', $eligibility['missing'])])->withInput();
            }
            $leaderId = auth()->id() ?? 1;

            // 1. Lưu bản ghi lịch sát hạch cho 1 cá nhân
            DB::table('danh_gia_nang_luc')->insert([
                'user_id' => $request->user_id,
                'NguoiDanhGia_id' => $leaderId,
                'MaCTDT' => $maCTDT_Value,
                'TieuChuan' => $tieuChuanTen,
                'NgayHen' => $request->NgayDanhGia,
                'NgayDanhGia' => null,
                'Diem' => null,
                'TrangThai' => 'ChoDanhGia',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // 2. Bắn thông báo cho nhân viên đó
            DB::table('notifications')->insert([
                'user_id' => $request->user_id,
                'title' => 'Lịch sát hạch năng lực mới từ Trưởng ca',
                'message' => $messageContent,
                'type' => 'schedule',
                'is_read' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Đã xếp lịch và phát thông báo tới nhân viên thành công!');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Có lỗi xảy ra khi tạo lịch sát hạch: '.$e->getMessage());
        }
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
            'muc_do' => ['required', Rule::in(['Nhanh', 'TrungBinh', 'NghiemTrong'])],
            'thoi_diem' => ['nullable', 'date', 'before_or_equal:now'],
            'mo_ta' => ['required', 'string', 'max:2000'],
        ], [
            'user_id.required' => 'Vui lòng chọn nhân viên.',
            'user_id.in' => 'Chỉ ghi nhận lỗi cho nhân viên thuộc phạm vi phụ trách.',
        ]);

        DB::transaction(function () use ($data): void {
            DB::table('loi_nghiep_vu')->insert([
                'user_id' => $data['user_id'], 'nguoi_ghi_nhan_id' => Auth::id(), 'loai_loi' => $data['loai_loi'],
                'muc_do' => $data['muc_do'], 'diem_tru_risk' => ['Nhanh' => 5, 'TrungBinh' => 10, 'NghiemTrong' => 20][$data['muc_do']],
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
     * Xử lý lưu, tính kết quả đánh giá năng lực & TỰ ĐỘNG BẮN THÔNG BÁO BÁO KẾT QUẢ
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

        // Công thức tính trọng số: 20% NV + 30% KN + 20% QT + 15% PV + 15% TP
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

        $assessment = DB::table('danh_gia_nang_luc')->where('id', $request->assessment_id)->first();

        if (! $assessment) {
            return redirect()->back()->with('error', 'Không tìm thấy hồ sơ đánh giá tương ứng!');
        }
        if (auth()->user()?->cua_hang_id && ! $this->staffQuery()->where('id', $assessment->user_id)->exists()) {
            abort(403);
        }

        DB::beginTransaction();
        try {
            // 1. Chuẩn bị dữ liệu cập nhật
            $assessment = DB::table('danh_gia_nang_luc')->where('id', $request->assessment_id)->lockForUpdate()->first();
            if (! in_array($assessment->TrangThai, ['ChoDanhGia', 'TraLai'])) {
                DB::rollBack();

                return back()->with('error', 'Đánh giá đang chờ duyệt hoặc đã duyệt. Vui lòng tạo lượt đánh giá mới.');
            }
            $updateData = [
                'Diem' => $diemTong,
                'NgayDanhGia' => now(),
                'NhanXet' => $request->comment,
                'TrangThai' => 'ChoPheDuyet',
                'updated_at' => now(),
            ];

            if (Schema::hasColumn('danh_gia_nang_luc', 'IsPassed')) {
                $updateData['IsPassed'] = $isPassed ? 1 : 0;
            }

            if (Schema::hasColumn('danh_gia_nang_luc', 'ChiTietDiem')) {
                $updateData['ChiTietDiem'] = json_encode([
                    'nghiep_vu' => $nv,
                    'ky_nang' => $kn,
                    'quy_trinh' => $qt,
                    'phuc_vu' => $pv,
                    'tac_phong' => $tp,
                ]);
            }

            foreach (['Diem_NghiepVu' => $nv, 'Diem_KyNang' => $kn, 'Diem_QuyTrinh' => $qt, 'Diem_PhucVu' => $pv, 'Diem_TacPhong' => $tp] as $column => $score) {
                if (Schema::hasColumn('danh_gia_nang_luc', $column)) {
                    $updateData[$column] = $score;
                }
            }
            $updateData['DeXuatXuLy'] = $isPassed ? null : json_encode($request->input('proposals'), JSON_UNESCAPED_UNICODE);
            if (Schema::hasColumn('danh_gia_nang_luc', 'nguoi_duyet_id')) {
                $updateData['nguoi_duyet_id'] = null;
                $updateData['ngay_duyet'] = null;
                $updateData['ly_do_tra_lai'] = null;
            }
            $updateData['ChiTietDeXuat'] = $isPassed ? null : json_encode([
                'source' => 'Trưởng ca đề xuất', 'proposed_by' => Auth::id(),
                'status' => 'ChoPheDuyet', 'details' => $proposalDetails,
            ], JSON_UNESCAPED_UNICODE);

            // 2. Cập nhật bảng kết quả
            DB::table('danh_gia_nang_luc')
                ->where('id', $request->assessment_id)
                ->update($updateData);

            if (Schema::hasColumn('users', 'cua_hang_id')) {
                $storeId = DB::table('users')->where('id', $assessment->user_id)->value('cua_hang_id');
                if ($storeId) {
                    foreach (DB::table('users')->where('cua_hang_id', $storeId)->whereRaw('LOWER(TRIM(ChucVu)) IN (?, ?)', ['quản lý cửa hàng', 'quan ly cua hang'])->pluck('id') as $managerId) {
                        $notification = ['user_id' => $managerId, 'title' => 'Đánh giá chờ phê duyệt', 'message' => 'Trưởng ca đã gửi đánh giá năng lực của nhân viên '.(DB::table('users')->where('id', $assessment->user_id)->value('name') ?? '').'. Vui lòng xem tại mục Duyệt đánh giá.', 'type' => 'training', 'is_read' => 0, 'created_at' => now(), 'updated_at' => now()];
                        if (Schema::hasColumn('notifications', 'event_key')) {
                            $notification['event_key'] = 'pending-review-'.$managerId.'-'.$assessment->id.'-'.$updateData['updated_at']->toDateTimeString();
                        }
                        DB::table('notifications')->insertOrIgnore($notification);
                    }
                }
            }
            DB::commit();

            $msg = $isPassed
                ? "Đã gửi kết quả đánh giá ĐẠT ({$diemTong}/100 điểm) cho Quản lý cửa hàng phê duyệt. Nhân viên nhận kết quả sau khi được duyệt."
                : "Đã gửi đề xuất xử lý cho nhân viên CHƯA ĐẠT ({$diemTong}/100 điểm) tới Quản lý cửa hàng!";

            return redirect()->back()->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Có lỗi xảy ra khi lưu kết quả: '.$e->getMessage());
        }
    }

    /**
     * Hiển thị danh sách nhân sự ca (/leader/staff-list)
     */
    public function staffList(): View
    {
        $this->authorizeLeader();
        $truongCa = Auth::user();
        $currentUserId = Auth::id();

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
            $userChucVu = $staff->ChucVu ?? $staff->chuc_vu ?? '';
            $cleanChucVu = mb_strtoupper(trim($userChucVu), 'UTF-8');

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
            $staff->assessment_status = null;

            if ($assessment) {
                if ($assessment->TrangThai === 'ChoPheDuyet') {
                    $staff->assessment_status = 'Chờ duyệt';
                } elseif (isset($assessment->IsPassed)) {
                    $staff->assessment_status = $assessment->IsPassed ? 'Đạt' : 'Chưa đạt';
                } else {
                    $criteria = json_decode($assessment->ChiTietDiem ?? '', true);
                    $criterionKeys = ['nghiep_vu', 'ky_nang', 'quy_trinh', 'phuc_vu', 'tac_phong'];
                    if (is_array($criteria) && count(array_intersect($criterionKeys, array_keys($criteria))) === 5) {
                        $isPassed = $assessment->Diem >= 80;
                        foreach ($criterionKeys as $key) {
                            $isPassed = $isPassed && is_numeric($criteria[$key]) && $criteria[$key] >= 70;
                        }
                        $staff->assessment_status = $isPassed ? 'Đạt' : 'Chưa đạt';
                    } else {
                        $staff->assessment_status = 'Đã duyệt';
                    }
                }
            }
        }

        $totalStaffs = $staffs->count();

        $dueCount = app(AssessmentReminder::class)->decorate($staffs)->where('needs_reminder', true)->count();

        $avgProgress = $totalStaffs > 0 ? round($staffs->avg('overall_progress') ?? 0, 1) : 0;
        $highRiskCount = $staffs->filter(fn ($staff) => $staff->overall_progress < 30)->count();

        return view('leader.staff_list', compact(
            'truongCa',
            'staffs',
            'totalStaffs',
            'dueCount',
            'avgProgress',
            'highRiskCount'
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
