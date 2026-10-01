<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

class DemoWorkflowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
        (require database_path('migrations/0001_01_01_000000_create_users_table.php'))->up();
        (require database_path('migrations/2026_09_23_150425_add_employee_fields_to_users_table.php'))->up();
        Schema::table('users', function ($table) {
            $table->string('ChucVu')->nullable();
        });
        Schema::create('chuong_trinh_dao_tao', function ($table) {
            $table->id('MaCTDT');
            $table->string('ChucVuApDung');
        });
        Schema::create('nhan_vien_khoa_hoc', function ($table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('MaCTDT');
            $table->float('TienDo');
            $table->string('TrangThai')->nullable();
            $table->timestamps();
        });
        Schema::create('bai_hoc', function ($table) {
            $table->id('MaBaiHoc');
            $table->integer('FK_MaCTDT');
        });
        Schema::create('user_lesson_progresses', function ($table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('lesson_id');
            $table->integer('progress_percent')->default(0);
            $table->boolean('is_completed')->default(false);
            $table->integer('visit_count')->default(0);
            $table->float('last_watched_seconds')->default(0);
            $table->timestamps();
        });
        (require database_path('migrations/2026_09_22_142623_create_bai_kiem_tra_table.php'))->up();
        Schema::create('cau_hoi_kiem_tra', function ($table) {
            $table->id();
            $table->integer('MaBaiKiemTra');
            $table->string('DapAnDung');
            $table->string('CauHoi')->default('Câu hỏi demo');
            foreach (['A', 'B', 'C', 'D'] as $answer) {
                $table->string('PhuongAn'.$answer)->default($answer);
            }
        });
        Schema::create('ket_qua_kiem_tra', function ($table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('MaBaiKiemTra');
            $table->integer('Diem');
            $table->string('TrangThai');
            $table->integer('SoCauDung');
            $table->integer('TongSoCau');
            $table->timestamps();
        });
        Schema::create('yeu_cau_ho_tro', function ($table) {
            $table->id();
            $table->integer('user_id');
            foreach (['loai_yeu_cau', 'chu_de', 'noi_dung', 'muc_do_uu_tien', 'trang_thai'] as $column) {
                $table->string($column);
            }
            $table->timestamps();
        });
        Schema::create('danh_gia_nang_luc', function ($table) {
            $table->id();
            $table->integer('user_id');
            $table->float('Diem')->nullable();
            $table->string('TrangThai');
            $table->timestamps();
        });
        (require database_path('migrations/2026_10_01_140519_add_demo_settings_and_attempt_tokens.php'))->up();
    }

    public function test_account_settings_persist_for_the_logged_in_user(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $this->actingAs($user)->put(route('account.update-contact'), [
            'secondary_email' => 'demo@example.com', 'phone' => '0901234567',
            'emergency_contact' => 'Người thân', 'emergency_phone' => '0987654321', 'user_id' => $other->id,
        ])->assertRedirect()->assertSessionHas('success');
        $this->assertDatabaseHas('users', ['id' => $user->id, 'secondary_email' => 'demo@example.com']);
        $this->assertNull($other->fresh()->secondary_email);
        $this->put(route('account.update-notifications'), ['notify_progress' => 1])->assertRedirect();
        $this->assertSame(['notify_new_lessons' => false, 'notify_quiz_reminders' => false, 'notify_progress' => true], $user->fresh()->notification_settings);
        $this->put(route('account.update-preferences'), ['theme' => 'dark', 'language' => 'vi'])->assertRedirect();
        $this->get(route('users.edit'))->assertOk()->assertSee('dark');
        $this->assertDatabaseHas('users', ['id' => $user->id, 'theme' => 'dark']);
        $this->put(route('account.update-contact'), ['secondary_email' => 'invalid'])->assertSessionHasErrors(['secondary_email', 'phone']);
    }

    public function test_password_change_requires_current_password_and_keeps_current_session(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->put(route('account.update-password'), [
            'current_password' => 'wrong', 'password' => 'new-password', 'password_confirmation' => 'new-password',
        ])->assertSessionHasErrors('current_password');
        $this->put(route('account.update-password'), [
            'current_password' => 'password', 'password' => 'new-password', 'password_confirmation' => 'new-password',
        ])->assertRedirect()->assertSessionHas('success');
        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
        $this->get(route('users.edit'))->assertOk();
        $this->post(route('account.logout-other-devices'), ['current_password' => 'new-password'])->assertRedirect()->assertSessionHas('success');
        $this->assertAuthenticatedAs($user);
    }

    public function test_help_reads_and_saves_the_logged_in_users_tickets(): void
    {
        $other = User::factory()->create();
        $user = User::factory()->create();
        $this->actingAs($user)->post(route('help.store'), [
            'user_id' => $other->id, 'loai_yeu_cau' => 'Lỗi hiển thị dữ liệu',
            'chu_de' => 'Yêu cầu của tôi', 'noi_dung' => 'Nội dung demo', 'muc_do_uu_tien' => 'TrungBinh',
        ])->assertRedirect()->assertSessionHas('success');
        $this->assertDatabaseHas('yeu_cau_ho_tro', ['user_id' => $user->id, 'chu_de' => 'Yêu cầu của tôi']);
        $this->get(route('help.index', ['user_id' => $other->id]))->assertOk()->assertSee('Yêu cầu của tôi');
        $this->post(route('help.store'), [])->assertSessionHasErrors(['chu_de', 'noi_dung']);
    }

    public function test_logging_out_other_devices_removes_only_this_users_other_sessions(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        config(['session.driver' => 'database']);
        app('session')->forgetDrivers();
        app()->forgetInstance('session.store');
        Auth::forgetGuards();
        $this->actingAs($user)->get(route('users.edit'))->assertOk();
        $currentSessionId = session()->getId();
        $this->withCookie(config('session.cookie'), $currentSessionId);
        DB::table('sessions')->insert([
            ['id' => 'other-device', 'user_id' => $user->id, 'payload' => '', 'last_activity' => time()],
            ['id' => 'another-user', 'user_id' => $other->id, 'payload' => '', 'last_activity' => time()],
        ]);
        $this->post(route('account.logout-other-devices'), ['current_password' => 'password'])->assertRedirect()->assertSessionHas('success');
        $this->assertDatabaseMissing('sessions', ['id' => 'other-device']);
        $this->assertDatabaseHas('sessions', ['id' => 'another-user']);
        $this->assertDatabaseHas('sessions', ['id' => $currentSessionId]);
        $this->get(route('users.edit'))->assertOk();
    }

    public function test_partial_learning_progress_is_identical_after_reload_and_in_leader_summary(): void
    {
        $user = User::factory()->create(['ChucVu' => 'Barista']);
        $leader = User::factory()->create(['ChucVu' => 'Trưởng Ca']);
        DB::table('chuong_trinh_dao_tao')->insert(['MaCTDT' => 1, 'ChucVuApDung' => 'Barista']);
        for ($id = 1; $id <= 4; $id++) {
            DB::table('bai_hoc')->insert(['MaBaiHoc' => $id, 'FK_MaCTDT' => 1]);
        }
        $this->actingAs($user)->postJson(route('courses.updateProgress'), ['lesson_id' => 1, 'current_time' => 95, 'progress_percent' => 95])->assertOk()->assertJsonPath('course_progress', 25);
        $this->postJson(route('courses.updateProgress'), ['lesson_id' => 2, 'current_time' => 50, 'progress_percent' => 50])->assertOk()->assertJsonPath('course_progress', 37.5);
        $this->get(route('courses.index'))->assertOk()->assertSee('37.5%');
        $this->actingAs($leader)->get(route('leader.staffList'))->assertOk()->assertSee('37.5%');
        $this->assertDatabaseHas('nhan_vien_khoa_hoc', ['user_id' => $user->id, 'TienDo' => 37.5]);
    }

    public function test_demo_progress_is_preserved_when_learning_starts(): void
    {
        $user = User::factory()->create(['ChucVu' => 'Barista']);
        DB::table('chuong_trinh_dao_tao')->insert(['MaCTDT' => 1, 'ChucVuApDung' => 'Barista']);
        DB::table('nhan_vien_khoa_hoc')->insert(['user_id' => $user->id, 'MaCTDT' => 1, 'TienDo' => 60, 'demo_progress_baseline' => 60]);
        foreach ([1, 2] as $id) {
            DB::table('bai_hoc')->insert(['MaBaiHoc' => $id, 'FK_MaCTDT' => 1]);
        }
        $this->actingAs($user)->get(route('courses.index'))->assertOk()->assertSee('60%');
        $this->postJson(route('courses.updateProgress'), ['lesson_id' => 1, 'current_time' => 70, 'progress_percent' => 70])->assertOk()->assertJsonPath('course_progress', 65);
        $this->get(route('courses.index'))->assertOk()->assertSee('65%');
        $this->assertDatabaseHas('user_lesson_progresses', ['user_id' => $user->id, 'lesson_id' => 2, 'progress_percent' => 60, 'visit_count' => 0]);
    }

    public function test_retries_keep_history_and_duplicate_submissions_return_original_score(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        DB::table('bai_kiem_tra')->insert(['MaBaiKiemTra' => 1, 'TenBaiKiemTra' => 'Bài demo', 'DiemChuan' => 80]);
        DB::table('cau_hoi_kiem_tra')->insert(['MaBaiKiemTra' => 1, 'DapAnDung' => 'A']);
        $this->actingAs($user);
        $first = $this->get(route('tests.show', 1))->assertOk()->viewData('attemptToken');
        $this->postJson(route('tests.submit', 1), ['attempt_token' => $first, 'answers' => ['A'], 'user_id' => $other->id])->assertOk()->assertJsonPath('score', 100);
        $this->postJson(route('tests.submit', 1), ['attempt_token' => $first, 'answers' => ['B']])->assertOk()->assertJsonPath('score', 100);
        $this->assertDatabaseCount('ket_qua_kiem_tra', 1);
        $second = $this->get(route('tests.show', 1))->viewData('attemptToken');
        $this->postJson(route('tests.submit', 1), ['attempt_token' => $second, 'answers' => ['B']])->assertOk()->assertJsonPath('score', 0);
        $this->assertDatabaseCount('ket_qua_kiem_tra', 2);
        $this->assertDatabaseMissing('ket_qua_kiem_tra', ['user_id' => $other->id]);
        $this->get(route('tests.index'))->assertOk()->assertSee('Lịch sử 2 lượt thi')->assertSee('Điểm lần gần nhất: 0/100');
        DB::table('bai_kiem_tra')->insert(['MaBaiKiemTra' => 2, 'TenBaiKiemTra' => 'Bài tiếp theo', 'DiemChuan' => 80]);
        $this->get(route('tests.index'))->assertOk()->assertDontSee('Cần đạt bài trước để mở');
        $this->postJson(route('tests.submit', 1), ['attempt_token' => (string) Str::uuid(), 'answers' => []])->assertStatus(422);
    }
}
