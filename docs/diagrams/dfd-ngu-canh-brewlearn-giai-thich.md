# DFD ngữ cảnh BrewLearn — giải thích và cân bằng với mức 0

Hệ thống được xem là một tiến trình 0, với ba tác nhân ngoài: Nhân viên, Trưởng ca, Quản lý cửa hàng. Không vẽ kho dữ liệu ở mức ngữ cảnh. Python thuộc phạm vi hệ thống, chạy riêng và ghi kết quả để web đọc; không thêm tác nhân Python.

## Các luồng tổng hợp

| Số | Nguồn → Đích | Thành phần dữ liệu | Luồng tương ứng ở mức 0 |
|---|---|---|---|
| 1 | Nhân viên → Hệ thống | Thông tin đăng nhập/đăng xuất, hồ sơ, mật khẩu, cài đặt; yêu cầu tra cứu khóa, bài học, tiêu chuẩn, quy trình/chính sách; dữ liệu tiến độ, đáp án các lượt quiz; yêu cầu hỗ trợ và thao tác thông báo cá nhân | 1, 7 |
| 2 | Hệ thống → Nhân viên | Kết quả xác thực và cập nhật; nội dung khóa/bài/quiz/tiêu chuẩn/quy trình/chính sách; tiến độ, điểm và lịch sử quiz; thông báo lịch đánh giá, kết quả đã duyệt, kế hoạch đào tạo lại; kết quả gửi và lịch sử hỗ trợ | 2, 8 |
| 3 | Trưởng ca → Hệ thống | Thông tin tài khoản/cài đặt; yêu cầu và dữ liệu tự học/quiz; yêu cầu theo dõi nhân viên; dữ liệu lập lịch, năm điểm, nhận xét, đề xuất và bổ sung đánh giá trả lại; yêu cầu xem nhiệm vụ và xác nhận đào tạo lại; nội dung nhắc việc/sự cố; hỗ trợ và thao tác thông báo cá nhân | 5, 9, 11 |
| 4 | Hệ thống → Trưởng ca | Kết quả tài khoản và hỗ trợ; nội dung, tiến độ và kết quả tự học; thông tin theo dõi nhân viên; lịch, hồ sơ và trạng thái đánh giá; nhiệm vụ đào tạo lại được giao, điều kiện học/quiz và kết quả xác nhận; thông báo nhắc việc, phê duyệt/trả lại | 6, 10, 12 |
| 5 | Quản lý cửa hàng → Hệ thống | Thông tin tài khoản/cài đặt; dữ liệu tạo ca, phân ca, trạng thái làm việc; yêu cầu tra cứu/lọc và tổng hợp; quyết định duyệt/trả lại đánh giá và lý do, duyệt/từ chối đề xuất đang chờ duyệt; dữ liệu phân công/cập nhật vòng và lập lịch đánh giá lại; yêu cầu hỗ trợ và thao tác thông báo cá nhân | 3, 13, 15 |
| 6 | Hệ thống → Quản lý cửa hàng | Kết quả tài khoản, nhân sự và hỗ trợ; hồ sơ học tập/quiz; thông tin đánh giá, kế hoạch và lịch sử vòng; kết quả xử lý duyệt/phân công/lịch; thống kê đào tạo; nguy cơ đã lưu và thời điểm dự đoán; thông báo | 4, 14, 16 |

Các số luồng ở cột cuối tham chiếu file dfd-muc-0-brewlearn-danh-so.xml (bố cục mới gồm 5 tiến trình, 5 kho logic). Không tham chiếu các bản mức 0 cũ gồm 6 tiến trình/7 kho.

## Nguyên tắc cân bằng

- Cùng ba tác nhân và cùng ranh giới hệ thống.
- Mỗi luồng ngữ cảnh là hợp của các luồng biên mức 0 ở cột cuối, giữ nguyên chiều vào/ra.
- Luồng nội bộ tới kho dữ liệu không xuất hiện ở ngữ cảnh.
- Các chức năng chung về hỗ trợ/thông báo được gom trong nhãn ngắn “thông tin tài khoản”/“thông báo”, nhưng thành phần đầy đủ được nêu trong bảng.
- Nguy cơ được đọc từ kết quả đã lưu, không thay thế đánh giá chính thức và không tự tạo quyết định đào tạo lại.
- Số 1–6 là mã tham chiếu, không chỉ thứ tự thực hiện.

Đã kiểm tra cấu trúc: 3 tác nhân, 1 tiến trình, 6 luồng có tên, không có kho dữ liệu hoặc đường tác nhân–tác nhân. Bố cục các đường nối cố định, không giao cắt.
