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
        Schema::create('ca_lam_viec', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cua_hang_id')->index();
            $table->string('ten_ca');
            $table->time('bat_dau')->nullable();
            $table->time('ket_thuc')->nullable();
            $table->timestamps();
            $table->unique(['cua_hang_id', 'ten_ca']);
        });
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('ca_lam_viec_id')->nullable()->constrained('ca_lam_viec')->nullOnDelete();
        });
        Schema::table('danh_gia_nang_luc', function (Blueprint $table) {
            $table->unsignedBigInteger('nguoi_duyet_id')->nullable();
            $table->dateTime('ngay_duyet')->nullable();
            $table->text('ly_do_tra_lai')->nullable();
        });
        Schema::table('yeu_cau_dao_tao_lai', function (Blueprint $table) {
            $table->unsignedBigInteger('assessment_id')->nullable()->unique();
            $table->string('nguon_de_xuat')->nullable();
            $table->json('ke_hoach')->nullable();
            $table->unsignedBigInteger('nguoi_phu_trach_id')->nullable();
            $table->date('han_hoan_thanh')->nullable();
            $table->date('ngay_danh_gia_lai')->nullable();
            $table->string('tien_trinh')->default('ChoGiao');
            $table->text('ghi_chu_thuc_hien')->nullable();
            $table->unsignedBigInteger('danh_gia_lai_id')->nullable();
        });
        Schema::table('notifications', function (Blueprint $table) {
            $table->string('event_key')->nullable()->unique();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', fn (Blueprint $table) => $table->dropColumn('event_key'));
        Schema::table('yeu_cau_dao_tao_lai', fn (Blueprint $table) => $table->dropColumn(['assessment_id', 'nguon_de_xuat', 'ke_hoach', 'nguoi_phu_trach_id', 'han_hoan_thanh', 'ngay_danh_gia_lai', 'tien_trinh', 'ghi_chu_thuc_hien', 'danh_gia_lai_id']));
        Schema::table('danh_gia_nang_luc', fn (Blueprint $table) => $table->dropColumn(['nguoi_duyet_id', 'ngay_duyet', 'ly_do_tra_lai']));
        Schema::table('users', fn (Blueprint $table) => $table->dropConstrainedForeignId('ca_lam_viec_id'));
        Schema::dropIfExists('ca_lam_viec');
    }
};
