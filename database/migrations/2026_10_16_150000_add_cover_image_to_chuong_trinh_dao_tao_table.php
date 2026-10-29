<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ảnh bìa khóa học: đường dẫn trong thư mục public (vd: images/courses/lam-viec-an-toan.jpg) hoặc URL đầy đủ.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('chuong_trinh_dao_tao', 'HinhAnh')) {
            Schema::table('chuong_trinh_dao_tao', function (Blueprint $table) {
                $table->string('HinhAnh', 500)->nullable()->after('MucTieuHoc');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('chuong_trinh_dao_tao', 'HinhAnh')) {
            Schema::table('chuong_trinh_dao_tao', function (Blueprint $table) {
                $table->dropColumn('HinhAnh');
            });
        }
    }
};
