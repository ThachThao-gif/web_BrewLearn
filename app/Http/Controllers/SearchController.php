<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = trim($request->input('q'));
        $userId = $request->input('user_id', 1);
        $currentUser = User::find($userId) ?? User::first();

        // Tìm kiếm trong bảng bài học
        $lessons = DB::table('bai_hoc')
            ->where('TenBaiHoc', 'LIKE', "%{$query}%")
            ->get();

        return view('search.results', compact('query', 'lessons', 'currentUser'));
    }
}
