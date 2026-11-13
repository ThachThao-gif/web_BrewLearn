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
}
