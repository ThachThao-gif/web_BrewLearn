<?php

namespace Tests\Feature;

use App\Models\AssessmentEligibility;
use App\Models\CourseProgress;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
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
            $t->float('last_watched_seconds')->default(0);
        });
        Schema::create('bai_kiem_tra', function (Blueprint $t) {
            $t->id('MaBaiKiemTra');
            $t->string('TenBaiKiemTra');
            $t->float('DiemChuan')->default(80);
            $t->integer('MaBaiHoc')->nullable();
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
            $t->json('chi_tiet_yeu_to')->nullable();
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
        (require database_path('migrations/2026_10_02_100000_create_retraining_rounds_table.php'))->up();
        (require database_path('migrations/2026_10_02_110000_add_retraining_completion_requirements.php'))->up();
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

    public function test_staff_progress_is_read_in_batches_and_reflects_new_learning_without_writes(): void
    {
        $this->readyForInitialAssessment();
        DB::table('bai_hoc')->insert(['MaBaiHoc' => 2, 'FK_MaCTDT' => 1, 'TenBaiHoc' => 'Bàn giao']);
        DB::table('nhan_vien_khoa_hoc')->insert(['user_id' => $this->employee->id, 'MaCTDT' => 1, 'TienDo' => 10]);
        $staff = collect([$this->employee, $this->leader, $this->other]);
        DB::enableQueryLog();
        DB::flushQueryLog();
        $summary = app(CourseProgress::class)->summarize($staff);
        $queries = DB::getQueryLog();
        DB::disableQueryLog();
        $this->assertSame(50.0, $summary->get($this->employee->id));
        $this->assertCount(4, $queries);
        $this->assertDatabaseHas('nhan_vien_khoa_hoc', ['user_id' => $this->employee->id, 'TienDo' => 10]);
        DB::table('user_lesson_progresses')->insert(['user_id' => $this->employee->id, 'lesson_id' => 2, 'progress_percent' => 95, 'is_completed' => false]);
        $this->assertSame(100.0, app(CourseProgress::class)->summarize($staff)->get($this->employee->id));
    }

    public function test_dashboard_fetches_notification_feed_only_once_despite_many_partials(): void
    {
        DB::enableQueryLog();
        DB::flushQueryLog();
        $this->get(route('manager.dashboard'))->assertOk();
        $queries = collect(DB::getQueryLog());
        DB::disableQueryLog();
        $reads = $queries->filter(fn ($query) => str_contains($query['query'], 'from "notifications"'));
        $this->assertCount(1, $reads);
    }

    public function test_supplementing_store_demo_is_additive_and_does_not_reset_new_web_records(): void
    {
        Storage::fake('local');
        Schema::table('users', function (Blueprint $table) {
            $table->integer('CapDo')->nullable();
            $table->string('ChiNhanh')->nullable();
        });
        $this->travelTo(Carbon::parse('2026-10-04 12:00:00'));
        $this->manager->forceFill(['email' => 'ngocbich@brewlearn.com'])->save();
        $morning = DB::table('ca_lam_viec')->insertGetId(['cua_hang_id' => 1, 'ten_ca' => 'Ca sáng']);
        $afternoon = DB::table('ca_lam_viec')->insertGetId(['cua_hang_id' => 1, 'ten_ca' => 'Ca chiều']);
        $this->leader->forceFill(['ca_lam_viec_id' => $morning])->save();
        User::factory()->create(['ChucVu' => 'Trưởng ca', 'cua_hang_id' => 1, 'ca_lam_viec_id' => $afternoon]);
        $existing = $this->assessment(true);
        $this->artisan('brewlearn:supplement-store-demo')->assertSuccessful();
        $this->assertDatabaseHas('danh_gia_nang_luc', ['id' => $existing, 'TrangThai' => 'ChoPheDuyet']);
        $demoIds = User::where('email', 'like', 'operations%.demo@brewlearn.com')->pluck('id');
        $this->assertCount(6, $demoIds);
        $this->assertSame(10, DB::table('danh_gia_nang_luc')->whereIn('user_id', $demoIds)->count());
        $this->assertSame(2, DB::table('yeu_cau_dao_tao_lai')->whereIn('user_id', $demoIds)->where('trang_thai', 'HoanThanh')->count());
        $this->assertSame(2, DB::table('retraining_rounds')->max('round_number'));
        $this->assertSame(1, DB::table('yeu_cau_dao_tao_lai')->whereIn('user_id', $demoIds)->where('tien_trinh', 'DangThucHien')->count());
        $person = User::findOrFail($demoIds->first());
        $person->forceFill(['employment_status' => 'TamNgung'])->save();
        DB::table('danh_gia_nang_luc')->where('id', $existing)->update(['NhanXet' => 'Nhận xét mới trên web']);
        $this->artisan('brewlearn:supplement-store-demo')->assertSuccessful();
        $this->assertDatabaseHas('users', ['id' => $person->id, 'employment_status' => 'TamNgung']);
        $this->assertDatabaseHas('danh_gia_nang_luc', ['id' => $existing, 'NhanXet' => 'Nhận xét mới trên web']);
        $this->assertSame(10, DB::table('danh_gia_nang_luc')->whereIn('user_id', $demoIds)->count());
        $this->artisan('brewlearn:compact-report-demo')->assertSuccessful();
        $this->artisan('brewlearn:compact-report-demo')->assertSuccessful();
        $this->assertSame(10, DB::table('danh_gia_nang_luc')->whereIn('user_id', $demoIds)->count());
        $this->assertDatabaseHas('danh_gia_nang_luc', ['id' => $existing, 'NhanXet' => 'Nhận xét mới trên web']);
    }

    public function test_leader_dashboard_shows_one_completed_assessment_per_employee_without_deleting_history(): void
    {
        foreach (['2026-05-15', '2026-06-15', '2026-10-09'] as $date) {
            DB::table('danh_gia_nang_luc')->insert(['user_id' => $this->employee->id, 'NgayHen' => $date.' 10:00:00', 'TrangThai' => 'DaDuyet', 'Diem' => 90]);
        }
        $this->actingAs($this->leader)->get(route('leader.dashboard'))->assertOk()
            ->assertViewHas('schedules', fn ($rows) => $rows->count() === 1 && $rows->first()->NgayHen === '2026-10-09 10:00:00');
        $this->assertDatabaseCount('danh_gia_nang_luc', 3);
        $pending = DB::table('danh_gia_nang_luc')->insertGetId(['user_id' => $this->employee->id, 'NgayHen' => '2026-10-13 10:00:00', 'TrangThai' => 'ChoDanhGia']);
        $this->get(route('leader.dashboard'))->assertOk()
            ->assertViewHas('schedules', fn ($rows) => $rows->count() === 1 && $rows->first()->id === $pending);
    }

    public function test_dashboard_charts_use_approved_latest_monthly_results_and_latest_risks(): void
    {
        $this->travelTo(Carbon::parse('2026-10-04 12:00:00'));
        $second = User::factory()->create(['ChucVu' => 'Barista', 'cua_hang_id' => 1]);
        $inactive = User::factory()->create(['ChucVu' => 'Barista', 'cua_hang_id' => 1, 'employment_status' => 'NghiViec']);
        foreach ([[$this->employee, '2026-09-01', false, 'DaDuyet'], [$this->employee, '2026-09-15', true, 'DaDuyet'], [$second, '2026-09-10', false, 'HoanThanh'], [$second, '2026-09-20', true, 'ChoPheDuyet'], [$this->leader, '2026-09-10', true, 'DaDuyet'], [$this->other, '2026-09-10', true, 'DaDuyet'], [$inactive, '2026-09-10', true, 'DaDuyet']] as [$person, $date, $passed, $status]) {
            DB::table('danh_gia_nang_luc')->insert(['user_id' => $person->id, 'NgayDanhGia' => $date.' 12:00:00', 'TrangThai' => $status, 'IsPassed' => $passed, 'Diem' => 85]);
        }
        foreach (['Cao', 'Thap'] as $index => $risk) {
            DB::table('du_doan_nguy_co')->insert(['user_id' => $this->employee->id, 'cua_hang_id' => 1, 'muc_canh_bao' => $risk, 'phien_ban_mo_hinh' => 'rules-v3', 'ngay_du_doan' => '2026-10-0'.($index + 1)]);
        }
        $this->get(route('manager.dashboard'))->assertOk()
            ->assertViewHas('assessmentTrend', function ($trend) {
                $september = $trend->firstWhere('label', '09/2026');

                return $september['total'] === 2 && $september['passRate'] === 50.0
                    && $september['failRate'] === 50.0 && $trend->firstWhere('label', '08/2026')['passRate'] === null;
            })
            ->assertViewHas('riskDistribution', fn ($risk) => $risk->all() === ['Cao' => 0, 'TrungBinh' => 0, 'Thap' => 1, 'ChuaDuDuLieu' => 0, 'ChuaCoKetQua' => 1]);
        $this->get(route('manager.dashboard', ['q' => 'không có nhân viên']))->assertOk()
            ->assertViewHas('riskDistribution', fn ($risk) => $risk->sum() === 0)
            ->assertViewHas('assessmentTrend', fn ($trend) => $trend->sum('total') === 0);
    }

    public function test_rule_warnings_exclude_old_rule_versions_and_show_score_reasons_coverage_and_timestamps(): void
    {
        $details = json_encode(['completed_quiz_count' => 2, 'applicable_quiz_count' => 7,
            'details' => ['tong_diem' => 1, 'ly_do_canh_bao' => ['Có bài kiểm tra chưa đạt']]]);
        DB::table('du_doan_nguy_co')->insert([
            ['user_id' => $this->employee->id, 'cua_hang_id' => 1, 'muc_canh_bao' => 'Thap', 'diem_rui_ro' => 0.125, 'phien_ban_mo_hinh' => 'rules-v3', 'chi_tiet_yeu_to' => $details, 'ngay_du_doan' => '2026-10-01 09:00:00'],
            ['user_id' => $this->employee->id, 'cua_hang_id' => 1, 'muc_canh_bao' => 'Cao', 'diem_rui_ro' => 0.95, 'phien_ban_mo_hinh' => 'rules-v2', 'chi_tiet_yeu_to' => null, 'ngay_du_doan' => '2026-10-02 09:00:00'],
        ]);
        $this->get(route('manager.staff'))->assertOk()->assertSee('Thấp')->assertDontSee('Có bài kiểm tra chưa đạt');
        $this->assessment();
        $this->get(route('manager.assessments'))->assertOk()->assertSee('Có bài kiểm tra chưa đạt');
        $this->get(route('manager.profile', $this->employee->id))->assertOk()->assertSee('12.5/100')
            ->assertSee('Đã làm 2/7')->assertSee('Có bài kiểm tra chưa đạt')
            ->assertSee('01/10/2026 16:00:00')->assertDontSee('95.0/100');
        DB::table('du_doan_nguy_co')->where('phien_ban_mo_hinh', 'rules-v3')->delete();
        $this->get(route('manager.dashboard'))->assertOk()
            ->assertViewHas('staff', fn ($staff) => $staff->firstWhere('id', $this->employee->id)->risk === 'ChuaCoKetQua');
    }

    public function test_latest_rule_uses_id_for_tied_timestamps_and_keeps_insufficient_distinct(): void
    {
        foreach (['Cao', 'ChuaDuDuLieu'] as $risk) {
            DB::table('du_doan_nguy_co')->insert(['user_id' => $this->employee->id, 'cua_hang_id' => 1,
                'muc_canh_bao' => $risk, 'phien_ban_mo_hinh' => 'rules-v3', 'ngay_du_doan' => '2026-10-01 09:00:00']);
        }
        $this->get(route('manager.dashboard'))->assertOk()
            ->assertViewHas('riskDistribution', fn ($risk) => $risk['ChuaDuDuLieu'] === 1 && $risk['ChuaCoKetQua'] === 0 && $risk['Cao'] === 0);
    }

    public function test_staff_list_is_paginated_and_keeps_filters(): void
    {
        User::factory()->count(12)->create(['ChucVu' => 'Barista', 'cua_hang_id' => 1, 'joined_at' => now()->subMonth()]);

        $first = $this->get(route('manager.staff'))->assertOk()->viewData('staffPage');
        $this->assertSame(10, $first->count());
        $this->assertGreaterThan(10, $first->total());
        $this->get(route('manager.staff', ['page' => 2]))->assertOk()
            ->assertViewHas('staffPage', fn ($page) => $page->count() === $first->total() - 10);
        $this->get(route('manager.staff', ['q' => 'Nhân viên A']))->assertOk()
            ->assertViewHas('staffPage', fn ($page) => $page->total() === 1 && ! $page->hasPages());
    }

    public function test_unknown_positions_are_not_counted_as_missing_rule_predictions(): void
    {
        $this->employee->forceFill(['ChucVu' => 'Thực Tập Sinh'])->save();
        $this->get(route('manager.dashboard'))->assertOk()
            ->assertViewHas('riskDistribution', fn ($risk) => $risk->sum() === 0);
        $this->get(route('manager.staff'))->assertOk()->assertSee('Không thuộc đối tượng cảnh báo');
    }

    private function confirmRound(int $plan): void
    {
        DB::table('retraining_rounds')->where('request_id', $plan)->where('status', 'DangThucHien')
            ->update(['confirmed_by' => $this->leader->id, 'confirmed_at' => now(), 'confirmation_note' => 'Đã thực hành đủ']);
    }

    private function readyForInitialAssessment(): void
    {
        DB::table('chuong_trinh_dao_tao')->insert(['MaCTDT' => 1, 'TenCTDT' => 'An toàn', 'ChucVuApDung' => 'ALL STAFF']);
        DB::table('bai_hoc')->insert(['MaBaiHoc' => 1, 'FK_MaCTDT' => 1, 'TenBaiHoc' => 'Vệ sinh']);
        DB::table('user_lesson_progresses')->insert(['user_id' => $this->employee->id, 'lesson_id' => 1, 'progress_percent' => 100, 'is_completed' => true]);
        DB::table('bai_kiem_tra')->insert(['MaBaiKiemTra' => 1, 'TenBaiKiemTra' => 'Quiz vệ sinh', 'DiemChuan' => 90]);
        DB::table('ket_qua_kiem_tra')->insert(['user_id' => $this->employee->id, 'MaBaiKiemTra' => 1, 'Diem' => 90]);
    }

    public function test_leaders_are_not_assessment_targets_but_remain_learning_and_staff_members(): void
    {
        $this->leader->forceFill(['joined_at' => now()->subYear()])->save();
        $id = DB::table('danh_gia_nang_luc')->insertGetId(['user_id' => $this->leader->id, 'TrangThai' => 'ChoPheDuyet', 'IsPassed' => true, 'Diem' => 90, 'updated_at' => now()]);
        $this->get(route('manager.dashboard'))->assertViewHas('pendingCount', 0);
        $this->get(route('manager.staff'))->assertDontSee($this->leader->name);
        $this->get(route('manager.profile', $this->leader->id))->assertNotFound();
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

    public function test_employee_opening_manager_page_can_switch_to_manager_account(): void
    {
        $this->flushSession();
        $this->actingAs($this->employee)->get(route('manager.dashboard'))->assertForbidden()
            ->assertSee($this->employee->name)->assertSee('Đổi tài khoản')->assertSee('ngocbich@brewlearn.com');
        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->post(route('login.post'), ['email' => $this->manager->email, 'password' => 'test-manager-password'])
            ->assertRedirect(route('manager.dashboard'));
        $this->get(route('manager.dashboard'))->assertOk()->assertSee('Duyệt đánh giá');
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

    public function test_approved_result_notification_lists_each_criterion_score(): void
    {
        $id = $this->assessment();
        DB::table('danh_gia_nang_luc')->where('id', $id)->update(['Diem_NghiepVu' => 85, 'Diem_KyNang' => 65, 'Diem_QuyTrinh' => 80, 'Diem_PhucVu' => 90, 'Diem_TacPhong' => 88]);

        $this->post(route('manager.review', $id), ['action' => 'approve'])->assertRedirect();

        $message = DB::table('notifications')->where('user_id', $this->employee->id)->where('title', 'Kết quả đánh giá đã duyệt: CHƯA ĐẠT')->value('message');
        $this->assertStringContainsString('- Nghiệp vụ (20%): 85/100', $message);
        $this->assertStringContainsString('- Kỹ năng (30%): 65/100 — dưới mức tối thiểu 70', $message);
        $this->assertStringNotContainsString('Quy trình (20%): 80/100 — dưới mức', $message);
    }

    public function test_prediction_stays_independent_of_approved_evaluation(): void
    {
        DB::table('du_doan_nguy_co')->insert([['user_id' => $this->employee->id, 'cua_hang_id' => 1, 'muc_canh_bao' => 'Thap', 'phien_ban_mo_hinh' => 'rules-v3', 'ngay_du_doan' => now()->subDay()], ['user_id' => $this->employee->id, 'cua_hang_id' => 1, 'muc_canh_bao' => 'Cao', 'phien_ban_mo_hinh' => 'rules-v3', 'ngay_du_doan' => now()]]);
        $this->get(route('manager.dashboard'))->assertOk()->assertViewHas('staff', fn ($staff) => $staff->first()->id === $this->employee->id && $staff->first()->risk === 'Cao');
        $this->post(route('manager.review', $this->assessment(true)), ['action' => 'approve'])->assertRedirect();
        $this->assertDatabaseHas('notifications', ['user_id' => $this->employee->id, 'title' => 'Kết quả đánh giá đã duyệt: ĐẠT']);
        $this->assertDatabaseHas('notifications', ['user_id' => $this->leader->id, 'title' => 'Đánh giá bạn gửi đã được duyệt']);
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
        $colleague = $this->employee->replicate();
        $colleague->email = 'training-colleague@example.com';
        $colleague->save();
        $this->get(route('manager.training'))->assertOk()
            ->assertViewHas('trainingOwners', fn ($owners) => $owners->pluck('id')->all() === [$this->leader->id]);
        $this->put(route('manager.training.assign', $plan), array_replace($payload, ['nguoi_phu_trach_id' => $colleague->id]))->assertSessionHasErrors('nguoi_phu_trach_id');
        $this->put(route('manager.training.assign', $plan), array_replace($payload, ['nguoi_phu_trach_id' => $this->employee->id]))->assertSessionHasErrors('nguoi_phu_trach_id');
        $this->put(route('manager.training.assign', $plan), array_replace($payload, ['nguoi_phu_trach_id' => $this->manager->id]))->assertSessionHasErrors('nguoi_phu_trach_id');
        $this->put(route('manager.training.assign', $plan), array_replace($payload, ['nguoi_phu_trach_id' => $this->other->id]))->assertSessionHasErrors('nguoi_phu_trach_id');
        $this->put(route('manager.training.assign', $plan), $payload)->assertSessionHas('success');
        $employeeMessage = DB::table('notifications')->where('user_id', $this->employee->id)->where('title', 'Kế hoạch đào tạo lại')->value('message');
        $this->assertStringStartsWith("Người phụ trách: Trưởng ca B\nHạn hoàn thành: ".$payload['han_hoan_thanh'], $employeeMessage);
        $this->assertStringContainsString('Ngày đánh giá lại dự kiến: '.$payload['ngay_danh_gia_lai'], $employeeMessage);
        $this->assertStringStartsWith("Nhân viên: Nhân viên A\nNgười phụ trách: Trưởng ca B",
            DB::table('notifications')->where('user_id', $this->leader->id)->where('title', 'Phân công phụ trách đào tạo lại')->value('message'));
        $this->flushSession();
        $this->actingAs($this->leader)->get(route('training.assignments'))->assertOk()
            ->assertViewHas('rounds', fn ($rows) => $rows->count() === 1);
        $this->flushSession();
        $this->actingAs($this->manager);
        $this->confirmRound($plan);
        $this->post(route('manager.training.reassess', $plan), ['assessor_id' => $this->leader->id, 'assessment_date' => now()->addWeeks(2)->toDateString(), 'assessment_time' => '09:30', 'criteria' => 'Tác phong bàn giao ca'])->assertSessionHas('success');
        $assessment = DB::table('yeu_cau_dao_tao_lai')->find($plan)->danh_gia_lai_id;
        $this->assertDatabaseHas('yeu_cau_dao_tao_lai', ['id' => $plan, 'tien_trinh' => 'ChoDanhGiaLai', 'ket_qua_sau_dao_tao_lai' => 'ChuaCoKetQua']);
        $this->flushSession();
        $this->actingAs($this->leader)->get(route('training.assignments'))->assertOk()
            ->assertViewHas('rounds', fn ($rows) => $rows->isEmpty());
        $this->flushSession();
        $this->actingAs($this->manager);
        DB::table('danh_gia_nang_luc')->where('id', $assessment)->update(['Diem' => 90, 'IsPassed' => true, 'TrangThai' => 'ChoPheDuyet']);
        $this->post(route('manager.review', $assessment), ['action' => 'approve'])->assertSessionHas('success');
        $this->assertDatabaseHas('yeu_cau_dao_tao_lai', ['id' => $plan, 'trang_thai' => 'HoanThanh', 'tien_trinh' => 'HoanTat', 'ket_qua_sau_dao_tao_lai' => 'Dat']);
        $this->assertDatabaseCount('yeu_cau_dao_tao_lai', 1);
        $roundId = DB::table('retraining_rounds')->where('request_id', $plan)->value('id');
        $this->flushSession();
        $this->actingAs($this->leader)->get(route('training.assignments'))->assertOk()
            ->assertViewHas('rounds', fn ($rows) => $rows->isEmpty());
        $this->post(route('training.confirm', $roundId), ['confirmation_note' => 'Xác nhận lại'])->assertStatus(409);
        $this->assertDatabaseCount('retraining_rounds', 1);
    }

    #[TestWith([true])]
    #[TestWith([false])]
    public function test_multiple_retraining_rounds_stop_after_three_rounds(bool $passesFinalRound): void
    {
        $this->post(route('manager.review', $this->assessment()), ['action' => 'approve']);
        $plan = DB::table('yeu_cau_dao_tao_lai')->value('id');
        $assessmentIds = [];
        foreach ([1, 2, 3] as $number) {
            $payload = ['nguoi_phu_trach_id' => $this->leader->id, 'han_hoan_thanh' => now()->addWeek()->toDateString(),
                'ngay_danh_gia_lai' => now()->addWeeks(2)->toDateString(), 'ghi_chu_thuc_hien' => 'Nội dung vòng '.$number];
            $this->put(route('manager.training.assign', $plan), $payload)->assertSessionHas('success');
            // Updating an ongoing plan does not create a new round.
            $this->put(route('manager.training.assign', $plan), $payload)->assertSessionHas('success');
            $this->assertDatabaseCount('retraining_rounds', $number);
            $this->confirmRound($plan);
            $this->post(route('manager.training.reassess', $plan), ['assessor_id' => $this->leader->id,
                'assessment_date' => now()->addWeeks(2)->toDateString(), 'assessment_time' => '09:30', 'criteria' => 'Phục vụ'])->assertSessionHas('success');
            $assessmentId = DB::table('yeu_cau_dao_tao_lai')->find($plan)->danh_gia_lai_id;
            $assessmentIds[] = $assessmentId;
            $passed = $number === 3 && $passesFinalRound;
            DB::table('danh_gia_nang_luc')->where('id', $assessmentId)->update(['Diem' => $passed ? 90 : 60,
                'IsPassed' => $passed, 'TrangThai' => 'ChoPheDuyet', 'ChiTietDeXuat' => $passed ? null : json_encode(['details' => ['theodoica' => ['behavior' => 'Hướng dẫn vòng tiếp', 'shift_count' => 3]]])]);
            $this->assertDatabaseHas('yeu_cau_dao_tao_lai', ['id' => $plan, 'trang_thai' => 'DaDuyet', 'tien_trinh' => 'ChoDanhGiaLai']);
            if ($number === 2) {
                $this->post(route('manager.review', $assessmentId), ['action' => 'return', 'reason' => 'Bổ sung nhận xét'])->assertSessionHas('success');
                $this->assertDatabaseHas('retraining_rounds', ['round_number' => 2, 'result' => 'ChuaCoKetQua']);
                DB::table('danh_gia_nang_luc')->where('id', $assessmentId)->update(['TrangThai' => 'ChoPheDuyet']);
            }
            $this->post(route('manager.review', $assessmentId), ['action' => 'approve'])->assertSessionHas('success');
            $this->assertDatabaseHas('retraining_rounds', ['request_id' => $plan, 'round_number' => $number,
                'assessment_id' => $assessmentId, 'instructions' => 'Nội dung vòng '.$number, 'result' => $passed ? 'Dat' : 'ChuaDat']);
            $this->assertDatabaseHas('yeu_cau_dao_tao_lai', ['id' => $plan,
                'trang_thai' => $passed ? 'HoanThanh' : 'DaDuyet', 'tien_trinh' => $passed ? 'HoanTat' : ($number === 3 ? 'DungDaoTao' : 'ChoGiao')]);
        }
        $this->assertCount(3, array_unique($assessmentIds));
        $this->assertDatabaseCount('yeu_cau_dao_tao_lai', 1);
        $this->get(route('manager.training'))->assertOk()->assertSee('Đào tạo lại lần 1')->assertSee('Đào tạo lại lần 2')->assertSee('Đào tạo lại lần 3');
        $this->get(route('manager.profile', $this->employee->id))->assertOk()->assertSee('Đào tạo lại lần 1');
        $this->put(route('manager.training.assign', $plan), $payload)->assertStatus(409);
        if (! $passesFinalRound) {
            $this->get(route('manager.training'))->assertSee('Đã đạt giới hạn 3 vòng')->assertDontSee('Lưu & giao đào tạo lại');
            $this->assertDatabaseHas('notifications', ['user_id' => $this->manager->id, 'title' => 'Đào tạo lại đã đạt giới hạn']);
            // Existing requests waiting for a fourth round must also be blocked.
            DB::table('yeu_cau_dao_tao_lai')->where('id', $plan)->update(['tien_trinh' => 'ChoGiao']);
            $this->put(route('manager.training.assign', $plan), $payload)->assertStatus(409);
            $this->assertDatabaseCount('retraining_rounds', 3);
        }
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
        $this->get(route('manager.profile', $this->employee->id))->assertOk()->assertSee('1/2 bài học đã hoàn thành')->assertSee('50%')->assertViewHas('quizzes', fn ($quizzes) => $quizzes->count() === 2)
            ->assertSee('Chỉ hỗ trợ xem xét hồ sơ');
        $this->assertDatabaseCount('nhan_vien_khoa_hoc', 0);
    }

    public function test_leader_views_profiles_only_for_staff_in_their_store(): void
    {
        $this->actingAs($this->leader)->get(route('leader.staffList'))->assertOk()->assertSee(route('leader.staffProfile', $this->employee->id));
        $this->get(route('leader.staffProfile', $this->employee->id))->assertOk()
            ->assertSee('Nhân viên A')->assertSee('Flagship')->assertSee($this->employee->joined_at->format('d/m/Y'))->assertSee('Quá hạn');
        $this->get(route('leader.staffProfile', $this->other->id))->assertNotFound();
        $this->get(route('leader.staffProfile', $this->manager->id))->assertNotFound();
    }

    public function test_dashboard_trend_can_be_filtered_by_year(): void
    {
        $this->travelTo(Carbon::parse('2026-10-02 10:00:00', 'Asia/Ho_Chi_Minh'));
        DB::table('danh_gia_nang_luc')->insert([
            ['user_id' => $this->employee->id, 'TrangThai' => 'DaDuyet', 'Diem' => 85, 'IsPassed' => true, 'NgayDanhGia' => '2025-11-10 10:00:00'],
            ['user_id' => $this->employee->id, 'TrangThai' => 'DaDuyet', 'Diem' => 70, 'IsPassed' => false, 'NgayDanhGia' => '2026-08-10 10:00:00'],
        ]);

        $this->get(route('manager.dashboard'))->assertOk()
            ->assertViewHas('trendYears', fn ($years) => $years->all() === [2026, 2025])
            ->assertViewHas('assessmentTrend', fn ($trend) => $trend->count() === 6 && $trend->sum('total') === 1);
        $this->get(route('manager.dashboard', ['year' => 2025]))->assertOk()
            ->assertViewHas('assessmentTrend', fn ($trend) => $trend->count() === 12 && $trend->firstWhere('label', '11/2025')['passed'] === 1);
        $this->get(route('manager.dashboard', ['year' => 2026]))->assertOk()
            ->assertViewHas('assessmentTrend', fn ($trend) => $trend->count() === 10 && $trend->firstWhere('label', '08/2026')['failed'] === 1);
        $this->get(route('manager.dashboard', ['year' => 1999]))->assertOk()
            ->assertViewHas('trendYear', null)->assertViewHas('assessmentTrend', fn ($trend) => $trend->count() === 6);
    }

    public function test_demo_dates_shift_together_and_keep_intervals(): void
    {
        $anchorFile = storage_path('app/demo-anchor-date.txt');
        $originalAnchor = is_file($anchorFile) ? file_get_contents($anchorFile) : null;
        $this->employee->forceFill(['joined_at' => '2026-07-20'])->save();
        $id = DB::table('danh_gia_nang_luc')->insertGetId(['user_id' => $this->employee->id, 'TrangThai' => 'ChoDanhGia', 'NgayHen' => '2026-10-17 10:00:00', 'created_at' => '2026-10-14 16:00:00']);
        DB::table('notifications')->insert(['user_id' => $this->employee->id, 'title' => 'Lịch', 'message' => 'Hẹn lúc 10:00 17/10/2026, hạn 2026-10-20.', 'type' => 'schedule', 'is_read' => false, 'created_at' => '2026-10-14 16:00:00']);

        try {
            $this->artisan('brewlearn:shift-demo-dates', ['--from' => '2026-10-16', '--to' => '2026-11-20', '--skip-risk' => true])->assertSuccessful();
        } finally {
            $originalAnchor === null ? @unlink($anchorFile) : file_put_contents($anchorFile, $originalAnchor);
        }

        $this->assertSame('2026-08-24', Carbon::parse(DB::table('users')->where('id', $this->employee->id)->value('joined_at'))->toDateString());
        $assessment = DB::table('danh_gia_nang_luc')->find($id);
        $this->assertSame('2026-11-21 10:00:00', Carbon::parse($assessment->NgayHen)->format('Y-m-d H:i:s'));
        $this->assertSame('2026-11-18 16:00:00', Carbon::parse($assessment->created_at)->format('Y-m-d H:i:s'));
        $this->assertDatabaseHas('notifications', ['message' => 'Hẹn lúc 10:00 21/11/2026, hạn 2026-11-24.']);
    }

    public function test_only_leaders_can_use_leader_functions(): void
    {
        $this->actingAs($this->employee);
        $this->get(route('leader.dashboard'))->assertForbidden();
        $this->get(route('leader.staffList'))->assertForbidden();
        $this->post(route('leader.createSchedule'), ['user_id' => $this->leader->id, 'MaCTDT' => 'STD_BARISTA', 'NgayDanhGia' => now()->addDay()->format('Y-m-d\TH:i')])->assertForbidden();
        $this->post(route('leader.sendNotification'), ['user_id' => 'all', 'title' => 'Nhắc', 'message' => 'Nội dung'])->assertForbidden();
        $this->assertDatabaseCount('danh_gia_nang_luc', 0);
    }

    public function test_leader_notifications_and_incidents_stay_within_scope(): void
    {
        (require database_path('migrations/2026_09_26_121224_create_loi_nghiep_vu_table.php'))->up();
        $this->actingAs($this->leader);
        $this->post(route('leader.sendNotification'), ['user_id' => 'all', 'title' => 'Nhắc lịch', 'message' => 'Kiểm tra lịch'])->assertSessionHas('success');
        $this->assertSame([$this->employee->id], DB::table('notifications')->where('title', 'Nhắc lịch')->pluck('user_id')->all());
        $this->post(route('leader.sendNotification'), ['user_id' => $this->other->id, 'title' => 'Ngoài', 'message' => 'Ngoài phạm vi'])->assertSessionHasErrors('user_id');
        $this->post(route('leader.sendNotification'), ['user_id' => $this->manager->id, 'title' => 'Quản lý', 'message' => 'Ngoài phạm vi'])->assertSessionHasErrors('user_id');

        $incident = ['loai_loi' => 'Vệ sinh quầy bar', 'muc_do' => 'Nhanh', 'thoi_diem' => now()->subHour()->format('Y-m-d\TH:i'), 'mo_ta' => 'Chưa xả vòi hơi'];
        $this->post(route('leader.recordIncident'), $incident + ['user_id' => $this->employee->id])->assertSessionHas('success');
        $this->assertDatabaseHas('loi_nghiep_vu', ['user_id' => $this->employee->id, 'nguoi_ghi_nhan_id' => $this->leader->id, 'muc_do' => 'Nhanh']);
        $this->post(route('leader.recordIncident'), $incident + ['user_id' => $this->other->id])->assertSessionHasErrors('user_id');
        $this->assertDatabaseCount('loi_nghiep_vu', 1);
    }

    public function test_leader_profile_shows_course_quiz_and_assessment_history_without_risk_or_writes(): void
    {
        DB::table('chuong_trinh_dao_tao')->insert(['MaCTDT' => 1, 'TenCTDT' => 'Khóa pha chế', 'ChucVuApDung' => 'Barista']);
        DB::table('bai_hoc')->insert([['MaBaiHoc' => 1, 'FK_MaCTDT' => 1, 'TenBaiHoc' => 'Bài 1'], ['MaBaiHoc' => 2, 'FK_MaCTDT' => 1, 'TenBaiHoc' => 'Bài 2']]);
        DB::table('user_lesson_progresses')->insert(['user_id' => $this->employee->id, 'lesson_id' => 1, 'progress_percent' => 100, 'is_completed' => true]);
        DB::table('bai_kiem_tra')->insert(['MaBaiKiemTra' => 1, 'TenBaiKiemTra' => 'Quiz pha chế', 'DiemChuan' => 80]);
        foreach ([60, 90] as $score) {
            DB::table('ket_qua_kiem_tra')->insert(['user_id' => $this->employee->id, 'MaBaiKiemTra' => 1, 'Diem' => $score, 'NgayLam' => now()]);
        }
        DB::table('danh_gia_nang_luc')->insert(['user_id' => $this->employee->id, 'NguoiDanhGia_id' => $this->leader->id, 'TieuChuan' => 'Tiêu chuẩn Barista', 'TrangThai' => 'TraLai', 'Diem' => 75, 'IsPassed' => false, 'NhanXet' => 'Cần luyện đánh sữa', 'NgayDanhGia' => now(), 'created_at' => now(), 'updated_at' => now()]);
        DB::table('du_doan_nguy_co')->insert(['user_id' => $this->employee->id, 'cua_hang_id' => 1, 'muc_canh_bao' => 'Cao', 'chi_tiet_yeu_to' => json_encode(['ly_do' => 'YẾU TỐ NGUY CƠ RIÊNG']), 'ngay_du_doan' => now()]);

        $this->actingAs($this->leader)->get(route('leader.staffProfile', $this->employee->id))->assertOk()
            ->assertSee('1/2 bài học đã hoàn thành')->assertSee('Lần 2')
            ->assertSee('Cần luyện đánh sữa')->assertSee('Bị trả lại')->assertSee('Trưởng ca B')
            ->assertDontSee('YẾU TỐ NGUY CƠ RIÊNG');
        $this->assertDatabaseCount('nhan_vien_khoa_hoc', 0);
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
        $this->assertDatabaseMissing('notifications', ['user_id' => $this->employee->id]);
        $this->artisan('brewlearn:remind-assessments')->assertSuccessful();
        $this->assertSame(1, DB::table('notifications')->where('user_id', $this->manager->id)->where('title', 'Đánh giá chờ phê duyệt')->count());
        $this->flushSession();
        $this->actingAs($this->manager)->post(route('manager.review', $id), ['action' => 'return', 'reason' => 'Cần cụ thể hơn'])->assertSessionHas('success');
        $this->assertDatabaseMissing('notifications', ['user_id' => $this->employee->id]);
        $this->assertDatabaseHas('notifications', ['user_id' => $this->leader->id, 'title' => 'Đánh giá cần bổ sung']);
        $this->flushSession();
        $this->actingAs($this->leader)->post(route('leader.saveScore'), $payload)->assertSessionHas('success');
        $this->assertDatabaseHas('danh_gia_nang_luc', ['id' => $id, 'TrangThai' => 'ChoPheDuyet', 'nguoi_duyet_id' => null, 'ly_do_tra_lai' => null]);
        $this->flushSession();
        $this->actingAs($this->manager)->post(route('manager.review', $id), ['action' => 'approve'])->assertSessionHas('success');
        $this->assertDatabaseHas('yeu_cau_dao_tao_lai', ['assessment_id' => $id, 'nguon_de_xuat' => 'Trưởng ca đề xuất']);
        $this->assertDatabaseHas('notifications', ['user_id' => $this->employee->id, 'title' => 'Kết quả đánh giá đã duyệt: CHƯA ĐẠT']);
        $message = DB::table('notifications')->where('user_id', $this->employee->id)->value('message');
        $this->assertStringContainsString('Theo dõi bàn giao', $message);
        $this->assertStringContainsString('3 ca', $message);
        $this->assertDatabaseHas('notifications', ['user_id' => $this->leader->id, 'title' => 'Đánh giá bạn gửi đã được duyệt']);
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
                && ! $staff->contains('id', $this->leader->id);
        });
        $this->flushSession();
        $this->actingAs($this->leader)->get(route('leader.dashboard'))->assertSee('05/10/2026')->assertSee('còn 3 ngày')->assertSee('Chờ quản lý duyệt')->assertSee('Đã chấm, chờ quản lý duyệt');
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
                && ! $staff->contains('id', $this->leader->id);
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
        $this->readyForInitialAssessment();
        $this->flushSession();
        $this->actingAs($this->leader)->post(route('leader.createSchedule'), [
            'user_id' => $this->employee->id, 'MaCTDT' => 'STD_BARISTA',
            'NgayDanhGia' => now()->addDays(2)->format('Y-m-d\TH:i'), 'note' => 'Ôn quy trình bàn giao ca',
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('danh_gia_nang_luc', ['user_id' => $this->employee->id, 'NguoiDanhGia_id' => $this->leader->id, 'TrangThai' => 'ChoDanhGia']);
        $this->assertDatabaseHas('notifications', ['user_id' => $this->employee->id, 'type' => 'schedule', 'title' => 'Lịch sát hạch năng lực mới từ Trưởng ca']);
        $this->assertDatabaseCount('notifications', 1);
    }

    public function test_initial_schedule_requires_all_lessons_and_quizzes_but_keeps_due_reminders(): void
    {
        $this->readyForInitialAssessment();
        DB::table('user_lesson_progresses')->where('user_id', $this->employee->id)->update(['progress_percent' => 20, 'is_completed' => false]);
        DB::table('ket_qua_kiem_tra')->where('user_id', $this->employee->id)->update(['Diem' => 85]);
        $this->flushSession();
        $this->actingAs($this->leader)->get(route('leader.dashboard'))->assertOk()->assertSee('Chưa đủ điều kiện đánh giá')->assertSee('Vệ sinh')->assertSee('Quiz vệ sinh');
        $payload = ['user_id' => $this->employee->id, 'MaCTDT' => 'STD_BARISTA', 'NgayDanhGia' => now()->addDay()->format('Y-m-d\TH:i')];
        $this->post(route('leader.createSchedule'), $payload)->assertSessionHasErrors('user_id');
        $this->assertDatabaseCount('danh_gia_nang_luc', 0);
        DB::table('user_lesson_progresses')->where('user_id', $this->employee->id)->update(['progress_percent' => 100, 'is_completed' => true]);
        $this->post(route('leader.createSchedule'), $payload)->assertSessionHasErrors('user_id');
        DB::table('ket_qua_kiem_tra')->where('user_id', $this->employee->id)->update(['Diem' => 90]);
        $this->post(route('leader.createSchedule'), $payload)->assertSessionHas('success');
        $this->assertDatabaseCount('danh_gia_nang_luc', 1);
    }

    public function test_stopped_retraining_does_not_block_periodic_assessments_but_active_requests_do(): void
    {
        $this->readyForInitialAssessment();
        $plan = DB::table('yeu_cau_dao_tao_lai')->insertGetId([
            'user_id' => $this->employee->id, 'trang_thai' => 'DaDuyet',
            'tien_trinh' => 'DungDaoTao', 'ket_qua_sau_dao_tao_lai' => 'ChuaDat',
            'ly_do' => 'Chưa đạt sau 3 vòng đào tạo lại',
        ]);
        $payload = ['user_id' => $this->employee->id, 'MaCTDT' => 'STD_BARISTA',
            'NgayDanhGia' => now()->addDay()->format('Y-m-d\TH:i')];
        $this->actingAs($this->leader);
        foreach (['ChoGiao', 'DangThucHien', 'ChoDanhGiaLai'] as $progress) {
            DB::table('yeu_cau_dao_tao_lai')->where('id', $plan)->update(['tien_trinh' => $progress]);
            $this->post(route('leader.createSchedule'), $payload)->assertSessionHasErrors('user_id');
            $this->assertDatabaseCount('danh_gia_nang_luc', 0);
        }
        DB::table('yeu_cau_dao_tao_lai')->where('id', $plan)->update(['tien_trinh' => 'DungDaoTao']);
        $this->post(route('leader.createSchedule'), $payload)->assertSessionHas('success');
        $this->assertDatabaseHas('danh_gia_nang_luc', ['user_id' => $this->employee->id, 'TrangThai' => 'ChoDanhGia']);
        $this->assertDatabaseHas('yeu_cau_dao_tao_lai', ['id' => $plan, 'tien_trinh' => 'DungDaoTao', 'ket_qua_sau_dao_tao_lai' => 'ChuaDat']);
    }

    public function test_reassessment_requires_fresh_target_learning_quiz_and_owner_confirmation_only(): void
    {
        $this->readyForInitialAssessment();
        Schema::table('user_lesson_progresses', fn (Blueprint $t) => $t->timestamp('updated_at')->nullable());
        DB::table('bai_hoc')->insert(['MaBaiHoc' => 2, 'FK_MaCTDT' => 1, 'TenBaiHoc' => 'Bài không được giao lại']);
        DB::table('bai_kiem_tra')->where('MaBaiKiemTra', 1)->update(['MaBaiHoc' => 1]);
        DB::table('ket_qua_kiem_tra')->where('user_id', $this->employee->id)->update(['created_at' => now()->subDay()]);
        $id = $this->assessment();
        DB::table('danh_gia_nang_luc')->where('id', $id)->update(['ChiTietDeXuat' => json_encode(['details' => ['hoclaibaihoc' => ['course_id' => 1, 'lesson_ids' => [1]]]])]);
        $this->post(route('manager.review', $id), ['action' => 'approve']);
        $plan = DB::table('yeu_cau_dao_tao_lai')->value('id');
        $assignment = ['nguoi_phu_trach_id' => $this->leader->id, 'han_hoan_thanh' => now()->addWeek()->toDateString(),
            'ngay_danh_gia_lai' => now()->addWeeks(2)->toDateString(), 'ghi_chu_thuc_hien' => 'Ôn vệ sinh trong ca'];
        $this->put(route('manager.training.assign', $plan), $assignment)->assertSessionHas('success');
        $round = DB::table('retraining_rounds')->first();
        $this->assertDatabaseHas('user_lesson_progresses', ['user_id' => $this->employee->id, 'lesson_id' => 1, 'progress_percent' => 0]);
        $this->flushSession();
        $this->actingAs($this->employee)->get(route('courses.index'))->assertOk()
            ->assertSee('Tiến độ học lại')->assertSee('Đang học lại')->assertSee('Trưởng ca B')->assertSee('đã hoàn thành 0/1 bài')->assertSee('đã đạt 0/1 bài')->assertSee(route('courses.lesson', 1))->assertSee('Học lại (1)');
        $this->get(route('dashboard'))->assertOk()->assertDontSee('Tiến độ học lại');
        $this->flushSession();
        $this->actingAs($this->manager);
        $schedule = ['assessor_id' => $this->leader->id, 'assessment_date' => now()->addWeeks(2)->toDateString(), 'assessment_time' => '09:30', 'criteria' => 'Vệ sinh'];
        $this->post(route('manager.training.reassess', $plan), $schedule)->assertSessionHasErrors('training');
        $this->flushSession();
        $this->actingAs($this->other)->post(route('training.confirm', $round->id), ['confirmation_note' => 'Đã xong'])->assertNotFound();
        $this->flushSession();
        $this->actingAs($this->leader)->post(route('training.confirm', $round->id), ['confirmation_note' => 'Đã xong'])->assertSessionHasErrors('training');
        DB::table('user_lesson_progresses')->where('user_id', $this->employee->id)->update(['progress_percent' => 100, 'is_completed' => true, 'updated_at' => now()]);
        $this->post(route('training.confirm', $round->id), ['confirmation_note' => 'Đã xong'])->assertSessionHasErrors('training');
        DB::table('ket_qua_kiem_tra')->insert(['user_id' => $this->employee->id, 'MaBaiKiemTra' => 1, 'Diem' => 90, 'created_at' => now()]);
        $this->post(route('training.confirm', $round->id), ['confirmation_note' => 'Đã thực hành đủ 3 ca'])->assertSessionHas('success');
        $this->assertStringStartsWith('Đánh giá chưa đạt (điểm tổng', DB::table('yeu_cau_dao_tao_lai')->value('ly_do'));
        $this->assertSame([['type' => 'lesson', 'id' => 1, 'done' => true], ['type' => 'quiz', 'id' => 1, 'done' => true]],
            array_map(fn (array $item) => array_diff_key($item, ['name' => 0]), app(AssessmentEligibility::class)->roundChecklist($round, $this->employee->id)));
        $this->get(route('training.assignments'))->assertOk()->assertSee('Đã thực hành đủ 3 ca');
        $this->flushSession();
        $this->actingAs($this->manager)->put(route('manager.training.assign', $plan), array_replace($assignment, ['ghi_chu_thuc_hien' => 'Đổi nội dung']))->assertSessionHas('success');
        $this->assertDatabaseHas('retraining_rounds', ['id' => $round->id, 'confirmed_at' => null]);
        $this->post(route('manager.training.reassess', $plan), $schedule)->assertSessionHasErrors('training');
        $this->flushSession();
        $this->actingAs($this->leader)->post(route('training.confirm', $round->id), ['confirmation_note' => 'Đã hoàn thành nội dung mới'])->assertSessionHas('success');
        $this->flushSession();
        $this->actingAs($this->manager)->post(route('manager.training.reassess', $plan), $schedule)->assertSessionHas('success');
        $this->assertDatabaseCount('ket_qua_kiem_tra', 2);
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
