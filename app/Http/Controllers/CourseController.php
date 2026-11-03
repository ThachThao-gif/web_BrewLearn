<?php

namespace App\Http\Controllers;

use App\Models\AssessmentEligibility;
use App\Models\CourseProgress;
use App\Models\QuizProgress;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    /**
     * Trang tổng quan học tập của nhân viên; quản lý cửa hàng được chuyển sang trang quản lý.
     */
    public function dashboard(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        if ($user->isStoreManager()) {
            return redirect()->route('manager.dashboard');
        }

        $assignedCourses = $this->assignedCoursesWithLock($user->id, $user->ChucVu);
        $assignedTests = app(QuizProgress::class)->assignedTests($user->ChucVu ?? '');
        $quizResults = app(QuizProgress::class)->results($user->id, $assignedTests);

        return view('dashboard', [
            'currentUser' => $user,
            'assignedCourses' => $assignedCourses,
            'assignedCoursesCount' => $assignedCourses->count(),
            'inProgressCoursesCount' => $assignedCourses->where('TienDo', '>', 0)->where('TienDo', '<', 100)->count(),
            'completedCoursesCount' => $assignedCourses->where('TienDo', '>=', 100)->count(),
            'overallProgress' => $assignedCourses->isNotEmpty() ? round($assignedCourses->avg('TienDo'), 1) : 0,
            'avgQuizScore' => round($quizResults->avg('Diem') ?? 0, 1),
            'totalTestsCount' => $assignedTests->count(),
            'completedTestsCount' => $assignedTests->filter(fn ($test) => $quizResults->contains(
                fn ($result) => (int) $result->MaBaiKiemTra === (int) $test->MaBaiKiemTra && $result->Diem >= ($test->DiemChuan ?? 80)
            ))->count(),
        ]);
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        $courses = $this->assignedCoursesWithLock($user->id, $user->ChucVu);
        $retraining = $this->currentRetraining($user->id);
        $retrainingCourseIds = $this->retrainingCourseIds($retraining['checklist'] ?? []);

        return view('courses.index', compact('courses', 'retraining', 'retrainingCourseIds'));
    }

    public function show(Request $request, int $id): View|RedirectResponse
    {
        $user = $request->user();
        $course = $this->assignedCoursesWithLock($user->id, $user->ChucVu)->firstWhere('MaCTDT', $id);

        if (! $course) {
            return redirect()->route('courses.index')->with('error', 'Khóa học không tồn tại!');
        }
        if ($course->IsLocked && ($course->TienDo ?? 0) == 0) {
            return redirect()->route('courses.index')->with('error', 'Khóa học đang bị khóa!');
        }

        $lessons = DB::table('bai_hoc')->where('FK_MaCTDT', $course->MaCTDT)->orderBy('ThuTu')->get();
        $this->attachLessonStatus($user->id, $course->MaCTDT, $lessons);

        return view('courses.show', compact('course', 'lessons'));
    }

    public function showLesson(Request $request, int $id): View|RedirectResponse
    {
        $userId = $request->user()->id;
        $lesson = DB::table('bai_hoc')->where('MaBaiHoc', $id)->first();
        if (! $lesson) {
            return redirect()->route('courses.index')->with('error', 'Bài học không tồn tại!');
        }

        $course = DB::table('chuong_trinh_dao_tao')->where('MaCTDT', $lesson->FK_MaCTDT)->first();
        if ($course) {
            $course->TienDo = round(DB::table('nhan_vien_khoa_hoc')->where('user_id', $userId)
                ->where('MaCTDT', $course->MaCTDT)->value('TienDo') ?? 0, 1);
        }

        app(CourseProgress::class)->initializeLessons($userId, (int) $lesson->FK_MaCTDT);
        $lessonProgress = DB::table('user_lesson_progresses')->where('user_id', $userId)->where('lesson_id', $lesson->MaBaiHoc);
        if ($lessonProgress->exists()) {
            $lessonProgress->increment('visit_count');
        } else {
            DB::table('user_lesson_progresses')->insert([
                'user_id' => $userId, 'lesson_id' => $lesson->MaBaiHoc, 'progress_percent' => 0, 'is_completed' => 0,
                'visit_count' => 1, 'last_watched_seconds' => 0, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        $lessons = DB::table('bai_hoc')->where('FK_MaCTDT', $lesson->FK_MaCTDT)->orderBy('ThuTu')->get();
        $progresses = $this->attachLessonStatus($userId, $lesson->FK_MaCTDT, $lessons);

        return view('courses.lesson', [
            'lesson' => $lesson,
            'course' => $course,
            'lessons' => $lessons,
            'progress' => $progresses->get($lesson->MaBaiHoc),
            'nextLesson' => $lessons->first(fn ($item) => $item->ThuTu > $lesson->ThuTu),
            'prevLesson' => $lessons->last(fn ($item) => $item->ThuTu < $lesson->ThuTu),
        ]);
    }

    /**
     * Lưu tiến độ xem video; tiến độ chỉ tăng, từ 95% được tính là hoàn thành.
     */
    public function updateProgress(Request $request): JsonResponse
    {
        $data = $request->validate([
            'lesson_id' => 'required|integer',
            'current_time' => 'required|numeric|min:0',
            'progress_percent' => 'required|integer|min:0|max:100',
        ]);
        $userId = $request->user()->id;

        $lesson = DB::table('bai_hoc')->where('MaBaiHoc', $data['lesson_id'])->first();
        if (! $lesson) {
            return response()->json(['error' => 'Bài học không tồn tại'], 404);
        }

        app(CourseProgress::class)->initializeLessons($userId, (int) $lesson->FK_MaCTDT);
        $existing = DB::table('user_lesson_progresses')->where('user_id', $userId)->where('lesson_id', $lesson->MaBaiHoc)->first();

        $percent = max($existing->progress_percent ?? 0, $data['progress_percent']);
        $isCompleted = $percent >= 95 || ($existing && $existing->is_completed);
        if ($isCompleted) {
            $percent = 100;
        }

        DB::table('user_lesson_progresses')->updateOrInsert(
            ['user_id' => $userId, 'lesson_id' => $lesson->MaBaiHoc],
            [
                'last_watched_seconds' => $data['current_time'],
                'progress_percent' => $percent,
                'is_completed' => $isCompleted,
                'updated_at' => now(),
                'created_at' => $existing->created_at ?? now(),
            ]
        );

        return response()->json([
            'status' => 'success',
            'lesson_percent' => $percent,
            'is_completed' => $isCompleted,
            'course_progress' => app(CourseProgress::class)->sync($userId, (int) $lesson->FK_MaCTDT),
        ]);
    }

    public function completeLesson(Request $request, int $id): JsonResponse
    {
        $userId = $request->user()->id;
        $lesson = DB::table('bai_hoc')->where('MaBaiHoc', $id)->first();
        if (! $lesson) {
            return response()->json(['status' => 'error', 'message' => 'Bài học không tồn tại!'], 404);
        }

        app(CourseProgress::class)->initializeLessons($userId, (int) $lesson->FK_MaCTDT);
        DB::table('user_lesson_progresses')->updateOrInsert(
            ['user_id' => $userId, 'lesson_id' => $id],
            ['progress_percent' => 100, 'is_completed' => 1, 'updated_at' => now()]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Đã đánh dấu hoàn thành bài học!',
            'course_progress' => app(CourseProgress::class)->sync($userId, (int) $lesson->FK_MaCTDT),
        ]);
    }

    /**
     * Danh sách bài kiểm tra theo chức danh kèm lịch sử làm bài của nhân viên.
     */
    public function testsIndex(Request $request): View
    {
        $user = $request->user();
        $tests = app(QuizProgress::class)->assignedTests($user->ChucVu ?? '');
        $results = app(QuizProgress::class)->results($user->id, $tests)->groupBy('MaBaiKiemTra');

        foreach ($tests as $test) {
            $history = $results->get($test->MaBaiKiemTra, collect());
            $latest = $history->first();
            $test->History = $history;
            $test->HasPassed = $history->contains(fn ($row) => $row->Diem >= ($test->DiemChuan ?? 80));
            $test->Diem = $latest->Diem ?? null;
            $test->TrangThai = $latest ? ($latest->TrangThai ?? 'DaLam') : 'ChuaLam';
        }

        return view('tests.index', compact('tests'));
    }

    /**
     * Mở bài kiểm tra và cấp mã lượt làm bài, dùng để chống gửi trùng.
     */
    public function showTest(Request $request, int $id): View|RedirectResponse
    {
        $test = DB::table('bai_kiem_tra')->where('MaBaiKiemTra', $id)->first();
        if (! $test) {
            return redirect()->route('tests.index')->with('error', 'Bài kiểm tra không tồn tại!');
        }

        $questions = DB::table('cau_hoi_kiem_tra')->where('MaBaiKiemTra', $id)->orderBy('id')->get();
        $attemptToken = (string) Str::uuid();
        $request->session()->put('test_attempts.'.$attemptToken, ['user_id' => $request->user()->id, 'test_id' => $id]);

        return view('tests.show', compact('test', 'questions', 'attemptToken'));
    }

    /**
     * Chấm bài kiểm tra. Gửi lại cùng mã lượt sẽ trả về kết quả đã lưu, không tạo lượt mới.
     */
    public function submitTest(Request $request, int $id): JsonResponse
    {
        $request->validate(['attempt_token' => 'required|uuid', 'answers' => 'present|array', 'answers.*' => 'nullable|in:A,B,C,D']);
        $userId = $request->user()->id;
        $token = $request->input('attempt_token');
        $attempt = $request->session()->get('test_attempts.'.$token);
        if (! $attempt || (int) $attempt['user_id'] !== $userId || (int) $attempt['test_id'] !== $id) {
            return response()->json(['success' => false, 'message' => 'Lượt thi không hợp lệ. Vui lòng mở lại bài kiểm tra.'], 422);
        }

        $test = DB::table('bai_kiem_tra')->where('MaBaiKiemTra', $id)->first();
        if (! $test) {
            return response()->json(['success' => false, 'message' => 'Bài kiểm tra không tồn tại!'], 404);
        }
        $questions = DB::table('cau_hoi_kiem_tra')->where('MaBaiKiemTra', $id)->orderBy('id')->get()->values();
        if ($questions->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Bài kiểm tra chưa có câu hỏi nào!'], 400);
        }

        $answers = $request->input('answers', []);
        $correctCount = $questions->filter(fn ($question, $index) => isset($answers[$index])
            && strtoupper(trim((string) $answers[$index])) === strtoupper(trim((string) $question->DapAnDung)))->count();
        $totalQuestions = $questions->count();
        $score = (int) round($correctCount / $totalQuestions * 100);
        $passScore = $test->DiemChuan ?? 80;

        $result = DB::transaction(function () use ($userId, $id, $token, $score, $passScore, $correctCount, $totalQuestions) {
            DB::table('users')->where('id', $userId)->lockForUpdate()->first();
            $existing = DB::table('ket_qua_kiem_tra')->where('attempt_token', $token)->where('user_id', $userId)->where('MaBaiKiemTra', $id)->first();
            if ($existing) {
                return $existing;
            }
            $resultId = DB::table('ket_qua_kiem_tra')->insertGetId([
                'user_id' => $userId, 'MaBaiKiemTra' => $id, 'attempt_token' => $token,
                'Diem' => $score, 'TrangThai' => $score >= $passScore ? 'Dat' : 'KhongDat',
                'SoCauDung' => $correctCount, 'TongSoCau' => $totalQuestions,
                'created_at' => now(), 'updated_at' => now(),
            ]);

            return DB::table('ket_qua_kiem_tra')->find($resultId);
        });
        $score = (int) $result->Diem;

        return response()->json([
            'success' => true,
            'score' => $score,
            'correct_count' => (int) $result->SoCauDung,
            'total_questions' => (int) $result->TongSoCau,
            'message' => $result->TrangThai === 'Dat'
                ? "Chúc mừng! Bạn đã ĐẠT bài kiểm tra với điểm số {$score}/100."
                : "Rất tiếc! Bạn chỉ đạt {$score}/100 điểm. Chưa đủ điểm chuẩn ({$passScore} điểm).",
        ]);
    }

    /**
     * Khóa học được giao theo chức danh, kèm tiến độ và trạng thái khóa:
     * khóa học chỉ mở khi khóa học liền trước đã hoàn thành (hoặc đã bắt đầu học).
     */
    private function assignedCoursesWithLock(int $userId, ?string $position): Collection
    {
        $courses = app(CourseProgress::class)->assignedCourses($position ?? 'Barista');
        foreach ($courses as $index => $course) {
            $course->TienDo = app(CourseProgress::class)->sync($userId, (int) $course->MaCTDT);
            $course->IsLocked = $index > 0 && $course->TienDo == 0 && ($courses[$index - 1]->TienDo ?? 0) < 100;
        }

        return $courses;
    }

    /**
     * Gắn tiến độ và trạng thái (HoanThanh, DangHoc, ChuaHoc) cho từng bài học; trả về tiến độ theo mã bài học.
     */
    private function attachLessonStatus(int $userId, int $courseId, Collection $lessons): Collection
    {
        app(CourseProgress::class)->initializeLessons($userId, $courseId);
        $progresses = DB::table('user_lesson_progresses')->where('user_id', $userId)
            ->whereIn('lesson_id', $lessons->pluck('MaBaiHoc'))->get()->keyBy('lesson_id');

        foreach ($lessons as $lesson) {
            $progress = $progresses->get($lesson->MaBaiHoc);
            $lesson->TienDo = (int) ($progress->progress_percent ?? 0);
            $lesson->is_completed = $lesson->TienDo >= 95 || (bool) ($progress->is_completed ?? false);
            $lesson->LessonStatus = $lesson->is_completed ? 'HoanThanh' : ($lesson->TienDo > 0 ? 'DangHoc' : 'ChuaHoc');
        }

        return $progresses;
    }

    /**
     * Yêu cầu đào tạo lại đang xử lý của nhân viên: vòng hiện tại, việc cần làm và bước tiếp theo.
     *
     * @return array<string, mixed>|null
     */
    private function currentRetraining(int $userId): ?array
    {
        $plan = DB::table('yeu_cau_dao_tao_lai')->where('user_id', $userId)->where('trang_thai', 'DaDuyet')
            ->whereIn('tien_trinh', ['ChoGiao', 'DangThucHien', 'ChoDanhGiaLai'])->orderByDesc('id')->first();
        if (! $plan) {
            return null;
        }
        $round = DB::table('retraining_rounds')->where('request_id', $plan->id)->orderByDesc('round_number')->first();
        $data = ['plan' => $plan, 'round' => null, 'checklist' => [], 'owner' => null, 'reassessment' => null,
            'roundNumber' => ($round->round_number ?? 0) + ($plan->tien_trinh === 'ChoGiao' ? 1 : 0), 'state' => 'waiting_assignment'];
        if ($plan->tien_trinh === 'ChoGiao' || ! $round) {
            return $data;
        }
        $checklist = app(AssessmentEligibility::class)->roundChecklist($round, $userId);
        $reassessment = $round->assessment_id ? DB::table('danh_gia_nang_luc')->find($round->assessment_id) : null;
        $learningDone = collect($checklist)->every(fn (array $item) => $item['done']);

        return array_merge($data, [
            'round' => $round, 'checklist' => $checklist, 'reassessment' => $reassessment,
            'owner' => DB::table('users')->where('id', $round->owner_id)->value('name'),
            'state' => match (true) {
                $reassessment !== null && in_array($reassessment->TrangThai, ['ChoPheDuyet', 'TraLai']) => 'reviewing',
                $reassessment !== null => 'scheduled',
                (bool) $round->confirmed_at => 'confirmed',
                $learningDone => 'waiting_confirmation',
                default => 'learning',
            },
        ]);
    }

    /**
     * Khóa học chứa các bài học, bài kiểm tra được giao trong vòng đào tạo lại.
     *
     * @param  list<array{type: string, id: int, name: string, done: bool}>  $checklist
     * @return list<int>
     */
    private function retrainingCourseIds(array $checklist): array
    {
        $items = collect($checklist);
        $lessonIds = $items->where('type', 'lesson')->pluck('id')
            ->merge(DB::table('bai_kiem_tra')->whereIn('MaBaiKiemTra', $items->where('type', 'quiz')->pluck('id'))->pluck('MaBaiHoc'));

        return DB::table('bai_hoc')->whereIn('MaBaiHoc', $lessonIds)->distinct()->pluck('FK_MaCTDT')->map(fn ($id) => (int) $id)->all();
    }
}
