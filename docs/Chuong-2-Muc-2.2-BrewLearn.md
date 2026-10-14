## 2.2 Quy trình đề xuất TO-BE

### 2.2.1 Ranh giới quy trình và trách nhiệm

Trên cơ sở quy trình AS-IS và các nhu cầu cải tiến tại mục 2.1, đề tài đề xuất quy trình TO-BE nhằm quản lý tập trung thông tin học tập, kiểm tra, đánh giá và đào tạo lại. Quy trình áp dụng cho nhân viên mới và nhân viên đang làm việc thuộc phạm vi đào tạo, không giới hạn ở giai đoạn thử việc. Trọng tâm cải tiến là ghi nhận dữ liệu, xác định điều kiện chuyển bước, phân biệt kết quả đang xem xét với kết quả chính thức và lưu lịch sử các lần xử lý.

Phạm vi sử dụng hệ thống gồm ba vai trò: Nhân viên, Trưởng ca và Quản lý cửa hàng. So với bốn làn của AS-IS, HR không được xây dựng thành vai trò sử dụng riêng; các hoạt động nhân sự ngoài quản lý đào tạo tại cửa hàng nằm ngoài phạm vi đồ án. Ca trưởng trong AS-IS tương ứng với Trưởng ca trong TO-BE. Việc giới hạn vai trò là quyết định về phạm vi thiết kế, không đồng nghĩa bộ phận HR bị loại khỏi tổ chức.

**Nhân viên** thực hiện nội dung học và kiểm tra phù hợp chức danh, theo dõi lịch đánh giá, xem kết quả đã được phê duyệt và hoàn thành nội dung đào tạo lại được giao. **Trưởng ca** theo dõi nhân viên trong phạm vi phụ trách, tạo lịch đánh giá lần đầu, chấm điểm, ghi nhận xét, đề xuất khi chưa đạt và bổ sung đánh giá bị trả lại. **Quản lý cửa hàng** xem xét, phê duyệt hoặc trả lại kết quả; giao kế hoạch đào tạo lại, phân công người phụ trách, tạo lịch đánh giá lại và quyết định kết thúc yêu cầu trên cơ sở kết quả đã duyệt.

Người phụ trách đào tạo lại là người được quản lý phân công cho từng vòng, không mặc định là người chấm đánh giá lại hoặc chính nhân viên đang được đào tạo. Trong sơ đồ đào tạo lại, lane “Trưởng ca / Người phụ trách” thể hiện hai trách nhiệm: người được phân công xác nhận hoàn thành nội dung, còn hoạt động chấm đánh giá lại thuộc Trưởng ca. Nếu người phụ trách được chọn có vai trò khác, trách nhiệm xác nhận vẫn gắn với phân công của vòng, không tạo thêm một vai trò hệ thống thứ tư.

TO-BE được tách thành ba quy trình liên quan: học tập và kiểm tra; đánh giá và phê duyệt; đào tạo lại và đánh giá lại. Sơ đồ học tập có lane Hệ thống BrewLearn để biểu diễn các hoạt động tự động như lấy nội dung, lưu tiến độ và tính điểm; đây là thành phần xử lý, không phải vai trò người dùng. Các tác vụ lưu dữ liệu và gửi thông báo trong hai sơ đồ còn lại được thực hiện bởi hệ thống theo thao tác của vai trò tương ứng. Phần dự đoán nguy cơ được xử lý độc lập, không quyết định các nhánh đạt/chưa đạt hoặc tự giao đào tạo lại trong ba quy trình này.

### 2.2.2 Quy trình học tập và kiểm tra

Quy trình tổ chức việc tiếp cận nội dung theo chức danh, ghi nhận tiến độ và kiểm tra kiến thức trước khi xem xét điều kiện đánh giá năng lực. Đầu ra là thông tin về mức độ hoàn thành các yêu cầu học tập và kiểm tra, không phải kết luận nhân viên đã đạt chuẩn năng lực thực hành.

[CHÈN SƠ ĐỒ BPMN TO-BE HỌC TẬP VÀ KIỂM TRA TẠI ĐÂY]

Hình 2.2 Quy trình học tập và kiểm tra đề xuất tại BrewLearn

**Đăng nhập và lấy nội dung phù hợp.** Nhân viên đăng nhập tài khoản. Hệ thống lấy các khóa học, bài học và bài kiểm tra được áp dụng theo chức danh. Nhân viên xem lộ trình, khóa học và nội dung cần thực hiện. Việc hiển thị theo chức danh giúp tập trung vào yêu cầu đào tạo của vị trí công việc.

**Học và thực hiện kiểm tra.** Nhân viên học, hoàn thành bài học được giao và làm bài kiểm tra tương ứng. Nếu cần, nhân viên có thể thi lại để cải thiện kết quả. Hệ thống ghi nhận tiến độ, lưu từng lượt kiểm tra và tính điểm từ các đáp án. Kết quả được đối chiếu với DiemChuan của từng bài kiểm tra, không áp dụng một ngưỡng cố định cho mọi bài.

**Xác định điều kiện chuyển bước.** Tại gateway, hệ thống kiểm tra việc hoàn thành các bài học và đạt các bài kiểm tra cần thiết. Nếu chưa đủ, nhân viên học hoặc ôn phần còn thiếu và thi lại bài chưa đạt, sau đó tiếp tục được kiểm tra điều kiện. Nếu đủ, quy trình học tập kết thúc với đầu ra đủ điều kiện học/quiz để được xem xét bố trí đánh giá.

Điều kiện học/quiz là một phần của điều kiện tạo lịch. Trước khi lưu lịch, hệ thống còn kiểm tra phạm vi phụ trách và tình trạng các hồ sơ đang xử lý. Do đó, kết thúc quy trình học tập không tự tạo lịch đánh giá và không tự xác nhận kết quả năng lực. Trưởng ca cũng có hoạt động tự học và làm kiểm tra theo nội dung áp dụng, nhưng không thuộc đối tượng đánh giá năng lực nhân viên trong phạm vi đề tài.

### 2.2.3 Quy trình đánh giá và phê duyệt

Quy trình này tổ chức đánh giá lần đầu và xem xét kết quả theo hai trách nhiệm tách biệt: Trưởng ca ghi nhận kết quả, Quản lý cửa hàng phê duyệt hoặc trả lại. Kết quả đạt/chưa đạt và quyết định duyệt/trả lại là hai nội dung khác nhau; một kết quả chưa đạt vẫn có thể được phê duyệt nếu hồ sơ đầy đủ và có đề xuất xử lý phù hợp.

[CHÈN SƠ ĐỒ BPMN TO-BE ĐÁNH GIÁ VÀ PHÊ DUYỆT TẠI ĐÂY]

Hình 2.3 Quy trình đánh giá và phê duyệt đề xuất tại BrewLearn

**Kiểm tra điều kiện và tạo lịch.** Trưởng ca chọn nhân viên trong phạm vi phụ trách. Hệ thống kiểm tra điều kiện học/quiz và các ràng buộc hồ sơ đang xử lý; khi có yêu cầu đào tạo lại đang mở, đánh giá lại phải được tạo từ vòng tương ứng. Nếu chưa đủ điều kiện, chưa tạo lịch và tiếp tục chờ nhân viên hoàn thành yêu cầu. Khi hợp lệ, Trưởng ca tạo lịch, hệ thống lưu hồ sơ chờ đánh giá và thông báo để nhân viên tham gia đúng thời gian.

**Chấm và gửi kết quả.** Trưởng ca đánh giá theo năm nhóm tiêu chí: nghiệp vụ, kỹ năng, quy trình, phục vụ và tác phong, đồng thời nhập nhận xét. Điểm tổng được tính theo trọng số; nhân viên đạt khi tổng điểm từ 80 và từng tiêu chí từ 70. Khi chưa đạt, Trưởng ca kèm đề xuất xử lý và nội dung cần cải thiện. Sau khi gửi, bản ghi chuyển sang chờ phê duyệt, chưa phải kết quả chính thức.

**Xem xét, phê duyệt hoặc trả lại.** Quản lý xem kết quả, nhận xét và đề xuất. Nếu cần chỉnh sửa, quản lý trả lại kèm lý do; Trưởng ca bổ sung rồi gửi lại để quản lý xem xét. Luồng này xử lý cùng bản ghi đánh giá, không chuyển trực tiếp từ trả lại sang đã duyệt. Khi hồ sơ đủ căn cứ, quản lý phê duyệt và hệ thống thông báo kết quả cho nhân viên.

**Xử lý kết quả đã duyệt.** Gateway tiếp theo xác định kết quả đạt hay chưa đạt. Nếu đạt, lượt đánh giá kết thúc. Nếu chưa đạt và có đề xuất đào tạo lại, hệ thống tạo yêu cầu ở trạng thái đã duyệt, chờ giao kế hoạch, làm đầu vào cho quy trình tại mục 2.2.4. Việc phê duyệt đánh giá đồng thời chấp thuận đề xuất đi kèm, nên không phát sinh thêm bước duyệt riêng cho yêu cầu được tạo theo luồng này.

Trong dữ liệu hiện tại, bản ghi đánh giá đi từ ChoDanhGia đến ChoPheDuyet; quản lý chuyển thành DaDuyet hoặc TraLai. Sau bổ sung, bản ghi TraLai được gửi lại ChoPheDuyet. Yêu cầu phát sinh từ kết quả chưa đạt đã duyệt được tạo với trang_thai = DaDuyet và tien_trinh = ChoGiao. Các tên này được giải thích để đối chiếu dữ liệu; các ô tác vụ trong BPMN tập trung diễn đạt hành động nghiệp vụ.

### 2.2.4 Quy trình đào tạo lại và đánh giá lại

Quy trình tiếp nhận yêu cầu đào tạo lại từ kết quả chưa đạt đã được quản lý phê duyệt. Một yêu cầu có thể trải qua nhiều vòng; mỗi vòng lưu nội dung, người phụ trách, thời hạn, xác nhận và kết quả đánh giá riêng. Quy trình chỉ kết thúc yêu cầu khi nhân viên đạt trong một lượt đánh giá lại đã được quản lý phê duyệt.

[CHÈN SƠ ĐỒ BPMN TO-BE ĐÀO TẠO LẠI VÀ ĐÁNH GIÁ LẠI TẠI ĐÂY]

Hình 2.4 Quy trình đào tạo lại và đánh giá lại đề xuất tại BrewLearn

**Giao kế hoạch đào tạo.** Quản lý xác định kế hoạch, nội dung cần học lại hoặc thực hành, người phụ trách và thời hạn. Hệ thống ghi nhận vòng đào tạo, các yêu cầu học/quiz và dữ liệu mốc để theo dõi việc thực hiện trong vòng hiện tại. Phân công được thông báo cho nhân viên và người phụ trách. Việc tạo hoặc giao vòng tiếp theo là thao tác chủ động của quản lý, không phải quyết định tự động từ mô hình dự đoán.

**Thực hiện và xác nhận.** Nhân viên học lại, thực hành và làm các bài kiểm tra được yêu cầu. Nếu vòng có nội dung học/quiz, việc xác định hoàn thành dựa trên dữ liệu thực hiện trong vòng, không chỉ sử dụng kết quả đạt đã có trước khi giao kế hoạch. Người được phân công xác nhận hoàn thành nội dung thực hành hoặc theo dõi. Quyền xác nhận gắn với người phụ trách của vòng; thay đổi người phụ trách hoặc nội dung hướng dẫn có thể làm mất hiệu lực xác nhận trước đó và yêu cầu xác nhận lại.

**Kiểm tra điều kiện và tạo lịch đánh giá lại.** Quản lý kiểm tra điều kiện của vòng gồm các nội dung học/quiz được yêu cầu và xác nhận của người phụ trách. Nếu chưa đủ, tiếp tục hoàn thành phần còn thiếu trong vòng hiện tại và chưa tạo lịch. Nếu đủ, quản lý tạo lịch, phân công Trưởng ca đánh giá lại và hệ thống thông báo đến các bên. Tạo lịch không đồng nghĩa vòng hoặc yêu cầu đã hoàn thành.

**Đánh giá lại và phê duyệt.** Trưởng ca chấm đánh giá lại, nhập nhận xét, kèm đề xuất nếu chưa đạt và gửi để quản lý xem xét. Quy tắc điểm đạt và phê duyệt áp dụng như đánh giá lần đầu. Nếu bị trả lại, Trưởng ca bổ sung, chỉnh sửa rồi gửi lại cùng bản ghi. Việc trả lại không tạo một vòng đào tạo mới; chỉ sau khi quản lý phê duyệt mới sử dụng kết quả để quyết định hướng xử lý của yêu cầu.

**Kết thúc hoặc tiếp tục đào tạo.** Nếu kết quả đã duyệt là đạt, hệ thống ghi nhận kết quả của vòng và xác nhận hoàn thành yêu cầu đào tạo lại. Nếu chưa đạt, hệ thống lưu kết quả vòng hiện tại và chuyển yêu cầu về chờ giao kế hoạch tiếp theo. Quản lý điều chỉnh nội dung, phân công và chủ động giao vòng mới. Sơ đồ không giới hạn số vòng; lịch sử các vòng được giữ lại để theo dõi quá trình cải thiện.

Trong cơ sở dữ liệu, khi đánh giá lại được duyệt, status của vòng chuyển thành DaDuyet và result là Dat hoặc ChuaDat. Với kết quả đạt, yêu cầu chuyển trang_thai = HoanThanh và tien_trinh = HoanTat. Với kết quả chưa đạt, yêu cầu trở về trang_thai = DaDuyet và tien_trinh = ChoGiao để chờ quản lý giao kế hoạch. Trạng thái xét duyệt của yêu cầu, tiến trình thực hiện và kết quả của vòng là các thông tin riêng, không được gộp thành một trạng thái chung.

Ba quy trình liên kết theo thông tin đầu ra: kết quả học/quiz hỗ trợ kiểm tra điều kiện tạo lịch; kết quả đánh giá chưa đạt đã duyệt tạo yêu cầu đào tạo lại; kết quả đánh giá lại đã duyệt quyết định hoàn thành hoặc tiếp tục yêu cầu. Cách tổ chức này bảo đảm giữ quyền quyết định của quản lý, đồng thời tạo căn cứ dữ liệu cho việc theo dõi đào tạo xuyên suốt.
