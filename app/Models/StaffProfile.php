<?php

namespace App\Models;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StaffProfile
{
    /**
     * Gom dữ liệu hồ sơ nhân viên dùng chung cho trưởng ca và quản lý cửa hàng (chỉ đọc).
     *
     * @return array{shift: ?object, store: ?object, learning: array{progress: float, completed: int, total: int}, courses: Collection, quizzes: Collection, assessments: Collection, latestAssessment: ?object}
     */
    public function build(User $staff): array
    {
        app(AssessmentReminder::class)->decorate(collect([$staff]));

        $coursePercentages = app(CourseProgress::class)->coursePercentages(collect([$staff]))->get($staff->id, collect());
        $courses = app(CourseProgress::class)->assignedCourses($staff->ChucVu ?? '');
        $lessonsByCourse = DB::table('bai_hoc')->whereIn('FK_MaCTDT', $courses->pluck('MaCTDT'))->get(['MaBaiHoc', 'FK_MaCTDT'])->groupBy('FK_MaCTDT');
        $finishedLessons = DB::table('user_lesson_progresses')->where('user_id', $staff->id)
            ->where(fn ($query) => $query->where('is_completed', true)->orWhere('progress_percent', '>=', 95))
            ->pluck('lesson_id')->flip();
        foreach ($courses as $course) {
            $lessons = $lessonsByCourse->get($course->MaCTDT, collect());
            $course->progress = $coursePercentages->get($course->MaCTDT, 0);
            $course->total = $lessons->count();
            $course->completed = $lessons->filter(fn ($lesson) => $finishedLessons->has($lesson->MaBaiHoc))->count();
        }

        $quizzes = DB::table('ket_qua_kiem_tra as k')
            ->leftJoin('bai_kiem_tra as b', 'k.MaBaiKiemTra', '=', 'b.MaBaiKiemTra')
            ->where('k.user_id', $staff->id)
            ->select('k.*', 'b.TenBaiKiemTra', 'b.DiemChuan')
            ->orderByDesc('k.id')
            ->get();
        $attemptNumbers = [];
        foreach ($quizzes->reverse() as $quiz) {
            $quiz->attempt_number = $attemptNumbers[$quiz->MaBaiKiemTra] = ($attemptNumbers[$quiz->MaBaiKiemTra] ?? 0) + 1;
        }

        $assessments = DB::table('danh_gia_nang_luc as d')
            ->leftJoin('users as assessor', 'd.NguoiDanhGia_id', '=', 'assessor.id')
            ->where('d.user_id', $staff->id)
            ->select('d.*', 'assessor.name as assessor_name')
            ->orderByDesc('d.id')
            ->get();

        return [
            'shift' => $staff->ca_lam_viec_id ? DB::table('ca_lam_viec')->find($staff->ca_lam_viec_id) : null,
            'store' => $staff->cua_hang_id ? DB::table('cua_hang')->find($staff->cua_hang_id) : null,
            'learning' => [
                'progress' => round($coursePercentages->avg() ?? 0, 1),
                'completed' => $coursePercentages->filter(fn ($value) => $value >= 100)->count(),
                'total' => $coursePercentages->count(),
            ],
            'courses' => $courses,
            'quizzes' => $quizzes,
            'assessments' => $assessments,
            'latestAssessment' => $assessments
                ->filter(fn ($assessment) => in_array($assessment->TrangThai, ['ChoPheDuyet', 'DaDuyet', 'HoanThanh'], true) && $assessment->Diem !== null)
                ->sortByDesc(fn ($assessment) => [$assessment->updated_at, $assessment->id])
                ->first(),
        ];
    }
}
