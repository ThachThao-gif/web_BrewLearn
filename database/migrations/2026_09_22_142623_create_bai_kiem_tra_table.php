<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bai_kiem_tra', function (Blueprint $table) {
            $table->id('MaBaiKiemTra'); // Khóa chính
            $table->string('TenBaiKiemTra');
            $table->integer('ThoiGianLamBai')->nullable()->comment('Thời gian tính bằng phút');
            $table->integer('DiemChuan')->default(80)->comment('Điểm đạt (%)');

            // Khóa ngoại liên kết với bài học (hoặc chương trình đào tạo) nếu có
            $table->unsignedBigInteger('MaBaiHoc')->nullable();

            $table->timestamps();

            // Nếu bạn có bảng bai_hoc, có thể mở comment dòng dưới để tạo khóa ngoại:
            // $table->foreign('MaBaiHoc')->references('MaBaiHoc')->on('bai_hoc')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bai_kiem_tra');
    }
};
