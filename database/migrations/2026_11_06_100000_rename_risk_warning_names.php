<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Đổi tên bảng và cột cảnh báo cho đúng cách làm hiện tại: kết quả được tính theo
 * bộ quy tắc nghiệp vụ, không phải kết quả dự đoán. Dữ liệu giữ nguyên.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->rename(
            ['du_doan_nguy_co', 'canh_bao_nguy_co', 'ddnc', 'cbnc'],
            ['phien_ban_mo_hinh', 'phien_ban_quy_tac', 'Phiên bản bộ quy tắc cảnh báo'],
            ['ngay_du_doan', 'thoi_diem_tinh', 'Thời điểm tính cảnh báo, giờ UTC'],
            ['du_doan_id', 'canh_bao_id', 'du_doan', 'canh_bao', 'Kết quả cảnh báo tham khảo, dự phòng'],
            'Kết quả cảnh báo nguy cơ theo bộ quy tắc',
        );
    }

    public function down(): void
    {
        $this->rename(
            ['canh_bao_nguy_co', 'du_doan_nguy_co', 'cbnc', 'ddnc'],
            ['phien_ban_quy_tac', 'phien_ban_mo_hinh', 'Phiên bản bộ quy tắc cảnh báo (tên cột kế thừa)'],
            ['thoi_diem_tinh', 'ngay_du_doan', 'Thời điểm tính cảnh báo, giờ UTC (tên cột kế thừa)'],
            ['canh_bao_id', 'du_doan_id', 'canh_bao', 'du_doan', 'Kết quả cảnh báo tham khảo, dự phòng (tên cột kế thừa)'],
            'Kết quả cảnh báo nguy cơ theo bộ quy tắc (tên bảng kế thừa)',
        );
    }

    private function rename(array $table, array $version, array $time, array $reference, string $comment): void
    {
        [$from, $to, $oldKey, $newKey] = $table;
        [$oldRef, $newRef, $oldRefKey, $newRefKey, $refComment] = $reference;

        DB::statement("ALTER TABLE yeu_cau_dao_tao_lai DROP FOREIGN KEY fk_ytdl_{$oldRefKey}");
        DB::statement("ALTER TABLE {$from} DROP FOREIGN KEY fk_{$oldKey}_cua_hang, DROP FOREIGN KEY fk_{$oldKey}_user");
        DB::statement("RENAME TABLE {$from} TO {$to}");

        DB::statement("ALTER TABLE {$to}
            CHANGE {$version[0]} {$version[1]} varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '{$version[2]}',
            CHANGE {$time[0]} {$time[1]} timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '{$time[2]}',
            RENAME INDEX idx_{$oldKey}_user TO idx_{$newKey}_user,
            RENAME INDEX idx_{$oldKey}_cua_hang TO idx_{$newKey}_cua_hang,
            RENAME INDEX idx_{$oldKey}_ngay TO idx_{$newKey}_ngay,
            ADD CONSTRAINT fk_{$newKey}_cua_hang FOREIGN KEY (cua_hang_id) REFERENCES cua_hang (id) ON DELETE SET NULL,
            ADD CONSTRAINT fk_{$newKey}_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
            COMMENT = '{$comment}'");

        DB::statement("ALTER TABLE yeu_cau_dao_tao_lai
            CHANGE {$oldRef} {$newRef} bigint unsigned DEFAULT NULL COMMENT '{$refComment}',
            RENAME INDEX idx_ytdl_{$oldRefKey} TO idx_ytdl_{$newRefKey},
            ADD CONSTRAINT fk_ytdl_{$newRefKey} FOREIGN KEY ({$newRef}) REFERENCES {$to} (id) ON DELETE SET NULL");
    }
};
