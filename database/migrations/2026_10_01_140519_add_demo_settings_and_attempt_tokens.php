<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach (['secondary_email', 'theme', 'language', 'notification_settings'] as $column) {
            if (! Schema::hasColumn('users', $column)) {
                Schema::table('users', function (Blueprint $table) use ($column): void {
                    if ($column === 'notification_settings') {
                        $table->json($column)->nullable();
                    } else {
                        $table->string($column)->nullable();
                    }
                });
            }
        }
        Schema::table('ket_qua_kiem_tra', function (Blueprint $table): void {
            $table->uuid('attempt_token')->nullable()->unique();
        });
        Schema::table('nhan_vien_khoa_hoc', function (Blueprint $table): void {
            $table->float('demo_progress_baseline')->nullable();
        });
        DB::table('nhan_vien_khoa_hoc')->update(['demo_progress_baseline' => DB::raw('TienDo')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ket_qua_kiem_tra', function (Blueprint $table): void {
            $table->dropUnique(['attempt_token']);
            $table->dropColumn('attempt_token');
        });
        Schema::table('nhan_vien_khoa_hoc', function (Blueprint $table): void {
            $table->dropColumn('demo_progress_baseline');
        });
    }
};
