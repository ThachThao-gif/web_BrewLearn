<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class CourseController extends Controller
{
    /**
     * Lấy ID người dùng hiện tại (Ưu tiên Auth, fallback sang Request hoặc mặc định 2)
     */
    private function getCurrentUserId(Request $request)
    {
        return Auth::id() 
            ?? $request->query('user_id') 
            ?? 2; // Mặc định cho môi trường Test
    }

    /**
     * 1. TRANG DASHBOARD THỐNG KÊ
     */
    public function dashboard(Request $request)
    {
        $userId = $this->getCurrentUserId($request);
        $currentUser = User::find($userId) ?? User::first();
        $userId = $currentUser->id ?? 1;
        $userChucVu = $currentUser->ChucVu ?? 'Barista';

        $assignedCourses = $this->getAssignedCoursesWithLockStatus($userId, $userChucVu);

        $assignedCoursesCount = $assignedCourses->count();
        $inProgressCoursesCount = $assignedCourses->where('TienDo', '>', 0)->count();
        $completedCoursesCount = $assignedCourses->where('TienDo', '>=', 100)->count();

        $overallProgress = $assignedCoursesCount > 0 
            ? round($assignedCourses->avg('TienDo'), 1) 
            : 0;

        $avgQuizScore = round(
            DB::table('ket_qua_kiem_tra')
                ->where('user_id', $userId)
                ->avg('Diem') ?? 0,
            1
        );

        $completedTestsCount = DB::table('ket_qua_kiem_tra')
            ->leftJoin('bai_kiem_tra', 'ket_qua_kiem_tra.MaBaiKiemTra', '=', 'bai_kiem_tra.MaBaiKiemTra')
            ->where('ket_qua_kiem_tra.user_id', $userId)
            ->where(function ($q) {
                $q->whereIn('ket_qua_kiem_tra.TrangThai', ['Dat', 'DaDat'])
                  ->orWhereRaw('ket_qua_kiem_tra.Diem >= COALESCE(bai_kiem_tra.DiemChuan, 80)');
            })
            ->distinct('ket_qua_kiem_tra.MaBaiKiemTra')
            ->count('ket_qua_kiem_tra.MaBaiKiemTra');

        $currentLesson = null; 

        return view('welcome', compact(
            'currentUser',
            'assignedCoursesCount',
            'inProgressCoursesCount',
            'overallProgress',
            'completedCoursesCount',
            'avgQuizScore',
            'completedTestsCount',
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

        return view('courses.index', compact('courses', 'currentUser'));
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

        if (!$course) {
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

        if (!$targetCourse) {
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

        if (!$lesson) {
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
            $course->TienDo = round($userCourse->TienDo ?? 0);
        }

        // Lấy danh sách bài học thuộc khóa
        $lessonsInCourse = DB::table('bai_hoc')
            ->where('FK_MaCTDT', $lesson->FK_MaCTDT)
            ->orderBy('ThuTu', 'asc')
            ->get();

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
            $percent = $prog ? (int)$prog->progress_percent : 0;
            $isCompleted = $prog ? (bool)$prog->is_completed : false;

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
            'current_time' => 'required|numeric',
            'progress_percent' => 'required|integer|min:0|max:100',
        ]);

        $userId = $this->getCurrentUserId($request);
        $lessonId = $request->lesson_id;
        $currentTime = $request->current_time;
        $incomingPercent = $request->progress_percent;

        $lesson = DB::table('bai_hoc')->where('MaBaiHoc', $lessonId)->first();
        if (!$lesson) {
            return response()->json(['error' => 'Bài học không tồn tại'], 404);
        }

        $maCTDT = $lesson->FK_MaCTDT;

        $existing = DB::table('user_lesson_progresses')
            ->where('user_id', $userId)
            ->where('lesson_id', $lessonId)
            ->first();

        $maxPercent = $existing ? max($existing->progress_percent, $incomingPercent) : $incomingPercent;
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
            'is_completed' => (bool)$isCompleted,
            'course_progress' => $courseProgress
        ]);
    }

    /**
     * 7. ĐÁNH DẤU HOÀN THÀNH BÀI HỌC
     */
    public function completeLesson(Request $request, $id)
    {
        $userId = $this->getCurrentUserId($request);

        $lesson = DB::table('bai_hoc')->where('MaBaiHoc', $id)->first();
        if (!$lesson) {
            return response()->json(['status' => 'error', 'message' => 'Bài học không tồn tại!'], 404);
        }

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
            'course_progress' => $courseProgress
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
        $tests = DB::table('bai_kiem_tra')->get();

        // Lấy kết quả làm bài của user
        $userResults = DB::table('ket_qua_kiem_tra')
            ->where('user_id', $userId)
            ->get()
            ->keyBy('MaBaiKiemTra');

        // Ghép điểm và trạng thái vào từng bài test
        foreach ($tests as $test) {
            $result = $userResults->get($test->MaBaiKiemTra);
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
        $allLessons = DB::table('bai_hoc')->where('FK_MaCTDT', $maCTDT)->pluck('MaBaiHoc');
        $totalLessonsCount = $allLessons->count();

        if ($totalLessonsCount === 0) {
            return 0;
        }

        $sumProgress = DB::table('user_lesson_progresses')
            ->where('user_id', $userId)
            ->whereIn('lesson_id', $allLessons)
            ->sum('progress_percent');

        $courseProgress = min(100, round($sumProgress / $totalLessonsCount));
        $trangThai = ($courseProgress >= 100) ? 'HoanThanh' : 'DangHoc';

        DB::table('nhan_vien_khoa_hoc')->updateOrInsert(
            ['user_id' => $userId, 'MaCTDT' => $maCTDT],
            ['TienDo' => $courseProgress, 'TrangThai' => $trangThai, 'updated_at' => now()]
        );

        return $courseProgress;
    }

    /**
     * LẤY DANH SÁCH KHÓA HỌC ĐƯỢC GIAO KÈM TRẠNG THÁI KHÓA/MỞ
     */
    private function getAssignedCoursesWithLockStatus($userId, $userChucVu)
    {
        $cleanChucVu = mb_strtoupper(trim($userChucVu), 'UTF-8');

        $courses = DB::table('chuong_trinh_dao_tao')
            ->where(function ($query) use ($cleanChucVu) {
                $query->whereRaw('UPPER(TRIM(ChucVuApDung)) = ?', [$cleanChucVu])
                      ->orWhereRaw('UPPER(TRIM(ChucVuApDung)) = ?', ['ALL STAFF'])
                      ->orWhereRaw('UPPER(TRIM(ChucVuApDung)) = ?', ['ALL']);
            })
            ->orderBy('chuong_trinh_dao_tao.MaCTDT', 'asc')
            ->get();

        foreach ($courses as $index => $course) {
            $allLessons = DB::table('bai_hoc')
                ->where('FK_MaCTDT', $course->MaCTDT)
                ->pluck('MaBaiHoc')
                ->toArray();

            $hasRealtimeProgress = DB::table('user_lesson_progresses')
                ->where('user_id', $userId)
                ->whereIn('lesson_id', $allLessons)
                ->exists();

            if ($hasRealtimeProgress) {
                $totalLessonsCount = count($allLessons);
                if ($totalLessonsCount > 0) {
                    $completedLessonsCount = DB::table('user_lesson_progresses')
                        ->where('user_id', $userId)
                        ->whereIn('lesson_id', $allLessons)
                        ->where(function ($q) {
                            $q->where('is_completed', 1)
                              ->orWhere('progress_percent', '>=', 95);
                        })
                        ->count();

                    $realProgress = round(($completedLessonsCount / $totalLessonsCount) * 100);
                } else {
                    $realProgress = 0;
                }

                DB::table('nhan_vien_khoa_hoc')->updateOrInsert(
                    ['user_id' => $userId, 'MaCTDT' => $course->MaCTDT],
                    ['TienDo' => $realProgress, 'TrangThai' => ($realProgress >= 100 ? 'HoanThanh' : 'DangHoc'), 'updated_at' => now()]
                );
            } else {
                $dbRecord = DB::table('nhan_vien_khoa_hoc')
                    ->where('user_id', $userId)
                    ->where('MaCTDT', $course->MaCTDT)
                    ->first();

                $realProgress = $dbRecord ? round($dbRecord->TienDo ?? 0) : 0;
            }

            $course->TienDo = $realProgress;

            if ($index === 0 || $realProgress > 0) {
                $course->IsLocked = false;
            } else {
                $prevCourse = $courses[$index - 1];
                $prevProgress = round($prevCourse->TienDo ?? 0);
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
        $userId = $request->query('user_id') ?? auth()->id() ?? 1;
        $user = \App\Models\User::find($userId) ?? \App\Models\User::first();

        // Lấy thông tin bài kiểm tra
        $test = DB::table('bai_kiem_tra')
            ->where('MaBaiKiemTra', $id)
            ->first();

        if (!$test) {
            return redirect()->route('tests.index', ['user_id' => $userId])
                             ->with('error', 'Bài kiểm tra không tồn tại!');
        }

        // Lấy danh sách câu hỏi
        $questions = DB::table('cau_hoi_kiem_tra')
            ->where('MaBaiKiemTra', $id)
            ->get();

        return view('tests.show', compact('test', 'questions', 'user'));
    }

   public function submitTest(Request $request, $id)
{
    try {
        $userId = $request->input('user_id') ?? auth()->id() ?? 1;

        $test = DB::table('bai_kiem_tra')
            ->where('MaBaiKiemTra', $id)
            ->first();

        if (!$test) {
            return response()->json([
                'success' => false,
                'message' => 'Bài kiểm tra không tồn tại!'
            ], 404);
        }

        $questions = DB::table('cau_hoi_kiem_tra')
            ->where('MaBaiKiemTra', $id)
            ->get()
            ->values();

        if ($questions->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Bài kiểm tra chưa có câu hỏi nào!'
            ], 400);
        }

        $userAnswers = $request->input('answers', []);
        $correctCount = 0;
        $totalQuestions = $questions->count();

        foreach ($questions as $index => $q) {
            if (isset($userAnswers[$index])) {
                $selected = strtoupper(trim((string)$userAnswers[$index]));
                $correct  = strtoupper(trim((string)$q->DapAnDung));

                if ($selected === $correct) {
                    $correctCount++;
                }
            }
        }

        // Tính điểm phần trăm
        $score = (int) round(($correctCount / $totalQuestions) * 100);
        $passScore = $test->DiemChuan ?? 80;
        $isPassed = $score >= $passScore;

        // Lưu hoặc cập nhật kết quả vào database
        DB::table('ket_qua_kiem_tra')->updateOrInsert(
            [
                'user_id' => $userId,
                'MaBaiKiemTra' => $id,
            ],
            [
                'Diem' => $score,
                'TrangThai' => $isPassed ? 'Dat' : 'KhongDat',
                'SoCauDung' => $correctCount,
                'TongSoCau' => $totalQuestions,
                'updated_at' => now(),
                'created_at' => now()
            ]
        );

        return response()->json([
            'success' => true,
            'score' => $score,
            'correct_count' => $correctCount,
            'total_questions' => $totalQuestions,
            'message' => $isPassed 
                ? "Chúc mừng! Bạn đã ĐẠT bài kiểm tra với điểm số {$score}/100." 
                : "Rất tiếc! Bạn chỉ đạt {$score}/100 điểm. Chưa đủ điểm chuẩn ({$passScore} điểm)."
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Lỗi xử lý Server: ' . $e->getMessage()
        ], 500);
    }
}
}