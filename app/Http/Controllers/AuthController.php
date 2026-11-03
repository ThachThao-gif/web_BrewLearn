<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return $this->redirectByRole(Auth::user());
        }

        return back()->withErrors([
            'email' => 'Thông tin đăng nhập (Email hoặc Mật khẩu) không chính xác.',
        ])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Chuyển đến trang chính theo chức danh: quản lý cửa hàng, trưởng ca hoặc nhân viên.
     */
    private function redirectByRole(User $user): RedirectResponse
    {
        if ($user->isStoreManager()) {
            return redirect()->route('manager.dashboard');
        }
        if ($user->isLeader()) {
            return redirect()->route('leader.dashboard');
        }

        return redirect()->route('dashboard');
    }
}
