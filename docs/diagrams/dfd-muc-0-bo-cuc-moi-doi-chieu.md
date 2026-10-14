# Đối chiếu DFD mức 0 BrewLearn — bố cục mới

Sơ đồ có 3 tác nhân, 5 tiến trình, 5 kho logic, 36 luồng một chiều. Tên luồng được gom nhóm ở mức 0; không phải một luồng cho mỗi endpoint hay trường dữ liệu.

## Tiến trình và căn cứ mã nguồn

| Tiến trình | Phạm vi | Căn cứ |
|---|---|---|
| 1.0 Quản lý tài khoản, nhân sự và hỗ trợ | Đăng nhập/đăng xuất, hồ sơ, bảo mật, cài đặt, ca/phân ca, trạng thái làm việc, thông báo, nhắc việc/sự cố, hỗ trợ và tra cứu thông tin chung | AuthController, UserController, HelpController, NotificationController; ManagerController::createShift/assignShift/updateEmployment; LeaderEvaluationController::sendNotification/recordIncident; routes/web.php |
| 2.0 Quản lý học tập và kiểm tra | Khóa, bài học, quiz, tiến độ, lịch sử lượt kiểm tra; tự học của trưởng ca và theo dõi nhân viên; trang tiêu chuẩn năng lực là nội dung giao diện tĩnh | CourseController, CourseProgress, QuizProgress, UserController::getStaffDetail; resources/views/assessments/show.blade.php |
| 3.0 Quản lý đánh giá và đào tạo lại | Lập lịch, chấm 5 tiêu chí, đề xuất, duyệt/trả lại; duyệt yêu cầu chờ duyệt, phân công/cập nhật vòng, đặt lại tiến độ, xác nhận, tạo lịch đánh giá lại và lưu kết quả vòng | LeaderEvaluationController, ManagerController::review/reviewTraining/assignTraining/reassess; TrainingCompletionController; AssessmentEligibility |
| 4.0 Tổng hợp thông tin đào tạo | Tổng quan, danh sách/hồ sơ, tìm kiếm/lọc, kết quả đánh giá, lịch sử đào tạo lại, mức nguy cơ đã lưu | ManagerController::context/dashboard/staff/profile/training |
| 5.0 Dự đoán nguy cơ | Python chạy riêng; lấy nhân viên/chức danh và dữ liệu học tập/quiz, tính biến, ghi kết quả hoặc trạng thái chưa đủ dữ liệu | app/ml/ket_noi_db.py; app/ml/predict.py |

## Kho dữ liệu logic

| Kho | Dữ liệu đại diện |
|---|---|
| D1 Tài khoản, nhân sự | users; dữ liệu cửa hàng, ca_lam_viec, phân ca, trạng thái làm việc; thông tin xác thực/phiên và cài đặt tài khoản khi áp dụng |
| D2 Thông báo, hỗ trợ | notifications; yeu_cau_ho_tro. Ghi nhận sự cố trong xử lý trưởng ca hiện được lưu thành thông báo warning |
| D3 Đào tạo, kiểm tra | chuong_trinh_dao_tao; bai_hoc; bai_kiem_tra; cau_hoi_kiem_tra; user_lesson_progresses; nhan_vien_khoa_hoc; ket_qua_kiem_tra |
| D4 Đánh giá, đào tạo lại | danh_gia_nang_luc; yeu_cau_dao_tao_lai; retraining_rounds |
| D5 Dự đoán nguy cơ | du_doan_nguy_co |

D3 và D4 là các kho nhóm ở mức phân tích, không phải tên một bảng vật lý. Kho lặp cùng mã chỉ là một kho logic.

## Quy tắc bảo toàn

- Ba tác nhân ngoài là Nhân viên, Trưởng ca, Quản lý cửa hàng; Python thuộc phạm vi xử lý nên không thêm tác nhân thứ tư.
- Nhân viên nhận lịch, kết quả đã duyệt và phân công thông qua thông báo: 3.0 → D2 → 1.0 → Nhân viên.
- Trưởng ca gửi xác nhận hoàn thành theo nhiệm vụ được giao; xác nhận không thay thế chấm điểm và phê duyệt.
- Tiến trình 3.0 ghi lịch sử vòng và trạng thái phê duyệt vào D4. Yêu cầu chỉ hoàn tất khi đánh giá lại Đạt được quản lý duyệt.
- Python không đọc điểm đánh giá làm đầu vào. Web đọc D5; không có luồng kích hoạt dự đoán từ thao tác mở trang.
- Sơ đồ diễn tả luồng nghiệp vụ; không khẳng định việc kiểm soát mọi quyền trong mã hiện tại đã hoàn thiện.

## Kiểm tra trình bày

Đã kiểm tra giao cắt giữa các đoạn nối vuông góc, đường xuyên qua ô không liên quan, nhãn che luồng khác và nhãn chồng nhau. Đã xuất SVG/PNG để kiểm tra hình. XML dùng cùng tọa độ cố định với SVG; các nhãn là ô chữ độc lập để draw.io không tự dời khi mở.

Bản mới gom nhóm tiến trình khác bản cũ, vì vậy khi đưa vào báo cáo cần cập nhật tên và số tiến trình/kho trong phần thuyết minh. Cần đối chiếu lại tên các nhóm luồng của DFD ngữ cảnh với bảng trên; không tuyên bố đã cân bằng đầy đủ nếu chưa rà từng nhóm luồng ngữ cảnh.
