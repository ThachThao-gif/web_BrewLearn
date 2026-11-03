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
        Schema::create('user_lesson_progresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Chỉ định rõ liên kết tới bảng `bai_hoc` và cột khóa chính `MaBaiHoc`
            $table->unsignedBigInteger('lesson_id');
            $table->foreign('lesson_id')->references('MaBaiHoc')->on('bai_hoc')->onDelete('cascade');

            $table->float('last_watched_seconds')->default(0);
            $table->integer('progress_percent')->default(0);
            $table->boolean('is_completed')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'lesson_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_lesson_progresses');
    }
};
