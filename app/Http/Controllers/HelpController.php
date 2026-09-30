<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HelpController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->input('user_id', 1);
        $user = DB::table('users')->where('id', $userId)->first();

        $myTickets = Schema::hasTable('yeu_cau_ho_tro') 
            ? DB::table('yeu_cau_ho_tro')->where('user_id', $userId)->orderBy('created_at', 'desc')->get()
            : [];

        return view('help.index', compact('user', 'myTickets'));
    }

    public function store(Request $request)
    {
        $userId = $request->input('user_id', 1);

        if (Schema::hasTable('yeu_cau_ho_tro')) {
            DB::table('yeu_cau_ho_tro')->insert([
                'user_id' => $userId,
                'loai_yeu_cau' => $request->loai_yeu_cau ?? 'Yêu cầu hỗ trợ',
                'chu_de' => $request->chu_de,
                'noi_dung' => $request->noi_dung,
                'muc_do_uu_tien' => $request->muc_do_uu_tien ?? 'TrungBinh',
                'trang_thai' => 'DaGui',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Đã gửi yêu cầu hỗ trợ thành công!');
    }
}