<?php

namespace App\Http\Controllers;

use App\Models\CourseProgress;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class LeaderEvaluationController extends Controller
{
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
        $currentUserId = Auth::id();

        // 1. Tất cả danh sách nhân viên
        $staffs = User::where('id', '!=', $currentUserId)->get();

        // 2. Lọc danh sách nhân viên ĐẾN HẠN SÁT HẠCH 3 THÁNG (~ 80 đến 105 ngày làm việc)
        $eightyDaysAgo = Carbon::now()->subDays(80);
        $oneHundredFiveDaysAgo = Carbon::now()->subDays(105);

        $dueStaffs = User::where('id', '!=', $currentUserId)
            ->whereNotNull('joined_at')
            ->whereBetween('joined_at', [$oneHundredFiveDaysAgo, $eightyDaysAgo])
            ->get()
            ->map(function ($staff) {
                $staff->days_worked = Carbon::parse($staff->joined_at)->diffInDays(now());
                $staff->joined_date_formatted = Carbon::parse($staff->joined_at)->format('d/m/Y');

                return $staff;
            });

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
            ->where('danh_gia_nang_luc.user_id', '!=', $currentUserId)
            ->whereIn('danh_gia_nang_luc.TrangThai', ['ChoDanhGia', 'ChoPheDuyet', 'DaDuyet', 'HoanThanh'])
            ->orderBy('danh_gia_nang_luc.NgayHen', 'asc')
            ->get();

        $proposalOwners = User::orderBy('name')->get(['id', 'name']);
        $proposalCourses = DB::table('chuong_trinh_dao_tao')->orderBy('MaCTDT')->get(['MaCTDT', 'TenCTDT']);
        $proposalLessons = DB::table('bai_hoc')->orderBy('FK_MaCTDT')->orderBy('ThuTu')->get(['MaBaiHoc', 'FK_MaCTDT', 'TenBaiHoc']);

        return view('leader.dashboard', compact('staffs', 'standards', 'schedules', 'dueStaffs', 'proposalOwners', 'proposalCourses', 'proposalLessons'));
    }

    /**
     * Tạo lịch sát hạch mới cho 1 NHÂN VIÊN CỤ THỂ & TỰ ĐỘNG BẮN THÔNG BÁO (type = schedule)
     * (Bắt buộc chọn 1 nhân viên, không gửi hàng loạt)
     */
    public function createSchedule(Request $request): RedirectResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
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
            $now = now();
            $leaderId = auth()->id() ?? 1;

            // 1. Lưu bản ghi lịch sát hạch cho 1 cá nhân
            DB::table('danh_gia_nang_luc')->insert([
                'user_id' => $request->user_id,
                'NguoiDanhGia_id' => $leaderId,
                'MaCTDT' => $maCTDT_Value,
                'TieuChuan' => $tieuChuanTen,
                'NgayHen' => $request->NgayDanhGia,
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
     * Xử lý gửi thông báo trực tiếp cho nhân viên (Thông báo chung -> type = general)
     * Hỗ trợ gửi cho 1 cá nhân HOẶC gửi hàng loạt (user_id = 'all')
     */
    public function sendNotification(Request $request): RedirectResponse
    {
        $request->validate([
            'user_id' => 'required',
        ], [
            'user_id.required' => 'Vui lòng chọn người nhận thông báo.',
        ]);

        // Kiểm tra nếu chọn 1 người cụ thể thì phải tồn tại trong bảng users
        if ($request->user_id !== 'all') {
            $userExists = DB::table('users')->where('id', $request->user_id)->exists();
            if (! $userExists) {
                return redirect()->back()->with('error', 'Nhân viên được chọn không tồn tại.');
            }
        }

        // 1. Nhận linh hoạt mọi dạng biến Tiêu đề gửi lên
        $title = $request->input('title')
              ?? $request->input('tieu_de')
              ?? 'Thông báo từ Trưởng ca';

        // 2. Nhận linh hoạt mọi dạng biến Nội dung từ Form Modal
        $message = $request->input('message')
                ?? $request->input('content')
                ?? $request->input('noi_dung')
                ?? $request->input('note')
                ?? $request->input('description')
                ?? $request->input('comment')
                ?? 'Thông báo mới từ Trưởng ca.';

        DB::beginTransaction();
        try {
            // Lấy danh sách ID nhận thông báo (Tất cả hoặc 1 người)
            if ($request->user_id === 'all') {
                $targetUserIds = DB::table('users')->where('id', '!=', Auth::id())->pluck('id')->toArray();
            } else {
                $targetUserIds = [$request->user_id];
            }

            $notificationsToInsert = [];
            $now = now();

            foreach ($targetUserIds as $userId) {
                $notificationsToInsert[] = [
                    'user_id' => $userId,
                    'title' => trim($title),
                    'message' => trim($message),
                    'type' => 'general', // Phân loại thông báo chung (Màu xanh)
                    'is_read' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('notifications')->insert($notificationsToInsert);

            DB::commit();

            $successMsg = ($request->user_id === 'all')
                ? 'Đã phát thông báo tới TẤT CẢ nhân viên thành công!'
                : 'Đã gửi thông báo cho nhân viên thành công!';

            return redirect()->back()->with('success', $successMsg);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Lỗi khi gửi thông báo: '.$e->getMessage());
        }
    }

    /**
     * Ghi nhận sự cố / vi phạm của 1 nhân viên (Nhắc nhở / Sự cố -> type = warning)
     * Bắt buộc chọn đúng 1 nhân viên cụ thể (Không gửi hàng loạt)
     */
    public function recordIncident(Request $request): RedirectResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ], [
            'user_id.required' => 'Vui lòng chọn nhân viên vi phạm.',
            'user_id.exists' => 'Nhân viên được chọn không hợp lệ.',
        ]);

        // Đón nhận linh hoạt mọi biến nội dung ghi nhận từ Modal
        $noiDung = $request->input('description')
                ?? $request->input('incident_description')
                ?? $request->input('reason')
                ?? $request->input('content')
                ?? $request->input('message')
                ?? $request->input('noi_dung')
                ?? $request->input('note')
                ?? $request->input('comment')
                ?? 'Nhắc nhở vi phạm quy trình ca làm việc.';

        $loaiLoi = $request->input('loai_loi')
                ?? $request->input('incident_type')
                ?? $request->input('type')
                ?? 'Vận hành quầy bar';

        DB::beginTransaction();
        try {
            // Thêm bản ghi vào bảng notifications với type = 'warning'
            DB::table('notifications')->insert([
                'user_id' => $request->user_id,
                'title' => 'Nhắc nhở sự cố: '.$loaiLoi,
                'message' => trim($noiDung),
                'type' => 'warning',
                'is_read' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Đã ghi nhận sự cố và gửi nhắc nhở cho nhân viên thành công!');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Có lỗi xảy ra: '.$e->getMessage());
        }
    }

    /**
     * Xử lý lưu, tính kết quả đánh giá năng lực & TỰ ĐỘNG BẮN THÔNG BÁO BÁO KẾT QUẢ
     */
    public function saveScore(Request $request): RedirectResponse
    {
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

        DB::beginTransaction();
        try {
            // 1. Chuẩn bị dữ liệu cập nhật
            $updateData = [
                'Diem' => $diemTong,
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
            $updateData['ChiTietDeXuat'] = $isPassed ? null : json_encode([
                'source' => 'Trưởng ca đề xuất', 'proposed_by' => Auth::id(),
                'status' => 'ChoPheDuyet', 'details' => $proposalDetails,
            ], JSON_UNESCAPED_UNICODE);

            // 2. Cập nhật bảng kết quả
            DB::table('danh_gia_nang_luc')
                ->where('id', $request->assessment_id)
                ->update($updateData);

            // 3. TỰ ĐỘNG BẮN THÔNG BÁO KẾT QUẢ CHO NHÂN VIÊN
            $resultStatusText = $isPassed ? 'ĐẠT' : 'CHƯA ĐẠT';
            $notifMessage = "Trưởng ca đã hoàn tất chấm điểm bài đánh giá của bạn.\n".
                            "- Điểm tổng kết: {$diemTong}/100 ({$resultStatusText})\n\n".
                            "Điểm chi tiết các tiêu chí:\n".
                            "- Kiến thức nghiệp vụ: {$nv}/100\n".
                            "- Thao tác công việc: {$kn}/100\n".
                            "- Tuân thủ quy trình: {$qt}/100\n".
                            "- Thái độ phục vụ: {$pv}/100\n".
                            "- Tác phong & phối hợp: {$tp}/100\n";

            if (! empty($request->comment)) {
                $notifMessage .= "\nNhận xét của trưởng ca: ".$request->comment;
            }

            DB::table('notifications')->insert([
                'user_id' => $assessment->user_id,
                'title' => "Kết quả đánh giá năng lực: {$resultStatusText}",
                'message' => $notifMessage,
                'type' => 'general',
                'is_read' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            $msg = $isPassed
                ? "Đã gửi kết quả đánh giá ĐẠT ({$diemTong}/100 điểm) cho Quản lý cửa hàng phê duyệt và thông báo tới nhân viên!"
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
        $truongCa = Auth::user();
        $currentUserId = Auth::id();

        $staffs = User::where('id', '!=', $currentUserId)->get();

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

        foreach ($staffs as $staff) {
            $userChucVu = $staff->ChucVu ?? $staff->chuc_vu ?? '';
            $cleanChucVu = mb_strtoupper(trim($userChucVu), 'UTF-8');

            $assignedCourses = app(CourseProgress::class)->assignedCourses($userChucVu);
            $totalCoursesCount = $assignedCourses->count();
            $totalProgressSum = 0;
            $completedCoursesCount = 0;
            foreach ($assignedCourses as $course) {
                $progress = app(CourseProgress::class)->sync((int) $staff->id, (int) $course->MaCTDT);
                $totalProgressSum += $progress;
                if ($progress >= 100) {
                    $completedCoursesCount++;
                }
            }
            $staff->overall_progress = $totalCoursesCount > 0
                ? round($totalProgressSum / $totalCoursesCount, 1)
                : 0;

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

        $dueCount = $staffs->filter(function ($staff) {
            $startDate = $staff->joined_at ?? $staff->created_at ?? null;
            if (empty($startDate)) {
                return false;
            }
            $days = Carbon::parse($startDate)->diffInDays(now());

            return $days >= 80 && $days <= 105;
        })->count();

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
     * Nộp kết quả đánh giá năng lực theo mã DGNL
     */
    public function submitEvaluation(Request $request, $MaDGNL): RedirectResponse
    {
        DB::table('danh_gia_nang_luc')
            ->where('id', $MaDGNL)
            ->orWhere('MaDGNL', $MaDGNL)
            ->update([
                'TrangThai' => 'ChoPheDuyet',
                'updated_at' => now(),
            ]);

        return redirect()->back()->with('success', 'Đã nộp kết quả đánh giá thành công!');
    }
}
