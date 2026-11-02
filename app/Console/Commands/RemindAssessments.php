<?php

namespace App\Console\Commands;

use App\Models\AssessmentReminder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

#[Signature('brewlearn:remind-assessments {--store= : Chỉ nhắc một cửa hàng}')]
#[Description('Nhắc đánh giá định kỳ và hạn đào tạo lại, không gửi cảnh báo nguy cơ')]
class RemindAssessments extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $managers = User::working()->whereNotNull('cua_hang_id')->whereRaw('LOWER(TRIM(ChucVu)) IN (?, ?)', ['quản lý cửa hàng', 'quan ly cua hang']);
        if ($this->option('store')) {
            $managers->where('cua_hang_id', $this->option('store'));
        }
        foreach ($managers->get() as $manager) {
            $staff = User::working()->assessmentEligible()->where('cua_hang_id', $manager->cua_hang_id)->where('id', '!=', $manager->id)
                ->whereRaw("LOWER(TRIM(COALESCE(ChucVu, ''))) NOT IN (?, ?, ?, ?)", ['quản lý cửa hàng', 'quản lý vùng', 'hr', 'hr đào tạo'])->get();
            foreach (app(AssessmentReminder::class)->decorate($staff) as $person) {
                $due = $person->due_at;
                if ($person->needs_reminder && ! $person->scheduled_assessment) {
                    $overdue = $person->due_status === 'Quá hạn';
                    $this->remind($manager->id, $overdue ? 'Đánh giá định kỳ quá hạn' : 'Sắp đến hạn đánh giá định kỳ', $person->name.' — hạn đánh giá '.$due->format('d/m/Y').'.', 'periodic-'.$manager->id.'-'.$person->id.'-'.$due->toDateString().'-'.($overdue ? 'overdue' : 'soon'));
                }
            }
            foreach (DB::table('danh_gia_nang_luc')->whereIn('user_id', $staff->pluck('id'))->where('TrangThai', 'ChoPheDuyet')->get() as $assessment) {
                $this->remind($manager->id, 'Đánh giá chờ phê duyệt', ($staff->firstWhere('id', $assessment->user_id)?->name ?? 'Nhân viên').' — đánh giá năng lực đang chờ phê duyệt. Xem kết quả và nội dung tại mục Duyệt đánh giá.', 'pending-review-'.$manager->id.'-'.$assessment->id.'-'.$assessment->updated_at);
            }
            foreach (DB::table('yeu_cau_dao_tao_lai')->whereIn('user_id', $staff->pluck('id'))->where('trang_thai', 'DaDuyet')->where('tien_trinh', 'DangThucHien')->whereNotNull('han_hoan_thanh')->whereDate('han_hoan_thanh', '<=', today()->addDays(3))->get() as $plan) {
                $this->remind($manager->id, 'Nhắc hạn đào tạo lại', ($staff->firstWhere('id', $plan->user_id)?->name ?? 'Nhân viên').' — kế hoạch đào tạo lại, hạn '.$plan->han_hoan_thanh.'.', 'training-deadline-'.$manager->id.'-'.$plan->id.'-'.$plan->han_hoan_thanh.'-'.(Carbon::parse($plan->han_hoan_thanh)->lt(today()) ? 'overdue' : 'soon'));
            }
        }

        return self::SUCCESS;
    }

    private function remind(int $userId, string $title, string $message, string $key): void
    {
        DB::table('notifications')->insertOrIgnore(['user_id' => $userId, 'title' => $title, 'message' => $message, 'type' => 'reminder', 'is_read' => 0, 'event_key' => $key, 'created_at' => now(), 'updated_at' => now()]);
    }
}
