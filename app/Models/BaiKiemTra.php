<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaiKiemTra extends Model
{
    use HasFactory;

    protected $table = 'bai_kiem_tra';
    protected $primaryKey = 'MaBaiKiemTra';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['MaBaiKiemTra', 'FK_MaCTDT', 'TenBaiKT', 'SoCauHoi', 'DiemDat', 'ThoiGianLamBai'];

    public function cauHois()
    {
        return $this->hasMany(CauHoi::class, 'FK_MaBaiKiemTra', 'MaBaiKiemTra');
    }
}