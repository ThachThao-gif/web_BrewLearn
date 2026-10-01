<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class StaffAssessmentSummaryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
        (require database_path('migrations/0001_01_01_000000_create_users_table.php'))->up();

        Schema::create('chuong_trinh_dao_tao', function (Blueprint $table): void {
            $table->id('MaCTDT');
            $table->string('ChucVuApDung');
        });
        Schema::create('nhan_vien_khoa_hoc', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('MaCTDT');
            $table->float('TienDo');
        });
        Schema::create('ket_qua_kiem_tra', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->float('Diem');
            $table->timestamps();
        });
        Schema::create('danh_gia_nang_luc', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->float('Diem')->nullable();
            $table->string('TrangThai');
            $table->boolean('IsPassed')->nullable();
            $table->text('ChiTietDiem')->nullable();
            $table->timestamps();
        });
    }

    public function test_staff_without_results_are_not_shown_as_zero_scores(): void
    {
        $leader = User::factory()->create();
        User::factory()->create();

        $this->actingAs($leader)->get(route('leader.staffList'))
            ->assertOk()
            ->assertSee('Kiểm tra lý thuyết')
            ->assertSee('Đánh giá năng lực')
            ->assertSee('Chưa làm bài')
            ->assertSee('Chưa đánh giá')
            ->assertDontSee('Rubric');
    }

    #[TestWith(['ChoPheDuyet', true, null, 'Chờ duyệt'])]
    #[TestWith(['DaDuyet', true, null, 'Đạt'])]
    #[TestWith(['HoanThanh', false, null, 'Chưa đạt'])]
    #[TestWith(['DaDuyet', null, '{"nghiep_vu":85,"ky_nang":85,"quy_trinh":85,"phuc_vu":85,"tac_phong":65}', 'Chưa đạt'])]
    #[TestWith(['DaDuyet', null, '{"nghiep_vu":85,"ky_nang":85,"quy_trinh":85,"phuc_vu":85,"tac_phong":85}', 'Đạt'])]
    #[TestWith(['DaDuyet', null, null, 'Đã duyệt'])]
    public function test_latest_scores_and_assessment_status_are_displayed(
        string $status, ?bool $passed, ?string $criteria, string $expectedLabel
    ): void {
        $leader = User::factory()->create();
        $staff = User::factory()->create();

        DB::table('ket_qua_kiem_tra')->insert([
            ['user_id' => $staff->id, 'Diem' => 40, 'created_at' => '2026-09-01 10:00:00'],
            ['user_id' => $staff->id, 'Diem' => 92, 'created_at' => '2026-09-02 10:00:00'],
            ['user_id' => $staff->id, 'Diem' => 30, 'created_at' => '2026-08-01 10:00:00'],
        ]);
        DB::table('danh_gia_nang_luc')->insert([
            ['user_id' => $staff->id, 'Diem' => 50, 'TrangThai' => 'DaDuyet', 'IsPassed' => false, 'ChiTietDiem' => null, 'updated_at' => '2026-09-01 10:00:00'],
            ['user_id' => $staff->id, 'Diem' => 83.1, 'TrangThai' => $status, 'IsPassed' => $passed, 'ChiTietDiem' => $criteria, 'updated_at' => '2026-09-02 10:00:00'],
            ['user_id' => $staff->id, 'Diem' => null, 'TrangThai' => 'ChoDanhGia', 'IsPassed' => null, 'ChiTietDiem' => null, 'updated_at' => '2026-09-03 10:00:00'],
        ]);

        $this->actingAs($leader)->get(route('leader.staffList'))
            ->assertOk()
            ->assertSee('92/100')
            ->assertSee('Lần gần nhất')
            ->assertSee('83.1/100')
            ->assertSee($expectedLabel)
            ->assertDontSee('Chưa đánh giá');
    }
}
