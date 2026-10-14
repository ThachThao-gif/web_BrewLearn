# 1.4 Phạm vi dữ liệu và giới hạn

## 1.4.1 Phạm vi không gian

Đề tài được xây dựng trên bối cảnh giả định của chuỗi cửa hàng cà phê BrewLearn. Bối cảnh này phục vụ việc xác định yêu cầu, phân tích, thiết kế và minh họa hệ thống, không phải kết quả khảo sát tại một doanh nghiệp cụ thể.

Phạm vi hệ thống đề xuất gồm ba nhóm người dùng: nhân viên, trưởng ca và quản lý cửa hàng. Nhóm nhân viên bao gồm các vị trí nghiệp vụ như pha chế, thu ngân và phục vụ. Quy trình hiện trạng AS-IS có sự tham gia của bộ phận nhân sự (HR), nhưng phạm vi hệ thống TO-BE được giới hạn ở ba nhóm người dùng nêu trên; HR không được thiết kế thành một vai trò sử dụng riêng trong đồ án.

## 1.4.2 Phạm vi thời gian và dữ liệu

Thời gian thực hiện đề tài từ ngày 07/09/2026 đến ngày 14/11/2026. Các nội dung được giới hạn theo thời gian thực hiện, tập trung vào phân tích, thiết kế, giao diện minh họa và thử nghiệm mô hình dự đoán.

Dữ liệu dùng để huấn luyện và đánh giá mô hình dự đoán là dữ liệu giả lập. Dữ liệu trên nguyên mẫu phục vụ minh họa các quy trình nghiệp vụ, chưa đại diện cho quá trình vận hành thực tế được khảo sát dài hạn. Vì vậy, kết quả thử nghiệm mô hình chỉ có ý nghĩa trong phạm vi bộ dữ liệu mô phỏng, chưa chứng minh hiệu quả áp dụng tại doanh nghiệp.

Mô hình hiện tại xử lý các thông tin học tập và kiểm tra tại thời điểm tính toán để ước lượng nguy cơ chưa đạt chuẩn năng lực. Đề tài chưa có cơ sở thực nghiệm để khẳng định khả năng dự báo kết quả năng lực sau một hoặc ba tháng làm việc thực tế.

## 1.4.3 Phạm vi chức năng

Phạm vi chức năng tập trung vào quy trình học tập, kiểm tra, đánh giá năng lực, phê duyệt và đào tạo lại. Các chức năng phân ca, tài khoản, thông báo, tra cứu và hỗ trợ được trình bày ngắn như các chức năng hỗ trợ quy trình chính.

**Đối với nhân viên:**

- Xem lộ trình và nội dung đào tạo theo chức danh; học bài và theo dõi tiến độ.
- Thực hiện bài kiểm tra và xem kết quả kiểm tra.
- Theo dõi lịch đánh giá và kết quả đánh giá đã được quản lý phê duyệt.
- Học lại các nội dung được phân công trong kế hoạch đào tạo lại.
- Sử dụng các chức năng hỗ trợ về tài khoản, thông báo, tra cứu và gửi yêu cầu hỗ trợ.

**Đối với trưởng ca:**

- Tự học và làm bài kiểm tra theo nội dung áp dụng cho chức danh; không thuộc đối tượng đánh giá năng lực nhân viên trong phạm vi đề tài.
- Theo dõi tình hình học tập và đánh giá của nhân viên trong phạm vi phụ trách.
- Lập lịch đánh giá khi nhân viên đáp ứng điều kiện; chấm điểm, nhập nhận xét và đề xuất đào tạo lại khi chưa đạt.
- Bổ sung kết quả đánh giá bị trả lại và gửi lại để quản lý phê duyệt.
- Thực hiện nhiệm vụ đào tạo lại và xác nhận hoàn thành nội dung khi được phân công phụ trách.
- Sử dụng các chức năng hỗ trợ như ghi nhận lỗi nghiệp vụ và gửi thông báo nhắc việc.

**Đối với quản lý cửa hàng:**

- Xem tổng quan, hồ sơ và tình hình đào tạo của nhân viên tại cửa hàng.
- Phê duyệt hoặc trả lại kết quả đánh giá kèm lý do; việc phê duyệt áp dụng cho cả đánh giá lần đầu và đánh giá lại.
- Xem xét đề xuất đào tạo lại, giao kế hoạch, phân công người phụ trách và theo dõi lịch sử các vòng đào tạo lại.
- Lập lịch đánh giá lại khi đáp ứng điều kiện; xác nhận kết quả thông qua phê duyệt và kết thúc yêu cầu khi nhân viên đạt.
- Xem xu hướng kết quả đánh giá đã duyệt và thông tin nguy cơ dự đoán đã lưu.
- Sử dụng chức năng phân ca và cập nhật trạng thái làm việc như các chức năng hỗ trợ quản lý đào tạo.

## 1.4.4 Giới hạn của đề tài

Sản phẩm chính của đồ án là hồ sơ phân tích, thiết kế hệ thống, thiết kế giao diện và thử nghiệm mô hình dự đoán bằng Python trên dữ liệu giả lập. Website đã xây dựng được sử dụng để minh họa thiết kế; đồ án không đặt yêu cầu hoàn thiện toàn bộ hệ thống vận hành, triển khai công khai hoặc tổ chức kiểm thử toàn hệ thống thành một nội dung riêng.

Đề tài không triển khai quản lý tiền lương, chấm công hoặc toàn bộ hoạt động quản trị nhân sự. Nội dung chương trình, bài học và câu hỏi kiểm tra được chuẩn bị trong cơ sở dữ liệu; chức năng biên soạn và xuất bản nội dung trên giao diện web chưa thuộc phạm vi hiện tại.

Chương trình dự đoán chạy riêng và lưu kết quả để ứng dụng web đọc, chưa được cấu hình lịch chạy tự động. Kết quả dự đoán chỉ hỗ trợ tham khảo, không tự phê duyệt đánh giá, không tự phân công đào tạo lại và không được gộp thành điểm đánh giá năng lực.

Nhãn huấn luyện hiện tại được tạo theo quy tắc của dữ liệu giả lập. Khi phát triển với dữ liệu thực tế, nhãn phải được xác định từ kết quả đánh giá đã được quản lý phê duyệt; không sử dụng đánh giá đang chờ duyệt hoặc bị trả lại làm nhãn chính thức. Các biến đầu vào cần được xác định trước thời điểm của kết quả dùng làm nhãn để hạn chế rò rỉ dữ liệu.

Dữ liệu giả lập hiện sử dụng ngưỡng cố định 80 khi tạo số lượt kiểm tra không đạt, trong khi dữ liệu nghiệp vụ sử dụng ngưỡng DiemChuan riêng của từng bài kiểm tra. Đây là giới hạn của dữ liệu mô phỏng, cần được điều chỉnh và đánh giá lại khi có dữ liệu thực tế.
