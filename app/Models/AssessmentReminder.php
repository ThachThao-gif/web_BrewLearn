<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AssessmentReminder
{
    public const EMPLOYMENT_LABELS = ['DangLam' => 'Đang làm', 'TamNgung' => 'Tạm ngưng', 'NghiViec' => 'Nghỉ việc'];

    public function decorate(Collection $staff): Collection
    {
        $ids = $staff->pluck('id');
        $approved = DB::table('danh_gia_nang_luc')->whereIn('user_id', $ids)
            ->whereIn('TrangThai', ['DaDuyet', 'HoanThanh'])->whereNotNull('Diem')->whereNotNull('NgayDanhGia')
            ->orderByDesc('NgayDanhGia')->orderByDesc('id')->get()->unique('user_id')->keyBy('user_id');
        $pending = DB::table('danh_gia_nang_luc')->whereIn('user_id', $ids)
            ->whereIn('TrangThai', ['ChoDanhGia', 'ChoPheDuyet', 'TraLai'])->orderByDesc('id')->get()->unique('user_id')->keyBy('user_id');
        $today = Carbon::today('Asia/Ho_Chi_Minh');

        foreach ($staff as $person) {
            $person->due_at = null;
            $person->due_status = 'Chưa có ngày vào làm';
            $person->needs_reminder = false;
            $person->scheduled_assessment = null;
            if (in_array(mb_strtolower(trim($person->ChucVu ?? '')), User::LEADER_POSITIONS)
                || ($person->employment_status ?? 'DangLam') !== 'DangLam' || empty($person->cua_hang_id)) {
                $person->due_status = 'Không áp dụng';

                continue;
            }
            $last = $approved->get($person->id);
            $date = $last->NgayDanhGia ?? $person->joined_at ?? null;
            if (! $date) {
                continue;
            }
            $person->due_at = Carbon::parse($date, 'Asia/Ho_Chi_Minh')->startOfDay()->addMonthsNoOverflow(3);
            $person->due_status = $person->due_at->lt($today) ? 'Quá hạn' : ($person->due_at->eq($today) ? 'Đến hạn' : ($person->due_at->lte($today->copy()->addDays(7)) ? 'Sắp đến hạn' : 'Chưa đến hạn'));
            $person->scheduled_assessment = $pending->get($person->id);
            $person->needs_reminder = $person->due_at->lte($today->copy()->addDays(7));
        }

        return $staff;
    }
}
