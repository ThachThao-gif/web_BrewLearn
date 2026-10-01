<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Hiển thị màn hình đăng nhập
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectUserByRole(Auth::user());
        }

        return view('auth.login');
    }

    // Xử lý nút Đăng Nhập
    public function login(Request $request)
    {
        // 1. Validate dữ liệu đầu vào
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Thử đăng nhập với Auth::attempt
        if (Auth::attempt($credentials, $request->has('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Rẽ nhánh chuyển hướng thông minh dựa vào ChucVu / Role
            return $this->redirectUserByRole($user);
        }

        // 3. Nếu sai email hoặc password
        return back()->withErrors([
            'email' => 'Thông tin đăng nhập (Email hoặc Mật khẩu) không chính xác.',
        ])->onlyInput('email');
    }

    /**
     * Hàm phụ trợ: Điều hướng theo vai trò thực tế
     */
    private function redirectUserByRole($user)
    {
        $role = $user->ChucVu ?? $user->role;

        switch ($role) {
            case 'HR':
            case 'HR Đào tạo':
                return redirect()->route('hr.dashboard');

            case 'Quan Ly Vung':
            case 'Quản lý vùng':
                return redirect()->route('area.dashboard');

            case 'Quan Ly Cua Hang':
            case 'Quản lý cửa hàng':
                return redirect()->route('manager.dashboard');

            case 'Trưởng Ca':
            case 'Trưởng ca':
            case 'truong_ca':
            case 'quan_ly':
                return redirect()->route('leader.dashboard');

            default:
                // Nhân viên / Barista / Thực tập sinh -> Sang Dashboard Học tập
                return redirect()->route('dashboard');
        }
    }

    // Xử lý Đăng xuất
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
