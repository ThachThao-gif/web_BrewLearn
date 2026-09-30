<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoiNghiepVu extends Model
{
    use HasFactory;

    protected $table = 'loi_nghiep_vu';

    protected $fillable = [
        'user_id',
        'nguoi_ghi_nhan_id',
        'loai_loi',
        'muc_do',
        'diem_tru_risk',
        'mo_ta',
        'thoi_diem_xay_ra',
        'chi_nhanh_id'
    ];

    // Quan hệ với Nhân viên
    public function nhanVien()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Quan hệ với Ca trưởng ghi nhận
    public function nguoiGhiNhan()
    {
        return $this->belongsTo(User::class, 'nguoi_ghi_nhan_id');
    }
}