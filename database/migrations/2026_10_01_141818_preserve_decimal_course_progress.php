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
        Schema::table('nhan_vien_khoa_hoc', function (Blueprint $table): void {
            $table->decimal('TienDo', 5, 1)->nullable()->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nhan_vien_khoa_hoc', function (Blueprint $table): void {
            $table->integer('TienDo')->nullable()->default(0)->change();
        });
    }
};
