<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChuongTrinhDaoTao extends Model
{
    use HasFactory;

    protected $table = 'chuong_trinh_dao_tao';

    protected $primaryKey = 'MaCTDT';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = ['MaCTDT', 'TenCTDT', 'MoTa', 'ThoiLuong', 'NgayTao', 'FK_MaNguoiTao', 'ChucVuApDung'];

    // Quan hệ 1-N: Một chương trình đào tạo có nhiều bài học
    public function baiHocs()
    {
        return $this->hasMany(BaiHoc::class, 'FK_MaCTDT', 'MaCTDT')->orderBy('ThuTu', 'asc');
    }

    // Quan hệ 1-N: Một chương trình đào tạo có nhiều bài kiểm tra
    public function baiKiemTras()
    {
        return $this->hasMany(BaiKiemTra::class, 'FK_MaCTDT', 'MaCTDT');
    }
}
