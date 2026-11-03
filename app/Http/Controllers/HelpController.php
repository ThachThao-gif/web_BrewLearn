<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HelpController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $myTickets = DB::table('yeu_cau_ho_tro')->where('user_id', $user->id)->orderByDesc('created_at')->get();

        return view('help.index', compact('user', 'myTickets'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'loai_yeu_cau' => 'required|string|max:191',
            'chu_de' => 'required|string|max:191',
            'noi_dung' => 'required|string|max:10000',
            'muc_do_uu_tien' => 'required|in:Thap,TrungBinh,Cao,Khancap',
        ]);

        DB::table('yeu_cau_ho_tro')->insert($data + [
            'user_id' => $request->user()->id,
            'trang_thai' => 'DaGui',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Đã gửi yêu cầu hỗ trợ thành công!');
    }
}
