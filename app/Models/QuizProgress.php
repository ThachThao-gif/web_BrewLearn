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

    /**
     * Bài kiểm tra mở theo thứ tự (BR02): bài đầu luôn mở, bài sau mở khi bài liền trước đã có lượt đạt điểm chuẩn.
     */
    public function isUnlocked(int $userId, string $position, int $testId): bool
    {
        $tests = $this->assignedTests($position);
        $index = $tests->search(fn ($test) => (int) $test->MaBaiKiemTra === $testId);
        if ($index === false) {
            return false;
        }
        if ($index === 0) {
            return true;
        }
        $previous = $tests[$index - 1];

        return DB::table('ket_qua_kiem_tra')->where('user_id', $userId)->where('MaBaiKiemTra', $previous->MaBaiKiemTra)
            ->where('Diem', '>=', $previous->DiemChuan ?? 80)->exists();
    }

    public function results(int $userId, Collection $tests): Collection
    {
        return DB::table('ket_qua_kiem_tra')->where('user_id', $userId)
            ->whereIn('MaBaiKiemTra', $tests->pluck('MaBaiKiemTra'))
            ->orderByDesc('created_at')->orderByDesc('id')->get();
    }
}
