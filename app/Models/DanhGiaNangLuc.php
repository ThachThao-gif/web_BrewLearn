<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DanhGiaNangLuc extends Model
{
    use HasFactory;

    protected $table = 'danh_gia_nang_luc';
    protected $primaryKey = 'MaDGNL';
    public $timestamps = false; // Bảng dùng cột created_at tự động

    protected $fillable = [
        'user_id',
        'MaCTDT',
        'MaNguoiDanhGia',
        'NgayDanhGia',
        'DiemDanhGia',
        'KetQua',
        'NhanXet'
    ];

    // Quan hệ lấy thông tin Nhân viên
    public function nhanVien() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // Quan hệ lấy bài học bổ sung nếu có
    public function baiHocBoSung() {
        return $this->hasMany(BaiHocBoSung::class, 'MaDGNL', 'MaDGNL');
    }
}
