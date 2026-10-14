<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CompactReportDemo extends Command
{
    protected $signature = 'brewlearn:compact-report-demo';

    protected $description = 'Sao lưu và thu gọn hoạt động tài khoản operations demo để chụp báo cáo';

    public function handle(): int
    {
        $manager = User::where('email', 'ngocbich@brewlearn.com')->firstOrFail();
        $users = User::where('cua_hang_id', $manager->cua_hang_id)
            ->whereIn('email', collect(range(1, 6))->map(fn ($i) => "operations{$i}.demo@brewlearn.com"))->get();
        $ids = $users->modelKeys();
        $plans = DB::table('yeu_cau_dao_tao_lai')->whereIn('user_id', $ids)->get();
        $backup = ['created_at' => now()->toIso8601String(), 'users' => $users->map->getAttributes(),
            'assessments' => DB::table('danh_gia_nang_luc')->whereIn('user_id', $ids)->get(),
            'plans' => $plans,
            'rounds' => DB::table('retraining_rounds')->whereIn('request_id', $plans->pluck('id'))->get(),
            'notifications' => DB::table('notifications')->get()];
        $path = 'demo-backups/report-'.now()->format('Ymd-His').'-'.Str::random(6).'.json';
        if (! Storage::disk('local')->put($path, json_encode($backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR))) {
            $this->error('Không sao lưu được; chưa thay đổi dữ liệu.');

            return self::FAILURE;
        }
        DB::transaction(function () use ($users): void {
            foreach ($users as $user) {
                $assessments = DB::table('danh_gia_nang_luc')->where('user_id', $user->id)->orderBy('id')->lockForUpdate()->get();
                // Only compact seeded examples; never remove assessments entered through the UI.
                $demo = $assessments->filter(fn ($row) => str_starts_with($row->NhanXet ?? '', 'Dữ liệu minh họa:'));
                $keep = collect();
                $plans = DB::table('yeu_cau_dao_tao_lai')->where('user_id', $user->id)->lockForUpdate()->get();
                foreach ($plans as $plan) {
                    if (! $demo->contains('id', $plan->assessment_id)) {
                        continue;
                    }
                    $rounds = DB::table('retraining_rounds')->where('request_id', $plan->id)->orderBy('round_number')->get();
                    // Two redundant completed training scenarios become simple passed assessments.
                    if (in_array($user->email, ['operations4.demo@brewlearn.com', 'operations5.demo@brewlearn.com'])) {
                        if ($rounds->contains(fn ($r) => $r->assessment_id && ! $demo->contains('id', $r->assessment_id))) {
                            continue;
                        }
                        DB::table('retraining_rounds')->where('request_id', $plan->id)->delete();
                        DB::table('yeu_cau_dao_tao_lai')->where('id', $plan->id)->delete();

                        continue;
                    }
                    $keep->push($plan->assessment_id);
                    $selected = $rounds->count() > 2 ? collect([$rounds->last()]) : $rounds;
                    $removed = $rounds->whereNotIn('id', $selected->pluck('id'));
                    DB::table('retraining_rounds')->whereIn('id', $removed->pluck('id'))->delete();
                    foreach ($selected->values() as $index => $round) {
                        DB::table('retraining_rounds')->where('id', $round->id)->update(['round_number' => $index + 1]);
                        if ($round->assessment_id) {
                            $keep->push($round->assessment_id);
                        }
                        if ($round->status === 'DangThucHien') {
                            $dates = ['deadline' => today()->addDays(5)->toDateString(), 'assessment_date' => today()->addDays(7)->toDateString()];
                            DB::table('retraining_rounds')->where('id', $round->id)->update($dates);
                            DB::table('yeu_cau_dao_tao_lai')->where('id', $plan->id)->update(['han_hoan_thanh' => $dates['deadline'], 'ngay_danh_gia_lai' => $dates['assessment_date']]);
                        }
                    }
                    if ($plan->trang_thai === 'HoanThanh' && $plan->danh_gia_lai_id) {
                        $date = today()->subDays(3)->setTime(10, 0);
                        DB::table('danh_gia_nang_luc')->where('id', $plan->danh_gia_lai_id)->update(['NgayHen' => $date, 'NgayDanhGia' => $date, 'ngay_duyet' => $date->copy()->addHour()]);
                        DB::table('retraining_rounds')->where('assessment_id', $plan->danh_gia_lai_id)->update(['assessment_date' => $date->toDateString(), 'approved_at' => $date->copy()->addHour()]);
                    }
                }
                if ($keep->isEmpty() && $demo->isNotEmpty()) {
                    $keep->push($demo->last()->id);
                }
                DB::table('danh_gia_nang_luc')->whereIn('id', $demo->whereNotIn('id', $keep)->pluck('id'))->delete();
            }
            // Remove outdated demo notifications, including old round numbers, for all recipients.
            DB::table('notifications')->where('event_key', 'like', 'operations-round-%')->delete();
        });
        $this->info('Đã thu gọn dữ liệu demo. Sao lưu: '.Storage::disk('local')->path($path));

        return self::SUCCESS;
    }
}
