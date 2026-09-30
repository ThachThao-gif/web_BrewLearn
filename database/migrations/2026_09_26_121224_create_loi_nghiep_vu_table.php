<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loi_nghiep_vu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('ID Nhân viên vi phạm');
            $table->foreignId('nguoi_ghi_nhan_id')->constrained('users')->onDelete('cascade')->comment('ID Ca trưởng/Quản lý ghi nhận');
            $table->string('loai_loi', 100)->comment('Phân loại lỗi');
            $table->enum('muc_do', ['Nhanh', 'TrungBinh', 'NghiemTrong'])->default('Nhanh')->comment('Mức độ lỗi');
            $table->integer('diem_tru_risk')->default(5)->comment('Trừ vào Risk Score');
            $table->text('mo_ta')->comment('Mô tả hành vi/sự cố');
            $table->dateTime('thoi_diem_xay_ra')->comment('Thời điểm phát hiện');
            $table->unsignedBigInteger('chi_nhanh_id')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'thoi_diem_xay_ra']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loi_nghiep_vu');
    }
};