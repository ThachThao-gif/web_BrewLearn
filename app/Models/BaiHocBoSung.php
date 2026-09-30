<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaiHocBoSung extends Model
{
    use HasFactory;

    protected $table = 'bai_hoc_bo_sung';
    public $timestamps = false;

    protected $fillable = [
        'MaDGNL',
        'user_id',
        'MaBaiHoc',
        'TrangThai',
        'NgayGiao'
    ];

    public function baiHoc() {
        return $this->belongsTo(BaiHoc::class, 'MaBaiHoc', 'MaBaiHoc');
    }
}