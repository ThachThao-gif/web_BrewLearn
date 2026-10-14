import sys
from pathlib import Path
import xml.etree.ElementTree as ET
from collections import Counter
sys.path.insert(0, str(Path(__file__).parent / 'report-libs'))
from openpyxl import Workbook, load_workbook
from openpyxl.styles import Font, PatternFill, Alignment
from openpyxl.worksheet.table import Table, TableStyleInfo
from openpyxl.utils import get_column_letter

base = Path(__file__).parent
root = ET.parse(base / 'kiem-thu-brewlearn.xml').getroot()
suite = root.find('testsuite')
cases = list(root.iter('testcase'))
descriptions = '''that_true_is_true|Xác nhận mẫu: giá trị đúng
dashboard_displays_concrete_proposal_fields_and_database_choices|Hiển thị trường đề xuất cụ thể và lựa chọn từ cơ sở dữ liệu
selected_proposal_details_are_saved_with_pending_approval|Lưu chi tiết đề xuất với trạng thái chờ phê duyệt
failed_assessment_requires_a_proposal_and_selected_details_without_saving|Đánh giá chưa đạt phải có đề xuất và chi tiết; không lưu khi thiếu
lessons_must_belong_to_the_selected_course|Bài học đề xuất phải thuộc khóa học đã chọn
observation_accepts_an_end_date_and_rejects_missing_duration|Theo dõi ca chấp nhận ngày kết thúc và từ chối thiếu thời lượng
passed_assessment_clears_old_proposals_and_ignores_hidden_details|Đánh giá đạt xóa đề xuất cũ và bỏ qua chi tiết ẩn
reassessment_rejects_a_past_date_or_unknown_criterion|Từ chối ngày đánh giá lại trong quá khứ hoặc tiêu chí không hợp lệ
account_settings_persist_for_the_logged_in_user|Lưu thiết lập của tài khoản đang đăng nhập
quiz_dashboard_and_list_share_role_and_common_tests|Trang tổng quan và danh sách thống nhất bài kiểm tra theo vai trò và bài chung
password_change_requires_current_password_and_keeps_current_session|Đổi mật khẩu yêu cầu mật khẩu hiện tại và giữ phiên đang sử dụng
help_reads_and_saves_the_logged_in_users_tickets|Đọc và lưu yêu cầu hỗ trợ của người đăng nhập
logging_out_other_devices_removes_only_this_users_other_sessions|Đăng xuất thiết bị khác chỉ xóa phiên khác của cùng tài khoản
partial_learning_progress_is_identical_after_reload_and_in_leader_summary|Tiến độ học một phần nhất quán sau tải lại và trong tổng hợp trưởng ca
demo_progress_is_preserved_when_learning_starts|Giữ tiến độ nền demo khi bắt đầu học
retries_keep_history_and_duplicate_submissions_return_original_score|Thi lại giữ lịch sử; gửi lặp trả lại điểm đã lưu
the_application_returns_a_successful_response|Truy cập trang chủ khi chưa đăng nhập: ca mẫu yêu cầu HTTP 200
staff_progress_is_read_in_batches_and_reflects_new_learning_without_writes|Đọc tiến độ theo lô và phản ánh hoạt động học mới không ghi dữ liệu khi xem
dashboard_fetches_notification_feed_only_once_despite_many_partials|Tổng quan chỉ truy vấn nguồn thông báo một lần dù có nhiều thành phần giao diện
supplementing_store_demo_is_additive_and_does_not_reset_new_web_records|Bổ sung demo không đặt lại bản ghi mới phát sinh trên web
dashboard_charts_use_approved_latest_monthly_results_and_latest_risks|Biểu đồ dùng kết quả tháng mới nhất đã duyệt và dự đoán mới nhất
leaders_are_not_assessment_targets_but_remain_learning_and_staff_members|Trưởng ca không là đối tượng đánh giá nhưng vẫn thuộc danh sách nhân sự và học tập
login_and_all_manager_pages_use_store_scope|Đăng nhập và các trang quản lý áp dụng phạm vi cửa hàng
employee_opening_manager_page_can_switch_to_manager_account|Nhân viên mở trang quản lý có thể chuyển sang đăng nhập tài khoản quản lý
assessment_tabs_split_statuses_and_keep_employee_filters|Các tab phân chia trạng thái đánh giá và giữ bộ lọc nhân viên
assessments_follow_submission_time_and_hide_empty_comments|Sắp xếp đánh giá theo thời điểm gửi và ẩn nhận xét trống
prediction_stays_independent_of_approved_evaluation|Dự đoán độc lập với kết quả đánh giá đã duyệt
shift_assignment_and_filter_reject_other_store|Phân ca và bộ lọc từ chối dữ liệu cửa hàng khác
failed_review_creates_one_training_plan_and_blocks_duplicates|Duyệt chưa đạt tạo một kế hoạch đào tạo lại và chặn xử lý trùng
return_requires_reason_and_preserves_scores|Trả lại đánh giá yêu cầu lý do và giữ điểm
training_finishes_only_after_approved_pass_on_reassessment|Chỉ hoàn tất đào tạo lại sau đánh giá lại đạt được duyệt
multiple_retraining_rounds_preserve_history_and_close_only_after_approved_pass|Giữ lịch sử nhiều vòng và chỉ đóng sau kết quả đạt được duyệt
profile_displays_lesson_counts_and_all_attempts|Hồ sơ hiển thị số bài học và toàn bộ lượt kiểm tra
reminders_do_not_duplicate_or_show_risk_alerts|Nhắc việc không trùng và không trộn cảnh báo nguy cơ
leader_submission_notifies_manager_once_and_can_be_returned_and_resubmitted|Gửi đánh giá báo quản lý một lần; hỗ trợ trả lại và gửi lại
legacy_incomplete_evaluation_must_be_returned_for_details|Đánh giá cũ thiếu thông tin phải trả lại để bổ sung
leader_and_manager_share_due_dates_for_first_and_repeated_assessments|Trưởng ca và quản lý thống nhất hạn đánh giá đầu và định kỳ
overdue_staff_are_not_dropped_after_105_days|Nhân viên quá hạn hơn 105 ngày vẫn được theo dõi
inactive_staff_are_excluded_from_all_reminders_but_history_is_retained|Nhân viên không đang làm bị loại khỏi nhắc việc nhưng giữ lịch sử
status_updates_are_validated_and_scoped_to_the_managers_store|Cập nhật trạng thái được kiểm tra dữ liệu và phạm vi cửa hàng
end_of_month_dates_and_missing_start_dates_do_not_invent_due_dates|Xử lý ngày cuối tháng và không tự tạo hạn khi thiếu ngày vào làm
existing_schedule_suppresses_new_periodic_notifications_and_duplicate_schedules|Lịch đã có ngăn thông báo định kỳ mới và lịch trùng
working_staff_can_receive_a_new_assessment_schedule_notification|Nhân viên đang làm nhận được thông báo lịch đánh giá mới
initial_schedule_requires_all_lessons_and_quizzes_but_keeps_due_reminders|Lịch đánh giá đầu yêu cầu hoàn thành học và kiểm tra; vẫn giữ nhắc hạn
reassessment_requires_fresh_target_learning_quiz_and_owner_confirmation_only|Đánh giá lại yêu cầu học và quiz mới của nội dung mục tiêu cùng xác nhận đúng người phụ trách
due_categories_use_calendar_months_and_a_seven_day_window|Phân loại hạn theo tháng lịch và khoảng sắp đến hạn 7 ngày
staff_without_results_are_not_shown_as_zero_scores|Nhân viên chưa có kết quả không hiển thị như điểm 0
latest_scores_and_assessment_status_are_displayed|Hiển thị điểm mới nhất và trạng thái đánh giá'''
labels = dict(line.split('|', 1) for line in descriptions.splitlines())
wb = Workbook()
summary = wb.active
summary.title = 'Tong hop'
summary.append(['BÁO CÁO KIỂM THỬ HỆ THỐNG BREWLEARN'])
summary.merge_cells('A1:G1')
summary.append(['Ngày thực hiện', '05/10/2026', 'Nguồn', 'kiem-thu-brewlearn.xml'])
summary.append(['Phạm vi', 'Bộ kiểm thử sẵn có; SQLite trong bộ nhớ; không chạy lại khi xuất Excel'])
summary.merge_cells('B3:G3')
summary.append(['Tổng số ca', len(cases), 'Đạt', 59, 'Không đạt', 1])
summary.append(['Phép xác nhận', int(suite.get('assertions')), 'Thời gian (giây)', float(suite.get('time')), 'Tỷ lệ đạt', '=D4/B4'])
summary['F5'].number_format = '0.00%'
summary.append(['Lưu ý', 'Bộ kiểm thử tổng thể chưa đạt do 1 ca mẫu Feature yêu cầu HTTP 200 nhưng nhận 302.'])
summary.merge_cells('B6:G6')
summary.append([])
summary.append(['Nhóm kiểm thử', 'Số ca', 'Đạt', 'Không đạt', 'Lỗi thực thi', 'Bỏ qua', 'Phép xác nhận'])

detail = wb.create_sheet('Chi tiet 60 ca')
detail.append(['Mã ca', 'Loại', 'Nhóm kiểm thử', 'Nội dung kiểm thử', 'Bộ dữ liệu', 'Kết quả', 'Phép xác nhận', 'Thời gian (giây)', 'Tên ca gốc', 'Tệp mã nguồn', 'Dòng', 'Chi tiết lỗi / ghi chú'])
counts = {}
for index, case in enumerate(cases, 1):
    cls = case.get('class')
    name = case.get('name')
    method, _, dataset = name.partition(' with data set ')
    label = labels[method.removeprefix('test_')]
    result = 'Không đạt' if case.find('failure') is not None else 'Lỗi thực thi' if case.find('error') is not None else 'Bỏ qua' if case.find('skipped') is not None else 'Đạt'
    message = '\n'.join((child.text or '').strip() for child in case if child.tag in ['failure', 'error', 'skipped'])
    if not message:
        message = 'Các phép xác nhận trong ca đã đạt; nội dung tiếng Việt tóm tắt mục tiêu từ tên ca.'
    if cls == 'Tests\\Unit\\ExampleTest':
        message = 'Ca mẫu xác nhận giá trị đúng; không chứng minh một chức năng nghiệp vụ.'
    detail.append([f'TC{index:03}', 'Unit' if '\\Unit\\' in cls else 'Feature', cls, label, dataset or 'Không tách bộ dữ liệu', result, int(case.get('assertions')), float(case.get('time')), name, case.get('file'), int(case.get('line')), message])
    stats = counts.setdefault(cls, Counter())
    stats['total'] += 1
    stats[result] += 1
    stats['assertions'] += int(case.get('assertions'))
for cls, stats in counts.items():
    summary.append([cls, stats['total'], stats['Đạt'], stats['Không đạt'], stats['Lỗi thực thi'], stats['Bỏ qua'], stats['assertions']])
summary.append(['Tổng cộng', '=SUM(B9:B14)', '=SUM(C9:C14)', '=SUM(D9:D14)', '=SUM(E9:E14)', '=SUM(F9:F14)', '=SUM(G9:G14)'])

issues = wb.create_sheet('Ca khong dat')
issues.append(['Mã ca', 'Tên ca', 'Kỳ vọng trong ca hiện tại', 'Kết quả thực tế', 'Phân tích', 'Đề xuất xử lý', 'Trạng thái'])
for row in detail.iter_rows(min_row=2, values_only=True):
    if row[5] == 'Không đạt':
        issues.append([row[0], row[8], 'HTTP 200 khi gửi GET / không đăng nhập', 'HTTP 302', 'Trang chủ thuộc nhóm middleware auth; kỳ vọng của ca mẫu chưa phù hợp. Chỉ mã 302 chưa xác nhận đích chuyển hướng.', 'Đổi ca khách chưa đăng nhập sang kiểm tra chuyển đến login; thêm ca trang chủ với phiên và dữ liệu phù hợp.', 'Chưa sửa; chưa chạy lại sau sửa'])

notes = wb.create_sheet('Huong dan va nguon')
for row in [
    ['Mục', 'Thông tin'],
    ['Nguồn kết quả', str(base / 'kiem-thu-brewlearn.xml')],
    ['Lệnh đã chạy', 'php -d xdebug.mode=off vendor/phpunit/phpunit/phpunit --do-not-cache-result --log-junit docs/kiem-thu-brewlearn.xml'],
    ['Môi trường', 'PHP 8.3.14; Laravel 13.32.0; APP_ENV=testing; SQLite :memory:; session/cache array; queue sync.'],
    ['Số liệu', '60 ca; 59 đạt; 1 không đạt; 543 phép xác nhận; 0 lỗi thực thi; 0 bỏ qua.'],
    ['Ca nghiệp vụ', '4 lớp nghiệp vụ có 58 ca đều đạt. Một ca Unit mẫu đạt và một ca Feature mẫu không đạt.'],
    ['Bộ dữ liệu', 'Các ca có data set được giữ thành dòng riêng như JUnit; số # tương ứng định danh trong báo cáo gốc.'],
    ['Mô tả tiếng Việt', 'Tóm tắt mục tiêu từ tên ca; không phải kịch bản thao tác thủ công. Tên gốc, tệp và dòng được giữ để tra cứu.'],
    ['Giới hạn', 'Không suy rộng thành kiểm thử trình duyệt, tải, bảo mật toàn diện hoặc độ chính xác mô hình trên dữ liệu thật.'],
    ['Thời gian', 'Thời gian thực thi kiểm thử, không phải thời gian phản hồi trung bình của hệ thống.'],
    ['Xuất Excel', 'Chuyển đổi báo cáo đã lưu ngày 05/10/2026; không thay đổi kết quả hoặc chạy lại bộ kiểm thử.'],
]:
    notes.append(row)

def style_sheet(ws, header_row, widths, table_name=None):
    ws.freeze_panes = f'A{header_row + 1}'
    ws.sheet_view.showGridLines = False
    for row in ws:
        for cell in row:
            cell.font = Font(name='Calibri', size=11)
            cell.alignment = Alignment(vertical='top', wrap_text=True)
    for cell in ws[header_row]:
        cell.fill = PatternFill('solid', fgColor='4A2E1B')
        cell.font = Font(name='Calibri', size=11, bold=True, color='FFFFFF')
    ws.row_dimensions[header_row].height = 32
    for i, width in enumerate(widths, 1):
        ws.column_dimensions[get_column_letter(i)].width = width
    if table_name:
        table = Table(displayName=table_name, ref=f'A{header_row}:{get_column_letter(ws.max_column)}{ws.max_row}')
        table.tableStyleInfo = TableStyleInfo(name='TableStyleMedium2', showRowStripes=True)
        ws.add_table(table)
    ws.sheet_properties.pageSetUpPr.fitToPage = True
    ws.page_setup.orientation = 'landscape'
    ws.page_setup.paperSize = ws.PAPERSIZE_A4
    ws.page_setup.fitToWidth = 1
    ws.page_setup.fitToHeight = 0
    ws.print_title_rows = f'{header_row}:{header_row}'

style_sheet(summary, 8, [54, 16, 18, 22, 18, 18, 20])
summary['A1'].font = Font(name='Calibri', size=16, bold=True, color='4A2E1B')
for r in [3, 6]:
    summary.row_dimensions[r].height = 40
style_sheet(detail, 1, [12, 12, 38, 65, 24, 15, 17, 18, 70, 65, 10, 90], 'TestCases')
style_sheet(issues, 1, [12, 65, 45, 25, 70, 75, 40], 'FailedCases')
style_sheet(notes, 1, [26, 120])
for row in detail.iter_rows(min_row=2):
    row[7].number_format = '0.000000'
    if row[5].value == 'Không đạt':
        for cell in row:
            cell.fill = PatternFill('solid', fgColor='FCE4D6')
    else:
        row[5].fill = PatternFill('solid', fgColor='E2F0D9')

output = base / 'Kiem-thu-he-thong-BrewLearn.xlsx'
wb.save(output)
check = load_workbook(output)
assert check['Chi tiet 60 ca'].max_row == 61
assert sum(check['Chi tiet 60 ca'].cell(i, 6).value == 'Đạt' for i in range(2, 62)) == 59
assert sum(check['Chi tiet 60 ca'].cell(i, 7).value for i in range(2, 62)) == 543
assert check['Ca khong dat'].max_row == 2
print(f'Created: {output}; 4 sheets; 60 cases; 59 passed; 1 failed; {output.stat().st_size} bytes')
