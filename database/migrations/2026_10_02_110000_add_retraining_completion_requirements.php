<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('retraining_rounds', function (Blueprint $table) {
            $table->json('required_lessons')->nullable();
            $table->json('required_quizzes')->nullable();
            $table->json('learning_baseline')->nullable();
            $table->unsignedBigInteger('confirmed_by')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->text('confirmation_note')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('retraining_rounds', fn (Blueprint $table) => $table->dropColumn(['required_lessons', 'required_quizzes', 'learning_baseline', 'confirmed_by', 'confirmed_at', 'confirmation_note']));
    }
};
