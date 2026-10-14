# Đối chiếu cảnh báo sau kiểm tra dữ liệu

## Phiên bản rules-v2

- Đối tượng được phép: Barista, Thu ngân, Phục vụ đang làm và có cửa hàng.
  Tài khoản NULL, Admin, trưởng ca, quản lý, HR, Thực Tập Sinh không thuộc đối tượng.
- Công thức điểm giữ nguyên: tỷ lệ bài chưa đạt >0 đến <0,5 được 1 điểm.
- Laravel hiện sử dụng UTC. MySQL mặc định SYSTEM tại UTC+7. Chương trình
  lấy `UTC_TIMESTAMP()` một lần cho mỗi nhân viên, dùng giá trị đó để lọc
  kết quả quiz và lưu `ngay_du_doan`. Không thay múi giờ toàn ứng dụng hoặc
  diễn giải lại dữ liệu cũ. Giao diện rules-v2 chuyển UTC sang giờ Việt Nam.
- rules-v1 được giữ trong lịch sử. Giao diện mặc định chỉ đọc rules-v2,
  tránh bản ghi v1 mang giờ địa phương bị coi là mới hơn bản ghi v2 UTC.

## Kết quả kiểm tra cơ sở dữ liệu

- Không phát hiện nhiều dòng cho cùng `(user_id, MaCTDT)`.
- Bảng tiến độ hiện chỉ có khóa chính id, chưa có UNIQUE cho cặp trên.
  Nếu phát sinh trùng, chương trình ghi lỗi và bỏ qua nhân viên thay vì tự
  chọn một dòng hoặc tính trùng. Không dùng bảng này làm lịch sử từng vòng;
  lịch sử vòng nằm ở retraining_rounds.
- 66 kết quả quiz: 48 Dat, 18 KhongDat; không có điểm NULL hoặc created_at NULL.
- Kiểm tra ghi thử thời gian bằng `now()` của Laravel trong transaction và
  rollback: created_at trùng UTC_TIMESTAMP() tại thời điểm kiểm tra.
  Đây là kiểm tra quy ước ghi thời gian, không phải thao tác nộp bài qua UI.
- Sau chạy v2: 13 nhân viên đối tượng, 13 kết quả, 0 thiếu dữ liệu, 0 lỗi.
  Có 10 Thấp và 3 Cao. Tài khoản Thực Tập Sinh trước đây thuộc nhóm thiếu
  dữ liệu đã được loại khỏi đối tượng. Không tạo dữ liệu giả để có đủ năm nhóm.

## Nội dung nên cập nhật trong luận

Thời điểm tính được xác định một lần khi bắt đầu xử lý cảnh báo cho mỗi nhân
viên, theo UTC của ứng dụng và được lưu tại ngay_du_doan. Giao diện hiển thị
thời điểm theo giờ Việt Nam. Hai nhân viên có thể cùng thời điểm đến giây
do được xử lý trong cùng một giây; không có nghĩa dùng chung thời điểm cả lần chạy.

chi_tiet_yeu_to lưu progress_percent, avg_quiz_score, quiz_attempts,
failed_rate, learning_visits, completed_quiz_count, applicable_quiz_count,
applicable_course_count, failed_quiz_count, co_du_lieu_quiz,
du_dieu_kien_du_doan, missing_reasons, calculated_at và selected_results.
Mỗi phần tử selected_results gồm result_id, test_id, score, pass_score,
created_at. Khi đủ dữ liệu, JSON có thêm details gồm tong_diem,
diem_thanh_phan và ly_do_canh_bao. Khi thiếu dữ liệu, không có tổng điểm
hoặc điểm thành phần; diem_rui_ro bằng NULL.

Các lỗi dữ liệu, truy vấn hoặc nhân viên không còn đủ điều kiện được tính
vào errors; chương trình tiếp tục xử lý các nhân viên khác và trả mã thoát
1 nếu có lỗi. calculated, insufficient và errors cộng lại bằng số tài khoản
trong danh sách đầu lần chạy. Không có lịch chạy tự động.

Bảng luật dùng bậc >0 đến <0,50 = 1 điểm; các bảng kiểm thử đối chiếu với
tests/python/test_risk_rules.py. Bảng kỳ vọng không thay cho kết quả chạy test.
