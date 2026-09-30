<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chuong_trinh_dao_tao', function (Blueprint $table) {
            $table->string('MaCTDT', 50)->primary(); // Khóa chính (VD: CTDT_01)
            $table->string('TenCTDT');
            $table->text('MoTa')->nullable();
            $table->string('ChucVuApDung')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chuong_trinh_dao_tao');
    }
};