<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Thêm mục tiêu học tập (mỗi dòng một mục) cho phần "Những gì bạn sẽ học".
     */
    public function up(): void
    {
        if (! Schema::hasColumn('chuong_trinh_dao_tao', 'MucTieuHoc')) {
            Schema::table('chuong_trinh_dao_tao', function (Blueprint $table) {
                $table->text('MucTieuHoc')->nullable()->after('MoTa');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('chuong_trinh_dao_tao', 'MucTieuHoc')) {
            Schema::table('chuong_trinh_dao_tao', function (Blueprint $table) {
                $table->dropColumn('MucTieuHoc');
            });
        }
    }
};
