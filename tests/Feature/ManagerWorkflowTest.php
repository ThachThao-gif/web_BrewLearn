<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class ManagerWorkflowTest extends TestCase
{
    protected User $manager;

    protected User $employee;

    protected User $leader;

    protected User $other;

    protected function setUp(): void
    {
        parent::setUp();
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
        (require database_path('migrations/0001_01_01_000000_create_users_table.php'))->up();
        Schema::table('users', function (Blueprint $t) {
            $t->string('ChucVu')->nullable();
            $t->integer('cua_hang_id')->nullable();
            $t->date('joined_at')->nullable();
        });
        Schema::create('cua_hang', function (Blueprint $t) {
            $t->id();
            $t->string('ten_cua_hang');
        });
        Schema::create('chuong_trinh_dao_tao', function (Blueprint $t) {
            $t->id('MaCTDT');
            $t->string('TenCTDT');
            $t->string('ChucVuApDung');
        });
        Schema::create('bai_hoc', function (Blueprint $t) {
            $t->id('MaBaiHoc');
            $t->integer('FK_MaCTDT');
            $t->string('TenBaiHoc');
        });
        Schema::create('nhan_vien_khoa_hoc', function (Blueprint $t) {
            $t->id();
            $t->integer('user_id');
            $t->integer('MaCTDT');
            $t->float('TienDo')->default(0);
            $t->string('TrangThai')->nullable();
            $t->timestamps();
        });
        Schema::create('user_lesson_progresses', function (Blueprint $t) {
            $t->id();
            $t->integer('user_id');
            $t->integer('lesson_id');
            $t->float('progress_percent');
            $t->boolean('is_completed');
        });
        Schema::create('bai_kiem_tra', function (Blueprint $t) {
            $t->id('MaBaiKiemTra');
            $t->string('TenBaiKiemTra');
            $t->float('DiemChuan')->default(80);
        });
        Schema::create('ket_qua_kiem_tra', function (Blueprint $t) {
            $t->id();
            $t->integer('user_id');
            $t->integer('MaBaiKiemTra');
            $t->float('Diem');
            $t->dateTime('NgayLam')->nullable();
            $t->timestamps();
        });
        Schema::create('du_doan_nguy_co', function (Blueprint $t) {
            $t->id();
            $t->integer('user_id');
            $t->integer('cua_hang_id')->nullable();
            $t->string('muc_canh_bao');
            $t->float('diem_rui_ro')->nullable();
            $t->string('phien_ban_mo_hinh')->nullable();
            $t->dateTime('ngay_du_doan');
        });
        Schema::create('danh_gia_nang_luc', function (Blueprint $t) {
            $t->id();
            $t->integer('user_id');
            $t->integer('NguoiDanhGia_id')->nullable();
            $t->integer('MaCTDT')->nullable();
            $t->string('TieuChuan')->nullable();
            $t->dateTime('NgayHen')->nullable();
            $t->dateTime('NgayDanhGia')->nullable();
            $t->float('Diem')->nullable();
            $t->text('NhanXet')->nullable();
            $t->string('TrangThai');
            $t->boolean('IsPassed')->nullable();
            $t->json('ChiTietDeXuat')->nullable();
            $t->json('DeXuatXuLy')->nullable();
            $t->json('ChiTietDiem')->nullable();
            foreach (['NghiepVu', 'KyNang', 'QuyTrinh', 'PhucVu', 'TacPhong'] as $name) {
                $t->float('Diem_'.$name)->nullable();
            } $t->timestamps();
        });
        Schema::create('yeu_cau_dao_tao_lai', function (Blueprint $t) {
            $t->id();
            $t->integer('user_id');
            $t->integer('du_doan_id')->nullable();
            $t->integer('nguoi_de_xuat_id')->nullable();
            $t->integer('nguoi_duyet_id')->nullable();
            $t->text('ly_do');
            $t->string('trang_thai')->default('ChoDuyet');
            $t->string('ket_qua_sau_dao_tao_lai')->default('ChuaCoKetQua');
            $t->dateTime('ngay_de_xuat')->nullable();
            $t->dateTime('ngay_duyet')->nullable();
            $t->timestamps();
        });
        (require database_path('migrations/2026_09_24_150516_create_notifications_table.php'))->up();
        (require database_path('migrations/2026_10_01_153056_create_manager_training_workflow.php'))->up();
        (require database_path('migrations/2026_10_01_165925_add_employment_status_to_users_table.php'))->up();
        Schema::table('bai_hoc', fn (Blueprint $table) => $table->integer('ThuTu')->default(1));
        DB::table('cua_hang')->insert([['id' => 1, 'ten_cua_hang' => 'Flagship'], ['id' => 2, 'ten_cua_hang' => 'Cửa hàng khác']]);
        $this->manager = User::factory()->create(['ChucVu' => 'Quản lý cửa hàng', 'cua_hang_id' => 1, 'name' => 'Ngọc Bích', 'password' => Hash::make('test-manager-password')]);
        $this->employee = User::factory()->create(['ChucVu' => 'Barista', 'cua_hang_id' => 1, 'name' => 'Nhân viên A', 'joined_at' => now()->subMonths(4)]);
        $this->leader = User::factory()->create(['ChucVu' => 'Trưởng ca', 'cua_hang_id' => 1, 'name' => 'Trưởng ca B']);
        $this->other = User::factory()->create(['ChucVu' => 'Barista', 'cua_hang_id' => 2, 'name' => 'Nhân viên ngoài cửa hàng']);
        $this->actingAs($this->manager);
    }

    private function assessment(bool $passed = false): int
    {
        return DB::table('danh_gia_nang_luc')->insertGetId(['user_id' => $this->employee->id, 'NguoiDanhGia_id' => $this->leader->id, 'TrangThai' => 'ChoPheDuyet', 'Diem' => $passed ? 90 : 80.4, 'IsPassed' => $passed, 'NgayDanhGia' => now(), 'ChiTietDeXuat' => $passed ? null : json_encode(['source' => 'Trưởng ca đề xuất', 'details' => ['theodoica' => ['behavior' => 'Bàn giao ca', 'shift_count' => 3]]]), 'created_at' => now(), 'updated_at' => now()]);
    }

    public function test_leaders_are_not_assessment_targets_but_remain_learning_and_staff_members(): void
    {
        $this->leader->forceFill(['joined_at' => now()->subYear()])->save();
        $id = DB::table('danh_gia_nang_luc')->insertGetId(['user_id' => $this->leader->id, 'TrangThai' => 'ChoPheDuyet', 'IsPassed' => true, 'Diem' => 90, 'updated_at' => now()]);
        $this->get(route('manager.dashboard'))->assertViewHas('pendingCount', 0);
        $this->get(route('manager.staff'))->assertSee($this->leader->name)->assertSee('Không áp dụng');
        $this->get(route('manager.assessments'))->assertViewHas('assessments', fn ($rows) => ! $rows->contains('id', $id));
        $this->post(route('manager.review', $id), ['action' => 'approve'])->assertNotFound();
        $this->artisan('brewlearn:remind-assessments')->assertSuccessful();
        $this->assertDatabaseMissing('notifications', ['title' => 'Đánh giá chờ phê duyệt']);
        $peer = User::factory()->create(['ChucVu' => 'Trưởng ca', 'cua_hang_id' => 1, 'joined_at' => now()->subYear()]);
        $this->flushSession();
        $this->actingAs($this->leader)->get(route('leader.dashboard'))
            ->assertOk()
            ->assertViewHas('staffs', fn ($staff) => ! $staff->contains('id', $peer->id));
        $this->post(route('leader.createSchedule'), ['user_id' => $peer->id, 'MaCTDT' => 'STD_BARISTA', 'NgayDanhGia' => now()->addDay()->format('Y-m-d\TH:i')])->assertSessionHasErrors('user_id');
        $this->assertDatabaseHas('danh_gia_nang_luc', ['id' => $id, 'TrangThai' => 'ChoPheDuyet']);
    }

    public function test_login_and_all_manager_pages_use_store_scope(): void
    {
        auth()->logout();
        $this->post(route('login.post'), ['email' => $this->manager->email, 'password' => 'test-manager-password'])->assertRedirect(route('manager.dashboard'));
        $this->get('/')->assertRedirect(route('manager.dashboard'));
        foreach (['dashboard', 'staff', 'assessments', 'training', 'notifications'] as $page) {
            $this->get(route('manager.'.$page))->assertOk()
                ->assertSee('Quản lý cửa hàng')
                ->assertSee(route('manager.assessments'), false)
                ->assertSee('Duyệt đánh giá')
                ->assertDontSee('Tổng quan lộ trình')
                ->assertDontSee('Làm bài test nhanh')
                ->assertDontSee($this->other->name);
        }
        $this->get(route('manager.profile', $this->employee->id))->assertOk()->assertSee('Tiến độ theo khóa học');
        $this->get(route('manager.profile', $this->other->id))->assertNotFound();
    }

    public function test_assessment_tabs_split_statuses_and_keep_employee_filters(): void
    {
        $pending = $this->assessment(true);
        $approved = $this->assessment(true);
        DB::table('danh_gia_nang_luc')->where('id', $approved)->update(['TrangThai' => 'DaDuyet']);
        $returned = $this->assessment(true);
        DB::table('danh_gia_nang_luc')->where('id', $returned)->update(['TrangThai' => 'TraLai']);
        $completed = $this->assessment(true);
        DB::table('danh_gia_nang_luc')->where('id', $completed)->update(['TrangThai' => 'HoanThanh']);
        $this->get(route('manager.assessments'))->assertOk()->assertViewHas('statusTab', 'pending')
            ->assertViewHas('tabCounts', ['pending' => 2, 'approved' => 2])
            ->assertViewHas('assessments', fn ($rows) => $rows->pluck('id')->sort()->values()->all() === [$pending, $returned]);
        $this->get(route('manager.assessments', ['status' => 'approved', 'q' => $this->employee->name]))->assertOk()
            ->assertSee('name="status" value="approved"', false)
            ->assertViewHas('assessments', fn ($rows) => $rows->pluck('id')->sort()->values()->all() === [$approved, $completed]);
        $this->get(route('manager.assessments', ['status' => 'approved', 'q' => 'Không tồn tại']))->assertOk()
            ->assertViewHas('tabCounts', ['pending' => 0, 'approved' => 0])->assertSee('Chưa có đánh giá đã duyệt');
        $this->post(route('manager.review', $pending), ['action' => 'approve'])->assertRedirect();
        $this->get(route('manager.assessments'))->assertViewHas('tabCounts', ['pending' => 1, 'approved' => 3]);
    }

    public function test_assessments_follow_submission_time_and_hide_empty_comments(): void
    {
        $newer = $this->assessment();
        $older = $this->assessment();
        $sameTime = $this->assessment();
        DB::table('danh_gia_nang_luc')->where('id', $newer)->update(['NgayDanhGia' => '2026-10-02 10:00:00', 'NhanXet' => 'Nhận xét thực tế']);
        DB::table('danh_gia_nang_luc')->whereIn('id', [$older, $sameTime])->update(['NgayDanhGia' => '2026-10-01 09:00:00', 'NhanXet' => '  ']);
        $this->get(route('manager.assessments'))->assertOk()
            ->assertViewHas('assessments', fn ($rows) => $rows->pluck('id')->all() === [$older, $sameTime, $newer])
            ->assertSee('Nhận xét thực tế')->assertSee('Bàn giao ca')->assertDontSee('Chưa có nhận xét.');
        DB::table('danh_gia_nang_luc')->where('id', $older)->update(['TrangThai' => 'TraLai', 'updated_at' => now()]);
        $this->get(route('manager.assessments'))
            ->assertViewHas('assessments', fn ($rows) => $rows->pluck('id')->all() === [$older, $sameTime, $newer]);
    }

    public function test_prediction_stays_independent_of_approved_evaluation(): void
    {
        DB::table('du_doan_nguy_co')->insert([['user_id' => $this->employee->id, 'cua_hang_id' => 1, 'muc_canh_bao' => 'Thap', 'ngay_du_doan' => now()->subDay()], ['user_id' => $this->employee->id, 'cua_hang_id' => 1, 'muc_canh_bao' => 'Cao', 'ngay_du_doan' => now()]]);
        $this->get(route('manager.dashboard'))->assertOk()->assertViewHas('staff', fn ($staff) => $staff->first()->id === $this->employee->id && $staff->first()->risk === 'Cao');
        $this->post(route('manager.review', $this->assessment(true)), ['action' => 'approve'])->assertRedirect();
        $this->assertDatabaseCount('du_doan_nguy_co', 2);
        $this->assertDatabaseHas('du_doan_nguy_co', ['muc_canh_bao' => 'Cao']);
    }

    public function test_shift_assignment_and_filter_reject_other_store(): void
    {
        $this->post(route('manager.shifts.create'), ['ten_ca' => 'Ca sáng', 'bat_dau' => '07:00', 'ket_thuc' => '15:00'])->assertSessionHas('success');
        $shift = DB::table('ca_lam_viec')->value('id');
        $this->put(route('manager.shifts.assign', $this->employee->id), ['ca_lam_viec_id' => $shift])->assertSessionHas('success');
        $this->get(route('manager.staff', ['shift' => $shift]))->assertOk()->assertSee($this->employee->name)->assertDontSee($this->leader->name);
        $foreign = DB::table('ca_lam_viec')->insertGetId(['cua_hang_id' => 2, 'ten_ca' => 'Ca khác']);
        $this->put(route('manager.shifts.assign', $this->employee->id), ['ca_lam_viec_id' => $foreign])->assertSessionHasErrors('ca_lam_viec_id');
        $this->put(route('manager.shifts.assign', $this->other->id), ['ca_lam_viec_id' => $shift])->assertNotFound();
    }

    public function test_failed_review_creates_one_training_plan_and_blocks_duplicates(): void
    {
        $id = $this->assessment();
        $this->get(route('manager.assessments'))->assertOk()->assertSee('Bàn giao ca');
        $this->post(route('manager.review', $id), ['action' => 'approve'])->assertSessionHas('success');
        $this->assertDatabaseHas('yeu_cau_dao_tao_lai', ['assessment_id' => $id, 'nguon_de_xuat' => 'Trưởng ca đề xuất', 'tien_trinh' => 'ChoGiao', 'trang_thai' => 'DaDuyet', 'du_doan_id' => null]);
        $this->get(route('manager.training'))->assertOk()->assertSee('Bàn giao ca');
        $this->post(route('manager.review', $id), ['action' => 'approve'])->assertStatus(409);
        $this->assertDatabaseCount('yeu_cau_dao_tao_lai', 1);
    }

    public function test_return_requires_reason_and_preserves_scores(): void
    {
        $id = $this->assessment();
        $this->post(route('manager.review', $id), ['action' => 'return'])->assertSessionHasErrors('reason');
        $this->post(route('manager.review', $id), ['action' => 'return', 'reason' => 'Bổ sung nội dung thực hành'])->assertSessionHas('success');
        $this->assertDatabaseHas('danh_gia_nang_luc', ['id' => $id, 'TrangThai' => 'TraLai', 'Diem' => 80.4, 'ly_do_tra_lai' => 'Bổ sung nội dung thực hành']);
        $this->assertDatabaseCount('yeu_cau_dao_tao_lai', 0);
    }

    public function test_training_finishes_only_after_approved_pass_on_reassessment(): void
    {
        $this->post(route('manager.review', $this->assessment()), ['action' => 'approve']);
        $plan = DB::table('yeu_cau_dao_tao_lai')->value('id');
        $payload = ['nguoi_phu_trach_id' => $this->leader->id, 'han_hoan_thanh' => now()->addWeek()->toDateString(), 'ngay_danh_gia_lai' => now()->addWeeks(2)->toDateString(), 'ghi_chu_thuc_hien' => 'Thực hành bàn giao 3 ca'];
        $this->put(route('manager.training.assign', $plan), array_replace($payload, ['nguoi_phu_trach_id' => $this->other->id]))->assertSessionHasErrors('nguoi_phu_trach_id');
        $this->put(route('manager.training.assign', $plan), $payload)->assertSessionHas('success');
        $this->post(route('manager.training.reassess', $plan), ['assessor_id' => $this->leader->id, 'assessment_date' => now()->addWeeks(2)->toDateString(), 'assessment_time' => '09:30', 'criteria' => 'Tác phong bàn giao ca'])->assertSessionHas('success');
        $assessment = DB::table('yeu_cau_dao_tao_lai')->find($plan)->danh_gia_lai_id;
        $this->assertDatabaseHas('yeu_cau_dao_tao_lai', ['id' => $plan, 'tien_trinh' => 'ChoDanhGiaLai', 'ket_qua_sau_dao_tao_lai' => 'ChuaCoKetQua']);
        DB::table('danh_gia_nang_luc')->where('id', $assessment)->update(['Diem' => 90, 'IsPassed' => true, 'TrangThai' => 'ChoPheDuyet']);
        $this->post(route('manager.review', $assessment), ['action' => 'approve'])->assertSessionHas('success');
        $this->assertDatabaseHas('yeu_cau_dao_tao_lai', ['id' => $plan, 'trang_thai' => 'HoanThanh', 'tien_trinh' => 'HoanTat', 'ket_qua_sau_dao_tao_lai' => 'Dat']);
        $this->assertDatabaseCount('yeu_cau_dao_tao_lai', 1);
    }

    public function test_profile_displays_lesson_counts_and_all_attempts(): void
    {
        DB::table('chuong_trinh_dao_tao')->insert(['MaCTDT' => 1, 'TenCTDT' => 'Khóa pha chế', 'ChucVuApDung' => 'Barista']);
        DB::table('bai_hoc')->insert([['MaBaiHoc' => 1, 'FK_MaCTDT' => 1, 'TenBaiHoc' => 'Bài 1'], ['MaBaiHoc' => 2, 'FK_MaCTDT' => 1, 'TenBaiHoc' => 'Bài 2']]);
        DB::table('user_lesson_progresses')->insert(['user_id' => $this->employee->id, 'lesson_id' => 1, 'progress_percent' => 100, 'is_completed' => true]);
        DB::table('bai_kiem_tra')->insert(['MaBaiKiemTra' => 1, 'TenBaiKiemTra' => 'Quiz pha chế', 'DiemChuan' => 80]);
        foreach ([60, 90] as $score) {
            DB::table('ket_qua_kiem_tra')->insert(['user_id' => $this->employee->id, 'MaBaiKiemTra' => 1, 'Diem' => $score, 'NgayLam' => now()]);
        }
        $this->get(route('manager.profile', $this->employee->id))->assertOk()->assertSee('1 / 2 bài hoàn thành')->assertSee('50%')->assertViewHas('quizzes', fn ($quizzes) => $quizzes->count() === 2);
    }

    public function test_reminders_do_not_duplicate_or_show_risk_alerts(): void
    {
        $this->artisan('brewlearn:remind-assessments')->assertSuccessful();
        $count = DB::table('notifications')->count();
        $this->assertGreaterThan(0, $count);
        $this->artisan('brewlearn:remind-assessments')->assertSuccessful();
        $this->assertDatabaseCount('notifications', $count);
        DB::table('notifications')->insert(['user_id' => $this->manager->id, 'title' => 'CẢNH BÁO MODEL TRÙNG LẶP', 'message' => 'Risk', 'type' => 'risk', 'is_read' => false, 'created_at' => now(), 'updated_at' => now()]);
        $this->get(route('manager.notifications'))->assertOk()->assertDontSee('CẢNH BÁO MODEL TRÙNG LẶP');
    }

    public function test_leader_submission_notifies_manager_once_and_can_be_returned_and_resubmitted(): void
    {
        $id = DB::table('danh_gia_nang_luc')->insertGetId(['user_id' => $this->employee->id, 'NguoiDanhGia_id' => $this->leader->id, 'TrangThai' => 'ChoDanhGia']);
        $payload = ['assessment_id' => $id, 'diem_nghiep_vu' => 80, 'diem_ky_nang' => 80, 'diem_quy_trinh' => 90, 'diem_phuc_vu' => 82, 'diem_tac_phong' => 67, 'proposals' => ['theodoica'], 'proposal_details' => ['theodoica' => ['behavior' => 'Theo dõi bàn giao', 'shift_count' => 3]]];
        $this->flushSession();
        $this->actingAs($this->leader)->post(route('leader.saveScore'), $payload)->assertSessionHas('success');
        $this->assertDatabaseHas('notifications', ['user_id' => $this->manager->id, 'title' => 'Đánh giá chờ phê duyệt']);
        $this->artisan('brewlearn:remind-assessments')->assertSuccessful();
        $this->assertSame(1, DB::table('notifications')->where('user_id', $this->manager->id)->where('title', 'Đánh giá chờ phê duyệt')->count());
        $this->flushSession();
        $this->actingAs($this->manager)->post(route('manager.review', $id), ['action' => 'return', 'reason' => 'Cần cụ thể hơn'])->assertSessionHas('success');
        $this->flushSession();
        $this->actingAs($this->leader)->post(route('leader.saveScore'), $payload)->assertSessionHas('success');
        $this->assertDatabaseHas('danh_gia_nang_luc', ['id' => $id, 'TrangThai' => 'ChoPheDuyet', 'nguoi_duyet_id' => null, 'ly_do_tra_lai' => null]);
        $this->flushSession();
        $this->actingAs($this->manager)->post(route('manager.review', $id), ['action' => 'approve'])->assertSessionHas('success');
        $this->assertDatabaseHas('yeu_cau_dao_tao_lai', ['assessment_id' => $id, 'nguon_de_xuat' => 'Trưởng ca đề xuất']);
    }

    public function test_legacy_incomplete_evaluation_must_be_returned_for_details(): void
    {
        $id = $this->assessment();
        DB::table('danh_gia_nang_luc')->where('id', $id)->update(['IsPassed' => null, 'ChiTietDeXuat' => null]);
        $this->post(route('manager.review', $id), ['action' => 'approve'])->assertSessionHasErrors('assessment');
        $this->assertDatabaseHas('danh_gia_nang_luc', ['id' => $id, 'TrangThai' => 'ChoPheDuyet']);
        $this->assertDatabaseCount('yeu_cau_dao_tao_lai', 0);
    }

    public function test_leader_and_manager_share_due_dates_for_first_and_repeated_assessments(): void
    {
        $this->travelTo(Carbon::parse('2026-10-02 10:00:00', 'Asia/Ho_Chi_Minh'));
        $this->employee->forceFill(['joined_at' => '2026-01-02'])->save();
        $this->leader->forceFill(['joined_at' => '2026-01-02'])->save();
        DB::table('danh_gia_nang_luc')->insert([
            ['user_id' => $this->employee->id, 'TrangThai' => 'DaDuyet', 'Diem' => 85, 'NgayDanhGia' => '2026-07-05 09:00:00'],
            ['user_id' => $this->employee->id, 'TrangThai' => 'ChoPheDuyet', 'Diem' => 86, 'NgayDanhGia' => '2026-09-20 09:00:00'],
        ]);
        $this->get(route('manager.dashboard'))->assertViewHas('staff', function ($staff) {
            return $staff->firstWhere('id', $this->employee->id)->due_at->toDateString() === '2026-10-05'
                && $staff->firstWhere('id', $this->leader->id)->due_at === null
                && ! $staff->firstWhere('id', $this->leader->id)->needs_reminder;
        });
        $this->flushSession();
        $this->actingAs($this->leader)->get(route('leader.dashboard'))->assertSee('05/10/2026')->assertSee('Sắp đến hạn')->assertSee('Đang xử lý kết quả');
    }

    public function test_overdue_staff_are_not_dropped_after_105_days(): void
    {
        $this->employee->forceFill(['joined_at' => now()->subYear()->toDateString()])->save();
        $this->flushSession();
        $this->actingAs($this->leader)->get(route('leader.dashboard'))->assertViewHas('dueStaffs', fn ($staff) => $staff->contains('id', $this->employee->id))->assertSee('Quá hạn');
        $this->get(route('leader.staffList'))->assertViewHas('dueCount', 1);
    }

    #[TestWith(['TamNgung'])]
    #[TestWith(['NghiViec'])]
    public function test_inactive_staff_are_excluded_from_all_reminders_but_history_is_retained(string $status): void
    {
        $assessment = $this->assessment(true);
        $this->put(route('manager.employment.update', $this->employee->id), ['employment_status' => $status])->assertSessionHas('success');
        $this->assertDatabaseHas('users', ['id' => $this->employee->id, 'employment_status' => $status]);
        $this->artisan('brewlearn:remind-assessments')->assertSuccessful();
        $this->assertSame(0, DB::table('notifications')->where('user_id', $this->manager->id)->where('title', 'Đánh giá chờ phê duyệt')->count());
        $this->get(route('manager.dashboard'))->assertViewHas('staff', fn ($staff) => ! $staff->firstWhere('id', $this->employee->id)->needs_reminder);
        $this->get(route('manager.profile', $this->employee->id))->assertOk()->assertViewHas('assessments', fn ($records) => $records->contains('id', $assessment));
        $this->flushSession();
        $this->actingAs($this->leader)->get(route('leader.dashboard'))->assertViewHas('dueStaffs', fn ($staff) => ! $staff->contains('id', $this->employee->id));
        $this->post(route('leader.createSchedule'), ['user_id' => $this->employee->id, 'MaCTDT' => 'STD_BARISTA', 'NgayDanhGia' => now()->addDay()->format('Y-m-d\TH:i')])->assertSessionHasErrors('user_id');
        $this->assertDatabaseCount('notifications', 0);
    }

    public function test_status_updates_are_validated_and_scoped_to_the_managers_store(): void
    {
        $this->put(route('manager.employment.update', $this->employee->id), ['employment_status' => 'unknown'])->assertSessionHasErrors('employment_status');
        $this->put(route('manager.employment.update', $this->other->id), ['employment_status' => 'NghiViec'])->assertNotFound();
        $this->put(route('manager.employment.update', $this->employee->id), ['employment_status' => 'TamNgung']);
        $this->put(route('manager.employment.update', $this->employee->id), ['employment_status' => 'DangLam'])->assertSessionHas('success');
        $this->get(route('manager.dashboard'))->assertViewHas('staff', fn ($staff) => $staff->firstWhere('id', $this->employee->id)->needs_reminder);
    }

    public function test_end_of_month_dates_and_missing_start_dates_do_not_invent_due_dates(): void
    {
        $this->employee->forceFill(['joined_at' => '2026-01-31'])->save();
        $this->leader->forceFill(['joined_at' => null])->save();
        $this->get(route('manager.dashboard'))->assertViewHas('staff', function ($staff) {
            return $staff->firstWhere('id', $this->employee->id)->due_at->toDateString() === '2026-04-30'
                && $staff->firstWhere('id', $this->leader->id)->due_at === null;
        });
    }

    public function test_existing_schedule_suppresses_new_periodic_notifications_and_duplicate_schedules(): void
    {
        DB::table('danh_gia_nang_luc')->insert(['user_id' => $this->employee->id, 'NguoiDanhGia_id' => $this->leader->id, 'TrangThai' => 'ChoDanhGia', 'NgayHen' => now()->addDay()]);
        $this->artisan('brewlearn:remind-assessments')->assertSuccessful();
        $this->assertSame(0, DB::table('notifications')->where('title', 'Đánh giá định kỳ quá hạn')->count());
        $this->flushSession();
        $this->actingAs($this->leader)->get(route('leader.dashboard'))->assertSee('Đã xếp lịch');
        $this->post(route('leader.createSchedule'), ['user_id' => $this->employee->id, 'MaCTDT' => 'STD_BARISTA', 'NgayDanhGia' => now()->addDays(2)->format('Y-m-d\TH:i')])->assertSessionHasErrors('user_id');
        $this->assertDatabaseCount('danh_gia_nang_luc', 1);
    }

    public function test_working_staff_can_receive_a_new_assessment_schedule_notification(): void
    {
        $this->flushSession();
        $this->actingAs($this->leader)->post(route('leader.createSchedule'), [
            'user_id' => $this->employee->id, 'MaCTDT' => 'STD_BARISTA',
            'NgayDanhGia' => now()->addDays(2)->format('Y-m-d\TH:i'), 'note' => 'Ôn quy trình bàn giao ca',
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('danh_gia_nang_luc', ['user_id' => $this->employee->id, 'NguoiDanhGia_id' => $this->leader->id, 'TrangThai' => 'ChoDanhGia']);
        $this->assertDatabaseHas('notifications', ['user_id' => $this->employee->id, 'type' => 'schedule', 'title' => 'Lịch sát hạch năng lực mới từ Trưởng ca']);
        $this->assertDatabaseCount('notifications', 1);
    }

    #[TestWith(['2026-07-01', 'Quá hạn', true])]
    #[TestWith(['2026-07-02', 'Đến hạn', true])]
    #[TestWith(['2026-07-09', 'Sắp đến hạn', true])]
    #[TestWith(['2026-07-10', 'Chưa đến hạn', false])]
    public function test_due_categories_use_calendar_months_and_a_seven_day_window(string $joined, string $category, bool $remind): void
    {
        $this->travelTo(Carbon::parse('2026-10-02 10:00:00', 'Asia/Ho_Chi_Minh'));
        $this->employee->forceFill(['joined_at' => $joined])->save();

        $this->get(route('manager.dashboard'))->assertViewHas('staff', fn ($staff) => $staff->firstWhere('id', $this->employee->id)->due_status === $category
            && $staff->firstWhere('id', $this->employee->id)->needs_reminder === $remind);
    }
}
