<?php

namespace App\Models;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class QuizProgress
{
    public function assignedTests(string $position): Collection
    {
        return DB::table('bai_kiem_tra')->orderBy('MaBaiKiemTra')->get()
            ->filter(fn ($test) => in_array(mb_strtoupper(trim($test->ChucVu ?? '')),
                [mb_strtoupper(trim($position)), 'ALL STAFF', 'ALL', 'TẤT CẢ', '']))->values();
    }

    public function results(int $userId, Collection $tests): Collection
    {
        return DB::table('ket_qua_kiem_tra')->where('user_id', $userId)
            ->whereIn('MaBaiKiemTra', $tests->pluck('MaBaiKiemTra'))
            ->orderByDesc('created_at')->orderByDesc('id')->get();
    }
}
