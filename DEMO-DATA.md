# Bộ dữ liệu demo BrewLearn

## Dữ liệu chụp báo cáo ngày 12/10/2026

Đã chạy `php artisan brewlearn:compact-report-demo`: giảm đánh giá từ 38 xuống 11, yêu cầu đào tạo lại từ 7 xuống 5, vòng đào tạo lại từ 18 xuống 5. Mỗi nhân viên chỉ có một yêu cầu; tối đa 2 vòng. Giữ lịch sử ban đầu và kết quả đánh giá lại cần thiết để giải thích quy trình. Các hoạt động nhập trên web được giữ nguyên.

Trần Hải Yến hoàn thành sau 1 vòng; Lê Đức Hưng hoàn thành sau 2 vòng; Đặng Anh Khoa đang thực hiện vòng 1. Nguyễn Văn Nam chờ giao kế hoạch, Lê Hoàng Anh đang thực hiện kế hoạch có sẵn. Lệnh bổ sung demo cũng đã sửa để chỉ tạo các tình huống ngắn, tối đa 2 vòng, thay vì lặp 6 đánh giá mỗi nhân viên.

Lệnh thu gọn sao lưu trước khi cập nhật, chạy trong transaction và chỉ xử lý tài khoản `operations1` đến `operations6` của cửa hàng demo. Bản sao trước khi chỉnh: `storage/app/private/demo-backups/report-20261012-064801-OJq1Yj.json`.

Đã chuẩn hóa ngày 02/10/2026 cho cửa hàng Flagship. Dữ liệu nằm trong database; không có bước tự khôi phục dữ liệu khi đăng nhập.

## Phân ca

| Ca | Trưởng ca | Nhân viên đang làm |
| --- | --- | --- |
| Sáng, 07:00–14:00 | Trần Minh Thư | Nguyễn Văn Nam, Lê Hoàng Anh, Nguyễn Ngọc Lan, Lê Quốc Tuấn |
| Chiều, 14:00–22:00 | Võ Văn Thành | Phạm Thị Mai, Nguyễn Hoàng Long, Bùi Thảo Vân, Trần Gia Huy |

Ngọc Bích quản lý toàn cửa hàng. Phạm Thu Hà tạm ngưng ở ca sáng; Đỗ Anh Dũng nghỉ việc ở ca chiều. Hai tài khoản này có lịch sử học nhưng không được tính vào nhắc hạn hoặc chạy dự đoán.

## Các hạn đánh giá lần đầu

Chưa có đánh giá năng lực trước đó; hạn được tính từ ngày vào làm + 3 tháng lịch.

| Nhân viên | Ngày vào làm | Hạn đầu tiên | Trạng thái ngày 02/10/2026 |
| --- | --- | --- | --- |
| Nguyễn Văn Nam | 05/07/2026 | 05/10/2026 | Sắp đến hạn |
| Lê Hoàng Anh | 02/07/2026 | 02/10/2026 | Đến hạn |
| Lê Quốc Tuấn | 22/06/2026 | 22/09/2026 | Quá hạn |
| Phạm Thị Mai | 29/06/2026 | 29/09/2026 | Quá hạn |
| Trần Gia Huy | 08/07/2026 | 08/10/2026 | Sắp đến hạn |
| Nguyễn Hoàng Long | 20/07/2026 | 20/10/2026 | Chưa đến hạn |
| Nguyễn Ngọc Lan | 02/08/2026 | 02/11/2026 | Chưa đến hạn |
| Bùi Thảo Vân | 11/09/2026 | 11/12/2026 | Chưa đến hạn |

Trạng thái tự thay đổi theo ngày thực tế. Sau khi có đánh giá được duyệt, hệ thống tính chu kỳ tiếp theo từ ngày đánh giá đó.

## Học tập và dự đoán

Mỗi tài khoản có dữ liệu tiến độ từng bài học thuộc khóa được phân công theo vị trí, có số lượt học và số bài hoàn thành. Các khóa tổng hợp tiến độ từ dữ liệu từng bài qua `CourseProgress`.

Các tài khoản có lịch sử quiz với số câu đúng, tổng câu, điểm và kết quả đạt dựa trên ngưỡng của bài kiểm tra. Bùi Thảo Vân có dữ liệu học nhưng chưa thi quiz, minh họa trường hợp chưa đủ dữ liệu dự đoán.

Đã chạy model thật từ dữ liệu mới: Phạm Thị Mai và Lê Quốc Tuấn nguy cơ cao; Lê Hoàng Anh trung bình; Nam, Long, Lan, Huy thấp; Thảo Vân chưa đủ dữ liệu. Không tạo điểm rủi ro bằng tay. Trưởng ca và quản lý không thuộc đối tượng dự đoán.

Khi mở bài học hoặc nộp bài kiểm tra trên web, dữ liệu tài khoản đang đăng nhập được cập nhật trực tiếp vào database. Quiz mới được thêm thành lượt thi riêng. Dự đoán là ảnh chụp tại thời điểm chạy model; chạy lại model để cập nhật từ hoạt động mới.

## Đào tạo lại nhiều vòng

Trước khi trưởng ca tạo lịch đánh giá lần đầu, nhân viên phải hoàn thành các bài học được phân công và đạt từng quiz theo ngưỡng của bài. Nhắc hạn vẫn hiện nếu chưa đủ điều kiện; danh sách ghi rõ bài học và quiz còn thiếu.

Khi quản lý giao học lại bài liên quan trong một vòng, tiến độ các bài được chọn được đặt lại (trạng thái cũ lưu trong vòng); các quiz liên quan cần có lượt thi đạt mới sau lúc giao vòng đó. Không xóa lịch sử quiz hoặc đặt lại bài ngoài kế hoạch. Nội dung thực hành / theo dõi cần người phụ trách đăng nhập vào **Đào tạo lại tôi phụ trách** để xác nhận. Chỉ sau khi đủ các điều kiện này, quản lý mới tạo lịch đánh giá lại. Nếu đổi người phụ trách hoặc nội dung thực hiện, phải xác nhận lại.

Trong mục Giao đào tạo lại, quản lý giao vòng đầu tiên và tạo lịch đánh giá lại sau khi thực hiện. Mỗi vòng được lưu trong `retraining_rounds`, có số lần, kế hoạch, người phụ trách, thời hạn, đánh giá và kết quả duyệt riêng. Cập nhật kế hoạch đang thực hiện vẫn thuộc cùng vòng.

Nếu kết quả đánh giá lại chưa đạt được quản lý duyệt, vòng đó được giữ trong lịch sử và yêu cầu chuyển sang chờ giao vòng tiếp theo. Quản lý cần nhập nội dung và thời hạn mới; không tự động giao lại kế hoạch cũ. Chỉ khi kết quả Đạt được quản lý duyệt thì yêu cầu hoàn thành và không thể giao thêm vòng. Trả lại đánh giá để bổ sung không tạo vòng mới và không đóng yêu cầu.

Lịch sử các vòng xuất hiện trong màn hình Giao đào tạo lại và hồ sơ nhân viên; số lần cũng xuất hiện trong lịch đánh giá và thông báo.

## Tài khoản bổ sung

Mật khẩu các tài khoản có sẵn được giữ nguyên. Các tài khoản mới dùng mật khẩu `Demo@2026`:

| Email | Nhân viên | Trạng thái |
| --- | --- | --- |
| lan.demo@brewlearn.com | Nguyễn Ngọc Lan | Đang làm |
| tuan.demo@brewlearn.com | Lê Quốc Tuấn | Đang làm |
| huy.demo@brewlearn.com | Trần Gia Huy | Đang làm |
| ha.demo@brewlearn.com | Phạm Thu Hà | Tạm ngưng |
| dung.demo@brewlearn.com | Đỗ Anh Dũng | Nghỉ việc |

## Khôi phục bộ minh họa

`php artisan brewlearn:prepare-demo` chỉ chạy khi muốn xóa hoạt động demo và tạo lại bộ dữ liệu. Lệnh sao lưu các bảng liên quan trong `storage/app/private/demo-backups` trước khi thay đổi và thực hiện cập nhật trong transaction. Không chạy lệnh này tự động: nó xóa đánh giá, yêu cầu đào tạo, thông báo, dự đoán, tiến độ và quiz thử nghiệm của cửa hàng rồi tạo dữ liệu học/quiz mới. Hạn đánh giá mẫu được bố trí tương đối theo ngày chạy lệnh.

Sau đó chạy `venv\Scripts\python.exe -X utf8 app/ml/ket_noi_db.py` và `php artisan brewlearn:remind-assessments --store=1` để cập nhật dự đoán và thông báo đúng dữ liệu mới.
