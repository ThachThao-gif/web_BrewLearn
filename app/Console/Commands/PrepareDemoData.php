<?php

namespace App\Console\Commands;

use App\Models\CourseProgress;
use App\Models\QuizProgress;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PrepareDemoData extends Command
{
    protected $signature = 'brewlearn:prepare-demo';

    protected $description = 'Sao lưu và chuẩn hóa dữ liệu minh họa hai ca của cửa hàng Flagship';

    public function handle(): int
    {
        $manager = User::where('email', 'ngocbich@brewlearn.com')->firstOrFail();
        $storeId = $manager->cua_hang_id;
        $ids = User::where('cua_hang_id', $storeId)->pluck('id');
        $tables = ['users', 'nhan_vien_khoa_hoc', 'user_lesson_progresses', 'ket_qua_kiem_tra', 'danh_gia_nang_luc', 'yeu_cau_dao_tao_lai', 'du_doan_nguy_co', 'notifications'];
        $backup = ['created_at' => now()->toIso8601String(), 'store_id' => $storeId];
        foreach ($tables as $table) {
            $backup[$table] = DB::table($table)->whereIn($table === 'users' ? 'id' : 'user_id', $ids)->get();
        }
        $backup['ca_lam_viec'] = DB::table('ca_lam_viec')->where('cua_hang_id', $storeId)->get();
        $backup['retraining_rounds'] = DB::table('retraining_rounds')->whereIn('request_id', DB::table('yeu_cau_dao_tao_lai')->whereIn('user_id', $ids)->pluck('id'))->get();
        $backupPath = 'demo-backups/'.now()->format('Ymd-His').'-'.Str::random(6).'.json';
        if (! Storage::disk('local')->put($backupPath, json_encode($backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR))) {
            $this->error('Không tạo được bản sao lưu; không thay đổi database.');

            return self::FAILURE;
        }
        $today = Carbon::today('Asia/Ho_Chi_Minh');
        $rows = [
            ['minhthu@brewlearn.com', 'Trần Minh Thư', 'Trưởng ca', 'Ca sáng', null, 'DangLam', 85],
            ['vanthanh@brewlearn.com', 'Võ Văn Thành', 'Trưởng ca', 'Ca chiều', null, 'DangLam', 80],
            ['vannam@brewlearn.com', 'Nguyễn Văn Nam', 'Thu ngân', 'Ca sáng', 3, 'DangLam', 85],
            ['hoanganh@brewlearn.com', 'Lê Hoàng Anh', 'Barista', 'Ca sáng', 0, 'DangLam', 65],
            ['thimai@brewlearn.com', 'Phạm Thị Mai', 'Phục vụ', 'Ca chiều', -3, 'DangLam', 45],
            ['hoanglong@brewlearn.com', 'Nguyễn Hoàng Long', 'Barista', 'Ca chiều', 18, 'DangLam', 92],
            ['thaovan@brewlearn.com', 'Bùi Thảo Vân', 'Thực Tập Sinh', 'Ca chiều', 70, 'DangLam', 20],
            ['lan.demo@brewlearn.com', 'Nguyễn Ngọc Lan', 'Thu ngân', 'Ca sáng', 31, 'DangLam', 75],
            ['tuan.demo@brewlearn.com', 'Lê Quốc Tuấn', 'Barista', 'Ca sáng', -10, 'DangLam', 35],
            ['huy.demo@brewlearn.com', 'Trần Gia Huy', 'Phục vụ', 'Ca chiều', 6, 'DangLam', 70],
            ['ha.demo@brewlearn.com', 'Phạm Thu Hà', 'Thu ngân', 'Ca sáng', -30, 'TamNgung', 60],
            ['dung.demo@brewlearn.com', 'Đỗ Anh Dũng', 'Phục vụ', 'Ca chiều', -30, 'NghiViec', 55],
        ];
        DB::transaction(function () use ($storeId, $rows, $today, $manager): void {
            $shiftIds = [];
            foreach (['Ca sáng' => ['07:00:00', '14:00:00'], 'Ca chiều' => ['14:00:00', '22:00:00']] as $name => [$start, $end]) {
                DB::table('ca_lam_viec')->updateOrInsert(['cua_hang_id' => $storeId, 'ten_ca' => $name], ['bat_dau' => $start, 'ket_thuc' => $end, 'updated_at' => now()]);
                $shiftIds[$name] = DB::table('ca_lam_viec')->where('cua_hang_id', $storeId)->where('ten_ca', $name)->value('id');
            }
            foreach ($rows as [$email, $name, $position, $shift, $dueOffset, $status, $progress]) {
                $user = User::firstOrNew(['email' => $email]);
                if (! $user->exists) {
                    $user->password = Hash::make('Demo@2026');
                }
                // Calendar dates are deliberately relative to the day this command is run.
                $joined = $dueOffset === null ? $today->copy()->subYear() : $today->copy()->addDays($dueOffset)->subMonthsNoOverflow(3);
                $user->forceFill(['name' => $name, 'ChucVu' => $position, 'cua_hang_id' => $storeId,
                    'ca_lam_viec_id' => $shiftIds[$shift], 'joined_at' => $joined->toDateString(),
                    'employment_status' => $status, 'ChiNhanh' => $manager->ChiNhanh, 'CapDo' => $position === 'Trưởng ca' ? 2 : 1])->save();
            }
            $ids = User::where('cua_hang_id', $storeId)->pluck('id');
            DB::table('retraining_rounds')->whereIn('request_id', DB::table('yeu_cau_dao_tao_lai')->whereIn('user_id', $ids)->pluck('id'))->delete();
            // Remove test workflow records before replacing the learning examples.
            foreach (['yeu_cau_dao_tao_lai', 'danh_gia_nang_luc', 'notifications', 'du_doan_nguy_co', 'ket_qua_kiem_tra', 'user_lesson_progresses', 'nhan_vien_khoa_hoc'] as $table) {
                DB::table($table)->whereIn('user_id', $ids)->delete();
            }
            $profiles = collect($rows)->keyBy(fn ($row) => $row[0]);
            foreach (User::whereIn('id', $ids)->get() as $user) {
                $target = $profiles->get($user->email)[6] ?? 90;
                $courses = app(CourseProgress::class)->assignedCourses($user->ChucVu ?? '');
                foreach ($courses as $course) {
                    DB::table('nhan_vien_khoa_hoc')->insert(['user_id' => $user->id, 'MaCTDT' => $course->MaCTDT,
                        'TienDo' => 0, 'demo_progress_baseline' => 0, 'TrangThai' => 'DangHoc',
                        'han_hoan_thanh' => $today->copy()->addMonth()->toDateString(), 'created_at' => now(), 'updated_at' => now()]);
                    $lessons = DB::table('bai_hoc')->where('FK_MaCTDT', $course->MaCTDT)->orderBy('ThuTu')->get();
                    $remaining = $target * $lessons->count();
                    foreach ($lessons as $lesson) {
                        $percent = min(100, max(0, $remaining));
                        $remaining -= $percent;
                        $completed = $percent >= 95;
                        DB::table('user_lesson_progresses')->insert(['user_id' => $user->id, 'lesson_id' => $lesson->MaBaiHoc,
                            'progress_percent' => $percent, 'is_completed' => $completed,
                            'visit_count' => $percent > 0 ? ($target >= 70 ? 4 : 1) : 0,
                            'last_watched_seconds' => 0, 'created_at' => now()->subDays(2), 'updated_at' => now()->subDay()]);
                    }
                    app(CourseProgress::class)->sync($user->id, $course->MaCTDT);
                }
                // The new intern has learning activity but no quiz yet: insufficient model data.
                if ($user->email === 'thaovan@brewlearn.com') {
                    continue;
                }
                $tests = app(QuizProgress::class)->assignedTests($user->ChucVu ?? '')->take(3);
                foreach ($tests as $index => $test) {
                    $total = DB::table('cau_hoi_kiem_tra')->where('MaBaiKiemTra', $test->MaBaiKiemTra)->count();
                    if (! $total) {
                        continue;
                    }
                    $correct = (int) round($total * min(100, $target + ($index * 5)) / 100);
                    $score = round($correct * 100 / $total, 1);
                    $date = $today->copy()->subDays(4 - $index)->setTime(10 + $index, 0);
                    DB::table('ket_qua_kiem_tra')->insert(['user_id' => $user->id, 'MaBaiKiemTra' => $test->MaBaiKiemTra,
                        'Diem' => $score, 'TrangThai' => $score >= $test->DiemChuan ? 'Dat' : 'KhongDat',
                        'SoCauDung' => $correct, 'TongSoCau' => $total, 'attempt_token' => (string) Str::uuid(),
                        'created_at' => $date, 'updated_at' => $date]);
                }
            }
        });
        $this->info('Đã chuẩn hóa dữ liệu hai ca. Sao lưu: '.Storage::disk('local')->path($backupPath));
        $this->line('Tài khoản mới: lan.demo / tuan.demo / huy.demo / ha.demo / dung.demo @brewlearn.com — mật khẩu Demo@2026.');
        $this->line('Mật khẩu tài khoản có sẵn được giữ nguyên. Chạy model sau khi chuẩn hóa.');

        return self::SUCCESS;
    }
}
