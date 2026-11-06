<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AssessmentReminder
{
    public const EMPLOYMENT_LABELS = ['DangLam' => 'Đang làm', 'TamNgung' => 'Tạm ngưng', 'NghiViec' => 'Nghỉ việc'];

    /**
     * Nhãn ở cột hạn đánh giá khi nhắc hạn định kỳ tạm dừng, theo tiến trình của yêu cầu đào tạo lại.
     */
    public const RETRAINING_LABELS = ['ChoGiao' => 'Chờ giao đào tạo lại', 'DangThucHien' => 'Đang đào tạo lại', 'ChoDanhGiaLai' => 'Chờ đánh giá lại'];

    /**
     * Chưa đạt sau 3 vòng: luồng trong hệ thống kết thúc, không nhắc hạn định kỳ; hướng xử lý tiếp theo nằm ngoài phạm vi.
     */
    public const STOPPED_LABEL = 'Dừng đào tạo lại';

    public function decorate(Collection $staff): Collection
    {
        $ids = $staff->pluck('id');
        $approved = DB::table('danh_gia_nang_luc')->whereIn('user_id', $ids)
            ->whereIn('TrangThai', ['DaDuyet', 'HoanThanh'])->whereNotNull('Diem')->whereNotNull('NgayDanhGia')
            ->orderByDesc('NgayDanhGia')->orderByDesc('id')->get()->unique('user_id')->keyBy('user_id');
        $pending = DB::table('danh_gia_nang_luc')->whereIn('user_id', $ids)
            ->whereIn('TrangThai', ['ChoDanhGia', 'ChoPheDuyet', 'TraLai'])->orderByDesc('id')->get()->unique('user_id')->keyBy('user_id');
        // Đánh giá lại của vòng đào tạo lại thay cho đánh giá định kỳ (Bảng 3.6): tạm dừng nhắc hạn đến khi yêu cầu kết thúc
        $retraining = DB::table('yeu_cau_dao_tao_lai')->whereIn('user_id', $ids)
            ->whereIn('tien_trinh', AssessmentEligibility::OPEN_RETRAINING_STATES)->pluck('tien_trinh', 'user_id');
        $stopped = DB::table('yeu_cau_dao_tao_lai')->whereIn('user_id', $ids)->where('tien_trinh', 'DungDaoTao')->pluck('user_id')->flip();
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
            if ($stopped->has($person->id) && ! $retraining->has($person->id)) {
                $person->due_status = self::STOPPED_LABEL;

                continue;
            }
            $last = $approved->get($person->id);
            $date = $last->NgayDanhGia ?? $person->joined_at ?? null;
            if (! $date) {
                continue;
            }
            $person->due_at = Carbon::parse($date, 'Asia/Ho_Chi_Minh')->startOfDay()->addMonthsNoOverflow(3);
            if ($retraining->has($person->id)) {
                $person->due_status = self::RETRAINING_LABELS[$retraining->get($person->id)];

                continue;
            }
            $person->due_status = $person->due_at->lt($today) ? 'Quá hạn' : ($person->due_at->eq($today) ? 'Đến hạn' : ($person->due_at->lte($today->copy()->addDays(7)) ? 'Sắp đến hạn' : 'Chưa đến hạn'));
            $person->scheduled_assessment = $pending->get($person->id);
            $person->needs_reminder = $person->due_at->lte($today->copy()->addDays(7));
        }

        return $staff;
    }
}
