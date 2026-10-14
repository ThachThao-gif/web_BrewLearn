# Bộ sơ đồ use case BrewLearn

Phạm vi gồm ba chức danh: Nhân viên, Trưởng ca và Quản lý cửa hàng. HR và Quản lý vùng không thuộc mô hình này. Các tệp ảnh có kích thước từ 3000 pixel, nền trắng, chữ Times New Roman; tệp Draw.io có sáu trang chỉnh sửa được.

## Cách sử dụng trong báo cáo

Đặt hình 01 ở phần mô hình use case tổng quát. Hình này thể hiện mục tiêu nghiệp vụ ở mức tổng quát, không phải danh sách đầy đủ mọi thao tác. Đặt các hình 02–06 ở phần phân tích chi tiết, cùng đặc tả use case tương ứng. Các hình chi tiết bổ sung cho hình tổng quát, không phải phân rã bắt buộc theo ký pháp UML. Tên use case được giữ ngắn, chỉ mô tả mục tiêu của tác nhân.

## Giải thích quan hệ UML

- Liên kết tác nhân–use case: nét liền, không có mũi tên; biểu diễn tương tác, không biểu diễn luồng dữ liệu hoặc thứ tự thao tác.
- `Đánh giá năng lực` → `Chấm điểm theo 5 tiêu chí`: `«include»`, vì mỗi lần đánh giá bắt buộc nhập các điểm này.
- `Đề xuất đào tạo lại` → `Đánh giá năng lực`: `«extend»` với điều kiện `[kết quả chưa đạt]`. Điểm mở rộng là xử lý kết quả chưa đạt trước khi gửi chờ phê duyệt. Khi điều kiện đúng, code yêu cầu chọn ít nhất một nội dung đề xuất.
- Nhân viên, Trưởng ca, Quản lý cửa hàng → Người dùng: kế thừa tác nhân ở hình chức năng chung. Người dùng là tác nhân tổng quát, không phải chức danh thứ tư. Không có quan hệ kế thừa giữa ba chức danh.
- Đăng nhập là tiền điều kiện của chức năng được bảo vệ; không gắn `«include» Đăng nhập` vào mọi use case.
- Duyệt, trả lại hoặc từ chối là các luồng của use case xét duyệt; không dùng `«extend»` để nối các bước liên tiếp.
- Đánh giá lại dùng cùng nghiệp vụ đánh giá năng lực. Quản lý tạo lịch, trưởng ca đánh giá; không dùng `«extend»` chỉ vì hoạt động xảy ra sau đào tạo.

Quy ước đối chiếu: [OMG UML 2.5.1, chương 18 — UseCases](https://www.omg.org/spec/UML/2.5.1/PDF).

## Căn cứ mã nguồn

| Nhóm nghiệp vụ | Căn cứ |
|---|---|
| Đăng nhập, đăng xuất | `routes/web.php`, `AuthController::login`, `logout` |
| Lộ trình, học bài, tiến độ, quiz và kết quả | `CourseController::dashboard`, `index`, `showLesson`, `completeLesson`, `testsIndex`, `showTest`, `submitTest` |
| Theo dõi nhân viên theo ca/cửa hàng | `LeaderEvaluationController::staffQuery`, `index`, `staffList` |
| Nhắc hạn, lập lịch, thông báo, lỗi nghiệp vụ | `LeaderEvaluationController::index`, `createSchedule`, `sendNotification`, `recordIncident` |
| Đánh giá, chấm điểm và đề xuất khi chưa đạt | `LeaderEvaluationController::saveScore`, `submitEvaluation` |
| Hồ sơ, báo cáo cửa hàng, ca và trạng thái làm việc | `ManagerController::dashboard`, `profile`, `createShift`, `assignShift`, `updateEmployment` |
| Phê duyệt hoặc trả lại đánh giá | `ManagerController::review` |
| Xét duyệt đề xuất, phân công, theo dõi, lịch đánh giá lại | `ManagerController::reviewTraining`, `assignTraining`, `training`, `reassess` |
| Người phụ trách xác nhận đào tạo | `TrainingCompletionController::index`, `confirm` |
| Tài khoản, thông báo, quy trình, chính sách, hỗ trợ | `UserController`, `NotificationController`, `HelpController`, các route `/sop`, `/policies` |

## Các ràng buộc cần ghi trong đặc tả

1. Kết quả đánh giá chỉ gửi cho nhân viên sau khi quản lý phê duyệt.
2. Kết quả đạt yêu cầu điểm tổng từ 80 và từng tiêu chí từ 70. Năm tiêu chí là nghiệp vụ, kỹ năng, quy trình, phục vụ và tác phong.
3. Nếu chưa đạt, trưởng ca phải nhập đề xuất xử lý. Phê duyệt đánh giá chưa đạt có thể tạo yêu cầu đào tạo lại đã duyệt; không bắt buộc mọi yêu cầu phải qua thêm bước duyệt đề xuất riêng.
4. Chỉ người được giao phụ trách vòng đào tạo mới được xác nhận hoàn thành. Người phụ trách có thể là nhân viên hoặc trưởng ca khác, không mặc định là người đang được đào tạo.
5. Lập lịch đánh giá lại phải đáp ứng điều kiện hoàn thành nội dung của vòng đào tạo. Thực hành ngoài phần mềm được biểu diễn bằng chức năng xác nhận, không bằng một use case giả định rằng phần mềm thực hiện đào tạo trực tiếp.
6. Quyền học của chức danh trưởng ca có thể dùng sơ đồ học tập khi họ tham gia với vai trò người học; không suy ra trưởng ca kế thừa toàn bộ quyền nhân viên chỉ từ cấp bậc.
7. Các chức năng HR, quản lý vùng, biên soạn khóa học, ngân hàng câu hỏi và quản trị bộ tiêu chuẩn không có trong bộ sơ đồ này.

## Đoạn mô tả tổng quát có thể đưa vào báo cáo

Hệ thống BrewLearn hỗ trợ đào tạo và theo dõi năng lực nhân viên tại cửa hàng. Nhân viên sử dụng hệ thống để học các nội dung được phân công, thực hiện bài kiểm tra và theo dõi kết quả cá nhân. Trưởng ca theo dõi nhân viên trong phạm vi phụ trách, ghi nhận lỗi nghiệp vụ, lập lịch và thực hiện đánh giá năng lực. Quản lý cửa hàng theo dõi tình hình đào tạo, quản lý ca làm việc, xét duyệt kết quả đánh giá và tổ chức đào tạo lại. Đối với nhân viên chưa đạt, trưởng ca đề xuất nội dung xử lý; quản lý xét duyệt, phân công người phụ trách và lập lịch đánh giá lại khi đủ điều kiện. Các tác nhân sử dụng chung các chức năng tài khoản, thông báo, tra cứu và gửi yêu cầu hỗ trợ.
