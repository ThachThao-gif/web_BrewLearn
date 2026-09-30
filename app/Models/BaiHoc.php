<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaiHoc extends Model
{
    use HasFactory;

    protected $table = 'bai_hoc';
    protected $primaryKey = 'MaBaiHoc';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'MaBaiHoc', 
        'FK_MaCTDT', 
        'TenBaiHoc', 
        'NoiDung', 
        'ThuTu'
    ];

    /**
     * Quan hệ 1-N (Thuộc về một Chương trình đào tạo)
     */
    public function chuongTrinh()
    {
        return $this->belongsTo(ChuongTrinhDaoTao::class, 'FK_MaCTDT', 'MaCTDT');
    }

    /**
     * Quan hệ 1-N (Một bài học có thể có nhiều bài kiểm tra)
     */
    public function baiKiemTras()
    {
        return $this->hasMany(BaiKiemTra::class, 'MaBaiHoc', 'MaBaiHoc');
    }
}