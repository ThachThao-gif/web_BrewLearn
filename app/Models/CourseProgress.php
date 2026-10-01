<?php

namespace App\Models;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CourseProgress
{
    public function assignedCourses(string $position): Collection
    {
        return DB::table('chuong_trinh_dao_tao')->where(function ($query) use ($position): void {
            $query->whereRaw('UPPER(TRIM(ChucVuApDung)) = ?', [mb_strtoupper(trim($position), 'UTF-8')])
                ->orWhereRaw('UPPER(TRIM(ChucVuApDung)) IN (?, ?, ?)', ['ALL STAFF', 'ALL', 'TẤT CẢ']);
        })->orderBy('MaCTDT')->get();
    }

    public function initializeLessons(int $userId, int $courseId): void
    {
        $baseline = (float) (DB::table('nhan_vien_khoa_hoc')->where('user_id', $userId)
            ->where('MaCTDT', $courseId)->value('demo_progress_baseline') ?? 0);
        foreach (DB::table('bai_hoc')->where('FK_MaCTDT', $courseId)->pluck('MaBaiHoc') as $lessonId) {
            if (! DB::table('user_lesson_progresses')->where('user_id', $userId)->where('lesson_id', $lessonId)->exists()) {
                DB::table('user_lesson_progresses')->insert([
                    'user_id' => $userId, 'lesson_id' => $lessonId,
                    'progress_percent' => min(100, max(0, round($baseline))),
                    'is_completed' => $baseline >= 100, 'visit_count' => 0,
                    'last_watched_seconds' => 0, 'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        }
    }

    public function sync(int $userId, int $courseId): float
    {
        $record = DB::table('nhan_vien_khoa_hoc')->where('user_id', $userId)->where('MaCTDT', $courseId)->first();
        if (! Schema::hasTable('bai_hoc') || ! Schema::hasTable('user_lesson_progresses')) {
            return (float) ($record->TienDo ?? 0);
        }
        $lessonIds = DB::table('bai_hoc')->where('FK_MaCTDT', $courseId)->pluck('MaBaiHoc');
        $progresses = DB::table('user_lesson_progresses')->where('user_id', $userId)
            ->whereIn('lesson_id', $lessonIds)->get()->keyBy('lesson_id');
        if ($lessonIds->isEmpty() || $progresses->isEmpty()) {
            $progress = (float) ($record->TienDo ?? 0);
        } else {
            $sum = $lessonIds->sum(function ($lessonId) use ($progresses, $record): float {
                $lesson = $progresses->get($lessonId);
                if (! $lesson) {
                    return (float) ($record->demo_progress_baseline ?? 0);
                }

                return $lesson->is_completed || $lesson->progress_percent >= 95
                    ? 100 : min(100, max(0, (float) $lesson->progress_percent));
            });
            $progress = round($sum / $lessonIds->count(), 1);
        }
        DB::table('nhan_vien_khoa_hoc')->updateOrInsert(
            ['user_id' => $userId, 'MaCTDT' => $courseId],
            ['TienDo' => $progress, 'TrangThai' => $progress >= 100 ? 'HoanThanh' : ($progress > 0 ? 'DangHoc' : 'ChuaHoc'), 'updated_at' => now()]
        );

        return $progress;
    }
}
