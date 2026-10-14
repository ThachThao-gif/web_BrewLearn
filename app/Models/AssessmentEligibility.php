<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AssessmentEligibility
{
    public function initial(User $user): array
    {
        foreach (['bai_kiem_tra', 'ket_qua_kiem_tra', 'user_lesson_progresses'] as $table) {
            if (! Schema::hasTable($table)) {
                return ['ready' => false, 'missing' => ['Chưa có đủ dữ liệu học tập / quiz để kiểm tra điều kiện.']];
            }
        }
        $courses = app(CourseProgress::class)->assignedCourses($user->ChucVu ?? '');
        $lessons = DB::table('bai_hoc')->whereIn('FK_MaCTDT', $courses->pluck('MaCTDT'))->get();
        $tests = app(QuizProgress::class)->assignedTests($user->ChucVu ?? '');
        $missing = $this->missing($user->id, $lessons->pluck('MaBaiHoc')->all(), $tests->pluck('MaBaiKiemTra')->all());
        if ($courses->isEmpty() || $lessons->isEmpty()) {
            $missing[] = 'Chưa có khóa học / bài học được phân công để kiểm tra điều kiện.';
        }
        if (Schema::hasTable('yeu_cau_dao_tao_lai') && DB::table('yeu_cau_dao_tao_lai')->where('user_id', $user->id)->whereIn('trang_thai', ['ChoDuyet', 'DaDuyet'])->exists()) {
            $missing[] = 'Có yêu cầu đào tạo lại đang xử lý; tạo đánh giá lại từ vòng đào tạo hiện tại.';
        }

        return ['ready' => empty($missing), 'missing' => $missing];
    }

    public function round(object $round, int $userId, bool $requireConfirmation = true): array
    {
        $proposal = json_decode($round->proposal ?? '{}', true) ?? [];
        $lessons = $round->required_lessons === null
            ? ($proposal['details']['hoclaibaihoc']['lesson_ids'] ?? []) : (json_decode($round->required_lessons, true) ?? []);
        $quizzes = $round->required_quizzes === null
            ? DB::table('bai_kiem_tra')->whereIn('MaBaiHoc', $lessons)->pluck('MaBaiKiemTra')->all()
            : (json_decode($round->required_quizzes, true) ?? []);
        $baseline = json_decode($round->learning_baseline ?? '{}', true) ?? [];
        $missing = $this->missing($userId, $lessons, $quizzes, $round->created_at, $baseline['quiz_max_id'] ?? null);
        if ($requireConfirmation && ! $round->confirmed_at) {
            $missing[] = 'Người phụ trách chưa xác nhận hoàn thành thực hành / theo dõi của vòng này.';
        }

        return ['ready' => empty($missing), 'missing' => $missing];
    }

    private function missing(int $userId, array $lessonIds, array $testIds, ?string $since = null, ?int $quizMaxId = null): array
    {
        $missing = [];
        $progresses = DB::table('user_lesson_progresses')->where('user_id', $userId)->whereIn('lesson_id', $lessonIds)->get()->keyBy('lesson_id');
        $names = DB::table('bai_hoc')->whereIn('MaBaiHoc', $lessonIds)->pluck('TenBaiHoc', 'MaBaiHoc');
        $tests = DB::table('bai_kiem_tra')->whereIn('MaBaiKiemTra', $testIds)->get()->keyBy('MaBaiKiemTra');
        $results = DB::table('ket_qua_kiem_tra')->where('user_id', $userId)->whereIn('MaBaiKiemTra', $testIds);
        if ($since) {
            $results->where('created_at', '>=', $since);
        }
        if ($quizMaxId !== null) {
            $results->where('id', '>', $quizMaxId);
        }
        $scores = $results->get()->groupBy('MaBaiKiemTra')->map(fn ($rows) => $rows->max('Diem'));
        foreach ($lessonIds as $id) {
            $progress = $progresses->get($id);
            if (! $progress || (! $progress->is_completed && $progress->progress_percent < 95)
                || ($since && $progress->updated_at < $since)) {
                $name = $names->get($id) ?? '#'.$id;
                $missing[] = 'Chưa hoàn thành bài học: '.$name;
            }
        }
        foreach ($testIds as $id) {
            $test = $tests->get($id);
            if (! $test || ! $scores->has($id) || $scores->get($id) < ($test->DiemChuan ?? 80)) {
                $missing[] = 'Chưa đạt quiz: '.($test->TenBaiKiemTra ?? '#'.$id).($since ? ' (cần lượt thi trong vòng hiện tại)' : '');
            }
        }

        return $missing;
    }
}
