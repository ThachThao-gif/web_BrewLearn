<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NhanVien extends Model
{
    use HasFactory;

    protected $table = 'nhan_vien';
    protected $primaryKey = 'MaNV';
    public $incrementing = false; // Do MaNV là VARCHAR
    protected $keyType = 'string';
    public $timestamps = false; // Bỏ qua 2 cột created_at / updated_at mặc định của Laravel

    protected $fillable = [
        'MaNV', 'FK_MaVaiTro', 'FK_MaCuaHang', 'HoTen', 
        'NgaySinh', 'GioiTinh', 'SDT', 'Email', 'NgayVaoLam', 'TrangThai'
    ];

    // Quan hệ với Dự đoán nguy cơ
    public function duDoanNguyCos()
    {
        return $this->hasMany(DuDoanNguyCo::class, 'FK_MaNV', 'MaNV');
    }
}