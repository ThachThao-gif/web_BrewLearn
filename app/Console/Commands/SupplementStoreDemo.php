<?php

namespace App\Console\Commands;

use App\Models\CourseProgress;
use App\Models\QuizProgress;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SupplementStoreDemo extends Command
{
    protected $signature = 'brewlearn:supplement-store-demo';

    protected $description = 'Bổ sung một lần lịch sử cửa hàng, không xóa hoặc thay thế dữ liệu đã demo';

    public function handle(): int
    {
        $manager = User::where('email', 'ngocbich@brewlearn.com')->firstOrFail();
        $key = 'demo-operations-v1-store-'.$manager->cua_hang_id;
        if (DB::table('notifications')->where('event_key', $key)->exists()) {
            $this->notifyCurrentRounds($manager->cua_hang_id);
            $this->info('Dữ liệu đã bổ sung; giữ nguyên mọi thao tác demo mới.');

            return self::SUCCESS;
        }
        $ids = User::where('cua_hang_id', $manager->cua_hang_id)->pluck('id');
        $backup = [];
        foreach (['users', 'nhan_vien_khoa_hoc', 'user_lesson_progresses', 'ket_qua_kiem_tra', 'danh_gia_nang_luc', 'yeu_cau_dao_tao_lai', 'du_doan_nguy_co', 'notifications'] as $table) {
            $backup[$table] = DB::table($table)->whereIn($table === 'users' ? 'id' : 'user_id', $ids)->get();
        }
        $backup['retraining_rounds'] = DB::table('retraining_rounds')->whereIn('request_id', DB::table('yeu_cau_dao_tao_lai')->whereIn('user_id', $ids)->pluck('id'))->get();
        $path = 'demo-backups/operations-'.now()->format('Ymd-His').'-'.Str::random(6).'.json';
        if (! Storage::disk('local')->put($path, json_encode($backup, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR))) {
            $this->error('Không sao lưu được; chưa thay đổi dữ liệu.');

            return self::FAILURE;
        }
        DB::transaction(function () use ($manager, $key) {
            User::whereKey($manager->id)->lockForUpdate()->firstOrFail();
            if (DB::table('notifications')->where('event_key', $key)->exists()) {
                return;
            }
            $names = ['Nguyễn Minh Khang', 'Trần Hải Yến', 'Lê Đức Hưng', 'Phạm Minh Châu', 'Võ Thanh Trúc', 'Đặng Anh Khoa'];
            $scenarios = [[true], [false, true], [false, false, true], [true], [true], [false, false]];
            foreach ($names as $index => $name) {
                $email = 'operations'.($index + 1).'.demo@brewlearn.com';
                if (User::where('email', $email)->exists()) {
                    continue;
                }
                $shift = DB::table('ca_lam_viec')->where('cua_hang_id', $manager->cua_hang_id)->where('ten_ca', $index % 2 ? 'Ca chiều' : 'Ca sáng')->first();
                if (! $shift) {
                    throw new \RuntimeException('Thiếu ca sáng / chiều; không bổ sung.');
                }
                $leader = User::where('cua_hang_id', $manager->cua_hang_id)->where('ca_lam_viec_id', $shift->id)
                    ->whereRaw('LOWER(TRIM(ChucVu)) IN (?, ?)', ['trưởng ca', 'truong ca'])->firstOrFail();
                $person = new User;
                $person->forceFill(['name' => $name, 'email' => $email, 'password' => Hash::make('Demo@2026'),
                    'ChucVu' => ['Barista', 'Thu ngân', 'Phục vụ'][$index % 3], 'cua_hang_id' => $manager->cua_hang_id,
                    'ca_lam_viec_id' => $shift->id, 'employment_status' => 'DangLam', 'joined_at' => now()->subMonths(18)->toDateString(),
                    'CapDo' => 1, 'ChiNhanh' => $manager->ChiNhanh])->save();
                $baseline = now()->startOfMonth()->subMonths(7);
                foreach (app(CourseProgress::class)->assignedCourses($person->ChucVu) as $course) {
                    foreach (DB::table('bai_hoc')->where('FK_MaCTDT', $course->MaCTDT)->get() as $lesson) {
                        DB::table('user_lesson_progresses')->insert(['user_id' => $person->id, 'lesson_id' => $lesson->MaBaiHoc,
                            'progress_percent' => 100, 'is_completed' => true, 'visit_count' => 5, 'last_watched_seconds' => 0,
                            'created_at' => $baseline, 'updated_at' => $baseline]);
                    }
                    app(CourseProgress::class)->sync($person->id, $course->MaCTDT);
                }
                foreach (app(QuizProgress::class)->assignedTests($person->ChucVu) as $test) {
                    $total = DB::table('cau_hoi_kiem_tra')->where('MaBaiKiemTra', $test->MaBaiKiemTra)->count();
                    if (! $total) {
                        throw new \RuntimeException('Quiz không có câu hỏi; không tạo kết quả giả.');
                    }
                    DB::table('ket_qua_kiem_tra')->insert(['user_id' => $person->id, 'MaBaiKiemTra' => $test->MaBaiKiemTra,
                        'Diem' => 100, 'TrangThai' => 'Dat', 'SoCauDung' => $total, 'TongSoCau' => $total,
                        'attempt_token' => (string) Str::uuid(), 'created_at' => $baseline, 'updated_at' => $baseline]);
                }
                $plan = null;
                $number = 0;
                $proposal = json_encode(['source' => 'Trưởng ca đề xuất', 'details' => ['theodoica' => [
                    'behavior' => 'Thực hiện đủ bước kiểm tra và bàn giao ca; phối hợp với đồng đội.',
                    'shift_count' => 5,
                ]]], JSON_UNESCAPED_UNICODE);
                foreach ($scenarios[$index] as $monthIndex => $passed) {
                    $date = now()->startOfMonth()->subMonths(count($scenarios[$index]) - 1 - $monthIndex)->addDay()->setTime(10, 0);
                    $scores = $passed ? [90, 90, 90, 90, 90] : [75, 60, 75, 75, 75];
                    $score = $passed ? 90 : 70.5;
                    $assessment = DB::table('danh_gia_nang_luc')->insertGetId([
                        'user_id' => $person->id, 'NguoiDanhGia_id' => $leader->id, 'TieuChuan' => 'Tiêu chuẩn năng lực '.$person->ChucVu,
                        'NgayHen' => $date, 'NgayDanhGia' => $date, 'Diem' => $score, 'IsPassed' => $passed,
                        'Diem_NghiepVu' => $scores[0], 'Diem_KyNang' => $scores[1], 'Diem_QuyTrinh' => $scores[2],
                        'Diem_PhucVu' => $scores[3], 'Diem_TacPhong' => $scores[4],
                        'NhanXet' => $passed ? 'Dữ liệu minh họa: thực hiện ổn định các tiêu chí công việc.' : 'Dữ liệu minh họa: cần cải thiện thao tác và phối hợp trong ca.',
                        'ChiTietDeXuat' => $passed ? null : $proposal, 'TrangThai' => 'DaDuyet',
                        'nguoi_duyet_id' => $manager->id, 'ngay_duyet' => $date->copy()->addHour(),
                        'created_at' => $date, 'updated_at' => $date->copy()->addHour(),
                    ]);
                    if ($plan) {
                        $number++;
                        $assigned = $date->copy()->subDays(12);
                        DB::table('retraining_rounds')->insert(['request_id' => $plan, 'round_number' => $number,
                            'assigned_by' => $manager->id, 'owner_id' => $leader->id, 'instructions' => 'Hướng dẫn, quan sát 5 ca và kiểm tra bàn giao.',
                            'proposal' => $proposal, 'deadline' => $date->copy()->subDay()->toDateString(), 'assessment_date' => $date->toDateString(),
                            'assessment_id' => $assessment, 'status' => 'DaDuyet', 'result' => $passed ? 'Dat' : 'ChuaDat',
                            'score' => $score, 'approved_by' => $manager->id, 'approved_at' => $date->copy()->addHour(),
                            'required_lessons' => '[]', 'required_quizzes' => '[]', 'learning_baseline' => '{}',
                            'confirmed_by' => $leader->id, 'confirmed_at' => $date->copy()->subDay(),
                            'confirmation_note' => 'Dữ liệu minh họa: đã hoàn thành thực hành / theo dõi.',
                            'created_at' => $assigned, 'updated_at' => $date->copy()->addHour()]);
                        DB::table('yeu_cau_dao_tao_lai')->where('id', $plan)->update(['danh_gia_lai_id' => $passed ? $assessment : null,
                            'trang_thai' => $passed ? 'HoanThanh' : 'DaDuyet', 'tien_trinh' => $passed ? 'HoanTat' : 'ChoGiao',
                            'ket_qua_sau_dao_tao_lai' => $passed ? 'Dat' : 'ChuaDat', 'updated_at' => $date->copy()->addHour()]);
                        if ($passed) {
                            $plan = null;
                        }
                    } elseif (! $passed) {
                        $plan = DB::table('yeu_cau_dao_tao_lai')->insertGetId(['user_id' => $person->id, 'assessment_id' => $assessment,
                            'nguoi_de_xuat_id' => $leader->id, 'nguoi_duyet_id' => $manager->id, 'nguon_de_xuat' => 'Trưởng ca đề xuất',
                            'ly_do' => 'Cải thiện thao tác và phối hợp trong ca (dữ liệu minh họa).', 'ke_hoach' => $proposal,
                            'trang_thai' => 'DaDuyet', 'tien_trinh' => 'ChoGiao', 'ket_qua_sau_dao_tao_lai' => 'ChuaCoKetQua',
                            'ngay_de_xuat' => $date, 'ngay_duyet' => $date->copy()->addHour(), 'created_at' => $date, 'updated_at' => $date]);
                    }
                }
                if ($plan) {
                    $deadline = now()->addDays(5)->toDateString();
                    $reassess = now()->addDays(7)->toDateString();
                    DB::table('retraining_rounds')->insert(['request_id' => $plan, 'round_number' => ++$number,
                        'assigned_by' => $manager->id, 'owner_id' => $leader->id, 'instructions' => 'Quan sát 5 ca, hướng dẫn thao tác và phối hợp bàn giao.',
                        'proposal' => $proposal, 'deadline' => $deadline, 'assessment_date' => $reassess, 'status' => 'DangThucHien',
                        'required_lessons' => '[]', 'required_quizzes' => '[]', 'learning_baseline' => '{}', 'created_at' => now(), 'updated_at' => now()]);
                    DB::table('yeu_cau_dao_tao_lai')->where('id', $plan)->update(['tien_trinh' => 'DangThucHien',
                        'nguoi_phu_trach_id' => $leader->id, 'han_hoan_thanh' => $deadline, 'ngay_danh_gia_lai' => $reassess,
                        'ghi_chu_thuc_hien' => 'Quan sát 5 ca, hướng dẫn thao tác và phối hợp bàn giao.', 'updated_at' => now()]);
                    foreach ([$person->id, $leader->id] as $recipient) {
                        DB::table('notifications')->insert(['user_id' => $recipient, 'title' => 'Đào tạo lại lần '.$number,
                            'message' => 'Quan sát 5 ca, hướng dẫn thao tác và phối hợp bàn giao. Hạn: '.$deadline,
                            'type' => 'system', 'event_key' => 'operations-round-'.$plan.'-'.$number.'-'.$recipient,
                            'created_at' => now(), 'updated_at' => now()]);
                    }
                }
            }
            DB::table('notifications')->insert(['user_id' => $manager->id, 'title' => 'Đã bổ sung dữ liệu vận hành minh họa',
                'message' => 'Lịch sử 6 tháng và các vòng đào tạo lại là dữ liệu minh họa. Mọi thao tác mới trên web tiếp tục lưu vào database.',
                'type' => 'system', 'event_key' => $key, 'created_at' => now(), 'updated_at' => now()]);
        });
        $this->info('Đã bổ sung dữ liệu. Sao lưu: '.Storage::disk('local')->path($path));

        return self::SUCCESS;
    }

    private function notifyCurrentRounds(int $storeId): void
    {
        $rounds = DB::table('retraining_rounds as r')
            ->join('yeu_cau_dao_tao_lai as p', 'p.id', '=', 'r.request_id')
            ->join('users as u', 'u.id', '=', 'p.user_id')
            ->where('u.cua_hang_id', $storeId)->where('u.email', 'like', 'operations%.demo@brewlearn.com')
            ->where('r.status', 'DangThucHien')->where('p.tien_trinh', 'DangThucHien')
            ->select('r.*', 'p.user_id')->get();
        foreach ($rounds as $round) {
            foreach (array_filter([$round->user_id, $round->owner_id]) as $recipient) {
                DB::table('notifications')->insertOrIgnore(['user_id' => $recipient,
                    'title' => 'Đào tạo lại lần '.$round->round_number,
                    'message' => $round->instructions.' Hạn: '.$round->deadline,
                    'type' => 'system', 'event_key' => 'operations-round-'.$round->request_id.'-'.$round->round_number.'-'.$recipient,
                    'created_at' => now(), 'updated_at' => now()]);
            }
        }
    }
}
