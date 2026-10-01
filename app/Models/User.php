<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'name_tag',
        'phone',
        'dob',
        'id_card',
        'ChiNhanh',
        'tram_lam_viec',
        'emergency_contact',
        'emergency_phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return ['notification_settings' => 'array', 'email_verified_at' => 'datetime'];
    }

    // ==========================================
    // 1. CÁC MỐI QUAN HỆ DATABASE
    // ==========================================

    /**
     * Quan hệ với Tiến độ bài học (Bảng user_lesson_progresses)
     */
    public function lessonProgresses()
    {
        return $this->hasMany(TienDoHoc::class, 'user_id');
    }

    /**
     * Quan hệ với Kết quả kiểm tra (Bảng ket_qua_kiem_tra)
     */
    public function quizResults()
    {
        return $this->hasMany(KetQuaKiemTra::class, 'user_id');
    }

    /**
     * Quan hệ N-Nhiều với Chương trình đào tạo (Bảng trung gian nhan_vien_khoa_hoc)
     */
    public function courses()
    {
        return $this->belongsToMany(ChuongTrinhDaoTao::class, 'nhan_vien_khoa_hoc', 'user_id', 'MaCTDT')
            ->withPivot('TienDo', 'TrangThai')
            ->withTimestamps();
    }

    // ==========================================
    // 2. TỰ ĐỘNG TÍNH TOÁN DỮ LIỆU (ACCESSORS THEO BẢNG NHAN_VIEN_KHOA_HOC)
    // ==========================================

    /**
     * 1. Tính % Tiến độ học tập trung bình (Lấy từ nhan_vien_khoa_hoc)
     */
    public function getProgressPercentAttribute()
    {
        $progress = app(CourseProgress::class);
        $courses = $progress->assignedCourses($this->ChucVu ?? 'Barista');

        return round($courses->avg(fn ($course) => $progress->sync((int) $this->id, (int) $course->MaCTDT)) ?? 0, 1);
    }

    /**
     * 2. Tổng số bài học / khóa học được giao
     */
    public function getTotalLessonsAttribute()
    {
        return $this->courses->count();
    }

    /**
     * 3. Số bài đã hoàn thành
     */
    public function getCompletedLessonsAttribute()
    {
        return $this->courses->filter(function ($c) {
            $status = strtolower($c->pivot->TrangThai ?? '');
            $progress = $c->pivot->TienDo ?? 0;

            return in_array($status, ['hoanthanh', 'completed']) || $progress >= 100;
        })->count();
    }

    /**
     * 4. Số bài đang học
     */
    public function getInProgressLessonsAttribute()
    {
        return $this->courses->filter(function ($c) {
            $status = strtolower($c->pivot->TrangThai ?? '');
            $progress = $c->pivot->TienDo ?? 0;

            return in_array($status, ['danghoc', 'in_progress']) || ($progress > 0 && $progress < 100);
        })->count();
    }

    /**
     * 5. Số bài chưa học
     */
    public function getNotStartedLessonsAttribute()
    {
        return max(0, $this->total_lessons - ($this->completed_lessons + $this->in_progress_lessons));
    }

    /**
     * 6. Điểm Quiz gần nhất (Lần làm mới nhất)
     */
    public function getQuizLatestScoreAttribute()
    {
        if ($this->quizResults->isEmpty()) {
            return 0;
        }

        $latestQuiz = $this->quizResults()->orderByDesc('created_at')->orderByDesc('id')->first();

        return round($latestQuiz->Diem ?? $latestQuiz->score ?? 0, 1);
    }

    /**
     * Điểm Quiz trung bình
     */
    public function getQuizScoreAttribute()
    {
        if ($this->quizResults->isEmpty()) {
            return 0;
        }

        $avg = $this->quizResults->avg(function ($q) {
            return $q->Diem ?? $q->score ?? 0;
        });

        return round($avg ?? 0, 1);
    }

    /**
     * 7. Tổng số lượt đã làm bài Quiz
     */
    public function getQuizAttemptsCountAttribute()
    {
        return $this->quizResults->count();
    }

    /**
     * 8. Số lần làm Quiz không đạt (< 80 điểm, đồng bộ mô hình dự đoán)
     */
    public function getQuizFailedCountAttribute()
    {
        return $this->quizResults->filter(function ($q) {
            $score = $q->Diem ?? $q->score ?? 0;

            return $score < 80;
        })->count();
    }

    /**
     * 9. Số lượt học (Số khóa/bài có tương tác TienDo > 0)
     */
    public function getTotalLearningVisitsAttribute()
    {
        return $this->courses->filter(function ($c) {
            return ($c->pivot->TienDo ?? 0) > 0;
        })->count();
    }

    /**
     * 10. Trạng thái đào tạo (Đánh giá tổng quan)
     */
    public function getTrainingStatusInfoAttribute()
    {
        if ($this->total_lessons == 0) {
            return ['label' => 'Chưa phân công', 'badge' => 'secondary'];
        }
        if ($this->progress_percent >= 100) {
            return ['label' => 'Hoàn thành', 'badge' => 'success'];
        }
        if ($this->progress_percent < 40 || $this->quiz_failed_count >= 3) {
            return ['label' => 'Cần đôn đốc', 'badge' => 'danger'];
        }

        return ['label' => 'Đang tiến hành', 'badge' => 'warning'];
    }
}
