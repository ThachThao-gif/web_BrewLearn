<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class AssessmentImprovementTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
        (require database_path('migrations/0001_01_01_000000_create_users_table.php'))->up();
        Schema::table('users', function (Blueprint $table): void {
            $table->date('joined_at')->nullable();
        });
        Schema::create('chuong_trinh_dao_tao', function (Blueprint $table): void {
            $table->id('MaCTDT');
            $table->string('TenCTDT');
            $table->string('ChucVuApDung')->default('ALL STAFF');
        });
        Schema::create('bai_hoc', function (Blueprint $table): void {
            $table->id('MaBaiHoc');
            $table->integer('FK_MaCTDT');
            $table->string('TenBaiHoc');
            $table->integer('ThuTu')->default(1);
        });
        Schema::create('nhan_vien_khoa_hoc', function (Blueprint $table): void {
            $table->id();
            $table->integer('user_id');
            $table->integer('MaCTDT');
            $table->float('TienDo');
        });
        Schema::create('danh_gia_nang_luc', function (Blueprint $table): void {
            $table->id();
            $table->integer('user_id');
            $table->integer('MaCTDT')->nullable();
            $table->string('TieuChuan')->nullable();
            $table->dateTime('NgayHen')->nullable();
            $table->dateTime('NgayDanhGia')->nullable();
            $table->float('Diem')->nullable();
            $table->text('NhanXet')->nullable();
            $table->string('TrangThai');
            foreach (['NghiepVu', 'KyNang', 'QuyTrinh', 'PhucVu', 'TacPhong'] as $criterion) {
                $table->float('Diem_'.$criterion)->nullable();
            }
            $table->timestamps();
        });
        (require database_path('migrations/2026_09_24_150516_create_notifications_table.php'))->up();
        (require database_path('migrations/2026_10_01_151137_store_assessment_improvement_details.php'))->up();
        $this->freezeTime();
    }

    private function prepareAssessment(): array
    {
        $leader = User::factory()->create();
        $staff = User::factory()->create();
        $course = DB::table('chuong_trinh_dao_tao')->insertGetId(['TenCTDT' => 'Quy trình bàn giao ca'], 'MaCTDT');
        $lesson = DB::table('bai_hoc')->insertGetId(['FK_MaCTDT' => $course, 'TenBaiHoc' => 'Checklist bàn giao'], 'MaBaiHoc');
        $assessment = DB::table('danh_gia_nang_luc')->insertGetId(['user_id' => $staff->id, 'TrangThai' => 'ChoDanhGia', 'Diem' => null]);
        $this->actingAs($leader);

        return [$leader, $staff, $course, $lesson, $assessment];
    }

    private function scores(int $assessment): array
    {
        return ['assessment_id' => $assessment, 'diem_nghiep_vu' => 80, 'diem_ky_nang' => 80,
            'diem_quy_trinh' => 90, 'diem_phuc_vu' => 82, 'diem_tac_phong' => 67, 'comment' => 'Cần cải thiện bàn giao ca'];
    }

    public function test_dashboard_displays_concrete_proposal_fields_and_database_choices(): void
    {
        [$leader] = $this->prepareAssessment();
        $this->get(route('leader.dashboard'))->assertOk()->assertSee('Kỹ năng cần cải thiện')
            ->assertSee('Nội dung hướng dẫn')->assertSee('Quy trình bàn giao ca')->assertSee('Checklist bàn giao')
            ->assertSee('Hành vi cần quan sát')->assertSee('Người đánh giá dự kiến')->assertSee($leader->name);
    }

    #[TestWith(['daotaobosung'])]
    #[TestWith(['hoclaibaihoc'])]
    #[TestWith(['theodoica'])]
    #[TestWith(['danhgialai'])]
    public function test_selected_proposal_details_are_saved_with_pending_approval(string $proposal): void
    {
        [$leader, $staff, $course, $lesson, $assessment] = $this->prepareAssessment();
        $plans = [
            'daotaobosung' => ['skill' => 'Bàn giao ca', 'instructions' => 'Hướng dẫn checklist và thực hành', 'owner_id' => $leader->id],
            'hoclaibaihoc' => ['course_id' => $course, 'lesson_ids' => [$lesson]],
            'theodoica' => ['behavior' => 'Đúng giờ và bàn giao đầy đủ', 'shift_count' => 5],
            'danhgialai' => ['assessment_date' => now()->addWeek()->toDateString(), 'assessor_id' => $leader->id, 'criteria' => ['tac_phong']],
        ];
        $this->post(route('leader.saveScore'), $this->scores($assessment) + [
            'proposals' => [$proposal], 'proposal_details' => [$proposal => $plans[$proposal]],
        ])->assertRedirect()->assertSessionHas('success');
        $record = DB::table('danh_gia_nang_luc')->find($assessment);
        $details = json_decode($record->ChiTietDeXuat, true);
        $this->assertSame($plans[$proposal], $details['details'][$proposal]);
        $this->assertSame('Trưởng ca đề xuất', $details['source']);
        $this->assertSame($leader->id, $details['proposed_by']);
        $this->assertSame('ChoPheDuyet', $record->TrangThai);
        $this->assertSame(80.4, (float) $record->Diem);
        $this->assertSame(67.0, (float) $record->Diem_TacPhong);
        $this->assertFalse((bool) $record->IsPassed);
        $this->assertDatabaseHas('notifications', ['user_id' => $staff->id, 'title' => 'Kết quả đánh giá năng lực: CHƯA ĐẠT']);
        $this->get(route('leader.dashboard'))->assertOk()->assertSee('Xem nội dung đề xuất đã gửi');
    }

    public function test_failed_assessment_requires_a_proposal_and_selected_details_without_saving(): void
    {
        [, , , , $assessment] = $this->prepareAssessment();
        $this->post(route('leader.saveScore'), $this->scores($assessment))->assertSessionHasErrors('proposals');
        $this->post(route('leader.saveScore'), $this->scores($assessment) + ['proposals' => ['daotaobosung'], 'proposal_details' => ['daotaobosung' => []]])
            ->assertSessionHasErrors(['proposal_details.daotaobosung.skill', 'proposal_details.daotaobosung.instructions', 'proposal_details.daotaobosung.owner_id']);
        $this->assertDatabaseHas('danh_gia_nang_luc', ['id' => $assessment, 'TrangThai' => 'ChoDanhGia', 'Diem' => null]);
        $this->assertDatabaseCount('notifications', 0);
        $this->from(route('leader.dashboard'))->followingRedirects()->post(route('leader.saveScore'), $this->scores($assessment) + ['proposals' => ['daotaobosung'], 'proposal_details' => ['daotaobosung' => []]])
            ->assertOk()->assertSee('Chưa gửi được đánh giá')->assertSee('restoreScoringInput', false);
    }

    public function test_lessons_must_belong_to_the_selected_course(): void
    {
        [, , , $lesson, $assessment] = $this->prepareAssessment();
        $otherCourse = DB::table('chuong_trinh_dao_tao')->insertGetId(['TenCTDT' => 'Khóa khác'], 'MaCTDT');
        $this->post(route('leader.saveScore'), $this->scores($assessment) + ['proposals' => ['hoclaibaihoc'],
            'proposal_details' => ['hoclaibaihoc' => ['course_id' => $otherCourse, 'lesson_ids' => [$lesson]]]])
            ->assertSessionHasErrors('proposal_details.hoclaibaihoc.lesson_ids.0');
        $this->assertDatabaseCount('notifications', 0);
    }

    public function test_observation_accepts_an_end_date_and_rejects_missing_duration(): void
    {
        [, , , , $assessment] = $this->prepareAssessment();
        $payload = $this->scores($assessment) + ['proposals' => ['theodoica'], 'proposal_details' => ['theodoica' => ['behavior' => 'Bàn giao cuối ca']]];
        $this->post(route('leader.saveScore'), $payload)->assertSessionHasErrors(['proposal_details.theodoica.shift_count', 'proposal_details.theodoica.until_date']);
        $payload['proposal_details']['theodoica']['until_date'] = now()->addDays(5)->toDateString();
        $this->post(route('leader.saveScore'), $payload)->assertSessionHas('success');
    }

    public function test_passed_assessment_clears_old_proposals_and_ignores_hidden_details(): void
    {
        [, , , , $assessment] = $this->prepareAssessment();
        DB::table('danh_gia_nang_luc')->where('id', $assessment)->update(['DeXuatXuLy' => '["theodoica"]', 'ChiTietDeXuat' => '{"old":true}']);
        $payload = $this->scores($assessment);
        $payload['diem_tac_phong'] = 85;
        $this->post(route('leader.saveScore'), $payload + ['proposals' => ['daotaobosung'], 'proposal_details' => ['daotaobosung' => ['skill' => 'Không còn cần']]])
            ->assertSessionHas('success');
        $this->assertDatabaseHas('danh_gia_nang_luc', ['id' => $assessment, 'IsPassed' => 1, 'DeXuatXuLy' => null, 'ChiTietDeXuat' => null]);
    }

    public function test_reassessment_rejects_a_past_date_or_unknown_criterion(): void
    {
        [$leader, , , , $assessment] = $this->prepareAssessment();
        $this->post(route('leader.saveScore'), $this->scores($assessment) + ['proposals' => ['danhgialai'],
            'proposal_details' => ['danhgialai' => ['assessment_date' => now()->subDay()->toDateString(), 'assessor_id' => $leader->id, 'criteria' => ['unknown']]]])
            ->assertSessionHasErrors(['proposal_details.danhgialai.assessment_date', 'proposal_details.danhgialai.criteria.0']);
        $this->assertDatabaseCount('notifications', 0);
    }
}
