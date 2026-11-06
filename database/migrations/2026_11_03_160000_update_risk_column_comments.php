<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Sửa chú thích cột còn mang nghĩa mô hình học máy (dự đoán, feature, nhãn huấn luyện):
 * bảng du_doan_nguy_co chỉ lưu kết quả cảnh báo theo bộ quy tắc. Tên bảng, tên cột giữ nguyên.
 */
return new class extends Migration
{
    /**
     * Định nghĩa cột kèm chú thích mới và chú thích cũ.
     *
     * @var array<string, array<string, array{0: string, 1: string, 2: string}>>
     */
    private const COLUMNS = [
        'du_doan_nguy_co' => [
            'user_id' => ['bigint unsigned NOT NULL', 'Nhân viên được tính cảnh báo', 'Nhân viên được dự đoán'],
            'cua_hang_id' => ['bigint unsigned DEFAULT NULL', 'Cửa hàng tại thời điểm tính cảnh báo', 'Cửa hàng tại thời điểm dự đoán'],
            'chi_tiet_yeu_to' => ['json DEFAULT NULL', 'Dữ liệu đầu vào, điểm thành phần và lý do cảnh báo', 'Lưu breakdown các feature ảnh hưởng (để giải thích kết quả)'],
            'phien_ban_mo_hinh' => ['varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL', 'Phiên bản bộ quy tắc cảnh báo (tên cột kế thừa)', 'Version model đã dùng để dự đoán'],
            'ngay_du_doan' => ['timestamp NULL DEFAULT CURRENT_TIMESTAMP', 'Thời điểm tính cảnh báo, giờ UTC (tên cột kế thừa)', ''],
        ],
        'yeu_cau_dao_tao_lai' => [
            'du_doan_id' => ['bigint unsigned DEFAULT NULL', 'Kết quả cảnh báo tham khảo, dự phòng (tên cột kế thừa)', 'FK tới du_doan_nguy_co làm căn cứ đề xuất'],
            'nguoi_de_xuat_id' => ['bigint unsigned DEFAULT NULL', 'Trưởng ca đề xuất', 'Trưởng ca/hệ thống đề xuất (NULL nếu hệ thống tự động)'],
            'ket_qua_sau_dao_tao_lai' => ["enum('ChuaCoKetQua','Dat','ChuaDat') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ChuaCoKetQua'", 'Kết quả cuối cùng của yêu cầu đào tạo lại', 'Dùng làm nhãn lịch sử (label) để huấn luyện lại mô hình'],
        ],
    ];

    public function up(): void
    {
        $this->applyComments(1);
        DB::statement("ALTER TABLE du_doan_nguy_co COMMENT = 'Kết quả cảnh báo nguy cơ theo bộ quy tắc (tên bảng kế thừa)'");
    }

    public function down(): void
    {
        $this->applyComments(2);
        DB::statement("ALTER TABLE du_doan_nguy_co COMMENT = ''");
    }

    private function applyComments(int $index): void
    {
        foreach (self::COLUMNS as $table => $columns) {
            foreach ($columns as $column => $definition) {
                DB::statement("ALTER TABLE `$table` MODIFY `$column` {$definition[0]} COMMENT ".DB::getPdo()->quote($definition[$index]));
            }
        }
    }
};
