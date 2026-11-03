<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bai_hoc', function (Blueprint $table) {
            $table->string('MaBaiHoc', 50)->primary(); // Khóa chính kiểu String (VD: BH_01)
            $table->string('FK_MaCTDT', 50)->nullable();
            $table->string('TenBaiHoc');
            $table->text('NoiDung')->nullable();
            $table->string('VideoURL')->nullable(); // Lưu tên file video (VD: steaming-sua.mp4)
            $table->integer('ThuTu')->default(1);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bai_hoc');
    }
};
