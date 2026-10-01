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
        foreach (['DeXuatXuLy', 'ChiTietDeXuat', 'ChiTietDiem', 'IsPassed'] as $column) {
            if (! Schema::hasColumn('danh_gia_nang_luc', $column)) {
                Schema::table('danh_gia_nang_luc', function (Blueprint $table) use ($column): void {
                    if ($column === 'IsPassed') {
                        $table->boolean($column)->nullable();
                    } else {
                        $table->json($column)->nullable();
                    }
                });
            }
        }
        Schema::table('danh_gia_nang_luc', function (Blueprint $table): void {
            $table->decimal('Diem', 5, 1)->nullable()->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('danh_gia_nang_luc', function (Blueprint $table): void {
            $table->dropColumn('ChiTietDeXuat');
        });
    }
};
