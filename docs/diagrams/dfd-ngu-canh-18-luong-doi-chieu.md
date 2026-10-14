# DFD ngữ cảnh BrewLearn — bản 18 luồng

Bản này thay thế bản 6 luồng để thể hiện rõ hơn nghiệp vụ, giữ nguyên ranh giới ba tác nhân của mức 0.

## Bảng cân bằng

Số luồng mức 0 tham chiếu **dfd-muc-0-brewlearn-danh-so.xml**, bản 5 tiến trình/5 kho logic/36 luồng.

| Luồng ngữ cảnh | Chiều | Luồng mức 0 | Nội dung |
|---|---|---|---|
| 1 | Nhân viên → Hệ thống | 1 | Đăng nhập/đăng xuất, hồ sơ, bảo mật, cài đặt, thao tác thông báo, tra cứu quy trình/chính sách, yêu cầu hỗ trợ. |
| 2 | Nhân viên → Hệ thống | 7 | Tra cứu khóa, bài, quiz, trang tiêu chuẩn; ghi tiến độ, đáp án của lượt thi/làm lại, bao gồm nội dung học lại được giao. |
| 3 | Hệ thống → Nhân viên | 2 (phần chung) | Xác thực/cập nhật tài khoản, quy trình/chính sách, thông báo chung và kết quả gửi/lịch sử hỗ trợ. |
| 4 | Hệ thống → Nhân viên | 2 (phần thông báo đánh giá/đào tạo lại) | Thông báo lịch, kết quả đánh giá đã duyệt, nội dung phân công/hạn/người phụ trách đào tạo lại. Luồng nội bộ mức 0: 3.0 → D2 → 1.0. |
| 5 | Hệ thống → Nhân viên | 8 | Khóa, bài, quiz, nội dung trang tiêu chuẩn năng lực, tiến độ, điểm và lịch sử kiểm tra. |
| 6 | Trưởng ca → Hệ thống | 5, 9 | Tài khoản/cài đặt, thao tác thông báo, hỗ trợ; tự học/quiz, yêu cầu theo dõi nhân viên; nội dung gửi nhắc việc và ghi nhận sự cố. |
| 7 | Trưởng ca → Hệ thống | 11 (phần đánh giá) | Lập lịch ban đầu, điểm năm tiêu chí, nhận xét, đề xuất xử lý; bổ sung và gửi lại đánh giá bị trả lại. Đánh giá lại dùng cùng cơ chế chấm. |
| 8 | Trưởng ca → Hệ thống | 11 (phần nhiệm vụ đào tạo lại) | Yêu cầu xem vòng được giao, xác nhận hoàn thành và ghi chú. Người xác nhận phải đúng người được phân công. |
| 9 | Hệ thống → Trưởng ca | 6, 10 (phần tự học) | Kết quả tài khoản/hỗ trợ, thông tin chung và thông báo; nội dung, tiến độ và kết quả tự học. |
| 10 | Hệ thống → Trưởng ca | 10 (phần theo dõi) | Danh sách và tình trạng học tập/quiz của nhân viên trong phạm vi phụ trách. |
| 11 | Hệ thống → Trưởng ca | 12 (phần đánh giá) | Lịch, hồ sơ, điều kiện đánh giá, trạng thái chờ duyệt/đã duyệt/trả lại và nội dung cần bổ sung. |
| 12 | Hệ thống → Trưởng ca | 12 (phần đào tạo lại) | Nội dung, thời hạn, lịch sử vòng được giao, điều kiện học/quiz và thông tin xác nhận. |
| 13 | Quản lý → Hệ thống | 3 | Tài khoản/cài đặt, tạo ca/phân ca/trạng thái làm việc, tra cứu thông tin chung, thao tác thông báo và yêu cầu hỗ trợ. |
| 14 | Quản lý → Hệ thống | 13 (phần xét duyệt) | Duyệt/trả lại đánh giá và lý do; duyệt/từ chối yêu cầu đào tạo lại đang chờ duyệt. Yêu cầu đã duyệt qua đánh giá không phải duyệt lần hai. |
| 15 | Quản lý → Hệ thống | 13 (phần phân công/lịch), 15 | Tìm kiếm/lọc, xem hồ sơ/thống kê/nguy cơ; phân công/cập nhật vòng và tạo lịch đánh giá lại. |
| 16 | Hệ thống → Quản lý | 4 | Kết quả xác thực/cập nhật, ca/phân ca/trạng thái làm việc, thông báo và kết quả gửi/lịch sử hỗ trợ. |
| 17 | Hệ thống → Quản lý | 14, 16 (phần đánh giá/đào tạo lại) | Hồ sơ đánh giá, nội dung đề xuất, điều kiện đánh giá lại, kế hoạch và lịch sử vòng; kết quả duyệt, phân công và lập lịch. |
| 18 | Hệ thống → Quản lý | 16 (phần tổng hợp) | Hồ sơ học/quiz, thống kê đào tạo, mức nguy cơ đã lưu và thời điểm dự đoán. Không kích hoạt Python khi xem trang. |

Việc chia một luồng tổng hợp ở mức 0 thành nhiều luồng ngữ cảnh là cách trình bày các thành phần dữ liệu, không phát sinh chức năng mới. Ví dụ luồng 2 mức 0 được chia thành luồng 3 và 4 ngữ cảnh. Toàn bộ 16 luồng biên mức 0 đều được bao phủ, giữ nguyên chiều và tác nhân; 20 luồng với kho dữ liệu là nội bộ nên không vẽ ở ngữ cảnh.

## Căn cứ mã nguồn đã đối chiếu

- routes/web.php; AuthController, UserController, HelpController, NotificationController: tài khoản, hỗ trợ, thông báo.
- CourseController; CourseProgress; QuizProgress: nội dung học, tiến độ, quiz và lịch sử.
- LeaderEvaluationController: tạo lịch, năm điểm thành phần, đề xuất, bổ sung trả lại, theo dõi nhân viên, nhắc việc và sự cố.
- ManagerController::review/reviewTraining/assignTraining/reassess: phê duyệt, yêu cầu đào tạo lại, vòng được giao và lịch đánh giá lại.
- TrainingCompletionController: danh sách nhiệm vụ của người được giao; xác nhận theo điều kiện vòng.
- ManagerController::context/dashboard/staff/profile: hồ sơ, thống kê, kết quả nguy cơ đã lưu.
- app/ml/ket_noi_db.py: Python chạy riêng, lấy dữ liệu học tập/quiz và thông tin nhân sự, ghi du_doan_nguy_co; không dùng điểm đánh giá chính thức làm đầu vào.

## Quy tắc cần giữ trong thuyết minh

- Kết quả đánh giá chỉ chính thức sau khi quản lý duyệt.
- Đánh giá Chưa đạt cần đề xuất; hồ sơ trả lại được bổ sung, không tự tạo vòng mới.
- Trưởng ca phụ trách xác nhận hoàn thành nội dung vòng; quản lý lập lịch đánh giá lại.
- Chỉ Đạt đã duyệt mới kết thúc yêu cầu đào tạo lại.
- Không tự sinh quyết định đào tạo lại từ nguy cơ dự đoán.
- Python là thành phần xử lý bên trong phạm vi hệ thống, không thêm tác nhân thứ tư.
- Ngữ cảnh không hiển thị kho dữ liệu; số 1–18 là mã tham chiếu, không phải thứ tự thực hiện.
- Nội dung trang tiêu chuẩn hiện có chỗ chưa đồng bộ quy tắc chấm; sơ đồ thể hiện luồng tra cứu, không coi nội dung trang là nguồn chấm điểm.
- Quyền phụ trách trưởng ca là quy tắc đã chốt; mã phân công hiện còn cho phép rộng hơn và cần hoàn thiện riêng, không khẳng định đã sửa quyền trong tác vụ vẽ này.

## Kiểm tra hình

XML và SVG dùng cùng tọa độ cố định. Đã kiểm tra đường không giao cắt và nhãn không che đường khác; xuất PNG để kiểm tra hình thực tế.
