<?php

namespace App\Http\Controllers;

use App\Models\AssessmentEligibility;
use App\Models\CourseProgress;
use App\Models\QuizProgress;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    /**
     * Lấy ID người dùng đang đăng nhập; không tin tham số user_id gửi từ trình duyệt.
     */
    private function getCurrentUserId(Request $request): int
    {
        return (int) Auth::id();
    }

    /**
     * 1. TRANG DASHBOARD THỐNG KÊ
     */
    public function dashboard(Request $request)
    {
        if (in_array(mb_strtolower(trim(auth()->user()?->ChucVu ?? '')), ['quản lý cửa hàng', 'quan ly cua hang', 'manager'])) {
            return redirect()->route('manager.dashboard');
        }
        $userId = $this->getCurrentUserId($request);
        $currentUser = User::find($userId) ?? User::first();
        $userId = $currentUser->id ?? 1;
        $userChucVu = $currentUser->ChucVu ?? 'Barista';

        $assignedCourses = $this->getAssignedCoursesWithLockStatus($userId, $userChucVu);

        $assignedCoursesCount = $assignedCourses->count();
        $inProgressCoursesCount = $assignedCourses->where('TienDo', '>', 0)->where('TienDo', '<', 100)->count();
        $completedCoursesCount = $assignedCourses->where('TienDo', '>=', 100)->count();

        $overallProgress = $assignedCoursesCount > 0
            ? round($assignedCourses->avg('TienDo'), 1)
            : 0;

        $assignedTests = app(QuizProgress::class)->assignedTests($userChucVu);
        $quizResults = app(QuizProgress::class)->results($userId, $assignedTests);
        $avgQuizScore = round($quizResults->avg('Diem') ?? 0, 1);
        $totalTestsCount = $assignedTests->count();
        $completedTestsCount = $assignedTests->filter(fn ($test) => $quizResults->contains(
            fn ($result) => (int) $result->MaBaiKiemTra === (int) $test->MaBaiKiemTra && $result->Diem >= ($test->DiemChuan ?? 80)
        ))->count();

        $currentLesson = null;

        return view('welcome', compact(
            'currentUser',
            'assignedCoursesCount',
            'inProgressCoursesCount',
            'overallProgress',
            'completedCoursesCount',
            'avgQuizScore',
            'completedTestsCount',
            'totalTestsCount',
            'assignedCourses',
            'currentLesson'
        ));
    }

    /**
     * 2. TRANG DANH SÁCH KHÓA HỌC
     */
    public function index(Request $request)
    {
        $userId = $this->getCurrentUserId($request);
        $currentUser = User::find($userId) ?? User::first();
        $userId = $currentUser->id ?? 1;
        $userChucVu = $currentUser->ChucVu ?? 'Barista';

        $courses = $this->getAssignedCoursesWithLockStatus($userId, $userChucVu);
        $retraining = $this->currentRetraining((int) $userId);
        $retrainingCourseIds = $this->retrainingCourseIds($retraining['checklist'] ?? []);

        return view('courses.index', compact('courses', 'currentUser', 'retraining', 'retrainingCourseIds'));
    }

    /**
     * 3. TRANG CHI TIẾT KHÓA HỌC
     */
    public function show(Request $request, $id)
    {
        $userId = $this->getCurrentUserId($request);
        $currentUser = User::find($userId) ?? User::first();
        $userId = $currentUser->id ?? 1;
        $userChucVu = $currentUser->ChucVu ?? 'Barista';

        $courses = $this->getAssignedCoursesWithLockStatus($userId, $userChucVu);
        $course = $courses->firstWhere('MaCTDT', $id);

        if (! $course) {
            return redirect()->route('courses.index', ['user_id' => $userId])
                ->with('error', 'Khóa học không tồn tại!');
        }

        if ($course->IsLocked && ($course->TienDo ?? 0) == 0) {
            return redirect()->route('courses.index', ['user_id' => $userId])
                ->with('error', 'Khóa học đang bị khóa!');
        }

        // Lấy danh sách bài học
        $lessons = DB::table('bai_hoc')
            ->where('FK_MaCTDT', $course->MaCTDT)
            ->orderBy('ThuTu', 'asc')
            ->get();

        // Lấy danh sách tiến độ chi tiết của từng bài học
        app(CourseProgress::class)->initializeLessons((int) $userId, (int) $course->MaCTDT);
        $userProgresses = DB::table('user_lesson_progresses')
            ->where('user_id', $userId)
            ->whereIn('lesson_id', $lessons->pluck('MaBaiHoc'))
            ->get()
            ->keyBy('lesson_id');

        // Gán % thực tế và trạng thái cho từng bài học
        foreach ($lessons as $lesson) {
            $prog = $userProgresses->get($lesson->MaBaiHoc);
            $percent = $prog ? $prog->progress_percent : 0;

            $lesson->TienDo = $percent;
            if ($percent >= 95 || ($prog && $prog->is_completed)) {
                $lesson->LessonStatus = 'HoanThanh';
            } elseif ($percent > 0) {
                $lesson->LessonStatus = 'DangHoc';
            } else {
                $lesson->LessonStatus = 'ChuaHoc';
            }
        }

        return view('courses.show', compact('course', 'lessons', 'currentUser'));
    }

    /**
     * 4. HỌC TIẾP TỰ ĐỘNG
     */
    public function continueCourse(Request $request)
    {
        $userId = $this->getCurrentUserId($request);
        $currentUser = User::find($userId) ?? User::first();
        $userId = $currentUser->id ?? 1;
        $userChucVu = $currentUser->ChucVu ?? 'Barista';

        $courses = $this->getAssignedCoursesWithLockStatus($userId, $userChucVu);

        $targetCourse = $courses->firstWhere(function ($c) {
            return $c->TienDo > 0 && $c->TienDo < 100;
        }) ?? $courses->firstWhere('IsLocked', false) ?? $courses->first();

        if (! $targetCourse) {
            return redirect()->route('courses.index', ['user_id' => $userId]);
        }

        return redirect()->route('courses.show', ['id' => $targetCourse->MaCTDT, 'user_id' => $userId]);
    }

    /**
     * 5. HIỂN THỊ CHI TIẾT BÀI HỌC
     */
    public function showLesson(Request $request, $id)
    {
        $userId = $this->getCurrentUserId($request);
        $currentUser = User::find($userId) ?? User::first();
        $userId = $currentUser->id ?? 1;

        $lesson = DB::table('bai_hoc')
            ->where('MaBaiHoc', $id)
            ->first();

        if (! $lesson) {
            return redirect()->route('courses.index', ['user_id' => $userId])
                ->with('error', 'Bài học không tồn tại!');
        }

        // Lấy thông tin khóa học & Tiến độ tổng khóa học
        $course = DB::table('chuong_trinh_dao_tao')
            ->where('MaCTDT', $lesson->FK_MaCTDT ?? null)
            ->first();

        if ($course) {
            $userCourse = DB::table('nhan_vien_khoa_hoc')
                ->where('user_id', $userId)
                ->where('MaCTDT', $course->MaCTDT)
                ->first();
            $course->TienDo = round($userCourse->TienDo ?? 0, 1);
        }

        // Lấy danh sách bài học thuộc khóa
        $lessonsInCourse = DB::table('bai_hoc')
            ->where('FK_MaCTDT', $lesson->FK_MaCTDT)
            ->orderBy('ThuTu', 'asc')
            ->get();

        app(CourseProgress::class)->initializeLessons((int) $userId, (int) $lesson->FK_MaCTDT);

        // Ghi nhận 1 lượt truy cập bài học
        $existingProgress = DB::table('user_lesson_progresses')
            ->where('user_id', $userId)
            ->where('lesson_id', $lesson->MaBaiHoc)
            ->first();

        if ($existingProgress) {
            DB::table('user_lesson_progresses')
                ->where('user_id', $userId)
                ->where('lesson_id', $lesson->MaBaiHoc)
                ->increment('visit_count');
        } else {
            DB::table('user_lesson_progresses')->insert([
                'user_id' => $userId,
                'lesson_id' => $lesson->MaBaiHoc,
                'progress_percent' => 0,
                'is_completed' => 0,
                'visit_count' => 1,
                'last_watched_seconds' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Lấy danh sách tiến độ bài học của User
        $userProgresses = DB::table('user_lesson_progresses')
            ->where('user_id', $userId)
            ->whereIn('lesson_id', $lessonsInCourse->pluck('MaBaiHoc'))
            ->get()
            ->keyBy('lesson_id');

        // Đánh dấu hoàn thành cho từng bài học trong danh sách
        foreach ($lessonsInCourse as $item) {
            $prog = $userProgresses->get($item->MaBaiHoc);
            $percent = $prog ? (int) $prog->progress_percent : 0;
            $isCompleted = $prog ? (bool) $prog->is_completed : false;

            $item->TienDo = $percent;
            if ($percent >= 95 || $isCompleted) {
                $item->is_completed = true;
                $item->LessonStatus = 'HoanThanh';
            } elseif ($percent > 0) {
                $item->is_completed = false;
                $item->LessonStatus = 'DangHoc';
            } else {
                $item->is_completed = false;
                $item->LessonStatus = 'ChuaHoc';
            }
        }

        // Tiến độ bài đang phát hiện tại
        $progress = $userProgresses->get($lesson->MaBaiHoc);

        // Bài học trước / sau
        $nextLesson = DB::table('bai_hoc')
            ->where('FK_MaCTDT', $lesson->FK_MaCTDT)
            ->where('ThuTu', '>', $lesson->ThuTu ?? 0)
            ->orderBy('ThuTu', 'asc')
            ->first();

        $prevLesson = DB::table('bai_hoc')
            ->where('FK_MaCTDT', $lesson->FK_MaCTDT)
            ->where('ThuTu', '<', $lesson->ThuTu ?? 0)
            ->orderBy('ThuTu', 'desc')
            ->first();

        $lessons = $lessonsInCourse;

        return view('courses.lesson', compact(
            'lesson',
            'course',
            'currentUser',
            'nextLesson',
            'prevLesson',
            'progress',
            'lessonsInCourse',
            'lessons'
        ));
    }

    /**
     * 6. AJAX CẬP NHẬT TIẾN ĐỘ XEM VIDEO
     */
    public function updateProgress(Request $request)
    {
        $request->validate([
            'lesson_id' => 'required|integer',
            'current_time' => 'required|numeric|min:0',
            'progress_percent' => 'required|integer|min:0|max:100',
        ]);

        $userId = $this->getCurrentUserId($request);
        $lessonId = $request->lesson_id;
        $currentTime = $request->current_time;
        $incomingPercent = $request->progress_percent;

        $lesson = DB::table('bai_hoc')->where('MaBaiHoc', $lessonId)->first();
        if (! $lesson) {
            return response()->json(['error' => 'Bài học không tồn tại'], 404);
        }

        $maCTDT = $lesson->FK_MaCTDT;
        app(CourseProgress::class)->initializeLessons((int) $userId, (int) $maCTDT);

        $existing = DB::table('user_lesson_progresses')
            ->where('user_id', $userId)
            ->where('lesson_id', $lessonId)
            ->first();

        $maxPercent = $existing ? max($existing->progress_percent, $incomingPercent) : $incomingPercent;
        if ($maxPercent >= 95 || ($existing && $existing->is_completed)) {
            $maxPercent = 100;
        }
        $isCompleted = ($maxPercent >= 95) ? 1 : ($existing ? $existing->is_completed : 0);

        DB::table('user_lesson_progresses')->updateOrInsert(
            [
                'user_id' => $userId,
                'lesson_id' => $lessonId,
            ],
            [
                'last_watched_seconds' => $currentTime,
                'progress_percent' => $maxPercent,
                'is_completed' => $isCompleted,
                'updated_at' => now(),
                'created_at' => $existing ? $existing->created_at : now(),
            ]
        );

        // Tính lại % tiến độ khóa học
        $courseProgress = $this->recalculateCourseProgress($userId, $maCTDT);

        return response()->json([
            'status' => 'success',
            'lesson_percent' => $maxPercent,
            'is_completed' => (bool) $isCompleted,
            'course_progress' => $courseProgress,
        ]);
    }

    /**
     * 7. ĐÁNH DẤU HOÀN THÀNH BÀI HỌC
     */
    public function completeLesson(Request $request, $id)
    {
        $userId = $this->getCurrentUserId($request);

        $lesson = DB::table('bai_hoc')->where('MaBaiHoc', $id)->first();
        if (! $lesson) {
            return response()->json(['status' => 'error', 'message' => 'Bài học không tồn tại!'], 404);
        }

        app(CourseProgress::class)->initializeLessons((int) $userId, (int) $lesson->FK_MaCTDT);
        DB::table('user_lesson_progresses')->updateOrInsert(
            ['user_id' => $userId, 'lesson_id' => $id],
            [
                'progress_percent' => 100,
                'is_completed' => 1,
                'updated_at' => now(),
            ]
        );

        // Tính lại tiến độ khóa học tổng thể
        $courseProgress = $this->recalculateCourseProgress($userId, $lesson->FK_MaCTDT);

        return response()->json([
            'status' => 'success',
            'message' => 'Đã đánh dấu hoàn thành bài học!',
            'course_progress' => $courseProgress,
        ]);
    }

    /**
     * 8. TRANG DANH SÁCH BÀI KIỂM TRA & ĐÁNH GIÁ (Khôi phục chuẩn)
     */
    public function testsIndex(Request $request)
    {
        $userId = $this->getCurrentUserId($request);
        $currentUser = User::find($userId) ?? User::first();

        // Lấy danh sách tất cả bài kiểm tra
        $tests = app(QuizProgress::class)->assignedTests($currentUser->ChucVu ?? '');

        // Lấy kết quả làm bài của user
        $userResults = app(QuizProgress::class)->results($userId, $tests)->groupBy('MaBaiKiemTra');

        // Ghép điểm và trạng thái vào từng bài test
        foreach ($tests as $test) {
            $history = $userResults->get($test->MaBaiKiemTra, collect());
            $result = $history->first();
            $test->History = $history;
            $test->HasPassed = $history->contains(fn ($row) => $row->Diem >= ($test->DiemChuan ?? 80));
            $test->Diem = $result->Diem ?? null;
            $test->TrangThai = $result ? ($result->TrangThai ?? 'DaLam') : 'ChuaLam';
        }

        return view('tests.index', compact('tests', 'currentUser'));
    }

    /**
     * TÍNH LẠI TIẾN ĐỘ TỔNG KHÓA HỌC
     */
    private function recalculateCourseProgress($userId, $maCTDT)
    {
        return app(CourseProgress::class)->sync((int) $userId, (int) $maCTDT);
    }

    /**
     * LẤY DANH SÁCH KHÓA HỌC ĐƯỢC GIAO KÈM TRẠNG THÁI KHÓA/MỞ
     */
    /**
     * Yêu cầu đào tạo lại đang xử lý của nhân viên (UC-NV07): vòng hiện tại, việc cần làm và bước tiếp theo.
     *
     * @return array<string, mixed>|null
     */
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

    private function currentRetraining(int $userId): ?array
    {
        if (! Schema::hasTable('yeu_cau_dao_tao_lai') || ! Schema::hasTable('retraining_rounds')) {
            return null;
        }
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

    private function getAssignedCoursesWithLockStatus($userId, $userChucVu)
    {
        $courses = app(CourseProgress::class)->assignedCourses($userChucVu);
        foreach ($courses as $index => $course) {
            $realProgress = app(CourseProgress::class)->sync((int) $userId, (int) $course->MaCTDT);
            $course->TienDo = $realProgress;

            if ($index === 0 || $realProgress > 0) {
                $course->IsLocked = false;
            } else {
                $prevCourse = $courses[$index - 1];
                $prevProgress = $prevCourse->TienDo ?? 0;
                $course->IsLocked = ($prevProgress < 100);
            }
        }

        return $courses;
    }

    /**
     * Hiển thị giao diện làm bài kiểm tra
     */
    public function showTest(Request $request, $id)
    {
        $userId = auth()->id();
        $user = User::find($userId) ?? User::first();

        // Lấy thông tin bài kiểm tra
        $test = DB::table('bai_kiem_tra')
            ->where('MaBaiKiemTra', $id)
            ->first();

        if (! $test) {
            return redirect()->route('tests.index', ['user_id' => $userId])
                ->with('error', 'Bài kiểm tra không tồn tại!');
        }

        // Lấy danh sách câu hỏi
        $questions = DB::table('cau_hoi_kiem_tra')
            ->where('MaBaiKiemTra', $id)
            ->orderBy('id')
            ->get();

        $attemptToken = (string) Str::uuid();
        $request->session()->put('test_attempts.'.$attemptToken, ['user_id' => $userId, 'test_id' => (int) $id]);

        return view('tests.show', compact('test', 'questions', 'user', 'attemptToken'));
    }

    public function submitTest(Request $request, $id)
    {
        $request->validate(['attempt_token' => 'required|uuid', 'answers' => 'present|array', 'answers.*' => 'nullable|in:A,B,C,D']);
        $userId = auth()->id();
        $token = $request->input('attempt_token');
        $attempt = $request->session()->get('test_attempts.'.$token);
        if (! $attempt || (int) $attempt['user_id'] !== (int) $userId || (int) $attempt['test_id'] !== (int) $id) {
            return response()->json(['success' => false, 'message' => 'Lượt thi không hợp lệ. Vui lòng mở lại bài kiểm tra.'], 422);
        }
        try {

            $test = DB::table('bai_kiem_tra')
                ->where('MaBaiKiemTra', $id)
                ->first();

            if (! $test) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bài kiểm tra không tồn tại!',
                ], 404);
            }

            $questions = DB::table('cau_hoi_kiem_tra')
                ->where('MaBaiKiemTra', $id)
                ->orderBy('id')
                ->get()
                ->values();

            if ($questions->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bài kiểm tra chưa có câu hỏi nào!',
                ], 400);
            }

            $userAnswers = $request->input('answers', []);
            $correctCount = 0;
            $totalQuestions = $questions->count();

            foreach ($questions as $index => $q) {
                if (isset($userAnswers[$index])) {
                    $selected = strtoupper(trim((string) $userAnswers[$index]));
                    $correct = strtoupper(trim((string) $q->DapAnDung));

                    if ($selected === $correct) {
                        $correctCount++;
                    }
                }
            }

            // Tính điểm phần trăm
            $score = (int) round(($correctCount / $totalQuestions) * 100);
            $passScore = $test->DiemChuan ?? 80;
            $isPassed = $score >= $passScore;

            $result = DB::transaction(function () use ($userId, $id, $token, $score, $isPassed, $correctCount, $totalQuestions) {
                DB::table('users')->where('id', $userId)->lockForUpdate()->first();
                $existing = DB::table('ket_qua_kiem_tra')->where('attempt_token', $token)
                    ->where('user_id', $userId)->where('MaBaiKiemTra', $id)->first();
                if ($existing) {
                    return $existing;
                }
                $resultId = DB::table('ket_qua_kiem_tra')->insertGetId([
                    'user_id' => $userId, 'MaBaiKiemTra' => $id, 'attempt_token' => $token,
                    'Diem' => $score, 'TrangThai' => $isPassed ? 'Dat' : 'KhongDat',
                    'SoCauDung' => $correctCount, 'TongSoCau' => $totalQuestions,
                    'created_at' => now(), 'updated_at' => now(),
                ]);

                return DB::table('ket_qua_kiem_tra')->where('id', $resultId)->first();
            });
            $score = (int) $result->Diem;
            $correctCount = (int) $result->SoCauDung;
            $totalQuestions = (int) $result->TongSoCau;
            $isPassed = $result->TrangThai === 'Dat';

            return response()->json([
                'success' => true,
                'score' => $score,
                'correct_count' => $correctCount,
                'total_questions' => $totalQuestions,
                'message' => $isPassed
                    ? "Chúc mừng! Bạn đã ĐẠT bài kiểm tra với điểm số {$score}/100."
                    : "Rất tiếc! Bạn chỉ đạt {$score}/100 điểm. Chưa đủ điểm chuẩn ({$passScore} điểm).",
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xử lý Server: '.$e->getMessage(),
            ], 500);
        }
    }
}
