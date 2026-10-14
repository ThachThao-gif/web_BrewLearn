<?php

namespace App\Http\Controllers;

use App\Models\CourseProgress;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UserController extends Controller
{
    public function updateContact(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'secondary_email' => 'required|email|max:191',
            'phone' => 'required|string|max:20',
            'emergency_contact' => 'nullable|string|max:191',
            'emergency_phone' => 'nullable|string|max:20',
        ]);
        $request->user()->forceFill($data)->save();

        return back()->with('success', 'Đã lưu thông tin liên hệ.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);
        $request->user()->forceFill(['password' => Hash::make($data['password'])])->save();
        $request->session()->put('password_hash_web', $request->user()->password);
        $request->session()->regenerate();

        return back()->with('success', 'Đã đổi mật khẩu.');
    }

    public function logoutOtherDevices(Request $request): RedirectResponse
    {
        $request->validate(['current_password' => 'required|current_password']);
        Auth::logoutOtherDevices($request->input('current_password'));
        if (config('session.driver') === 'database') {
            DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))
                ->where('user_id', Auth::id())->where('id', '!=', $request->session()->getId())->delete();
        }

        return back()->with('success', 'Đã đăng xuất các thiết bị khác.');
    }

    public function updateNotifications(Request $request): RedirectResponse
    {
        $keys = ['notify_new_lessons', 'notify_quiz_reminders', 'notify_progress'];
        $request->validate(array_fill_keys($keys, 'sometimes|boolean'));
        $settings = [];
        foreach ($keys as $key) {
            $settings[$key] = $request->boolean($key);
        }
        $request->user()->forceFill(['notification_settings' => $settings])->save();

        return back()->with('success', 'Đã lưu cấu hình thông báo.');
    }

    public function updatePreferences(Request $request): RedirectResponse
    {
        $data = $request->validate(['theme' => 'required|in:light,dark,system', 'language' => 'required|in:vi']);
        $request->user()->forceFill($data)->save();

        return back()->with('success', 'Đã lưu tùy chọn giao diện.');
    }

    /**
     * Hiển thị trang chỉnh sửa hồ sơ nhân viên & thông báo
     */
    public function edit(Request $request, $id = null)
    {
        abort_if($id !== null && (int) $id !== (int) Auth::id(), 403, 'Bạn chỉ được chỉnh sửa hồ sơ của chính mình.');

        $user = $request->user();

        if (! $user) {
            return redirect()->back()->with('error', 'Không tìm thấy dữ liệu nhân viên!');
        }

        // 2. Lấy danh sách thông báo dành riêng cho user này
        $notifications = Schema::hasTable('notifications')
            ? DB::table('notifications')
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get()
            : collect([]);

        // 3. Truyền cả $user và $notifications sang View
        return view('users.edit', compact('user', 'notifications'));
    }

    /**
     * Xử lý cập nhật thông tin nhân viên vào Database
     */
    public function update(Request $request, $id)
    {
        abort_unless((int) $id === (int) $request->user()->id, 403, 'Bạn chỉ được chỉnh sửa hồ sơ của chính mình.');
        $user = User::findOrFail($id);

        // Validate dữ liệu gửi lên từ form
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_tag' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'phone' => 'required|string|max:20',
            'dob' => 'nullable|date',
            'id_card' => 'nullable|string|max:20',
            'ChiNhanh' => 'required|string|max:255',
            'tram_lam_viec' => 'nullable|string|max:255',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:20',
        ], [
            'name.required' => 'Họ và tên không được để trống.',
            'name_tag.required' => 'Tên trên bảng tên không được để trống.',
            'email.required' => 'Email không được để trống.',
            'email.unique' => 'Email này đã được sử dụng bởi nhân viên khác.',
            'phone.required' => 'Số điện thoại không được để trống.',
        ]);

        // Cập nhật vào Database
        $user->update($validated);

        return redirect()->back()->with('success', 'Đã cập nhật thông tin hồ sơ & gửi duyệt thành công!');
    }

    /**
     * Lấy chi tiết thông tin nhân viên & tiến độ học tập (Trả về JSON cho AJAX Modal)
     */
    public function getStaffDetail($id)
    {
        $staff = User::findOrFail($id);

        // ====================================================
        // 1. TÍNH KẾT QUẢ QUIZ (Bảng ket_qua_kiem_tra)
        // ====================================================
        // Tổng số lượt thi thực tế
        $quizAttempts = DB::table('ket_qua_kiem_tra')
            ->where('user_id', $id)
            ->count();

        // Lọc số lần không đạt
        $quizFailed = DB::table('ket_qua_kiem_tra')
            ->where('user_id', $id)
            ->whereIn('TrangThai', ['ChuaDat', 'Không đạt', 'chuadat', 'khongdat', 'KhongDat'])
            ->count();

        // Lấy điểm thi gần nhất (Cột 'Diem' mới nhất theo created_at)
        $latestQuiz = DB::table('ket_qua_kiem_tra')
            ->where('user_id', $id)
            ->latest('created_at')
            ->orderByDesc('id')
            ->first();

        $quizScore = $latestQuiz ? round($latestQuiz->Diem, 1) : 0;

        // ====================================================
        // 2. TÍNH TIẾN ĐỘ BÀI HỌC (Bảng user_lesson_progresses & bai_hoc)
        // ====================================================
        // Tổng số bài học hiện có trên hệ thống
        $courses = app(CourseProgress::class)->assignedCourses($staff->ChucVu ?? 'Barista');
        $courseProgresses = $courses->map(fn ($course) => app(CourseProgress::class)->sync((int) $id, (int) $course->MaCTDT));
        $lessonIds = DB::table('bai_hoc')->whereIn('FK_MaCTDT', $courses->pluck('MaCTDT'))->pluck('MaBaiHoc');
        $totalLessons = $lessonIds->count();

        // Lấy dữ liệu tiến độ bài học của nhân viên
        $userLessons = DB::table('user_lesson_progresses')
            ->where('user_id', $id)
            ->whereIn('lesson_id', $lessonIds)
            ->get();

        // Bài học đã hoàn thành (is_completed = 1)
        $completedLessons = $userLessons->where('is_completed', 1)->count();

        // Bài học đang học (chưa xong nhưng tiến độ > 0%)
        $inProgressLessons = $userLessons->where('is_completed', 0)
            ->where('progress_percent', '>', 0)
            ->count();

        // Bài học chưa bắt đầu
        $notStartedLessons = max(0, $totalLessons - ($completedLessons + $inProgressLessons));

        // Phần trăm tiến độ tổng thể bài học
        $progressPercent = round($courseProgresses->avg() ?? 0, 1);

        // Trả về JSON chuẩn cho Popup
        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $staff->id,
                'code' => 'NV-'.str_pad($staff->id, 3, '0', STR_PAD_LEFT),
                'name' => $staff->name,
                'chucVu' => $staff->ChucVu ?? $staff->chuc_vu ?? 'Thu ngân',
                'phone' => $staff->phone ?? $staff->SoDienThoai ?? 'N/A',
                'tram' => $staff->tram_lam_viec ?? $staff->Tram ?? 'Quầy Bar Phụ',

                // Tiến độ học tập
                'lessonsTotal' => $totalLessons,
                'lessonsCompleted' => $completedLessons,
                'lessonsInProgress' => $inProgressLessons,
                'lessonsNotStarted' => $notStartedLessons,
                'progress' => $progressPercent,

                // Kết quả Quiz từ CSDL
                'quizLatest' => $quizScore,
                'quizAttempts' => $quizAttempts,
                'quizFailed' => $quizFailed,

            ],
        ]);
    }
}
