<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Đổi mã mức độ lỗi "Nhanh" thành "Nhe" (Nhẹ) và bỏ cột diem_tru_risk,
 * vì bộ quy tắc cảnh báo chỉ đếm số lỗi và số lỗi nghiêm trọng (S5).
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->changeSeverityCodes(from: 'Nhanh', to: 'Nhe');

        Schema::table('loi_nghiep_vu', function (Blueprint $table) {
            $table->dropColumn('diem_tru_risk');
        });
    }

    public function down(): void
    {
        Schema::table('loi_nghiep_vu', function (Blueprint $table) {
            $table->integer('diem_tru_risk')->default(5)->after('muc_do')->comment('Trừ vào Risk Score');
        });

        $this->changeSeverityCodes(from: 'Nhe', to: 'Nhanh');

        DB::table('loi_nghiep_vu')->update(['diem_tru_risk' => DB::raw(
            "CASE muc_do WHEN 'NghiemTrong' THEN 20 WHEN 'TrungBinh' THEN 10 ELSE 5 END"
        )]);
    }

    /**
     * Nới cột về chuỗi, đổi dữ liệu, rồi đặt lại danh sách giá trị hợp lệ.
     */
    private function changeSeverityCodes(string $from, string $to): void
    {
        Schema::table('loi_nghiep_vu', function (Blueprint $table) {
            $table->string('muc_do', 20)->change();
        });

        DB::table('loi_nghiep_vu')->where('muc_do', $from)->update(['muc_do' => $to]);

        Schema::table('loi_nghiep_vu', function (Blueprint $table) use ($to) {
            $table->enum('muc_do', [$to, 'TrungBinh', 'NghiemTrong'])->default($to)->comment('Mức độ lỗi')->change();
        });
    }
};
