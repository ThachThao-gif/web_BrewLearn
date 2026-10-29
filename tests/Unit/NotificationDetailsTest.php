<?php

namespace Tests\Unit;

use App\Models\NotificationDetails;
use PHPUnit\Framework\TestCase;

class NotificationDetailsTest extends TestCase
{
    public function test_retraining_assignment_is_split_into_labelled_rows(): void
    {
        $message = "Hạn hoàn thành: 2026-10-30. Nội dung: Học lại bài học liên quan\nKhóa: Kỹ Thuật Espresso & Đánh Sữa Nâng Cao\nKỹ thuật Đánh sữa Microfoam & Latte Art\nĐào tạo bổ sung: kèm cặp 3 ca cao điểm, kiểm tra độ mịn sữa từng ly";

        $this->assertSame(['summary' => [], 'rows' => [
            ['label' => 'Hạn hoàn thành', 'value' => '30/10/2026'],
            ['label' => 'Nội dung', 'value' => 'Học lại bài học liên quan'],
            ['label' => 'Khóa', 'value' => 'Kỹ Thuật Espresso & Đánh Sữa Nâng Cao'],
            ['label' => 'Bài học', 'value' => 'Kỹ thuật Đánh sữa Microfoam & Latte Art'],
            ['label' => 'Đào tạo bổ sung', 'value' => 'kèm cặp 3 ca cao điểm, kiểm tra độ mịn sữa từng ly'],
        ]], (new NotificationDetails)->parse($message));
    }

    public function test_assignment_lines_with_owner_and_reassessment_date(): void
    {
        $rows = (new NotificationDetails)->parse("Người phụ trách: Trần Minh Thư\nHạn hoàn thành: 2026-10-30\nNgày đánh giá lại dự kiến: 2026-11-02\nNội dung: Học lại bài học liên quan")['rows'];

        $this->assertSame([
            ['label' => 'Người phụ trách', 'value' => 'Trần Minh Thư'],
            ['label' => 'Hạn hoàn thành', 'value' => '30/10/2026'],
            ['label' => 'Ngày đánh giá lại dự kiến', 'value' => '02/11/2026'],
            ['label' => 'Nội dung', 'value' => 'Học lại bài học liên quan'],
        ], $rows);
    }

    public function test_intro_sentence_stays_as_summary_and_times_are_not_treated_as_labels(): void
    {
        $result = (new NotificationDetails)->parse("Kết quả đánh giá #85 đã được quản lý phê duyệt.\nĐiểm tổng: 77.6/100 — CHƯA ĐẠT\nĐánh giá lại ngày 2026-10-05 lúc 10:00. Kỹ năng đánh sữa");

        $this->assertSame(['Kết quả đánh giá #85 đã được quản lý phê duyệt.'], $result['summary']);
        $this->assertSame([['label' => 'Điểm tổng', 'value' => "77.6/100 — CHƯA ĐẠT\nĐánh giá lại ngày 2026-10-05 lúc 10:00. Kỹ năng đánh sữa"]], $result['rows']);
    }

    public function test_plain_message_has_no_rows(): void
    {
        $this->assertSame(['summary' => ['Vui lòng hoàn thành bài học trước thứ Sáu.'], 'rows' => []],
            (new NotificationDetails)->parse('Vui lòng hoàn thành bài học trước thứ Sáu.'));
    }
}
