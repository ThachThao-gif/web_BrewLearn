<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

#[Signature('brewlearn:shift-demo-dates {--to= : Ngày demo (Y-m-d), mặc định hôm nay} {--from= : Ngày gốc của dữ liệu, mặc định đọc từ storage/app/demo-anchor-date.txt} {--skip-risk : Không tính lại cảnh báo nguy cơ}')]
#[Description('Dời toàn bộ mốc thời gian của dữ liệu demo sang ngày demo, giữ nguyên khoảng cách giữa các sự kiện')]
class ShiftDemoDates extends Command
{
    /**
     * Ngày dữ liệu demo được thiết kế quanh đó (dùng khi chưa có file mốc).
     */
    private const DEFAULT_ANCHOR = '2026-10-16';

    /**
     * Các cột ngày giờ cần dời theo từng bảng.
     *
     * @var array<string, list<string>>
     */
    private const DATE_COLUMNS = [
        'users' => ['joined_at'],
        'danh_gia_nang_luc' => ['NgayHen', 'NgayDanhGia', 'ngay_duyet', 'created_at', 'updated_at'],
        'yeu_cau_dao_tao_lai' => ['ngay_de_xuat', 'ngay_duyet', 'han_hoan_thanh', 'ngay_danh_gia_lai', 'created_at', 'updated_at'],
        'retraining_rounds' => ['deadline', 'assessment_date', 'approved_at', 'confirmed_at', 'created_at', 'updated_at'],
        'ket_qua_kiem_tra' => ['NgayLam', 'created_at', 'updated_at'],
        'user_lesson_progresses' => ['created_at', 'updated_at'],
        'nhan_vien_khoa_hoc' => ['created_at', 'updated_at'],
        'notifications' => ['created_at', 'updated_at'],
        'du_doan_nguy_co' => ['ngay_du_doan'],
        'loi_nghiep_vu' => ['thoi_diem_xay_ra', 'created_at', 'updated_at'],
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $anchorFile = storage_path('app/demo-anchor-date.txt');
        $from = Carbon::parse($this->option('from') ?: (is_file($anchorFile) ? trim(file_get_contents($anchorFile)) : self::DEFAULT_ANCHOR))->startOfDay();
        $to = Carbon::parse($this->option('to') ?: now('Asia/Ho_Chi_Minh')->toDateString())->startOfDay();
        $days = (int) $from->diffInDays($to, false);

        if ($days === 0) {
            $this->info('Dữ liệu demo đã ở mốc '.$to->format('d/m/Y').', không cần dời.');

            return self::SUCCESS;
        }

        DB::transaction(function () use ($days): void {
            foreach (self::DATE_COLUMNS as $table => $columns) {
                if (! Schema::hasTable($table)) {
                    continue;
                }
                $columns = array_values(array_filter($columns, fn (string $column) => Schema::hasColumn($table, $column)));
                foreach (DB::table($table)->select(array_merge(['id'], $columns))->get() as $row) {
                    $changes = [];
                    foreach ($columns as $column) {
                        if ($row->$column !== null) {
                            $changes[$column] = $this->shift((string) $row->$column, $days);
                        }
                    }
                    if ($changes) {
                        DB::table($table)->where('id', $row->id)->update($changes);
                    }
                }
            }
            foreach (DB::table('notifications')->select(['id', 'message'])->get() as $notification) {
                $message = $this->shiftDatesInText((string) $notification->message, $days);
                if ($message !== $notification->message) {
                    DB::table('notifications')->where('id', $notification->id)->update(['message' => $message]);
                }
            }
        });

        @file_put_contents($anchorFile, $to->toDateString());
        $this->info('Đã dời dữ liệu demo '.($days > 0 ? '+' : '').$days.' ngày: '.$from->format('d/m/Y').' → '.$to->format('d/m/Y').'.');

        if (! $this->option('skip-risk')) {
            $this->call('brewlearn:calculate-risk');
        }

        return self::SUCCESS;
    }

    private function shift(string $value, int $days): string
    {
        $isDateOnly = (bool) preg_match('/^\d{4}-\d{2}-\d{2}$/', $value);

        return Carbon::parse($value)->addDays($days)->format($isDateOnly ? 'Y-m-d' : 'Y-m-d H:i:s');
    }

    /**
     * Dời các ngày ghi trong nội dung thông báo (dd/mm/yyyy và yyyy-mm-dd).
     */
    private function shiftDatesInText(string $text, int $days): string
    {
        $text = preg_replace_callback('/\b(\d{2})\/(\d{2})\/(\d{4})\b/', fn (array $m) => checkdate((int) $m[2], (int) $m[1], (int) $m[3])
            ? Carbon::createFromDate((int) $m[3], (int) $m[2], (int) $m[1])->addDays($days)->format('d/m/Y') : $m[0], $text);

        return preg_replace_callback('/\b(\d{4})-(\d{2})-(\d{2})\b/', fn (array $m) => checkdate((int) $m[2], (int) $m[3], (int) $m[1])
            ? Carbon::createFromDate((int) $m[1], (int) $m[2], (int) $m[3])->addDays($days)->format('Y-m-d') : $m[0], $text);
    }
}
