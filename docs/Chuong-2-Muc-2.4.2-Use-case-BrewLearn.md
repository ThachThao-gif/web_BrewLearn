## 2.4.2. Use case và đặc tả use case

### a. Tác nhân và phạm vi mô hình

Mô hình use case mô tả các mục tiêu tương tác giữa người sử dụng và hệ thống BrewLearn trong quá trình học tập, kiểm tra, đánh giá năng lực và đào tạo lại. Phạm vi hệ thống gồm ba tác nhân nghiệp vụ: Nhân viên, Trưởng ca và Quản lý cửa hàng. Nhân viên bao gồm các vị trí pha chế, thu ngân và phục vụ; nội dung đào tạo được xác định theo chức danh.

| Tác nhân | Trách nhiệm và phạm vi tương tác |
| --- | --- |
| Nhân viên | Học nội dung theo chức danh, làm bài kiểm tra, theo dõi tiến độ và kết quả cá nhân; xem lịch đánh giá, kết quả đã duyệt và thực hiện nội dung học lại được giao. |
| Trưởng ca | Theo dõi nhân viên trong phạm vi phụ trách; nhắc việc, ghi nhận lỗi nghiệp vụ; lập lịch, chấm đánh giá và đề xuất xử lý; bổ sung đánh giá bị trả lại; thực hiện đánh giá lại và xác nhận đào tạo khi được phân công. |
| Quản lý cửa hàng | Theo dõi tình hình đào tạo tại cửa hàng; quản lý ca và trạng thái làm việc; phê duyệt hoặc trả lại đánh giá; xét duyệt, phân công và theo dõi đào tạo lại; tạo lịch đánh giá lại và xem thông tin nguy cơ đã lưu. |

Các chức năng tài khoản, thông báo, tra cứu và hỗ trợ được mô tả thông qua tác nhân tổng quát **Người dùng**. Đây là cách biểu diễn chức năng dùng chung, không phải chức danh nghiệp vụ thứ tư. Người phụ trách đào tạo lại là nhân viên hoặc trưởng ca được phân công cho từng vòng, không phải vai trò hệ thống mới. Trưởng ca có thể sử dụng nhóm chức năng học tập theo chức danh của mình nhưng không thuộc đối tượng đánh giá năng lực nhân viên trong phạm vi đề tài.

HR và Quản lý vùng không thuộc phạm vi tác nhân của hệ thống TO-BE. Chức năng biên soạn khóa học, ngân hàng câu hỏi, quản trị bộ tiêu chuẩn và lập, gửi báo cáo lên cấp vùng không nằm trong mô hình này. Các nội dung đào tạo và câu hỏi được chuẩn bị trong cơ sở dữ liệu để phục vụ nguyên mẫu.

### b. Danh sách use case

Mã UC-CH được dùng cho chức năng chung, UC-NV cho mục tiêu của nhân viên, UC-TC cho trưởng ca và UC-QL cho quản lý cửa hàng. Mã trong mục này là bộ mã đã chuẩn hóa; tên và mã cần được dùng thống nhất trong bảng đặc tả, sơ đồ và các phần dẫn chiếu của báo cáo.

**Chức năng chung**

| ID | Use case | Mô tả ngắn |
| --- | --- | --- |
| UC-CH01 | Đăng nhập | Xác thực bằng email, mật khẩu và chuyển đến giao diện phù hợp với chức danh. |
| UC-CH02 | Đăng xuất | Kết thúc phiên làm việc hiện tại. |
| UC-CH03 | Quản lý hồ sơ và bảo mật tài khoản | Xem, cập nhật hồ sơ, thông tin liên hệ, mật khẩu và đăng xuất thiết bị khác. |
| UC-CH04 | Cài đặt thông báo và giao diện | Cập nhật tùy chọn cá nhân. |
| UC-CH05 | Quản lý thông báo cá nhân | Xem thông báo, đánh dấu đã đọc và xóa thông báo thuộc tài khoản. |
| UC-CH06 | Tra cứu quy trình và chính sách | Xem SOP, nội quy và chính sách phục vụ công việc. |
| UC-CH07 | Gửi và theo dõi yêu cầu hỗ trợ | Gửi nội dung yêu cầu và xem các yêu cầu của bản thân. |
| UC-CH08 | Xác nhận hoàn thành nội dung đào tạo lại | Người được giao phụ trách xác nhận thực hành, hướng dẫn hoặc theo dõi của vòng đào tạo khi đủ điều kiện. |

**Nhân viên**

| ID | Use case | Mô tả ngắn |
| --- | --- | --- |
| UC-NV01 | Xem lộ trình và khóa đào tạo | Xem khóa học, bài học và tiến độ theo chức danh. |
| UC-NV02 | Học bài và ghi nhận tiến độ | Xem video, tài liệu; tiếp tục bài học và ghi nhận hoàn thành. |
| UC-NV03 | Làm bài kiểm tra | Trả lời câu hỏi, nộp bài và nhận kết quả chấm tự động. |
| UC-NV04 | Làm lại bài kiểm tra | Thực hiện lượt kiểm tra mới; giữ lịch sử các lượt trước. |
| UC-NV05 | Xem tiến độ và kết quả kiểm tra | Tra cứu tiến độ học, điểm, trạng thái và lịch sử kiểm tra cá nhân. |
| UC-NV06 | Xem lịch và kết quả đánh giá năng lực | Xem lịch được thông báo và kết quả đánh giá chính thức sau phê duyệt. |
| UC-NV07 | Xem và thực hiện nội dung học lại | Xem yêu cầu được giao, học lại bài học và làm lượt kiểm tra mới theo vòng đào tạo. |

**Trưởng ca**

| ID | Use case | Mô tả ngắn |
| --- | --- | --- |
| UC-TC01 | Theo dõi nhân viên phụ trách | Xem danh sách, tiến độ học tập, kiểm tra và tình trạng đánh giá trong phạm vi được phép. |
| UC-TC02 | Tạo lịch đánh giá năng lực | Kiểm tra điều kiện và lập lịch đánh giá cho nhân viên. |
| UC-TC03 | Đánh giá năng lực | Nhập điểm, nhận xét và gửi kết quả để quản lý phê duyệt. |
| UC-TC04 | Chấm điểm theo năm tiêu chí | Nhập điểm nghiệp vụ, kỹ năng, quy trình, phục vụ và tác phong; hệ thống tính điểm tổng và xác định đạt/chưa đạt. |
| UC-TC05 | Đề xuất đào tạo lại | Đề xuất nội dung cải thiện khi nhân viên chưa đạt, gồm hướng dẫn bổ sung, học lại, theo dõi ca hoặc đánh giá lại. |
| UC-TC06 | Bổ sung đánh giá bị trả lại | Xem lý do trả lại, chỉnh sửa thông tin và gửi lại để phê duyệt. |
| UC-TC07 | Xem kết quả và lịch sử đánh giá | Theo dõi kết quả, trạng thái phê duyệt và thông tin liên quan của nhân viên. |
| UC-TC08 | Gửi thông báo nhắc việc | Gửi nhắc nhở hoặc thông báo nghiệp vụ đến nhân viên thuộc phạm vi phụ trách. |
| UC-TC09 | Ghi nhận lỗi nghiệp vụ | Lưu nội dung lỗi, sự cố phục vụ theo dõi và hướng dẫn nhân viên. |

Đánh giá lại sử dụng UC-TC03 và UC-TC04 trên hồ sơ được tạo từ vòng đào tạo lại, không hình thành một cơ chế chấm điểm khác. Trưởng ca được giao phụ trách sử dụng UC-CH08 để xác nhận nội dung đào tạo.

**Quản lý cửa hàng**

| ID | Use case | Mô tả ngắn |
| --- | --- | --- |
| UC-QL01 | Xem tổng quan đào tạo cửa hàng | Xem thống kê học tập, tình trạng đánh giá, xu hướng kết quả đã duyệt và các trường hợp cần theo dõi. |
| UC-QL02 | Xem danh sách và hồ sơ nhân viên | Tìm kiếm, lọc và tra cứu dữ liệu học tập, kiểm tra, đánh giá của nhân viên trong cửa hàng. |
| UC-QL03 | Quản lý ca làm việc | Tạo ca và phân ca cho nhân viên. |
| UC-QL04 | Cập nhật trạng thái làm việc | Cập nhật tình trạng làm việc phục vụ quản lý phạm vi đào tạo và đánh giá. |
| UC-QL05 | Phê duyệt kết quả đánh giá | Duyệt kết quả hoặc trả lại kèm lý do, áp dụng cả đánh giá ban đầu và đánh giá lại. |
| UC-QL06 | Xét duyệt đề xuất đào tạo lại | Duyệt hoặc từ chối yêu cầu đang chờ duyệt. |
| UC-QL07 | Phân công vòng đào tạo lại | Giao nội dung, người phụ trách, thời hạn và thông tin dự kiến đánh giá lại cho từng vòng. |
| UC-QL08 | Theo dõi lịch sử đào tạo lại | Xem nội dung, người phụ trách, xác nhận, đánh giá và kết quả của từng vòng. |
| UC-QL09 | Tạo lịch đánh giá lại | Kiểm tra điều kiện của vòng hiện tại và phân công trưởng ca đánh giá lại. |
| UC-QL10 | Xem thông tin nguy cơ dự đoán | Xem mức nguy cơ đã lưu, thời điểm tính và đối chiếu với hồ sơ, kết quả đánh giá. |

### c. Mô hình use case và quan hệ giữa các chức năng

Mô hình tổng quát thể hiện ba tác nhân và các mục tiêu chính trong ranh giới BrewLearn. Các sơ đồ chi tiết tổ chức thành nhóm học tập, đánh giá, quản lý cửa hàng, đào tạo lại và chức năng chung để thuận tiện theo dõi.

**Vị trí chèn hình:** lần lượt sử dụng các hình `UseCase-do-an-01-tong-quat.png`, `UseCase-do-an-02-hoc-tap.png`, `UseCase-do-an-03-danh-gia.png`, `UseCase-do-an-04-quan-ly.png`, `UseCase-do-an-05-dao-tao-lai.png` và `UseCase-do-an-06-chuc-nang-chung.png` trong thư mục `docs/diagrams`. Tên use case trên hình cần đối chiếu với danh mục đã chuẩn hóa ở trên.

- UC-TC03 **Đánh giá năng lực** bao gồm UC-TC04 **Chấm điểm theo năm tiêu chí**, vì mỗi lần đánh giá bắt buộc có đủ năm điểm thành phần.
- UC-TC05 **Đề xuất đào tạo lại** mở rộng UC-TC03 tại điểm xử lý kết quả chưa đạt. Khi điều kiện chưa đạt xảy ra, đề xuất là dữ liệu bắt buộc trước khi gửi phê duyệt.
- Đăng nhập được nêu là tiền điều kiện của các chức năng được bảo vệ, không lặp thành quan hệ bao gồm ở mọi use case.
- Phê duyệt và trả lại là các nhánh xử lý của UC-QL05; duyệt và từ chối đề xuất là các nhánh của UC-QL06.
- Việc thực hành, hướng dẫn tại cửa hàng và quan sát nhân viên diễn ra ngoài phần mềm. BrewLearn hỗ trợ giao việc, lưu thông tin, xác nhận và nhập kết quả.

### d. Đặc tả các use case trọng tâm

Các đặc tả sau trình bày hành vi nghiệp vụ cần đáp ứng. Tiền điều kiện về quyền và phạm vi dữ liệu là yêu cầu kiểm soát của thiết kế, không được hiểu là kết luận đã kiểm thử toàn bộ nguyên mẫu. Đối với ngoại lệ kỹ thuật, kết quả chỉ được coi là thành công khi hệ thống xác nhận đã lưu; người dùng cần tải lại dữ liệu để kiểm tra trạng thái trước khi gửi lại nếu phản hồi bị gián đoạn.

#### UC-CH01 — Đăng nhập

| Thuộc tính | Đặc tả |
| --- | --- |
| Mục tiêu | Truy cập BrewLearn theo đúng chức danh của tài khoản. |
| Tác nhân | Nhân viên, Trưởng ca, Quản lý cửa hàng. |
| Kích hoạt | Người dùng truy cập hệ thống hoặc phiên làm việc đã kết thúc. |
| Tiền điều kiện | Có tài khoản với email và mật khẩu hợp lệ. |
| Hậu điều kiện | Đăng nhập thành công: phiên xác thực được thiết lập và người dùng được chuyển đến giao diện theo chức danh. Thất bại: không tạo phiên xác thực mới. |
| Luồng chính | 1. Người dùng mở trang đăng nhập.<br>2. Hệ thống hiển thị biểu mẫu.<br>3. Người dùng nhập email, mật khẩu và chọn Đăng nhập.<br>4. Hệ thống kiểm tra dữ liệu và xác thực.<br>5. Hệ thống tạo lại mã phiên và xác định chức danh.<br>6. Hệ thống chuyển đến giao diện học tập, trưởng ca hoặc quản lý cửa hàng. |
| Luồng thay thế | A1. Thiếu dữ liệu hoặc email sai định dạng: thông báo trường cần sửa, quay lại bước 3.<br>A2. Email hoặc mật khẩu không đúng: báo xác thực thất bại, cho nhập lại.<br>A3. Người dùng đã đăng nhập: chuyển đến giao diện phù hợp. |
| Ngoại lệ | Không thể xử lý xác thực do lỗi hệ thống hoặc kết nối: thao tác không được xác nhận thành công; người dùng thử lại sau. |

#### UC-NV01 — Xem lộ trình và khóa đào tạo

| Thuộc tính | Đặc tả |
| --- | --- |
| Mục tiêu | Xác định nội dung học tập áp dụng cho chức danh của người học. |
| Tác nhân | Nhân viên; Trưởng ca khi tham gia học tập theo chức danh. |
| Kích hoạt | Người học mở lộ trình hoặc danh sách khóa đào tạo. |
| Tiền điều kiện | Đã đăng nhập; chức danh được xác định. Không bắt buộc đã có khóa học. |
| Hậu điều kiện | Hiển thị khóa học phù hợp và thông tin tiến độ hoặc trạng thái chưa có nội dung. |
| Luồng chính | 1. Người học chọn lộ trình hoặc khóa đào tạo.<br>2. Hệ thống xác định nội dung theo chức danh.<br>3. Hệ thống hiển thị danh sách khóa và tiến độ.<br>4. Người học chọn một khóa.<br>5. Hệ thống hiển thị thông tin khóa, bài học và trạng thái học tập. |
| Luồng thay thế | A1. Chưa có khóa phù hợp: hiển thị danh sách rỗng.<br>A2. Chưa bắt đầu học: hiển thị trạng thái chưa học và nội dung có thể bắt đầu. |
| Ngoại lệ | Khóa không tồn tại hoặc không thuộc phạm vi được phép: không hiển thị nội dung đó. Không tải được dữ liệu: người học tải lại trang. |

#### UC-NV02 — Học bài và ghi nhận tiến độ

| Thuộc tính | Đặc tả |
| --- | --- |
| Mục tiêu | Học nội dung được phân công và lưu tiến độ cá nhân. |
| Tác nhân | Nhân viên; Trưởng ca với tư cách người học. |
| Kích hoạt | Người học chọn bài học hoặc chức năng tiếp tục học. |
| Tiền điều kiện | Đã đăng nhập; bài học tồn tại, thuộc nội dung được phép truy cập và đáp ứng điều kiện truy cập áp dụng. |
| Hậu điều kiện | Tiến độ được lưu; trạng thái hoàn thành và tiến độ khóa được cập nhật theo quy tắc hệ thống. |
| Luồng chính | 1. Người học mở bài học.<br>2. Hệ thống hiển thị nội dung và tiến độ đã lưu.<br>3. Người học xem video hoặc đọc tài liệu.<br>4. Hệ thống nhận và kiểm tra dữ liệu tiến độ.<br>5. Hệ thống cập nhật tiến độ của người học.<br>6. Khi đáp ứng điều kiện hoàn thành, hệ thống ghi nhận trạng thái bài học và đồng bộ tiến độ khóa.<br>7. Người học xem tiến độ mới. |
| Luồng thay thế | A1. Dừng học giữa chừng: giữ tiến độ đã lưu để tiếp tục sau.<br>A2. Chưa đủ điều kiện hoàn thành: tiếp tục hiển thị trạng thái đang học. |
| Ngoại lệ | Tài liệu hoặc video không tải được: chưa thể tiếp tục nội dung đó. Lỗi lưu tiến độ: không coi phần cập nhật chưa được xác nhận là đã lưu. |

#### UC-NV03 — Làm bài kiểm tra

| Thuộc tính | Đặc tả |
| --- | --- |
| Mục tiêu | Kiểm tra kiến thức và nhận kết quả chấm tự động. |
| Tác nhân | Nhân viên; Trưởng ca với tư cách người học. |
| Kích hoạt | Người học chọn bài kiểm tra. |
| Tiền điều kiện | Đã đăng nhập; bài kiểm tra thuộc phạm vi nội dung được phép, có câu hỏi và lượt làm bài hợp lệ. |
| Hậu điều kiện | Lưu một kết quả cho lượt làm bài; hiển thị điểm, số câu đúng và đạt/chưa đạt theo điểm chuẩn của bài. |
| Luồng chính | 1. Người học mở bài kiểm tra.<br>2. Hệ thống hiển thị câu hỏi và tạo mã lượt làm bài.<br>3. Người học chọn câu trả lời.<br>4. Người học nộp bài.<br>5. Hệ thống kiểm tra lượt thi và dữ liệu trả lời.<br>6. Hệ thống tính điểm theo tỷ lệ câu đúng trên tổng số câu, quy đổi về thang 100.<br>7. Hệ thống so sánh điểm với DiemChuan của bài kiểm tra; dùng 80 khi chưa có giá trị cấu hình.<br>8. Hệ thống lưu kết quả và hiển thị phản hồi. |
| Luồng thay thế | A1. Điểm dưới chuẩn: ghi nhận chưa đạt; người học có thể xem lại bài học và thực hiện lượt mới.<br>A2. Có câu chưa trả lời: câu đó không được tính đúng.<br>A3. Gửi lặp cùng mã lượt đã lưu: trả lại kết quả hiện có, không tạo thêm lượt. |
| Ngoại lệ | Mã lượt không hợp lệ hoặc phiên không còn dữ liệu lượt thi: yêu cầu mở lại bài. Bài không có câu hỏi: không chấm điểm. Lỗi ghi dữ liệu: không xác nhận lưu thành công. |

#### UC-NV04 — Làm lại bài kiểm tra

| Thuộc tính | Đặc tả |
| --- | --- |
| Mục tiêu | Cải thiện kết quả hoặc đáp ứng yêu cầu kiểm tra của vòng đào tạo lại. |
| Tác nhân | Nhân viên; Trưởng ca với tư cách người học. |
| Kích hoạt | Người học chọn làm lại bài kiểm tra. |
| Tiền điều kiện | Đã đăng nhập; bài kiểm tra có thể truy cập; đã có lượt trước hoặc đang được yêu cầu thực hiện lượt mới. |
| Hậu điều kiện | Lưu kết quả của lượt mới; giữ các kết quả trước. Kết quả mới được dùng khi kiểm tra điều kiện tương ứng. |
| Luồng chính | 1. Người học xem kết quả trước hoặc yêu cầu của vòng đào tạo.<br>2. Người học xem lại nội dung cần thiết.<br>3. Người học mở lại bài kiểm tra.<br>4. Hệ thống tạo mã lượt mới.<br>5. Người học trả lời và nộp bài.<br>6. Hệ thống chấm, lưu và hiển thị kết quả theo UC-NV03. |
| Luồng thay thế | A1. Tiếp tục chưa đạt: giữ kết quả, cho phép tiếp tục ôn tập.<br>A2. Lượt trước đã đạt nhưng vòng đào tạo yêu cầu lượt mới: vẫn cần kết quả đạt phát sinh trong vòng hiện tại. |
| Ngoại lệ | Áp dụng các ngoại lệ về lượt thi, câu hỏi và lưu kết quả của UC-NV03. |

#### UC-NV06 — Xem lịch và kết quả đánh giá năng lực

| Thuộc tính | Đặc tả |
| --- | --- |
| Mục tiêu | Biết lịch đánh giá và kết quả chính thức của bản thân. |
| Tác nhân | Nhân viên. |
| Kích hoạt | Nhân viên mở thông báo hoặc thông tin đánh giá cá nhân. |
| Tiền điều kiện | Đã đăng nhập; chỉ truy cập dữ liệu của bản thân. |
| Hậu điều kiện | Hiển thị lịch hoặc kết quả đã duyệt; thao tác xem không thay đổi kết quả. |
| Luồng chính | 1. Nhân viên mở thông báo liên quan.<br>2. Hệ thống xác định thông tin thuộc tài khoản.<br>3. Hệ thống hiển thị lịch hẹn hoặc kết quả đã được duyệt.<br>4. Nhân viên xem điểm tổng, điểm thành phần, nhận xét và nội dung xử lý được thông báo nếu có. |
| Luồng thay thế | A1. Chưa có lịch: chưa hiển thị thông báo lịch tương ứng.<br>A2. Đánh giá đang chờ duyệt hoặc bị trả lại: chưa công bố như kết quả chính thức.<br>A3. Kết quả chưa đạt đã duyệt: nhân viên xem nội dung cải thiện được giao. |
| Ngoại lệ | Dữ liệu không tồn tại hoặc không thuộc tài khoản: không cho xem. Không tải được thông tin: nhân viên thử lại sau. |

#### UC-TC02 — Tạo lịch đánh giá năng lực

| Thuộc tính | Đặc tả |
| --- | --- |
| Mục tiêu | Lập lịch đánh giá khi nhân viên đủ điều kiện. |
| Tác nhân | Trưởng ca. |
| Kích hoạt | Trưởng ca chọn tạo lịch cho nhân viên. |
| Tiền điều kiện | Đã đăng nhập với quyền trưởng ca; nhân viên đang làm việc, thuộc phạm vi phụ trách; có nội dung đào tạo áp dụng. |
| Hậu điều kiện | Tạo hồ sơ chờ đánh giá và thông báo lịch; nếu không đủ điều kiện thì không tạo lịch. |
| Luồng chính | 1. Trưởng ca chọn nhân viên.<br>2. Hệ thống kiểm tra hoàn thành bài học và đạt các bài kiểm tra yêu cầu.<br>3. Hệ thống kiểm tra hồ sơ đánh giá đang chờ xử lý và yêu cầu đào tạo lại đang mở.<br>4. Trưởng ca nhập lịch hẹn và tiêu chuẩn đánh giá.<br>5. Hệ thống kiểm tra thông tin.<br>6. Hệ thống lưu lịch ở trạng thái Chờ đánh giá.<br>7. Hệ thống gửi thông báo lịch cho nhân viên. |
| Luồng thay thế | A1. Chưa hoàn thành học hoặc quiz: hiển thị điều kiện còn thiếu.<br>A2. Có lịch/hồ sơ đang chờ xử lý: không tạo thêm lịch trùng quy trình.<br>A3. Có yêu cầu đào tạo lại đang mở: thực hiện lập lịch từ vòng đào tạo theo UC-QL09. |
| Ngoại lệ | Nhân viên ngoài phạm vi, không còn đủ điều kiện hoặc lịch nhập không hợp lệ: từ chối thao tác. Lỗi lưu lịch: không xác nhận đã tạo. |

#### UC-TC03 — Đánh giá năng lực

| Thuộc tính | Đặc tả |
| --- | --- |
| Mục tiêu | Ghi nhận kết quả đánh giá thực tế và gửi quản lý phê duyệt. |
| Tác nhân | Trưởng ca. |
| Kích hoạt | Trưởng ca mở hồ sơ chờ đánh giá, gồm đánh giá ban đầu hoặc đánh giá lại. |
| Tiền điều kiện | Đã đăng nhập; có quyền với hồ sơ; hồ sơ ở trạng thái được phép nhập hoặc bổ sung. |
| Hậu điều kiện | Lưu đủ điểm, nhận xét và đề xuất khi chưa đạt; chuyển hồ sơ sang Chờ phê duyệt. Chưa công bố kết quả chính thức cho nhân viên. |
| Luồng chính | 1. Trưởng ca mở hồ sơ và xem tiêu chuẩn.<br>2. Trưởng ca nhập điểm dựa trên quan sát, đánh giá thực tế ngoài phần mềm.<br>3. Thực hiện UC-TC04 để nhập đủ năm tiêu chí.<br>4. Hệ thống tính điểm tổng và xác định đạt/chưa đạt.<br>5. Trưởng ca nhập nhận xét; nếu chưa đạt, thực hiện UC-TC05.<br>6. Trưởng ca gửi đánh giá.<br>7. Hệ thống kiểm tra, lưu dữ liệu và chuyển sang Chờ phê duyệt.<br>8. Hệ thống thông báo cho quản lý. |
| Luồng thay thế | A1. Thiếu điểm hoặc điểm ngoài 0–100: yêu cầu sửa.<br>A2. Chưa đạt nhưng thiếu đề xuất: không cho gửi.<br>A3. Hồ sơ bị trả lại: thực hiện UC-TC06 trước khi gửi lại. |
| Ngoại lệ | Hồ sơ đang chờ duyệt hoặc đã duyệt: không cho sửa trực tiếp. Mất quyền/phạm vi hoặc lỗi lưu: không xác nhận gửi thành công. |

#### UC-TC04 — Chấm điểm theo năm tiêu chí

| Thuộc tính | Đặc tả |
| --- | --- |
| Mục tiêu | Tính kết quả đánh giá thống nhất theo bộ tiêu chí của đề tài. |
| Tác nhân | Trưởng ca. |
| Kích hoạt | Thực hiện bước nhập điểm trong UC-TC03. |
| Tiền điều kiện | Hồ sơ đánh giá được phép nhập điểm. |
| Hậu điều kiện | Có đủ điểm thành phần, điểm tổng và kết luận đạt/chưa đạt để lưu cùng hồ sơ đánh giá. |
| Luồng chính | 1. Trưởng ca nhập điểm nghiệp vụ, kỹ năng, quy trình, phục vụ và tác phong.<br>2. Hệ thống kiểm tra mỗi điểm trong 0–100.<br>3. Hệ thống tính: Điểm tổng = 0,20 × Nghiệp vụ + 0,30 × Kỹ năng + 0,20 × Quy trình + 0,15 × Phục vụ + 0,15 × Tác phong.<br>4. Làm tròn điểm tổng đến một chữ số thập phân.<br>5. Kết luận Đạt khi điểm tổng từ 80 và từng tiêu chí từ 70; trường hợp còn lại là Chưa đạt.<br>6. Trả kết quả về luồng đánh giá. |
| Luồng thay thế | Tổng từ 80 nhưng có ít nhất một tiêu chí dưới 70: vẫn xác định Chưa đạt. |
| Ngoại lệ | Thiếu hoặc sai kiểu dữ liệu điểm: không tính và lưu một kết quả đánh giá hoàn chỉnh. |

#### UC-TC05 — Đề xuất đào tạo lại

| Thuộc tính | Đặc tả |
| --- | --- |
| Mục tiêu | Xác định nội dung xử lý phù hợp với hạn chế năng lực của nhân viên. |
| Tác nhân | Trưởng ca. |
| Kích hoạt | Kết quả tính trong UC-TC03 là Chưa đạt. |
| Tiền điều kiện | Đang nhập đánh giá chưa đạt và có quyền xử lý hồ sơ. |
| Hậu điều kiện | Đề xuất được lưu cùng đánh giá để quản lý xem xét; chưa đồng nghĩa đã giao vòng đào tạo. |
| Luồng chính | 1. Hệ thống yêu cầu ít nhất một đề xuất.<br>2. Trưởng ca chọn hướng dẫn bổ sung, học lại bài học, theo dõi ca hoặc đánh giá lại.<br>3. Trưởng ca nhập chi tiết theo loại đã chọn: kỹ năng/hướng dẫn, khóa và bài học, hành vi/số ca hoặc hạn theo dõi, lịch/người đánh giá và tiêu chí dự kiến.<br>4. Hệ thống kiểm tra dữ liệu bắt buộc và nội dung tham chiếu.<br>5. Hệ thống gắn đề xuất với hồ sơ đánh giá.<br>6. Tiếp tục gửi chờ phê duyệt theo UC-TC03. |
| Luồng thay thế | Chọn nhiều nội dung: lưu kết hợp các nội dung hợp lệ trong cùng đề xuất. |
| Ngoại lệ | Thiếu chi tiết hoặc bài học không thuộc khóa đã chọn: yêu cầu sửa trước khi gửi. |

#### UC-TC06 — Bổ sung đánh giá bị trả lại

| Thuộc tính | Đặc tả |
| --- | --- |
| Mục tiêu | Hoàn thiện hồ sơ theo lý do trả lại của quản lý. |
| Tác nhân | Trưởng ca. |
| Kích hoạt | Trưởng ca nhận thông báo hoặc mở hồ sơ bị trả lại. |
| Tiền điều kiện | Đã đăng nhập; có quyền với hồ sơ đang ở trạng thái Trả lại. |
| Hậu điều kiện | Hồ sơ được cập nhật và chuyển lại sang Chờ phê duyệt. |
| Luồng chính | 1. Trưởng ca mở hồ sơ.<br>2. Hệ thống hiển thị điểm, nhận xét, đề xuất và lý do trả lại.<br>3. Trưởng ca bổ sung hoặc chỉnh sửa.<br>4. Hệ thống tính lại kết quả và kiểm tra đề xuất khi chưa đạt.<br>5. Trưởng ca gửi lại.<br>6. Hệ thống lưu, cập nhật trạng thái và thông báo quản lý. |
| Luồng thay thế | Dữ liệu chưa đầy đủ: giữ biểu mẫu để tiếp tục chỉnh sửa. |
| Ngoại lệ | Trạng thái hồ sơ đã thay đổi: không ghi đè; yêu cầu tải lại. Lỗi lưu: không xác nhận gửi thành công. |

#### UC-QL05 — Phê duyệt kết quả đánh giá

| Thuộc tính | Đặc tả |
| --- | --- |
| Mục tiêu | Xác nhận kết quả đánh giá chính thức hoặc yêu cầu trưởng ca bổ sung. |
| Tác nhân | Quản lý cửa hàng. |
| Kích hoạt | Quản lý mở hồ sơ Chờ phê duyệt. |
| Tiền điều kiện | Đã đăng nhập với quyền quản lý; nhân viên thuộc cửa hàng và phạm vi đánh giá; hồ sơ đang chờ phê duyệt. |
| Hậu điều kiện | Duyệt: lưu quyết định, công bố kết quả cho nhân viên và cập nhật nghiệp vụ liên quan. Trả lại: lưu lý do, chuyển hồ sơ cho trưởng ca bổ sung. |
| Luồng chính | 1. Quản lý chọn hồ sơ.<br>2. Hệ thống hiển thị điểm thành phần, điểm tổng, nhận xét và đề xuất.<br>3. Quản lý kiểm tra và chọn Phê duyệt.<br>4. Hệ thống kiểm tra dữ liệu, trạng thái và lưu người duyệt, thời điểm duyệt.<br>5. Hệ thống chuyển hồ sơ sang Đã duyệt.<br>6. Với đánh giá ban đầu chưa đạt có đề xuất, hệ thống tạo yêu cầu đào tạo lại đã duyệt để chờ giao.<br>7. Với đánh giá lại, hệ thống cập nhật kết quả của vòng: Đạt thì hoàn tất yêu cầu; Chưa đạt thì giữ yêu cầu mở, chờ điều chỉnh và giao vòng sau.<br>8. Hệ thống gửi kết quả đã duyệt cho nhân viên và thông báo cho trưởng ca. |
| Luồng thay thế | A1. Quản lý chọn Trả lại: nhập lý do bắt buộc; hệ thống chuyển sang Trả lại và thông báo trưởng ca; chưa công bố kết quả chính thức.<br>A2. Thiếu kết luận hoặc thiếu đề xuất của hồ sơ chưa đạt: không duyệt, yêu cầu bổ sung. |
| Ngoại lệ | Hồ sơ đã được xử lý bởi thao tác khác: từ chối xử lý lặp. Ngoài phạm vi hoặc lỗi ghi dữ liệu: không xác nhận quyết định mới. |

#### UC-QL06 — Xét duyệt đề xuất đào tạo lại

| Thuộc tính | Đặc tả |
| --- | --- |
| Mục tiêu | Quyết định chấp thuận hoặc từ chối yêu cầu đào tạo lại đang chờ duyệt. |
| Tác nhân | Quản lý cửa hàng. |
| Kích hoạt | Quản lý chọn một yêu cầu Chờ duyệt. |
| Tiền điều kiện | Đã đăng nhập; yêu cầu thuộc cửa hàng và đang ở trạng thái Chờ duyệt. |
| Hậu điều kiện | Yêu cầu chuyển sang Đã duyệt hoặc Từ chối, có người và thời điểm xử lý. |
| Luồng chính | 1. Quản lý mở yêu cầu.<br>2. Hệ thống hiển thị lý do, nội dung và đánh giá liên quan.<br>3. Quản lý chọn Duyệt.<br>4. Hệ thống kiểm tra trạng thái và lưu quyết định.<br>5. Yêu cầu sẵn sàng cho việc phân công theo UC-QL07. |
| Luồng thay thế | A1. Từ chối: quản lý nhập lý do, hệ thống lưu quyết định Từ chối.<br>A2. Yêu cầu đã được duyệt thông qua UC-QL05: bỏ qua bước xét duyệt riêng và chuyển sang phân công. |
| Ngoại lệ | Yêu cầu không còn Chờ duyệt hoặc không thuộc phạm vi: từ chối xử lý. Lỗi lưu: chưa xác nhận quyết định. |

#### UC-QL07 — Phân công vòng đào tạo lại

| Thuộc tính | Đặc tả |
| --- | --- |
| Mục tiêu | Tổ chức nội dung cải thiện và trách nhiệm cho từng vòng đào tạo lại. |
| Tác nhân | Quản lý cửa hàng. |
| Kích hoạt | Quản lý chọn giao hoặc điều chỉnh vòng đào tạo của yêu cầu đã duyệt. |
| Tiền điều kiện | Yêu cầu thuộc cửa hàng, đã duyệt và ở tiến trình cho phép giao hoặc điều chỉnh. Người phụ trách thuộc cửa hàng, khác nhân viên được đào tạo. |
| Hậu điều kiện | Vòng đào tạo được tạo hoặc cập nhật; lưu nội dung, người phụ trách, hạn và mốc đánh giá lại dự kiến; thông báo cho các bên liên quan. |
| Luồng chính | 1. Quản lý mở yêu cầu và lịch sử các vòng.<br>2. Quản lý nhập nội dung thực hiện, người phụ trách, hạn hoàn thành và ngày đánh giá lại dự kiến.<br>3. Hệ thống kiểm tra thời hạn và người phụ trách.<br>4. Hệ thống tạo vòng mới với số thứ tự tiếp theo khi bắt đầu vòng mới.<br>5. Hệ thống lưu danh sách bài học, bài kiểm tra yêu cầu và mốc dữ liệu làm căn cứ kiểm tra kết quả mới.<br>6. Hệ thống khởi tạo lại tiến độ các bài học được giao học lại, giữ lịch sử kiểm tra cũ.<br>7. Hệ thống cập nhật tiến trình Đang thực hiện.<br>8. Hệ thống thông báo cho nhân viên và người phụ trách. |
| Luồng thay thế | A1. Điều chỉnh vòng đang thực hiện: cập nhật cùng vòng; khi đổi người phụ trách hoặc nội dung hướng dẫn, yêu cầu xác nhận lại.<br>A2. Vòng trước có kết quả Chưa đạt đã duyệt: giao vòng tiếp theo và giữ lịch sử vòng trước. |
| Ngoại lệ | Yêu cầu chưa duyệt, đã hoàn tất, người phụ trách không hợp lệ hoặc ngày đánh giá trước hạn hoàn thành: không giao. Lỗi lưu: không xác nhận phân công thành công. |

#### UC-NV07 — Xem và thực hiện nội dung học lại

| Thuộc tính | Đặc tả |
| --- | --- |
| Mục tiêu | Hoàn thành phần học tập được giao trong vòng đào tạo lại. |
| Tác nhân | Nhân viên được đào tạo. |
| Kích hoạt | Nhân viên nhận thông báo giao việc hoặc mở nội dung học lại. |
| Tiền điều kiện | Đã đăng nhập; có vòng được giao và đang thực hiện. |
| Hậu điều kiện | Lưu tiến độ và kết quả học/kiểm tra mới; chưa tự đóng yêu cầu đào tạo lại. |
| Luồng chính | 1. Nhân viên xem nội dung, thời hạn và người phụ trách qua thông tin được giao.<br>2. Nhân viên mở các bài học cần học lại.<br>3. Thực hiện học và ghi nhận tiến độ theo UC-NV02.<br>4. Thực hiện lượt kiểm tra mới theo UC-NV03 hoặc UC-NV04.<br>5. Hệ thống lưu kết quả làm căn cứ kiểm tra điều kiện của vòng.<br>6. Nhân viên phối hợp thực hành hoặc được hướng dẫn ngoài phần mềm theo kế hoạch. |
| Luồng thay thế | A1. Vòng không giao bài học trực tuyến: nhân viên thực hiện hướng dẫn/thực hành ngoài phần mềm theo nội dung được giao.<br>A2. Quiz mới chưa đạt: tiếp tục học và kiểm tra lại. |
| Ngoại lệ | Nội dung giao không còn hợp lệ hoặc không tải được: chưa ghi nhận hoàn thành phần đó. |

#### UC-CH08 — Xác nhận hoàn thành nội dung đào tạo lại

| Thuộc tính | Đặc tả |
| --- | --- |
| Mục tiêu | Ghi nhận xác nhận của người phụ trách cho vòng đào tạo hiện tại. |
| Tác nhân | Nhân viên hoặc Trưởng ca được giao phụ trách. |
| Kích hoạt | Người phụ trách mở nhiệm vụ được giao và chọn xác nhận. |
| Tiền điều kiện | Đã đăng nhập đúng tài khoản được giao; yêu cầu đã duyệt, vòng đang thực hiện; các bài học và quiz yêu cầu đã đáp ứng điều kiện. |
| Hậu điều kiện | Lưu người xác nhận, thời điểm và ghi chú; yêu cầu vẫn mở để quản lý lập lịch đánh giá lại. |
| Luồng chính | 1. Người phụ trách mở danh sách nhiệm vụ.<br>2. Hệ thống hiển thị vòng được giao và điều kiện còn thiếu.<br>3. Người phụ trách kiểm tra kết quả thực hành/hướng dẫn ngoài phần mềm.<br>4. Người phụ trách nhập ghi chú xác nhận và gửi.<br>5. Hệ thống kiểm tra quyền, trạng thái và điều kiện học/quiz của vòng.<br>6. Hệ thống lưu xác nhận hoàn thành nội dung. |
| Luồng thay thế | Chưa hoàn thành bài học hoặc chưa có quiz mới đạt chuẩn: hiển thị phần còn thiếu, chưa lưu xác nhận. |
| Ngoại lệ | Người gửi không được giao phụ trách hoặc vòng đã chuyển trạng thái: từ chối xác nhận. Ghi chú trống: yêu cầu bổ sung. Lỗi lưu: chưa xác nhận thành công. |

#### UC-QL09 — Tạo lịch đánh giá lại

| Thuộc tính | Đặc tả |
| --- | --- |
| Mục tiêu | Tổ chức đánh giá lại sau khi hoàn thành điều kiện của vòng đào tạo. |
| Tác nhân | Quản lý cửa hàng. |
| Kích hoạt | Quản lý chọn tạo lịch đánh giá lại từ vòng đang thực hiện. |
| Tiền điều kiện | Yêu cầu đã duyệt; vòng hiện tại đang thực hiện; nhân viên thuộc phạm vi cửa hàng và đối tượng đánh giá. |
| Hậu điều kiện | Tạo hồ sơ đánh giá liên kết đúng vòng; chuyển tiến trình sang Chờ đánh giá lại; thông báo lịch cho nhân viên và trưởng ca được phân công. |
| Luồng chính | 1. Quản lý mở vòng hiện tại.<br>2. Hệ thống kiểm tra hoàn thành bài học, quiz đạt trong vòng hiện tại và xác nhận của người phụ trách.<br>3. Quản lý chọn trưởng ca đánh giá, nhập ngày, giờ và tiêu chí.<br>4. Hệ thống kiểm tra thông tin.<br>5. Hệ thống tạo hồ sơ Chờ đánh giá và liên kết với vòng.<br>6. Hệ thống cập nhật tiến trình Chờ đánh giá lại.<br>7. Hệ thống gửi thông báo lịch.<br>8. Trưởng ca tiếp tục UC-TC03; quản lý xử lý kết quả qua UC-QL05. |
| Luồng thay thế | A1. Chưa đủ điều kiện: liệt kê bài học, quiz hoặc xác nhận còn thiếu.<br>A2. Có quiz đạt trước vòng hiện tại nhưng chưa có lượt mới đạt: chưa đủ điều kiện đánh giá lại. |
| Ngoại lệ | Vòng không còn Đang thực hiện, lịch không hợp lệ hoặc người đánh giá không thuộc nhóm được phép: không tạo lịch. Lỗi lưu: chưa xác nhận thành công. |

#### UC-QL10 — Xem thông tin nguy cơ dự đoán

| Thuộc tính | Đặc tả |
| --- | --- |
| Mục tiêu | Tham khảo nguy cơ chưa đạt chuẩn để ưu tiên theo dõi hồ sơ nhân viên. |
| Tác nhân | Quản lý cửa hàng. |
| Kích hoạt | Quản lý mở tổng quan, danh sách hoặc hồ sơ có thông tin nguy cơ. |
| Tiền điều kiện | Đã đăng nhập với quyền quản lý; nhân viên thuộc cửa hàng. Không bắt buộc đã có dự đoán. |
| Hậu điều kiện | Hiển thị kết quả nguy cơ đã lưu hoặc trạng thái chưa đủ dữ liệu; không thay đổi điểm đánh giá hay tự tạo quyết định đào tạo lại. |
| Luồng chính | 1. Quản lý mở danh sách hoặc hồ sơ.<br>2. Hệ thống đọc kết quả dự đoán gần nhất đã lưu.<br>3. Hệ thống hiển thị mức nguy cơ và thông tin thời điểm tính khi có dữ liệu.<br>4. Quản lý đối chiếu với tiến độ học, kiểm tra và kết quả đánh giá đã duyệt.<br>5. Quản lý sử dụng thông tin để xem xét theo dõi phù hợp. |
| Luồng thay thế | A1. Chưa có dự đoán hoặc thiếu dữ liệu: hiển thị Chưa đủ dữ liệu.<br>A2. Dự đoán khác kết quả đánh giá: giữ hai thông tin riêng, không thay thế kết quả đã duyệt. |
| Ngoại lệ | Không tải được dữ liệu: không suy diễn thành nguy cơ thấp hoặc kết quả đạt. |

### e. Các quy tắc dùng chung và tính nhất quán

1. **Phân biệt hai loại kết quả.** Điểm kiểm tra kiến thức được so với điểm chuẩn riêng của bài kiểm tra. Kết quả đánh giá năng lực sử dụng năm tiêu chí có trọng số; đạt khi tổng từ 80 và từng tiêu chí từ 70.
2. **Phê duyệt trước công bố.** Kết quả trưởng ca gửi mới ở trạng thái chờ phê duyệt. Nguyên tắc này áp dụng cả đánh giá ban đầu, định kỳ và đánh giá lại.
3. **Một quy trình đào tạo lại nhiều vòng.** Đào tạo bổ sung là một nội dung cải thiện có thể được chọn trong đề xuất, không xây dựng thành quy trình độc lập chỉ chuyển sang đào tạo lại khi “không tiến bộ”.
4. **Không duyệt đề xuất hai lần bắt buộc.** Phê duyệt đánh giá chưa đạt có thể tạo yêu cầu đào tạo lại đã duyệt. UC-QL06 chỉ áp dụng khi yêu cầu thực sự ở trạng thái Chờ duyệt.
5. **Kết quả của vòng phải có tính mới.** Các bài học và quiz được yêu cầu phải đáp ứng điều kiện của vòng hiện tại; kết quả quiz trước vòng không thay thế lượt mới cần thực hiện.
6. **Xác nhận không đồng nghĩa kết thúc.** Người được giao phụ trách xác nhận nội dung; quản lý tạo lịch đánh giá lại khi đủ điều kiện. Yêu cầu chỉ hoàn tất sau khi kết quả đánh giá lại Đạt được phê duyệt. Nếu Chưa đạt đã duyệt, tiếp tục giao vòng sau và giữ lịch sử.
7. **Dự đoán chỉ hỗ trợ theo dõi.** Chương trình dự đoán chạy độc lập và ghi kết quả để web đọc. Mô hình không phải tác nhân người dùng, không tự phê duyệt, tự giao đào tạo lại hoặc được cộng vào điểm đánh giá.

Các use case trên liên kết với tám nhóm chức năng của BFD: tài khoản và cài đặt; học tập và tiến độ; kiểm tra và kết quả; nhân sự và tổng quan; đánh giá và phê duyệt; đào tạo lại nhiều vòng; dự đoán nguy cơ; thông báo và hỗ trợ. Những thao tác tự động như tính điểm, kiểm tra điều kiện, lưu lịch sử và cập nhật trạng thái được mô tả trong luồng xử lý, không bổ sung thành chức danh nghiệp vụ mới.
