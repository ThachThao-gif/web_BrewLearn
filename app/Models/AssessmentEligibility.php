<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class AssessmentEligibility
{
    /**
     * Tiến trình của yêu cầu đào tạo lại chưa kết thúc (chưa Hoàn tất, chưa Dừng đào tạo lại).
     */
    public const OPEN_RETRAINING_STATES = ['ChoGiao', 'DangThucHien', 'ChoDanhGiaLai'];

    public function initial(User $user): array
    {
        $courses = app(CourseProgress::class)->assignedCourses($user->ChucVu ?? '');
        $lessons = DB::table('bai_hoc')->whereIn('FK_MaCTDT', $courses->pluck('MaCTDT'))->get();
        $tests = app(QuizProgress::class)->assignedTests($user->ChucVu ?? '');
        $missing = $this->missing($user->id, $lessons->pluck('MaBaiHoc')->all(), $tests->pluck('MaBaiKiemTra')->all());
        if ($courses->isEmpty() || $lessons->isEmpty()) {
            $missing[] = 'Chưa có khóa học / bài học được phân công để kiểm tra điều kiện.';
        }
        if (DB::table('yeu_cau_dao_tao_lai')->where('user_id', $user->id)->whereIn('tien_trinh', self::OPEN_RETRAINING_STATES)->exists()) {
            $missing[] = 'Có yêu cầu đào tạo lại đang xử lý; tạo đánh giá lại từ vòng đào tạo hiện tại.';
        } elseif (DB::table('yeu_cau_dao_tao_lai')->where('user_id', $user->id)->where('tien_trinh', 'DungDaoTao')->exists()) {
            $missing[] = 'Đã dừng đào tạo lại sau 3 vòng vẫn chưa đạt; trưởng ca và quản lý quyết định hướng xử lý ngoài hệ thống.';
        }

        return ['ready' => empty($missing), 'missing' => $missing];
    }

    public function round(object $round, int $userId, bool $requireConfirmation = true): array
    {
        $checklist = $this->roundChecklist($round, $userId);
        $missing = $this->messages($checklist, true);
        if ($requireConfirmation && ! $round->confirmed_at) {
            $missing[] = 'Người phụ trách chưa xác nhận hoàn thành thực hành / theo dõi của vòng này.';
        }

        return ['ready' => empty($missing), 'missing' => $missing, 'required' => count($checklist)];
    }

    /**
     * Từng bài học và bài kiểm tra bắt buộc của vòng đào tạo lại, kèm trạng thái đã đáp ứng hay chưa.
     *
     * @return list<array{type: string, id: int, name: string, done: bool}>
     */
    public function roundChecklist(object $round, int $userId): array
    {
        $proposal = json_decode($round->proposal ?? '{}', true) ?? [];
        $lessons = $round->required_lessons === null
            ? ($proposal['details']['hoclaibaihoc']['lesson_ids'] ?? []) : (json_decode($round->required_lessons, true) ?? []);
        $quizzes = $round->required_quizzes === null
            ? DB::table('bai_kiem_tra')->whereIn('MaBaiHoc', $lessons)->pluck('MaBaiKiemTra')->all()
            : (json_decode($round->required_quizzes, true) ?? []);
        $baseline = json_decode($round->learning_baseline ?? '{}', true) ?? [];

        return $this->checklist($userId, $lessons, $quizzes, $round->created_at, $baseline['quiz_max_id'] ?? null);
    }

    private function missing(int $userId, array $lessonIds, array $testIds): array
    {
        return $this->messages($this->checklist($userId, $lessonIds, $testIds), false);
    }

    /**
     * @param  list<array{type: string, id: int, name: string, done: bool}>  $checklist
     * @return list<string>
     */
    private function messages(array $checklist, bool $inRound): array
    {
        return collect($checklist)->reject(fn (array $item) => $item['done'])->map(fn (array $item) => $item['type'] === 'lesson'
            ? 'Chưa hoàn thành bài học: '.$item['name']
            : 'Chưa đạt bài kiểm tra: '.$item['name'].($inRound ? ' (cần lượt làm bài trong vòng hiện tại)' : ''))->values()->all();
    }

    /**
     * @return list<array{type: string, id: int, name: string, done: bool}>
     */
    private function checklist(int $userId, array $lessonIds, array $testIds, ?string $since = null, ?int $quizMaxId = null): array
    {
        $items = [];
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
            $done = $progress && ($progress->is_completed || $progress->progress_percent >= 95)
                && ! ($since && $progress->updated_at < $since);
            $items[] = ['type' => 'lesson', 'id' => (int) $id, 'name' => $names->get($id) ?? 'Bài học '.$id, 'done' => (bool) $done];
        }
        foreach ($testIds as $id) {
            $test = $tests->get($id);
            $done = $test && $scores->has($id) && $scores->get($id) >= ($test->DiemChuan ?? 80);
            $items[] = ['type' => 'quiz', 'id' => (int) $id, 'name' => $test->TenBaiKiemTra ?? 'Bài kiểm tra '.$id, 'done' => (bool) $done];
        }

        return $items;
    }
}
