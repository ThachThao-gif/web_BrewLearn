<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Đồng bộ ràng buộc của cơ sở dữ liệu với ERD (Hình 4.2) và từ điển dữ liệu (Phụ lục 7):
 * - bổ sung 14 khóa ngoại và ràng buộc duy nhất (người dùng, bài học) còn thiếu;
 * - đổi user_lesson_progresses.lesson_id về INT cho cùng kiểu với bai_hoc.MaBaiHoc;
 * - retraining_rounds.owner_id bắt buộc (BR14), users.password VARCHAR(255);
 * - bỏ 5 khóa ngoại bị khai báo trùng.
 */
return new class extends Migration
{
    private const DUPLICATES = [
        'bai_hoc' => ['fk_bai_hoc_ctdt', '`FK_MaCTDT`) REFERENCES `chuong_trinh_dao_tao` (`MaCTDT`) ON DELETE SET NULL ON UPDATE CASCADE'],
        'bai_kiem_tra' => ['fk_bai_kiem_tra_bai_hoc', '`MaBaiHoc`) REFERENCES `bai_hoc` (`MaBaiHoc`) ON DELETE SET NULL ON UPDATE CASCADE'],
        'cau_hoi_kiem_tra' => ['fk_cau_hoi_bai_kiem_tra', '`MaBaiKiemTra`) REFERENCES `bai_kiem_tra` (`MaBaiKiemTra`) ON DELETE CASCADE ON UPDATE CASCADE'],
    ];

    /** [bảng, tên ràng buộc, cột, bảng tham chiếu, cột tham chiếu, hành vi khi xóa] */
    private const FOREIGN_KEYS = [
        ['user_lesson_progresses', 'user_lesson_progresses_lesson_id_foreign', 'lesson_id', 'bai_hoc', 'MaBaiHoc', 'CASCADE'],
        ['retraining_rounds', 'retraining_rounds_request_id_foreign', 'request_id', 'yeu_cau_dao_tao_lai', 'id', 'CASCADE'],
        ['retraining_rounds', 'retraining_rounds_assessment_id_foreign', 'assessment_id', 'danh_gia_nang_luc', 'id', 'SET NULL'],
        ['retraining_rounds', 'retraining_rounds_assigned_by_foreign', 'assigned_by', 'users', 'id', 'RESTRICT'],
        ['retraining_rounds', 'retraining_rounds_owner_id_foreign', 'owner_id', 'users', 'id', 'RESTRICT'],
        ['retraining_rounds', 'retraining_rounds_approved_by_foreign', 'approved_by', 'users', 'id', 'SET NULL'],
        ['retraining_rounds', 'retraining_rounds_confirmed_by_foreign', 'confirmed_by', 'users', 'id', 'SET NULL'],
        ['yeu_cau_dao_tao_lai', 'fk_ytdl_nguoi_phu_trach', 'nguoi_phu_trach_id', 'users', 'id', 'SET NULL'],
        ['yeu_cau_dao_tao_lai', 'fk_ytdl_danh_gia_nguon', 'assessment_id', 'danh_gia_nang_luc', 'id', 'SET NULL'],
        ['yeu_cau_dao_tao_lai', 'fk_ytdl_danh_gia_lai', 'danh_gia_lai_id', 'danh_gia_nang_luc', 'id', 'SET NULL'],
        ['danh_gia_nang_luc', 'fk_dgnl_nguoi_duyet', 'nguoi_duyet_id', 'users', 'id', 'SET NULL'],
        ['loi_nghiep_vu', 'fk_loi_nghiep_vu_nguoi_ghi_nhan', 'nguoi_ghi_nhan_id', 'users', 'id', 'CASCADE'],
        ['ca_lam_viec', 'ca_lam_viec_cua_hang_id_foreign', 'cua_hang_id', 'cua_hang', 'id', 'CASCADE'],
        ['vung', 'vung_nguoi_quan_ly_id_foreign', 'nguoi_quan_ly_id', 'users', 'id', 'SET NULL'],
    ];

    public function up(): void
    {
        foreach (self::DUPLICATES as $table => [$name]) {
            DB::statement("ALTER TABLE `$table` DROP FOREIGN KEY `$name`");
        }
        DB::statement('ALTER TABLE `ket_qua_kiem_tra` DROP FOREIGN KEY `fk_kqkt_bai_kiem_tra`, DROP FOREIGN KEY `fk_kqkt_user`');

        DB::statement('ALTER TABLE `user_lesson_progresses` MODIFY `lesson_id` INT NOT NULL,
            ADD UNIQUE KEY `user_lesson_progresses_user_id_lesson_id_unique` (`user_id`, `lesson_id`)');
        DB::statement('ALTER TABLE `retraining_rounds` MODIFY `owner_id` BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE `users` MODIFY `password` VARCHAR(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL');

        foreach (self::FOREIGN_KEYS as [$table, $name, $column, $refTable, $refColumn, $onDelete]) {
            DB::statement("ALTER TABLE `$table` ADD CONSTRAINT `$name` FOREIGN KEY (`$column`)
                REFERENCES `$refTable` (`$refColumn`) ON DELETE $onDelete");
        }
    }

    public function down(): void
    {
        foreach (array_reverse(self::FOREIGN_KEYS) as [$table, $name]) {
            DB::statement("ALTER TABLE `$table` DROP FOREIGN KEY `$name`");
        }
        DB::statement('ALTER TABLE `users` MODIFY `password` VARCHAR(191) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL');
        DB::statement('ALTER TABLE `retraining_rounds` MODIFY `owner_id` BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE `user_lesson_progresses` DROP INDEX `user_lesson_progresses_user_id_lesson_id_unique`,
            MODIFY `lesson_id` BIGINT UNSIGNED NOT NULL');

        foreach (self::DUPLICATES as $table => [$name, $definition]) {
            DB::statement("ALTER TABLE `$table` ADD CONSTRAINT `$name` FOREIGN KEY ($definition");
        }
        DB::statement('ALTER TABLE `ket_qua_kiem_tra`
            ADD CONSTRAINT `fk_kqkt_bai_kiem_tra` FOREIGN KEY (`MaBaiKiemTra`) REFERENCES `bai_kiem_tra` (`MaBaiKiemTra`) ON DELETE CASCADE,
            ADD CONSTRAINT `fk_kqkt_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE');
    }
};
