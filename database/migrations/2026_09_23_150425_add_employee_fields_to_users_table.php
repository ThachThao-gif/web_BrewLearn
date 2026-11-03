<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'name_tag')) {
                $table->string('name_tag')->nullable()->after('name');
            }
            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            if (! Schema::hasColumn('users', 'dob')) {
                $table->date('dob')->nullable();
            }
            if (! Schema::hasColumn('users', 'id_card')) {
                $table->string('id_card')->nullable();
            }
            if (! Schema::hasColumn('users', 'ChiNhanh')) {
                $table->string('ChiNhanh')->nullable();
            }
            if (! Schema::hasColumn('users', 'tram_lam_viec')) {
                $table->string('tram_lam_viec')->nullable();
            }
            if (! Schema::hasColumn('users', 'emergency_contact')) {
                $table->string('emergency_contact')->nullable();
            }
            if (! Schema::hasColumn('users', 'emergency_phone')) {
                $table->string('emergency_phone')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Xóa các cột đã thêm nếu rollback
            $columns = ['name_tag', 'phone', 'dob', 'id_card', 'tram_lam_viec', 'emergency_contact', 'emergency_phone'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
