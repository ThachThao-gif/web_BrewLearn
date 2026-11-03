<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    /**
     * Tìm bài học theo tên.
     */
    public function index(Request $request): View
    {
        $query = trim((string) $request->input('q'));
        $lessons = DB::table('bai_hoc')->where('TenBaiHoc', 'LIKE', "%{$query}%")->get();

        return view('search.results', compact('query', 'lessons'));
    }
}
