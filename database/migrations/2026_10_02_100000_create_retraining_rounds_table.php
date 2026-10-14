<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('retraining_rounds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_id');
            $table->unsignedInteger('round_number');
            $table->unsignedBigInteger('assigned_by');
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->text('instructions')->nullable();
            $table->json('proposal')->nullable();
            $table->date('deadline')->nullable();
            $table->date('assessment_date')->nullable();
            $table->unsignedBigInteger('assessment_id')->nullable()->unique();
            $table->string('status')->default('DangThucHien');
            $table->string('result')->default('ChuaCoKetQua');
            $table->decimal('score', 5, 1)->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->unique(['request_id', 'round_number']);
        });
        foreach (DB::table('yeu_cau_dao_tao_lai')->whereNotNull('nguoi_phu_trach_id')->get() as $plan) {
            $assessment = $plan->danh_gia_lai_id ? DB::table('danh_gia_nang_luc')->find($plan->danh_gia_lai_id) : null;
            DB::table('retraining_rounds')->insert(['request_id' => $plan->id, 'round_number' => 1,
                'assigned_by' => $plan->nguoi_duyet_id ?? 0, 'owner_id' => $plan->nguoi_phu_trach_id,
                'instructions' => $plan->ghi_chu_thuc_hien, 'proposal' => $plan->ke_hoach,
                'deadline' => $plan->han_hoan_thanh, 'assessment_date' => $plan->ngay_danh_gia_lai,
                'assessment_id' => $plan->danh_gia_lai_id, 'status' => $plan->tien_trinh,
                'result' => $plan->ket_qua_sau_dao_tao_lai, 'score' => $assessment->Diem ?? null,
                'created_at' => $plan->created_at, 'updated_at' => $plan->updated_at]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('retraining_rounds');
    }
};
