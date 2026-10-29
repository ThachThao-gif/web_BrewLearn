<?php

namespace App\Console\Commands;

use App\Models\CourseProgress;
use App\Models\QuizProgress;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Signature('brewlearn:enrich-demo')]
#[Description('Bổ sung một lần dữ liệu minh họa: đủ câu hỏi, đủ trạng thái quy trình và nhân sự cửa hàng thứ hai')]
class EnrichDemoData extends Command
{
    private const MARKER = 'demo-enrich-v1';

    private const MIN_QUESTIONS = 5;

    private Carbon $today;

    private int $managerId;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $manager = User::where('email', 'ngocbich@brewlearn.com')->firstOrFail();
        if (DB::table('notifications')->where('event_key', self::MARKER)->exists()) {
            $this->info('Dữ liệu minh họa đã được bổ sung trước đó; không thay đổi gì.');

            return self::SUCCESS;
        }
        $backup = ['created_at' => now()->toIso8601String()];
        foreach (['users', 'ca_lam_viec', 'bai_kiem_tra', 'cau_hoi_kiem_tra', 'nhan_vien_khoa_hoc', 'user_lesson_progresses', 'ket_qua_kiem_tra',
            'danh_gia_nang_luc', 'yeu_cau_dao_tao_lai', 'retraining_rounds', 'notifications'] as $table) {
            $backup[$table] = DB::table($table)->get();
        }
        $path = 'demo-backups/enrich-'.now()->format('Ymd-His').'-'.Str::random(6).'.json';
        if (! Storage::disk('local')->put($path, json_encode($backup, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR))) {
            $this->error('Không sao lưu được; chưa thay đổi dữ liệu.');

            return self::FAILURE;
        }
        $this->today = Carbon::today('Asia/Ho_Chi_Minh');
        $this->managerId = $manager->id;

        DB::transaction(function () use ($manager): void {
            $this->fixQuizNames();
            $this->topUpQuestions();
            $this->convertIntern();
            $this->scheduleAssessment('operations1.demo@brewlearn.com');
            $this->pendingApproval('operations5.demo@brewlearn.com');
            $this->returnedAssessment('operations4.demo@brewlearn.com');
            $this->awaitingReassessment('operations6.demo@brewlearn.com');
            $this->stoppedRetraining($manager);
            $this->recentFailedAttempt('huy.demo@brewlearn.com');
            $this->secondStore();
            DB::table('notifications')->insert(['user_id' => $manager->id, 'title' => 'Cập nhật dữ liệu vận hành',
                'message' => 'Đã bổ sung ngân hàng câu hỏi, các hồ sơ đang xử lý và nhân sự cửa hàng Bùi Viện.',
                'type' => 'system', 'is_read' => 1, 'event_key' => self::MARKER, 'created_at' => now(), 'updated_at' => now()]);
        });

        $this->info('Đã bổ sung dữ liệu minh họa. Sao lưu: '.Storage::disk('local')->path($path));
        $this->line('Chạy tiếp: php artisan brewlearn:calculate-risk để cập nhật cảnh báo.');
        $this->line('Tài khoản mới dùng mật khẩu Demo@2026: quocbao.demo, thanhhang.bv, minhduc.bv, vanloc.bv, thithu.bv, phuonglinh.bv, minhtri.bv @brewlearn.com');

        return self::SUCCESS;
    }

    private function fixQuizNames(): void
    {
        $names = [
            3 => 'Sát Hạch Vệ Sinh An Toàn Thực Phẩm & Quy Trình Đóng Ca',
            15 => 'Sát Hạch Kỹ Năng Điều Phối Nhân Sự Giờ Cao Điểm',
            16 => 'Sát Hạch Đánh Giá Hiệu Suất Làm Việc & Phản Hồi Nhân Viên 1-1',
        ];
        foreach ($names as $id => $name) {
            DB::table('bai_kiem_tra')->where('MaBaiKiemTra', $id)->update(['TenBaiKiemTra' => $name, 'updated_at' => now()]);
        }
    }

    private function topUpQuestions(): void
    {
        foreach ($this->questionBank() as $quizId => $questions) {
            $missing = self::MIN_QUESTIONS - DB::table('cau_hoi_kiem_tra')->where('MaBaiKiemTra', $quizId)->count();
            foreach (array_slice($questions, 0, max(0, $missing)) as $index => [$question, $options, $correct, $explain]) {
                // Rotate the options so the correct answer is spread over A–D instead of always B.
                $shift = ($quizId + $index) % 4;
                $rotated = array_merge(array_slice($options, $shift), array_slice($options, 0, $shift));
                $letter = 'ABCD'[array_search($options[$correct], $rotated, true)];
                DB::table('cau_hoi_kiem_tra')->insert(['MaBaiKiemTra' => $quizId, 'CauHoi' => $question,
                    'PhuongAnA' => $rotated[0], 'PhuongAnB' => $rotated[1], 'PhuongAnC' => $rotated[2], 'PhuongAnD' => $rotated[3],
                    'DapAnDung' => $letter, 'GiaiThich' => $explain]);
            }
        }
    }

    private function convertIntern(): void
    {
        $intern = User::where('email', 'thaovan@brewlearn.com')->first();
        if (! $intern) {
            return;
        }
        $intern->forceFill(['ChucVu' => 'Phục vụ', 'joined_at' => $this->today->copy()->subWeeks(3)->toDateString()])->save();
        $this->learn($intern, 30, $this->today->copy()->subDays(3));
    }

    private function scheduleAssessment(string $email): void
    {
        $person = User::where('email', $email)->firstOrFail();
        $at = $this->today->copy()->addDays(2)->setTime(10, 0);
        DB::table('danh_gia_nang_luc')->insert(['user_id' => $person->id, 'NguoiDanhGia_id' => $this->leaderOf($person),
            'TieuChuan' => 'Tiêu chuẩn năng lực '.$person->ChucVu, 'NgayHen' => $at, 'TrangThai' => 'ChoDanhGia',
            'created_at' => now()->subDay(), 'updated_at' => now()->subDay()]);
        $this->notify($person->id, 'Lịch sát hạch năng lực mới từ Trưởng ca',
            "Bạn có lịch sát hạch 'Tiêu chuẩn năng lực {$person->ChucVu}' vào lúc ".$at->format('H:i d/m/Y').'.', 'schedule', 'demo-schedule-'.$person->id);
    }

    private function pendingApproval(string $email): void
    {
        $person = User::where('email', $email)->firstOrFail();
        $at = $this->today->copy()->subDay()->setTime(15, 0);
        $id = $this->assessment($person, $this->leaderOf($person), 'Tiêu chuẩn năng lực '.$person->ChucVu, $at, [88, 85, 90, 86, 92],
            'Thao tác POS nhanh, đối chiếu ghi chú đơn chính xác; giao tiếp lịch sự trong giờ cao điểm.', 'ChoPheDuyet');
        $this->notify($this->managerId, 'Đánh giá chờ phê duyệt', 'Trưởng ca đã gửi đánh giá năng lực của nhân viên '.$person->name.'. Vui lòng xem tại mục Duyệt đánh giá.', 'training', 'demo-pending-'.$id);
    }

    private function returnedAssessment(string $email): void
    {
        $person = User::where('email', $email)->firstOrFail();
        $leader = $this->leaderOf($person);
        $at = $this->today->copy()->subDays(2)->setTime(9, 30);
        $id = $this->assessment($person, $leader, 'Tiêu chuẩn năng lực '.$person->ChucVu, $at, [80, 65, 75, 80, 85],
            'Thao tác đánh sữa chưa ổn định trong giờ cao điểm.', 'TraLai',
            'Nhận xét chưa nêu cụ thể lỗi khi đánh sữa; bổ sung số ca theo dõi và mức nhiệt sữa cần đạt.');
        $this->notify($leader, 'Đánh giá cần bổ sung', 'Quản lý trả lại đánh giá của nhân viên '.$person->name.
            '. Vui lòng bổ sung: Nhận xét chưa nêu cụ thể lỗi khi đánh sữa; bổ sung số ca theo dõi và mức nhiệt sữa cần đạt.', 'training', 'demo-return-'.$id);
    }

    private function awaitingReassessment(string $email): void
    {
        $person = User::where('email', $email)->firstOrFail();
        $plan = DB::table('yeu_cau_dao_tao_lai')->where('user_id', $person->id)->where('tien_trinh', 'DangThucHien')->first();
        $round = $plan ? DB::table('retraining_rounds')->where('request_id', $plan->id)->where('status', 'DangThucHien')->orderByDesc('round_number')->first() : null;
        if (! $round) {
            return;
        }
        $leader = $round->owner_id ?: $this->leaderOf($person);
        $at = $this->today->copy()->addDay()->setTime(9, 30);
        $assessmentId = DB::table('danh_gia_nang_luc')->insertGetId(['user_id' => $person->id, 'NguoiDanhGia_id' => $leader,
            'TieuChuan' => 'Đánh giá lại lần '.$round->round_number.': quy trình bus table và phối hợp bàn giao ca',
            'NgayHen' => $at, 'TrangThai' => 'ChoDanhGia', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('retraining_rounds')->where('id', $round->id)->update(['confirmed_by' => $leader, 'confirmed_at' => now()->subDay(),
            'confirmation_note' => 'Đã theo dõi đủ 5 ca, nhân viên thực hiện đúng quy trình dọn bàn trong 60 giây.',
            'assessment_id' => $assessmentId, 'assessment_date' => $at->toDateString(), 'status' => 'ChoDanhGiaLai', 'updated_at' => now()]);
        DB::table('yeu_cau_dao_tao_lai')->where('id', $plan->id)->update(['danh_gia_lai_id' => $assessmentId,
            'ngay_danh_gia_lai' => $at->toDateString(), 'tien_trinh' => 'ChoDanhGiaLai', 'updated_at' => now()]);
        foreach ([$person->id, $leader] as $recipient) {
            $this->notify($recipient, 'Lịch đánh giá lại lần '.$round->round_number, 'Đánh giá lại ngày '.$at->format('d/m/Y').' lúc '.$at->format('H:i').'.',
                'training', 'demo-reassess-'.$assessmentId.'-'.$recipient);
        }
    }

    private function stoppedRetraining(User $manager): void
    {
        $person = $this->createStaff('quocbao.demo@brewlearn.com', 'Đặng Quốc Bảo', 'Barista', $manager->cua_hang_id, 'Ca sáng', '2026-05-12');
        $this->learn($person, 100, Carbon::parse('2026-07-20'));
        $this->takeTests($person, [100, 100, 90, 100, 100, 100], Carbon::parse('2026-08-05 10:00'));
        $leader = $this->leaderOf($person);
        $proposal = json_encode(['source' => 'Trưởng ca đề xuất', 'details' => ['huongdan' => [
            'content' => 'Kèm cặp thao tác chiết xuất espresso và đánh sữa microfoam theo định lượng chuẩn.', 'shift_count' => 5,
        ]]], JSON_UNESCAPED_UNICODE);
        $timeline = [
            ['2026-08-20 10:00', [78, 62, 72, 80, 82], 'Kiến thức lý thuyết tốt nhưng thao tác chiết xuất chưa đạt định lượng, sữa đánh còn bọt lớn.'],
            ['2026-09-05 10:00', [80, 66, 74, 82, 84], 'Có cải thiện tốc độ, kỹ năng đánh sữa vẫn chưa ổn định.'],
            ['2026-09-20 10:00', [80, 68, 76, 80, 85], 'Thao tác ổn hơn khi vắng khách, giờ cao điểm vẫn sai định lượng.'],
            ['2026-10-08 10:00', [82, 67, 78, 82, 85], 'Sau ba vòng đào tạo lại, kỹ năng pha chế vẫn dưới mức yêu cầu.'],
        ];
        $plan = null;
        foreach ($timeline as $round => [$date, $scores, $comment]) {
            $at = Carbon::parse($date);
            $criteria = $round === 0 ? 'Tiêu chuẩn năng lực Barista' : 'Đánh giá lại lần '.$round.': kỹ thuật espresso và microfoam';
            $assessment = $this->assessment($person, $leader, $criteria, $at, $scores, $comment, 'DaDuyet', null, $proposal);
            if ($round === 0) {
                $plan = DB::table('yeu_cau_dao_tao_lai')->insertGetId(['user_id' => $person->id, 'assessment_id' => $assessment,
                    'nguoi_de_xuat_id' => $leader, 'nguoi_duyet_id' => $manager->id, 'nguon_de_xuat' => 'Trưởng ca đề xuất',
                    'ly_do' => 'Đánh giá chưa đạt: '.$comment, 'ke_hoach' => $proposal, 'trang_thai' => 'DaDuyet', 'tien_trinh' => 'ChoGiao',
                    'ket_qua_sau_dao_tao_lai' => 'ChuaCoKetQua', 'ngay_de_xuat' => $at, 'ngay_duyet' => $at->copy()->addHour(),
                    'created_at' => $at, 'updated_at' => $at->copy()->addHour()]);

                continue;
            }
            DB::table('retraining_rounds')->insert(['request_id' => $plan, 'round_number' => $round, 'assigned_by' => $manager->id,
                'owner_id' => $leader, 'instructions' => 'Kèm 5 ca: chiết xuất espresso 18g/36g trong 25–30 giây, đánh sữa 55–65°C.',
                'proposal' => $proposal, 'deadline' => $at->copy()->subDays(2)->toDateString(), 'assessment_date' => $at->toDateString(),
                'assessment_id' => $assessment, 'status' => 'DaDuyet', 'result' => 'ChuaDat', 'score' => $this->total($scores),
                'approved_by' => $manager->id, 'approved_at' => $at->copy()->addHour(), 'required_lessons' => '[]', 'required_quizzes' => '[]',
                'learning_baseline' => '{}', 'confirmed_by' => $leader, 'confirmed_at' => $at->copy()->subDay(),
                'confirmation_note' => 'Đã kèm đủ 5 ca theo kế hoạch.', 'created_at' => $at->copy()->subDays(14), 'updated_at' => $at->copy()->addHour()]);
        }
        $last = Carbon::parse($timeline[3][0]);
        DB::table('yeu_cau_dao_tao_lai')->where('id', $plan)->update(['tien_trinh' => 'DungDaoTao', 'ket_qua_sau_dao_tao_lai' => 'ChuaDat',
            'danh_gia_lai_id' => DB::table('retraining_rounds')->where('request_id', $plan)->where('round_number', 3)->value('assessment_id'),
            'nguoi_phu_trach_id' => $leader, 'updated_at' => $last->copy()->addHour()]);
        $this->notify($manager->id, 'Đào tạo lại đã đạt giới hạn', 'Nhân viên '.$person->name.' vẫn chưa đạt sau 3 vòng đào tạo lại'.
            '. Đã dừng đào tạo lại; quản lý cần xem xét hướng xử lý.', 'training', 'training-limit-'.$plan, $last->copy()->addHour());
    }

    private function recentFailedAttempt(string $email): void
    {
        $person = User::where('email', $email)->firstOrFail();
        $test = app(QuizProgress::class)->assignedTests($person->ChucVu)
            ->first(fn ($quiz) => DB::table('ket_qua_kiem_tra')->where('user_id', $person->id)->where('MaBaiKiemTra', $quiz->MaBaiKiemTra)->exists());
        if ($test) {
            $this->attempt($person, $test, 40, now()->subDay());
        }
    }

    private function secondStore(): void
    {
        $store = DB::table('cua_hang')->where('ma_cua_hang', 'CH-Q1-02')->first();
        if (! $store) {
            return;
        }
        foreach (['Ca sáng' => ['07:00:00', '14:00:00'], 'Ca chiều' => ['14:00:00', '22:00:00']] as $name => [$start, $end]) {
            DB::table('ca_lam_viec')->updateOrInsert(['cua_hang_id' => $store->id, 'ten_ca' => $name], ['bat_dau' => $start, 'ket_thuc' => $end, 'updated_at' => now()]);
        }
        $this->createStaff('thanhhang.bv@brewlearn.com', 'Lý Thanh Hằng', 'Quản lý cửa hàng', $store->id, null, '2025-11-03');
        $this->createStaff('minhduc.bv@brewlearn.com', 'Phan Minh Đức', 'Trưởng ca', $store->id, 'Ca sáng', '2025-12-01');
        foreach ([
            ['vanloc.bv@brewlearn.com', 'Trịnh Văn Lộc', 'Barista', '2026-04-06', 100, [95, 90, 100]],
            ['thithu.bv@brewlearn.com', 'Ngô Thị Thu', 'Thu ngân', '2026-05-18', 80, [85, 90, 70]],
            ['phuonglinh.bv@brewlearn.com', 'Mai Phương Linh', 'Phục vụ', '2026-06-22', 55, [60, 70, 55]],
            ['minhtri.bv@brewlearn.com', 'Châu Minh Trí', 'Barista', '2026-08-10', 90, [80, 85, 90]],
        ] as [$email, $name, $position, $joined, $progress, $scores]) {
            $person = $this->createStaff($email, $name, $position, $store->id, 'Ca sáng', $joined);
            $this->learn($person, $progress, $this->today->copy()->subDays(5));
            $this->takeTests($person, $scores, $this->today->copy()->subDays(6)->setTime(9, 0));
        }
        $this->secondStoreHistory($store->id);
    }

    /**
     * Approved history and an overdue schedule so the second store looks like it has been operating for months.
     */
    private function secondStoreHistory(int $storeId): void
    {
        $manager = User::where('email', 'thanhhang.bv@brewlearn.com')->firstOrFail();
        $managerId = $this->managerId;
        $this->managerId = $manager->id;
        $leader = User::where('email', 'minhduc.bv@brewlearn.com')->value('id');
        foreach ([
            ['vanloc.bv@brewlearn.com', '2026-07-20 10:00', [90, 88, 92, 86, 90], 'Chiết xuất ổn định, đúng định lượng; phối hợp tốt giờ cao điểm.'],
            ['thithu.bv@brewlearn.com', '2026-08-25 15:00', [85, 82, 88, 90, 86], 'Thao tác POS chính xác, bàn giao két đúng quy trình.'],
        ] as [$email, $date, $scores, $comment]) {
            $person = User::where('email', $email)->firstOrFail();
            $this->assessment($person, $leader, 'Tiêu chuẩn năng lực '.$person->ChucVu, Carbon::parse($date), $scores, $comment, 'DaDuyet');
        }
        $linh = User::where('email', 'phuonglinh.bv@brewlearn.com')->firstOrFail();
        $at = $this->today->copy()->addDays(3)->setTime(14, 30);
        DB::table('danh_gia_nang_luc')->insert(['user_id' => $linh->id, 'NguoiDanhGia_id' => $leader, 'TieuChuan' => 'Tiêu chuẩn năng lực Phục vụ',
            'NgayHen' => $at, 'TrangThai' => 'ChoDanhGia', 'created_at' => now(), 'updated_at' => now()]);
        $this->notify($linh->id, 'Lịch sát hạch năng lực mới từ Trưởng ca',
            "Bạn có lịch sát hạch 'Tiêu chuẩn năng lực Phục vụ' vào lúc ".$at->format('H:i d/m/Y').'.', 'schedule', 'demo-schedule-'.$linh->id);
        $this->managerId = $managerId;
    }

    private function createStaff(string $email, string $name, string $position, int $storeId, ?string $shift, string $joined): User
    {
        $shiftId = $shift ? DB::table('ca_lam_viec')->where('cua_hang_id', $storeId)->where('ten_ca', $shift)->value('id') : null;
        $user = User::firstOrNew(['email' => $email]);
        if (! $user->exists) {
            $user->password = Hash::make('Demo@2026');
        }
        $user->forceFill(['name' => $name, 'ChucVu' => $position, 'cua_hang_id' => $storeId, 'ca_lam_viec_id' => $shiftId,
            'joined_at' => $joined, 'employment_status' => 'DangLam', 'CapDo' => $position === 'Trưởng ca' ? 2 : 1,
            'ChiNhanh' => DB::table('cua_hang')->where('id', $storeId)->value('ten_cua_hang')])->save();

        return $user;
    }

    /**
     * Give a user lesson progress so that each assigned course reaches roughly $target percent.
     */
    private function learn(User $person, int $target, Carbon $at): void
    {
        foreach (app(CourseProgress::class)->assignedCourses($person->ChucVu) as $course) {
            if (! DB::table('nhan_vien_khoa_hoc')->where('user_id', $person->id)->where('MaCTDT', $course->MaCTDT)->exists()) {
                DB::table('nhan_vien_khoa_hoc')->insert(['user_id' => $person->id, 'MaCTDT' => $course->MaCTDT, 'TienDo' => 0,
                    'demo_progress_baseline' => 0, 'TrangThai' => 'DangHoc', 'han_hoan_thanh' => $this->today->copy()->addMonth()->toDateString(),
                    'created_at' => $at, 'updated_at' => $at]);
            }
            $lessons = DB::table('bai_hoc')->where('FK_MaCTDT', $course->MaCTDT)->orderBy('ThuTu')->get();
            $remaining = $target * $lessons->count();
            foreach ($lessons as $lesson) {
                $percent = min(100, max(0, $remaining));
                $remaining -= $percent;
                DB::table('user_lesson_progresses')->updateOrInsert(['user_id' => $person->id, 'lesson_id' => $lesson->MaBaiHoc], [
                    'progress_percent' => $percent, 'is_completed' => $percent >= 95, 'visit_count' => $percent > 0 ? 3 : 0,
                    'last_watched_seconds' => 0, 'created_at' => $at, 'updated_at' => $at]);
            }
            app(CourseProgress::class)->sync($person->id, $course->MaCTDT);
        }
    }

    /**
     * @param  array<int, int>  $scores  one score per assigned test, in order
     */
    private function takeTests(User $person, array $scores, Carbon $at): void
    {
        foreach (app(QuizProgress::class)->assignedTests($person->ChucVu)->values() as $index => $test) {
            if (! isset($scores[$index])) {
                break;
            }
            $this->attempt($person, $test, $scores[$index], $at->copy()->addHours($index));
        }
    }

    private function attempt(User $person, object $test, int $target, Carbon $at): void
    {
        $total = DB::table('cau_hoi_kiem_tra')->where('MaBaiKiemTra', $test->MaBaiKiemTra)->count();
        $correct = (int) round($total * $target / 100);
        $score = round($correct * 100 / $total, 1);
        DB::table('ket_qua_kiem_tra')->insert(['user_id' => $person->id, 'MaBaiKiemTra' => $test->MaBaiKiemTra, 'Diem' => $score,
            'TrangThai' => $score >= ($test->DiemChuan ?? 80) ? 'Dat' : 'KhongDat', 'SoCauDung' => $correct, 'TongSoCau' => $total,
            'attempt_token' => (string) Str::uuid(), 'created_at' => $at, 'updated_at' => $at]);
    }

    /**
     * @param  array<int, int>  $scores  Nghiệp vụ, Kỹ năng, Quy trình, Phục vụ, Tác phong
     */
    private function assessment(User $person, int $leader, string $criteria, Carbon $at, array $scores, string $comment, string $status, ?string $returnReason = null, ?string $proposal = null): int
    {
        $total = $this->total($scores);
        $passed = $total >= 80 && min($scores) >= 70;
        $proposal ??= $passed ? null : json_encode(['source' => 'Trưởng ca đề xuất', 'details' => ['huongdan' => [
            'content' => 'Kèm cặp thao tác còn yếu trong 5 ca liên tiếp.', 'shift_count' => 5,
        ]]], JSON_UNESCAPED_UNICODE);
        $reviewed = in_array($status, ['DaDuyet', 'TraLai'], true);

        return DB::table('danh_gia_nang_luc')->insertGetId(['user_id' => $person->id, 'NguoiDanhGia_id' => $leader, 'TieuChuan' => $criteria,
            'NgayHen' => $at, 'NgayDanhGia' => $at, 'Diem' => $total, 'IsPassed' => $passed,
            'Diem_NghiepVu' => $scores[0], 'Diem_KyNang' => $scores[1], 'Diem_QuyTrinh' => $scores[2], 'Diem_PhucVu' => $scores[3], 'Diem_TacPhong' => $scores[4],
            'ChiTietDiem' => json_encode(array_combine(['nghiep_vu', 'ky_nang', 'quy_trinh', 'phuc_vu', 'tac_phong'], $scores)),
            'NhanXet' => $comment, 'ChiTietDeXuat' => $passed ? null : $proposal, 'TrangThai' => $status,
            'nguoi_duyet_id' => $reviewed ? $this->managerId : null, 'ngay_duyet' => $reviewed ? $at->copy()->addHour() : null,
            'ly_do_tra_lai' => $returnReason, 'created_at' => $at->copy()->subDays(3), 'updated_at' => $at->copy()->addHour()]);
    }

    /**
     * @param  array<int, int>  $scores
     */
    private function total(array $scores): float
    {
        return round($scores[0] * 0.20 + $scores[1] * 0.30 + $scores[2] * 0.20 + $scores[3] * 0.15 + $scores[4] * 0.15, 1);
    }

    private function leaderOf(User $person): int
    {
        return User::where('cua_hang_id', $person->cua_hang_id)->where('ca_lam_viec_id', $person->ca_lam_viec_id)
            ->whereRaw('LOWER(TRIM(ChucVu)) IN (?, ?)', ['trưởng ca', 'truong ca'])->value('id')
            ?? User::where('cua_hang_id', $person->cua_hang_id)->whereRaw('LOWER(TRIM(ChucVu)) IN (?, ?)', ['trưởng ca', 'truong ca'])->value('id');
    }

    private function notify(int $userId, string $title, string $message, string $type, string $key, ?Carbon $at = null): void
    {
        $at ??= now();
        DB::table('notifications')->insertOrIgnore(['user_id' => $userId, 'title' => $title, 'message' => $message, 'type' => $type,
            'is_read' => 0, 'event_key' => $key, 'created_at' => $at, 'updated_at' => $at]);
    }

    /**
     * Additional questions per quiz: [question, [4 options], index of the correct option, explanation].
     *
     * @return array<int, array<int, array{0: string, 1: array<int, string>, 2: int, 3: string}>>
     */
    private function questionBank(): array
    {
        return [
            4 => [
                ['Nhiệt độ bảo quản sữa tươi trong tủ mát quầy bar cần duy trì ở mức nào?', ['Dưới 0°C', 'Từ 0 đến 5°C', 'Từ 8 đến 12°C', 'Nhiệt độ phòng'], 1, 'Sữa tươi cần bảo quản 0–5°C để hạn chế vi khuẩn phát triển.'],
                ['Khi mở ca phát hiện ổ cắm thiết bị bị ướt, nhân viên cần làm gì?', ['Tiếp tục cắm và theo dõi', 'Lau khô rồi bật thiết bị ngay', 'Ngắt nguồn, báo trưởng ca và không sử dụng ổ cắm đó', 'Chuyển sang ổ cắm gần đó'], 2, 'An toàn điện được ưu tiên trước khi vận hành thiết bị.'],
                ['Hạng mục nào bắt buộc kiểm tra trong checklist mở ca?', ['Hạn sử dụng nguyên liệu trong tủ', 'Doanh thu của ca trước', 'Lịch nghỉ của nhân viên', 'Bảng giá của cửa hàng khác'], 0, 'Nguyên liệu hết hạn phải được loại bỏ trước khi mở bán.'],
            ],
            5 => [
                ['Nguyên tắc xuất kho nguyên liệu giúp giảm hao hụt là gì?', ['Nhập sau xuất trước', 'Nhập trước hoặc hết hạn trước thì xuất trước (FIFO/FEFO)', 'Xuất ngẫu nhiên', 'Ưu tiên dùng hàng mới nhập'], 1, 'FIFO/FEFO hạn chế nguyên liệu tồn lâu và hết hạn.'],
                ['Sổ ghi nhận hủy nguyên liệu (waste log) dùng để làm gì?', ['Ghi số đồ uống bán được', 'Ghi nguyên liệu bị hủy, lý do và số lượng để phân tích thất thoát', 'Chấm công nhân viên', 'Ghi phản hồi khách hàng'], 1, 'Waste log là căn cứ tìm nguyên nhân thất thoát.'],
                ['Tỷ lệ hao hụt sữa tăng đột biến trong tuần, bước xử lý đầu tiên là gì?', ['Giảm định lượng sữa trong công thức', 'Đối chiếu waste log, tồn kho và thao tác đánh sữa của từng ca', 'Ngừng bán đồ uống có sữa', 'Đổi nhà cung cấp sữa'], 1, 'Cần xác định nguyên nhân trước khi thay đổi quy trình.'],
            ],
            6 => [
                ['Shot espresso chảy quá nhanh, dưới 20 giây, thường do nguyên nhân nào?', ['Cà phê xay quá mịn', 'Cà phê xay quá thô hoặc nén chưa đủ lực', 'Nhiệt độ nước quá thấp', 'Tách đựng quá nóng'], 1, 'Bột thô hoặc nén lỏng làm nước chảy qua quá nhanh.'],
                ['Tỷ lệ chiết xuất (brew ratio) phổ biến của một shot espresso double là bao nhiêu?', ['1:1', '1:2', '1:5', '1:10'], 1, 'Ví dụ 18g cà phê cho khoảng 36g espresso.'],
                ['Espresso có vị chua gắt, thân mỏng thường cho thấy điều gì?', ['Chiết xuất thiếu (under-extraction)', 'Chiết xuất quá (over-extraction)', 'Cà phê rang quá đậm', 'Sữa bị hỏng'], 0, 'Chiết xuất thiếu chưa lấy đủ hợp chất tạo vị ngọt và thân.'],
            ],
            7 => [
                ['Nhiệt độ sữa phù hợp khi đánh microfoam cho latte là bao nhiêu?', ['30–40°C', '55–65°C', '80–90°C', 'Đun sôi 100°C'], 1, 'Quá 65°C sữa mất vị ngọt và bọt kém mịn.'],
                ['Trước khi đánh sữa cần làm gì với vòi hơi (steam wand)?', ['Không cần thao tác gì', 'Xả hơi để loại nước đọng trong vòi', 'Ngâm vào nước lạnh', 'Lau bằng khăn khô rồi dùng ngay'], 1, 'Nước đọng làm loãng sữa và ảnh hưởng chất lượng bọt.'],
                ['Sữa đánh xong có bọt to, rời rạc là do nguyên nhân nào?', ['Đặt đầu vòi hơi quá sâu', 'Đưa quá nhiều không khí do vòi hơi ở quá nông', 'Sữa quá lạnh', 'Dùng ca đánh sữa bằng inox'], 1, 'Vòi quá nông hút nhiều khí tạo bọt lớn.'],
            ],
            8 => [
                ['Để tạo hình latte art rõ nét, sữa cần có đặc điểm gì?', ['Bọt dày và khô', 'Microfoam mịn, bóng, hòa đều với sữa', 'Sữa đã nguội', 'Không có bọt'], 1, 'Microfoam mịn mới chảy và tạo hình được.'],
                ['Ở giai đoạn rót đầu tiên (hòa sữa vào espresso), ca sữa nên đặt thế nào?', ['Sát mặt cốc', 'Ở trên cao, rót dòng nhỏ để sữa chìm dưới lớp crema', 'Nghiêng 90 độ', 'Rót thật nhanh'], 1, 'Giai đoạn hòa giữ crema làm nền cho hình vẽ.'],
                ['Hình trái tim bị lệch, bè sang hai bên thường do lỗi nào?', ['Không rót vào tâm cốc, đưa ca sữa không đều tay', 'Espresso quá đậm', 'Sữa quá ít bọt', 'Cốc quá lớn'], 0, 'Điểm rót và nhịp tay quyết định độ cân đối của hình.'],
                ['Khách gọi latte mang đi có nắp đậy, barista nên làm gì?', ['Bỏ qua tỷ lệ bọt và đổ đầy sữa', 'Vẫn giữ đúng tỷ lệ sữa và bọt theo công thức; latte art là tùy chọn', 'Tăng gấp đôi lượng bọt', 'Dùng sữa nguội'], 1, 'Chất lượng đồ uống không phụ thuộc vào việc có vẽ hình.'],
            ],
            9 => [
                ['Backflush máy espresso khi đóng ca nhằm mục đích gì?', ['Làm nóng máy', 'Làm sạch đầu group và đường xả bằng dung dịch chuyên dụng', 'Tăng áp suất máy', 'Tiết kiệm điện'], 1, 'Dầu cà phê bám lâu ngày làm hỏng vị espresso.'],
                ['Gioăng và lưới lọc đầu group cần được xử lý thế nào?', ['Vệ sinh, kiểm tra định kỳ và thay khi bị chai cứng', 'Không bao giờ tháo ra', 'Ngâm vào dầu ăn', 'Rửa bằng nước sôi mỗi giờ'], 0, 'Gioăng chai làm rò nước và áp suất không ổn định.'],
                ['Trước khi tắt máy xay cuối ca cần làm gì với phễu hạt?', ['Để nguyên hạt qua đêm', 'Xả hết hạt còn lại, vệ sinh phễu và bảo quản hạt kín', 'Đổ nước vào phễu', 'Không cần thao tác'], 1, 'Hạt để ngoài không khí bị oxy hóa và mất hương.'],
                ['Trưởng ca xác nhận checklist đóng ca khi nào?', ['Khi nhân viên báo miệng là xong', 'Sau khi kiểm tra thực tế từng hạng mục và ký xác nhận', 'Khi hết giờ làm', 'Khi quản lý yêu cầu'], 1, 'Xác nhận phải dựa trên kiểm tra thực tế.'],
            ],
            10 => [
                ['Khách yêu cầu "ít đường, thêm đá" nhưng thu ngân không ghi chú vào đơn thì hậu quả là gì?', ['Không ảnh hưởng', 'Barista pha theo công thức chuẩn, đồ uống sai yêu cầu và phải làm lại', 'Hóa đơn bị sai giá', 'Máy POS báo lỗi'], 1, 'Ghi chú đơn là thông tin duy nhất barista nhận được về yêu cầu riêng.'],
            ],
            11 => [
                ['Nguyên tắc đúng khi bàn giao két tiền cuối ca là gì?', ['Một người tự đếm', 'Hai người cùng đếm, đối chiếu báo cáo POS và ký biên bản', 'Chỉ đếm tiền lẻ', 'Bàn giao không cần đếm'], 1, 'Hai người cùng xác nhận giúp phân định trách nhiệm.'],
                ['Phát hiện tiền mặt chênh lệch so với báo cáo POS, thu ngân phải làm gì?', ['Tự bù tiền và không báo', 'Ghi nhận số chênh lệch và báo trưởng ca kiểm tra giao dịch', 'Sửa số liệu trên POS', 'Bỏ qua nếu dưới 50.000 đồng'], 1, 'Mọi chênh lệch đều phải được ghi nhận và kiểm tra.'],
                ['Khi nhận một tờ tiền nghi là tiền giả, thu ngân nên làm gì?', ['Nhận và cho vào két', 'Lịch sự đề nghị khách đổi tờ khác và báo trưởng ca', 'Xé bỏ tờ tiền', 'Từ chối phục vụ khách'], 1, 'Xử lý lịch sự, không gây căng thẳng với khách.'],
            ],
            12 => [
                ['Khi mang đồ uống ra bàn, nhân viên phục vụ đặt ly theo nguyên tắc nào?', ['Đặt ở vị trí bất kỳ', 'Đặt từ phía bên phải khách, quai ly hướng về tay phải khách', 'Đưa tận tay khách', 'Để khay giữa bàn cho khách tự lấy'], 1, 'Giúp khách cầm ly thuận tiện và không làm đổ.'],
                ['Theo quy trình cửa hàng, một khay nên bưng tối đa bao nhiêu ly?', ['Không giới hạn', 'Tối đa 4–5 ly, ly nặng đặt ở giữa khay', 'Khoảng 10 ly', 'Chỉ 1 ly'], 1, 'Giữ trọng tâm khay ổn định, tránh đổ vỡ.'],
                ['Khách phàn nàn đồ uống bị sai món, nhân viên phục vụ nên làm gì?', ['Giải thích đó là lỗi của barista', 'Xin lỗi, ghi nhận và đổi đúng món ngay', 'Đề nghị khách uống tạm', 'Bỏ qua phàn nàn'], 1, 'Ưu tiên khắc phục trải nghiệm của khách.'],
            ],
            13 => [
                ['Tiêu chuẩn "bus table 60 giây" có nghĩa là gì?', ['Dọn xong bàn trong 60 giây sau khi khách rời đi', 'Khách phải rời bàn sau 60 giây', 'Lau bàn 60 lần mỗi ngày', 'Kiểm tra sảnh mỗi 60 phút'], 0, 'Bàn sạch nhanh giúp đón khách mới và giữ sảnh gọn gàng.'],
                ['Thứ tự dọn bàn đúng là gì?', ['Lau bàn trước rồi mới thu ly', 'Thu ly, đĩa, rác; lau bàn bằng khăn ẩm có dung dịch; xếp lại ghế', 'Chỉ xếp lại ghế', 'Thu rác và để ly lại'], 1, 'Thu dọn trước rồi mới vệ sinh bề mặt.'],
                ['Khăn lau bàn khách và khăn lau quầy pha chế phải được quản lý thế nào?', ['Dùng chung một loại', 'Phân biệt riêng theo màu hoặc khu vực để tránh nhiễm chéo', 'Giặt chung với khăn tay', 'Thay mỗi tuần một lần'], 1, 'Tách khăn theo khu vực là yêu cầu vệ sinh an toàn thực phẩm.'],
            ],
            14 => [
                ['Mục đích chính của checklist mở và đóng ca là gì?', ['Chấm điểm nhân viên', 'Bảo đảm các bước vận hành, an toàn, vệ sinh được thực hiện đủ và có người chịu trách nhiệm', 'Tính lương', 'Báo cáo doanh thu'], 1, 'Checklist chuẩn hóa vận hành giữa các ca.'],
                ['Một hạng mục checklist chưa đạt khi mở ca, trưởng ca nên làm gì?', ['Đánh dấu đạt để kịp mở cửa', 'Ghi nhận, phân công xử lý và chỉ mở bán khi bảo đảm an toàn', 'Bỏ qua hạng mục đó', 'Chờ quản lý đến xử lý'], 1, 'Không đánh đổi an toàn để mở cửa đúng giờ.'],
                ['Checklist đã hoàn thành cần được xử lý thế nào?', ['Hủy cuối ngày', 'Lưu lại để đối chiếu khi có sự cố', 'Cho khách xem', 'Không cần lưu'], 1, 'Checklist là bằng chứng truy vết khi có sự cố.'],
                ['Thời điểm phù hợp để kiểm tra tồn kho nguyên liệu chính trong ca là khi nào?', ['Chỉ vào cuối tháng', 'Đầu ca và trước giờ cao điểm', 'Khi khách phàn nàn', 'Không cần kiểm tra'], 1, 'Tránh hết nguyên liệu giữa giờ cao điểm.'],
            ],
            15 => [
                ['Khi lượng đơn tăng đột biến, ưu tiên điều phối đầu tiên của trưởng ca là gì?', ['Cho nhân viên nghỉ giải lao', 'Bố trí thêm người hỗ trợ khâu đang ùn tắc (pha chế hoặc thu ngân)', 'Tạm đóng cửa', 'Rút gọn thực đơn ngay'], 1, 'Giải phóng điểm nghẽn giúp giảm thời gian chờ.'],
                ['Phân công vị trí trong giờ cao điểm nên dựa trên tiêu chí nào?', ['Ngẫu nhiên', 'Năng lực và kết quả đào tạo của từng nhân viên', 'Chỉ thâm niên', 'Nguyện vọng của nhân viên'], 1, 'Người đã đạt chuẩn vị trí nào nên đứng vị trí đó.'],
                ['Để giảm thời gian chờ của khách trong giờ cao điểm, trưởng ca có thể làm gì?', ['Theo dõi thời gian ra món và thông báo thời gian chờ dự kiến cho khách', 'Bỏ qua các đơn phức tạp', 'Tăng giá bán', 'Ngừng nhận đơn'], 0, 'Chủ động thông tin giúp khách chấp nhận thời gian chờ.'],
                ['Sau giờ cao điểm, trưởng ca nên làm gì?', ['Không cần làm gì', 'Tổng kết nhanh các điểm nghẽn và nhắc cải thiện trong ca', 'Phạt toàn bộ nhân viên', 'Cho nghỉ sớm'], 1, 'Rút kinh nghiệm ngay khi thông tin còn mới.'],
            ],
            16 => [
                ['Nguyên tắc của một buổi phản hồi 1-1 hiệu quả là gì?', ['Chỉ nêu các lỗi', 'Nêu hành vi cụ thể, tác động và thống nhất hành động cải thiện', 'Nói chung chung để tránh căng thẳng', 'Phản hồi trước tập thể'], 1, 'Phản hồi cụ thể giúp nhân viên biết phải thay đổi điều gì.'],
                ['Khi đánh giá nhân viên, quản lý nên dựa vào đâu?', ['Cảm tính', 'Tiêu chí đã công bố, kết quả quan sát và dữ liệu đào tạo', 'Chỉ thâm niên', 'Ý kiến của một khách hàng'], 1, 'Căn cứ rõ ràng giúp đánh giá công bằng.'],
                ['Mục tiêu cải thiện sau buổi 1-1 nên được đặt thế nào?', ['Mơ hồ để linh hoạt', 'Cụ thể, đo được và có thời hạn', 'Không cần đặt', 'Nhân viên tự đặt, không trao đổi'], 1, 'Mục tiêu đo được mới theo dõi được.'],
                ['Tần suất phản hồi 1-1 phù hợp với nhân viên mới là bao lâu?', ['Mỗi năm một lần', 'Định kỳ ngắn, hằng tuần hoặc hai tuần, trong giai đoạn đầu', 'Chỉ khi vi phạm', 'Không cần'], 1, 'Nhân viên mới cần phản hồi thường xuyên để điều chỉnh sớm.'],
            ],
        ];
    }
}
