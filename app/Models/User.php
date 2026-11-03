<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    public const LEADER_POSITIONS = ['trưởng ca', 'truong ca', 'truong_ca', 'leader'];

    public const MANAGER_POSITION = 'quản lý cửa hàng';

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

    public function isLeader(): bool
    {
        return in_array(mb_strtolower(trim($this->ChucVu ?? '')), self::LEADER_POSITIONS, true);
    }

    public function isStoreManager(): bool
    {
        return mb_strtolower(trim($this->ChucVu ?? '')) === self::MANAGER_POSITION;
    }

    /**
     * Nhân viên thuộc diện được đánh giá năng lực (không gồm trưởng ca).
     */
    public function scopeAssessmentEligible(Builder $query): Builder
    {
        return $query->whereRaw("LOWER(TRIM(COALESCE(ChucVu, ''))) NOT IN (?, ?, ?, ?)", self::LEADER_POSITIONS);
    }

    /**
     * Nhân viên đang làm thuộc phạm vi trưởng ca phụ trách: cùng cửa hàng, cùng ca (nếu trưởng ca đã được gán ca).
     */
    public function scopeLedBy(Builder $query, User $leader): Builder
    {
        $query->working()->assessmentEligible()->where('id', '!=', $leader->id);
        if ($leader->cua_hang_id) {
            $query->where('cua_hang_id', $leader->cua_hang_id)
                ->whereRaw("LOWER(TRIM(COALESCE(ChucVu, ''))) != ?", [self::MANAGER_POSITION]);
            if ($leader->ca_lam_viec_id) {
                $query->where('ca_lam_viec_id', $leader->ca_lam_viec_id);
            }
        }

        return $query;
    }

    public function scopeWorking(Builder $query): Builder
    {
        return $query->where('employment_status', 'DangLam');
    }

    /**
     * Tiến độ học tập trung bình (%) trên các chương trình được giao theo chức danh.
     */
    public function getProgressPercentAttribute(): float
    {
        $progress = app(CourseProgress::class);
        $courses = $progress->assignedCourses($this->ChucVu ?? 'Barista');

        return round($courses->avg(fn ($course) => $progress->sync((int) $this->id, (int) $course->MaCTDT)) ?? 0, 1);
    }
}
