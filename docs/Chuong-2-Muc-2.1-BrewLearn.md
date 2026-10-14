# CHƯƠNG 2 PHÂN TÍCH HỆ THỐNG

## 2.1 Bối cảnh và quy trình hiện trạng AS-IS

### 2.1.1 Bối cảnh và các bên tham gia

BrewLearn là bối cảnh giả định của một chuỗi cửa hàng cà phê, được sử dụng để phân tích bài toán quản lý đào tạo nhân viên. Các vị trí nghiệp vụ được xem xét gồm pha chế, thu ngân và phục vụ. Đào tạo áp dụng cho cả nhân viên mới và nhân viên đang làm việc cần bổ sung kiến thức hoặc cải thiện kỹ năng. Quy trình không giới hạn trong giai đoạn thử việc và không lấy quyết định tuyển dụng làm kết quả đầu ra.

Quy trình hiện trạng AS-IS trong đề tài được mô tả theo cách tổ chức công việc trước khi áp dụng hệ thống quản lý đào tạo tập trung. Thông tin được trao đổi trực tiếp hoặc qua các công cụ riêng lẻ; tài liệu hướng dẫn, kết quả kiểm tra, nhận xét và quyết định xử lý chưa được liên kết thành một hồ sơ đào tạo thống nhất. Đây là mô hình hiện trạng được xây dựng phục vụ đồ án, không phải kết quả khảo sát, phỏng vấn hoặc quan sát tại một doanh nghiệp cụ thể.

Quy trình có sự tham gia của bốn bên: bộ phận nhân sự (HR), nhân viên, ca trưởng và quản lý cửa hàng. Trách nhiệm của từng bên được xác định theo sơ đồ AS-IS như sau:

**Bộ phận nhân sự (HR):** xác định nhân viên cần đào tạo, bao gồm nhân viên mới hoặc nhân viên đang làm việc, làm đầu vào cho việc tổ chức đào tạo tại cửa hàng. Các nghiệp vụ tuyển dụng, hợp đồng và tiền lương không được phân tích chi tiết trong đề tài.

**Nhân viên:** học và thực hành theo nội dung được hướng dẫn; tiếp tục thực hiện nội dung bổ sung khi kết quả chưa đáp ứng yêu cầu. Nhóm này bao gồm nhân viên mới và nhân viên đang làm việc tại các vị trí thuộc phạm vi bài toán.

**Ca trưởng:** hướng dẫn trực tiếp, cung cấp tài liệu nghiệp vụ, quan sát quá trình thực hiện, ghi nhận kết quả và đề xuất xử lý để quản lý cửa hàng xem xét.

**Quản lý cửa hàng:** xác định nội dung đào tạo, phân công người hướng dẫn và xem xét kết quả cùng đề xuất của ca trưởng. Nếu nhân viên đạt yêu cầu, quản lý xác nhận hoàn thành và thông báo kết quả. Nếu chưa đạt, quản lý yêu cầu đào tạo bổ sung, xác định nội dung cần cải thiện và tổ chức thực hiện tiếp.

Bốn bên tham gia AS-IS không đồng nghĩa với bốn vai trò sử dụng hệ thống đề xuất. Trong TO-BE, phạm vi ứng dụng được giới hạn ở Nhân viên, Trưởng ca và Quản lý cửa hàng; Ca trưởng tương ứng với Trưởng ca trong hệ thống. HR vẫn thuộc bối cảnh tổ chức nhưng nằm ngoài phạm vi vai trò sử dụng hệ thống của đồ án. Ranh giới và trách nhiệm trong phương án đề xuất được trình bày tại mục 2.2.

### 2.1.2 Quy trình hiện trạng

Quy trình bắt đầu khi HR xác định nhân viên cần đào tạo và kết thúc khi quản lý xác nhận hoàn thành, thông báo kết quả sau khi nhân viên đạt yêu cầu. Nếu chưa đạt, quy trình quay lại tổ chức đào tạo theo nội dung cần cải thiện. Nhánh này áp dụng cho cả nhân viên mới và nhân viên đang làm việc, không phải quyết định nhận chính thức hoặc chấm dứt thử việc.

[CHÈN SƠ ĐỒ BPMN AS-IS ĐÃ CHỈNH TẠI ĐÂY — BỐN LÀN: HR, NHÂN VIÊN, CA TRƯỞNG, QUẢN LÝ CỬA HÀNG]

Hình 2.1 Quy trình đào tạo nhân viên hiện trạng AS-IS trong bối cảnh BrewLearn

Sơ đồ gồm một pool thể hiện quy trình đào tạo thuộc phạm vi BrewLearn và bốn lane phân chia trách nhiệm của các bên. Các hoạt động nằm trong từng lane thể hiện bên thực hiện; mũi tên thể hiện thứ tự và sự chuyển giao công việc. Cổng rẽ nhánh sau hoạt động xem xét của quản lý phân chia hai hướng xử lý: đạt yêu cầu và chưa đạt.

**Xác định nhân viên và tổ chức đào tạo.** HR xác định nhân viên mới hoặc nhân viên đang làm việc cần đào tạo. Quản lý cửa hàng xác định nội dung và phân công người hướng dẫn. Thông tin nhân viên, nội dung và phân công được trao đổi hoặc ghi nhận thủ công trong tài liệu, biểu mẫu riêng lẻ, chưa hình thành hồ sơ theo dõi xuyên suốt. Sơ đồ không phân rã cách thu thập nhu cầu hoặc xử lý tuyển dụng.

**Hướng dẫn, học và thực hành.** Ca trưởng hướng dẫn trực tiếp và cung cấp tài liệu nghiệp vụ. Nhân viên học và thực hành theo nội dung được hướng dẫn. Việc theo dõi mức độ hoàn thành phụ thuộc vào trao đổi, quan sát và ghi nhận của người hướng dẫn. Sơ đồ chưa thể hiện bài học trực tuyến, hoạt động kiểm tra kiến thức riêng hoặc chức năng chấm điểm tự động.

**Quan sát, ghi nhận và đề xuất.** Ca trưởng quan sát quá trình thực hiện công việc, ghi nhận kết quả và đề xuất xử lý. Thông tin được chuyển đến quản lý để xem xét. Sơ đồ chưa quy định biểu mẫu chấm điểm, năm tiêu chí đánh giá hoặc ngưỡng điểm cụ thể. Các quy tắc của hệ thống TO-BE được trình bày riêng tại mục 2.3, không mặc định là quy tắc hiện trạng đã được doanh nghiệp xác nhận.

**Xem xét và xác nhận hoàn thành.** Quản lý xem xét kết quả và đề xuất của ca trưởng. Nếu nhân viên đạt yêu cầu, quản lý xác nhận hoàn thành, thông báo kết quả và kết thúc quy trình. Hoạt động xác nhận phản ánh quyết định nghiệp vụ trong hiện trạng, chưa phải thao tác chuyển trạng thái phê duyệt trên hệ thống. Sơ đồ chưa thể hiện nhánh trả lại hồ sơ để bổ sung.

**Đào tạo bổ sung khi chưa đạt.** Nếu nhân viên chưa đạt yêu cầu, quản lý yêu cầu đào tạo bổ sung và xác định nội dung cần cải thiện. Luồng xử lý quay lại hoạt động xác định nội dung và phân công người hướng dẫn, sau đó tiếp tục hướng dẫn, học, thực hành, quan sát và xem xét kết quả. Sơ đồ không đặt số lần lặp cố định; tuy nhiên, chưa thể hiện cách đánh số vòng hoặc lưu lịch sử từng lần đào tạo bổ sung bằng hệ thống.

Các thông tin chuyển giao gồm nhân viên cần đào tạo, nội dung và phân công, tài liệu hướng dẫn, kết quả quan sát, đề xuất và thông báo hoàn thành. Sơ đồ thể hiện thứ tự và trách nhiệm xử lý, chưa thể hiện kho dữ liệu hoặc nơi lưu hồ sơ cụ thể. Trong bối cảnh giả định, tài liệu và biểu mẫu riêng lẻ được sử dụng để ghi nhận thông tin. Nhu cầu cải tiến tập trung vào liên kết dữ liệu và lưu lịch sử; chi tiết TO-BE được trình bày tại mục 2.2.

### 2.1.3 Hạn chế và nhu cầu cải tiến

Từ mô hình hiện trạng giả định, đề tài xác định các hạn chế và nhu cầu xử lý trong Bảng 2.1. Các hạn chế là cơ sở hình thành yêu cầu thiết kế, không được xem là số liệu đo lường hoặc kết luận khảo sát tại doanh nghiệp thực tế.

Bảng 2.1 Hạn chế của quy trình hiện trạng và nhu cầu cải tiến

| Vấn đề hiện trạng | Ảnh hưởng đến quy trình | Nhu cầu xử lý tương ứng |
| --- | --- | --- |
| Thông tin nhân viên, tài liệu và phân công được ghi nhận riêng lẻ | Khó tra cứu nội dung cần thực hiện và người chịu trách nhiệm | Liên kết hồ sơ nhân viên với nội dung đào tạo và người phụ trách |
| Việc học và thực hành được theo dõi qua hướng dẫn, quan sát | Khó tổng hợp nội dung đã hoàn thành và mức độ tiếp thu kiến thức | Ghi nhận tiến độ học và bổ sung kiểm tra kiến thức theo nội dung |
| Kết quả quan sát và đề xuất chưa có cấu trúc theo dõi thống nhất | Khó đối chiếu căn cứ giữa các lần thực hiện | Thiết kế tiêu chí, điểm, nhận xét và lịch sử đánh giá có cấu trúc |
| Kết quả của ca trưởng và quyết định quản lý được chuyển giao thủ công | Khó nhận biết kết quả đang xem xét và đã được chấp thuận | Theo dõi chờ phê duyệt, đã duyệt và trả lại kèm lý do |
| Đào tạo bổ sung lặp lại nhưng chưa thể hiện lịch sử từng lần | Khó xác định nội dung cải thiện và kết quả của từng lần | Quản lý yêu cầu, kế hoạch và lịch sử từng vòng; chỉ kết thúc khi đạt và được duyệt |
| Phân công và kết quả được thông báo qua trao đổi riêng | Các bên cần đối chiếu nhiều nguồn để biết công việc cần làm | Hiển thị nhiệm vụ, lịch đánh giá và thông báo trên hệ thống |

Trên cơ sở các nhu cầu này, phương án TO-BE tập trung vào quy trình học tập – kiểm tra – đánh giá – phê duyệt – đào tạo lại. Các chức năng tài khoản, thông báo, phân ca và hỗ trợ chỉ phục vụ việc thực hiện quy trình chính. Phần dự đoán được thử nghiệm bằng Python trên dữ liệu giả lập và không thay thế kết quả đánh giá đã được quản lý phê duyệt.
