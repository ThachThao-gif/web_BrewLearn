<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KetQuaKiemTra extends Model
{
    use HasFactory;

    // Chỉ định chính xác bảng kết quả kiểm tra từ Database
    protected $table = 'ket_qua_kiem_tra';
}
