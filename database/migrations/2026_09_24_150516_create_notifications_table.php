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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // ID nhân viên nhận thông báo
            $table->string('title');               // Tiêu đề thông báo
            $table->text('message');              // Nội dung thông báo
            $table->string('type')->default('schedule'); // Loại thông báo
            $table->boolean('is_read')->default(false);  // Trạng thái đã đọc chưa
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
