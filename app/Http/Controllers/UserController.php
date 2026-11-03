<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
     * Trang tài khoản của người dùng đang đăng nhập.
     */
    public function edit(Request $request, ?int $id = null): View
    {
        abort_if($id !== null && $id !== (int) Auth::id(), 403, 'Bạn chỉ được chỉnh sửa hồ sơ của chính mình.');

        return view('users.edit', ['user' => $request->user()]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        abort_unless($id === (int) $request->user()->id, 403, 'Bạn chỉ được chỉnh sửa hồ sơ của chính mình.');
        $user = $request->user();

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
        $user->update($validated);

        return back()->with('success', 'Đã cập nhật thông tin hồ sơ & gửi duyệt thành công!');
    }
}
