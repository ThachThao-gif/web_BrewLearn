<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TienDoHoc extends Model
{
    use HasFactory;

    // Chỉ định chính xác bảng tiến độ bài học chi tiết từ Database
    protected $table = 'user_lesson_progresses';
}
