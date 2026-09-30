<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DuDoanNguyCo extends Model
{
    use HasFactory;

    protected $table = 'du_doan_nguy_co';
    protected $primaryKey = 'MaDuDoan';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'MaDuDoan', 'FK_MaNV', 'FK_BatchID', 
        'NgayDuDoan', 'MucDoNguyCo', 'CacChiSoDauVao'
    ];

    // Cấu hình tự động ép kiểu JSON cho CacChiSoDauVao (9 Features AI)
    protected $casts = [
        'CacChiSoDauVao' => 'array',
    ];

    // Quan hệ thuộc về Nhân viên
    public function nhanVien()
    {
        return $this->belongsTo(NhanVien::class, 'FK_MaNV', 'MaNV');
    }
}