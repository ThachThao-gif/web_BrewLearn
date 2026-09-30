<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;
use App\Models\User;

class LeaderEvaluationController extends Controller
{
    /**
     * Danh sách Bộ tiêu chuẩn năng lực chuẩn hóa theo vị trí công việc
     */
    private function getStandardList()
    {
        return collect([
            (object)['MaCTDT' => 'STD_BARISTA', 'TenCTDT' => 'Tiêu chuẩn năng lực Barista'],
            (object)['MaCTDT' => 'STD_CASHIER', 'TenCTDT' => 'Tiêu chuẩn năng lực Thu ngân'],
            (object)['MaCTDT' => 'STD_SERVER',  'TenCTDT' => 'Tiêu chuẩn năng lực Phục vụ'],
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
                'danh_gia_nang_luc.Diem'
            )
            ->where('danh_gia_nang_luc.user_id', '!=', $currentUserId)
            ->whereIn('danh_gia_nang_luc.TrangThai', ['ChoDanhGia', 'ChoPheDuyet', 'DaDuyet', 'HoanThanh'])
            ->orderBy('danh_gia_nang_luc.NgayHen', 'asc')
            ->get();

        return view('leader.dashboard', compact('staffs', 'standards', 'schedules', 'dueStaffs'));
    }

    /**
     * Tạo lịch sát hạch mới cho 1 NHÂN VIÊN CỤ THỂ & TỰ ĐỘNG BẮN THÔNG BÁO (type = schedule)
     * (Bắt buộc chọn 1 nhân viên, không gửi hàng loạt)
     */
    public function createSchedule(Request $request): RedirectResponse
    {
        $request->validate([
            'user_id'     => 'required|exists:users,id',
            'MaCTDT'      => 'required',
            'NgayDanhGia' => 'required',
            'note'        => 'nullable|string|max:2000',
        ], [
            'user_id.required'     => 'Vui lòng chọn nhân viên.',
            'user_id.exists'       => 'Nhân viên được chọn không hợp lệ.',
            'MaCTDT.required'      => 'Vui lòng chọn tiêu chuẩn đánh giá.',
            'NgayDanhGia.required' => 'Vui lòng chọn thời gian đánh giá.',
        ]);

        $standardMap = [
            'STD_BARISTA' => 'Tiêu chuẩn năng lực Barista',
            'STD_CASHIER' => 'Tiêu chuẩn năng lực Thu ngân',
            'STD_SERVER'  => 'Tiêu chuẩn năng lực Phục vụ',
        ];

        $tieuChuanTen = $standardMap[$request->MaCTDT] ?? ($request->MaCTDT ?: 'Tiêu chuẩn năng lực Barista');
        $maCTDT_Value = is_numeric($request->MaCTDT) ? $request->MaCTDT : null;
        $ghiChuText   = $request->input('note') ?? $request->input('ghi_chu') ?? $request->input('comment');

        // Tạo nội dung thông báo chuẩn hóa
        $messageContent = "Bạn có lịch sát hạch '$tieuChuanTen' vào lúc " . Carbon::parse($request->NgayDanhGia)->format('H:i d/m/Y') . ".";
        if (!empty(trim($ghiChuText))) {
            $messageContent .= "\n\nLời dặn từ Trưởng ca: " . trim($ghiChuText);
        }

        DB::beginTransaction();
        try {
            $now = now();
            $leaderId = auth()->id() ?? 1;

            // 1. Lưu bản ghi lịch sát hạch cho 1 cá nhân
            DB::table('danh_gia_nang_luc')->insert([
                'user_id'         => $request->user_id,
                'NguoiDanhGia_id' => $leaderId,
                'MaCTDT'          => $maCTDT_Value,
                'TieuChuan'       => $tieuChuanTen,
                'NgayHen'         => $request->NgayDanhGia,
                'TrangThai'       => 'ChoDanhGia',
                'created_at'      => $now,
                'updated_at'      => $now,
            ]);

            // 2. Bắn thông báo cho nhân viên đó
            DB::table('notifications')->insert([
                'user_id'    => $request->user_id,
                'title'      => 'Lịch sát hạch năng lực mới từ Trưởng ca',
                'message'    => $messageContent,
                'type'       => 'schedule',
                'is_read'    => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Đã xếp lịch và phát thông báo tới nhân viên thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi tạo lịch sát hạch: ' . $e->getMessage());
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
            if (!$userExists) {
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
                    'user_id'    => $userId,
                    'title'      => trim($title),
                    'message'    => trim($message),
                    'type'       => 'general', // Phân loại thông báo chung (Màu xanh)
                    'is_read'    => 0,
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
            return redirect()->back()->with('error', 'Lỗi khi gửi thông báo: ' . $e->getMessage());
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
            'user_id.exists'   => 'Nhân viên được chọn không hợp lệ.',
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
                'user_id'    => $request->user_id,
                'title'      => 'Nhắc nhở sự cố: ' . $loaiLoi,
                'message'    => trim($noiDung),
                'type'       => 'warning',
                'is_read'    => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Đã ghi nhận sự cố và gửi nhắc nhở cho nhân viên thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Xử lý lưu, tính kết quả đánh giá năng lực & TỰ ĐỘNG BẮN THÔNG BÁO BÁO KẾT QUẢ
     */
    public function saveScore(Request $request): RedirectResponse
    {
        $request->validate([
            'assessment_id'  => 'required',
            'diem_nghiep_vu' => 'required|numeric|min:0|max:100',
            'diem_ky_nang'   => 'required|numeric|min:0|max:100',
            'diem_quy_trinh'  => 'required|numeric|min:0|max:100',
            'diem_phuc_vu'   => 'required|numeric|min:0|max:100',
            'diem_tac_phong' => 'required|numeric|min:0|max:100',
            'comment'        => 'nullable|string',
            'proposals'      => 'nullable|array',
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

        $assessment = DB::table('danh_gia_nang_luc')->where('id', $request->assessment_id)->first();

        if (!$assessment) {
            return redirect()->back()->with('error', 'Không tìm thấy hồ sơ đánh giá tương ứng!');
        }

        DB::beginTransaction();
        try {
            // 1. Chuẩn bị dữ liệu cập nhật
            $updateData = [
                'Diem'       => $diemTong,
                'NhanXet'    => $request->comment,
                'TrangThai'  => 'ChoPheDuyet',
                'updated_at' => now(),
            ];

            if (Schema::hasColumn('danh_gia_nang_luc', 'IsPassed')) {
                $updateData['IsPassed'] = $isPassed ? 1 : 0;
            }

            if (Schema::hasColumn('danh_gia_nang_luc', 'ChiTietDiem')) {
                $updateData['ChiTietDiem'] = json_encode([
                    'nghiep_vu' => $nv,
                    'ky_nang'   => $kn,
                    'quy_trinh'  => $qt,
                    'phuc_vu'   => $pv,
                    'tac_phong'  => $tp,
                ]);
            }

            if (!$isPassed && $request->has('proposals') && Schema::hasColumn('danh_gia_nang_luc', 'DeXuatXuLy')) {
                $updateData['DeXuatXuLy'] = json_encode($request->proposals);
            }

            // 2. Cập nhật bảng kết quả
            DB::table('danh_gia_nang_luc')
                ->where('id', $request->assessment_id)
                ->update($updateData);

            // 3. TỰ ĐỘNG BẮN THÔNG BÁO KẾT QUẢ CHO NHÂN VIÊN
            $resultStatusText = $isPassed ? 'ĐẠT' : 'CHƯA ĐẠT';
            $notifMessage = "Trưởng ca đã hoàn tất chấm điểm bài đánh giá của bạn.\n" .
                            "- Điểm tổng kết: {$diemTong}/100 ({$resultStatusText})\n";

            if (!empty($request->comment)) {
                $notifMessage .= "- Nhận xét: " . $request->comment;
            }

            DB::table('notifications')->insert([
                'user_id'    => $assessment->user_id,
                'title'      => "Kết quả đánh giá năng lực: {$resultStatusText}",
                'message'    => $notifMessage,
                'type'       => 'general',
                'is_read'    => 0,
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
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi lưu kết quả: ' . $e->getMessage());
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

        foreach ($staffs as $staff) {
            $userChucVu = $staff->ChucVu ?? $staff->chuc_vu ?? '';
            $cleanChucVu = mb_strtoupper(trim($userChucVu), 'UTF-8');

            $assignedCourses = DB::table('chuong_trinh_dao_tao')
                ->where(function ($query) use ($cleanChucVu) {
                    if (!empty($cleanChucVu)) {
                        $query->whereRaw('UPPER(TRIM(ChucVuApDung)) LIKE ?', ['%' . $cleanChucVu . '%']);
                    }
                    $query->orWhereRaw('UPPER(TRIM(ChucVuApDung)) IN (?, ?, ?)', ['ALL STAFF', 'ALL', 'TẤT CẢ']);
                })
                ->get();

            if ($assignedCourses->isEmpty()) {
                $assignedCourses = DB::table('chuong_trinh_dao_tao')->get();
            }

            $totalCoursesCount = $assignedCourses->count();
            $totalProgressSum = 0;
            $completedCoursesCount = 0;

            foreach ($assignedCourses as $course) {
                $allLessons = DB::table('bai_hoc')
                    ->where('FK_MaCTDT', $course->MaCTDT)
                    ->pluck('MaBaiHoc')
                    ->toArray();

                $hasRealtimeProgress = false;
                if (!empty($allLessons)) {
                    $hasRealtimeProgress = DB::table('user_lesson_progresses')
                        ->where('user_id', $staff->id)
                        ->whereIn('lesson_id', $allLessons)
                        ->exists();
                }

                if ($hasRealtimeProgress && count($allLessons) > 0) {
                    $completedLessonsCount = DB::table('user_lesson_progresses')
                        ->where('user_id', $staff->id)
                        ->whereIn('lesson_id', $allLessons)
                        ->where(function ($q) {
                            $q->where('is_completed', 1)
                              ->orWhere('progress_percent', '>=', 95);
                        })
                        ->count();

                    $courseProgress = round(($completedLessonsCount / count($allLessons)) * 100);

                    DB::table('nhan_vien_khoa_hoc')->updateOrInsert(
                        ['user_id' => $staff->id, 'MaCTDT' => $course->MaCTDT],
                        ['TienDo' => $courseProgress, 'updated_at' => now()]
                    );
                } else {
                    $dbRecord = DB::table('nhan_vien_khoa_hoc')
                        ->where('user_id', $staff->id)
                        ->where('MaCTDT', $course->MaCTDT)
                        ->first();

                    $courseProgress = $dbRecord ? round($dbRecord->TienDo ?? 0) : 0;
                }

                $totalProgressSum += $courseProgress;
                if ($courseProgress >= 100) {
                    $completedCoursesCount++;
                }
            }

            if ($totalProgressSum == 0) {
                $userDbCourses = DB::table('nhan_vien_khoa_hoc')
                    ->where('user_id', $staff->id)
                    ->get();

                if ($userDbCourses->isNotEmpty()) {
                    $totalCoursesCount = $userDbCourses->count();
                    $totalProgressSum = $userDbCourses->sum('TienDo');
                    $completedCoursesCount = $userDbCourses->where('TienDo', '>=', 100)->count();
                }
            }

            $staff->overall_progress = $totalCoursesCount > 0 
                ? round($totalProgressSum / $totalCoursesCount, 1) 
                : 0;

            $staff->completed_courses_count = $completedCoursesCount;
            $staff->total_courses_count = $totalCoursesCount;

            $staff->quiz_latest_score = round(
                DB::table('ket_qua_kiem_tra')
                    ->where('user_id', $staff->id)
                    ->avg('Diem') ?? 0,
                1
            );
        }

        $totalStaffs = $staffs->count();

        $dueCount = $staffs->filter(function ($staff) {
            $startDate = $staff->joined_at ?? $staff->created_at ?? null;
            if (empty($startDate)) {
                return false;
            }
            $days = Carbon::parse($startDate)->diffInDays(now());
            return ($days >= 80 && $days <= 105);
        })->count();

        $avgProgress    = $totalStaffs > 0 ? round($staffs->avg('overall_progress') ?? 0, 1) : 0;
        $highRiskCount  = $staffs->filter(fn($staff) => $staff->overall_progress < 30)->count();
        $avgQuizScore   = $totalStaffs > 0 ? round($staffs->avg('quiz_latest_score') ?? 0, 1) : 0;
        $avgRubricScore = $totalStaffs > 0 ? round($staffs->avg('RubricScore') ?? 0, 1) : 0;

        return view('leader.staff_list', compact(
            'truongCa',
            'staffs',
            'totalStaffs',
            'dueCount',
            'avgProgress',
            'highRiskCount',
            'avgQuizScore',
            'avgRubricScore'
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
                'TrangThai'  => 'ChoPheDuyet',
                'updated_at' => now(),
            ]);

        return redirect()->back()->with('success', 'Đã nộp kết quả đánh giá thành công!');
    }
}