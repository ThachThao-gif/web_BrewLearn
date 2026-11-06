<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Bỏ cột trang_thai kế thừa của yêu cầu đào tạo lại: yêu cầu chỉ phát sinh khi quản lý đã phê duyệt
 * đánh giá chưa đạt, nên tiến trình được theo dõi duy nhất bằng tien_trinh (mục 4.2.4 của báo cáo).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('yeu_cau_dao_tao_lai', function (Blueprint $table) {
            $table->dropColumn('trang_thai');
        });
    }

    public function down(): void
    {
        Schema::table('yeu_cau_dao_tao_lai', function (Blueprint $table) {
            $table->enum('trang_thai', ['ChoDuyet', 'DaDuyet', 'TuChoi', 'HoanThanh'])->default('ChoDuyet')->after('ly_do');
        });

        DB::table('yeu_cau_dao_tao_lai')->update(['trang_thai' => DB::raw("CASE tien_trinh WHEN 'HoanTat' THEN 'HoanThanh' ELSE 'DaDuyet' END")]);
    }
};
