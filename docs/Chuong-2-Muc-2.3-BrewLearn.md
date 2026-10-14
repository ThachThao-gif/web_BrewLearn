## 2.3 Yêu cầu và quy tắc nghiệp vụ

### 2.3.1 Yêu cầu chức năng theo vai trò

Các yêu cầu chức năng được xác định từ ba quy trình TO-BE tại mục 2.2, tập trung vào học tập, kiểm tra, đánh giá, phê duyệt và đào tạo lại. Phạm vi gồm ba vai trò Nhân viên, Trưởng ca và Quản lý cửa hàng; chức năng tài khoản, phân ca, thông báo, tra cứu và hỗ trợ phục vụ quy trình chính, không được mở rộng thành một hệ thống quản trị nhân sự toàn diện.

**Đối với nhân viên.** Hệ thống cung cấp lộ trình, khóa học, bài học và bài kiểm tra phù hợp với chức danh. Nhân viên có thể học, theo dõi tiến độ, làm hoặc thi lại bài kiểm tra và xem kết quả của các lượt thực hiện. Nhân viên được theo dõi lịch đánh giá, nhận thông báo và xem kết quả đánh giá chính thức sau khi quản lý phê duyệt. Khi có kế hoạch đào tạo lại, hệ thống hiển thị nội dung được giao, người phụ trách và thời hạn để nhân viên thực hiện. Các chức năng tài khoản và gửi yêu cầu hỗ trợ được sử dụng trong phạm vi cá nhân.

**Đối với trưởng ca.** Hệ thống cho phép theo dõi tiến độ và tình hình đánh giá của nhân viên trong phạm vi phụ trách, kiểm tra điều kiện và tạo lịch đánh giá lần đầu. Trưởng ca chấm điểm theo năm tiêu chí, nhập nhận xét, chọn đề xuất xử lý và cung cấp nội dung cần cải thiện khi nhân viên chưa đạt. Sau khi gửi kết quả, Trưởng ca theo dõi việc phê duyệt; nếu bị trả lại, có thể bổ sung rồi gửi lại cùng bản ghi. Trưởng ca thực hiện đánh giá lại khi được phân công; việc xác nhận hoàn thành nội dung đào tạo lại chỉ thực hiện khi tài khoản đó là người phụ trách của vòng. Ngoài ra, Trưởng ca có hoạt động tự học và làm kiểm tra theo nội dung áp dụng, nhưng không thuộc đối tượng đánh giá năng lực nhân viên trong phạm vi đề tài. Ghi nhận lỗi nghiệp vụ và gửi nhắc việc là các chức năng hỗ trợ.

**Đối với quản lý cửa hàng.** Hệ thống cung cấp thông tin tổng quan, hồ sơ nhân viên và lịch sử đào tạo tại cửa hàng. Quản lý xem xét, phê duyệt hoặc trả lại đánh giá kèm lý do; giao nội dung đào tạo lại, phân công người phụ trách và theo dõi các vòng. Khi vòng đáp ứng điều kiện, quản lý tạo lịch đánh giá lại và phân công Trưởng ca chấm. Quản lý duyệt kết quả đánh giá lại để làm căn cứ hoàn thành yêu cầu hoặc tiếp tục giao kế hoạch. Thông tin xu hướng đánh giá đã duyệt và nguy cơ dự đoán đã lưu hỗ trợ xem xét hồ sơ. Phân ca và cập nhật trạng thái làm việc được trình bày như chức năng hỗ trợ; phạm vi giao diện quản lý không bao gồm tự học và kiểm tra cá nhân.

**Đối với xử lý tự động của hệ thống.** Hệ thống lấy nội dung theo chức danh, ghi nhận tiến độ, tính điểm quiz và điểm đánh giá từ dữ liệu đã nhập, kiểm tra điều kiện chuyển bước, lưu lịch sử và thông báo. Chương trình Python chạy riêng để tổng hợp dữ liệu, dự đoán và lưu kết quả cho website đọc. Kết quả này không tự tạo lịch, không phê duyệt đánh giá và không tự giao vòng đào tạo lại.

### 2.3.2 Yêu cầu phi chức năng thiết yếu

Các yêu cầu phi chức năng tập trung vào khả năng sử dụng, tính nhất quán dữ liệu và kiểm soát truy cập trong phạm vi đồ án. Đây là các yêu cầu và tiêu chí thiết kế; không được diễn giải thành kết luận hệ thống đã đáp ứng ở quy mô doanh nghiệp khi chưa có kiểm thử tương ứng.

**Dễ sử dụng và rõ thông tin.** Giao diện cần tổ chức theo vai trò, sử dụng tên chức năng dễ hiểu và thể hiện rõ nội dung cần thực hiện. Tiến độ học, kết quả quiz, lịch đánh giá, trạng thái phê duyệt và kế hoạch đào tạo lại cần được phân biệt. Khi chưa đủ điều kiện, hệ thống cần thông báo phần còn thiếu để người dùng biết cách tiếp tục. Thuật ngữ đạt/chưa đạt không được sử dụng thay cho duyệt/trả lại.

**Nhất quán và có thể đối chiếu dữ liệu.** Các màn hình cùng sử dụng dữ liệu và quy tắc tính tương ứng để tránh hiển thị mâu thuẫn. Lượt kiểm tra, đánh giá và vòng đào tạo lại cần gắn đúng nhân viên, người thực hiện và thời điểm. Các thao tác nhiều bước có liên quan cần bảo đảm cập nhật nhất quán; việc gửi lặp không được tạo thêm kết quả nghiệp vụ ngoài ý muốn. Lịch sử được giữ để đối chiếu thay vì ghi đè kết quả của các vòng đã kết thúc.

**Kiểm soát truy cập.** Người dùng phải được xác thực trước khi thực hiện chức năng nghiệp vụ. Truy cập dữ liệu cá nhân, phạm vi ca hoặc cửa hàng và các thao tác chấm, duyệt, giao kế hoạch cần được kiểm tra tại phía xử lý yêu cầu, không chỉ dựa vào việc ẩn nút trên giao diện. Quyền xác nhận đào tạo lại gắn với người được phân công. Thông tin nhân viên và kết quả đánh giá cần được giới hạn theo trách nhiệm sử dụng.

**Phản hồi và tổ chức xử lý.** Hệ thống cần thông báo khi lưu thành công hoặc khi thao tác bị từ chối do dữ liệu không hợp lệ, trạng thái không phù hợp hay thiếu điều kiện. Thông báo được lưu để người dùng đọc trên ứng dụng; không đặt yêu cầu thông báo đẩy thời gian thực. Dự đoán chạy riêng và website đọc kết quả đã lưu, tránh gộp quá trình huấn luyện hoặc dự đoán vào từng thao tác học tập. Đề tài không đặt ngưỡng thời gian phản hồi hoặc số người dùng đồng thời khi chưa có cơ sở đo lường.

### 2.3.3 Quy tắc học tập, kiểm tra và đánh giá

**Nội dung theo chức danh.** Khóa học, bài học và bài kiểm tra được xác định theo chức danh của người học. Điều kiện đánh giá lần đầu được kiểm tra trên các nội dung cần thực hiện, không chỉ dựa vào một tỷ lệ tổng hợp hoặc một bài kiểm tra riêng lẻ. Trường hợp chưa có khóa học hoặc bài học được áp dụng, hệ thống chưa xác nhận đủ điều kiện.

**Ghi nhận hoàn thành bài học.** Hệ thống lưu tiến độ từng bài và tổng hợp tiến độ khóa học. Trong xử lý kiểm tra điều kiện hiện tại, một bài học được chấp nhận khi đã có cờ hoàn thành hoặc tiến độ từ 95%. Ngưỡng này phục vụ kiểm tra điều kiện học tập; không phải ngưỡng điểm quiz hay điểm đánh giá năng lực. Chi tiết cách cập nhật và tổng hợp tiến độ được trình bày ở Chương 3.

**Điểm và lịch sử kiểm tra.** Mỗi lượt kiểm tra được tính điểm từ đáp án và lưu thành lịch sử. Điểm đạt được so với DiemChuan của bài kiểm tra đó; giá trị 80 chỉ là giá trị mặc định khi ngưỡng chưa được cung cấp, không phải quy tắc cố định cho mọi bài. Nhân viên được thi lại; khi xét điều kiện, hệ thống kiểm tra có lượt đạt trong phạm vi dữ liệu hợp lệ bằng điểm cao nhất của các lượt tương ứng. Điểm các bài kiểm tra không được gộp trực tiếp thành điểm đánh giá năng lực thực hành.

**Điều kiện tạo lịch lần đầu.** Nhân viên phải đang làm việc và thuộc phạm vi ca/cửa hàng mà Trưởng ca được phụ trách, hoàn thành các bài học và đạt các bài kiểm tra cần thiết. Không tạo thêm lịch lần đầu nếu nhân viên có lượt ở trạng thái chờ đánh giá, chờ phê duyệt hoặc trả lại chưa xử lý. Khi có yêu cầu đào tạo lại đang mở, việc đánh giá lại phải được tổ chức từ vòng hiện tại, không tạo một lượt lần đầu để thay thế quy trình đó.

**Cách tính điểm đánh giá.** Năm tiêu chí được nhập theo thang 0–100. Trọng số lần lượt là nghiệp vụ 20%, kỹ năng 30%, quy trình 20%, phục vụ 15% và tác phong 15%. Điểm tổng được tính theo công thức:

Điểm tổng = 0,20 × Điểm nghiệp vụ + 0,30 × Điểm kỹ năng + 0,20 × Điểm quy trình + 0,15 × Điểm phục vụ + 0,15 × Điểm tác phong.

Kết quả được làm tròn đến một chữ số thập phân. Nhân viên đạt khi điểm tổng từ 80 và cả năm điểm tiêu chí đều từ 70. Nếu một trong hai điều kiện không thỏa mãn, kết quả là chưa đạt. Chẳng hạn, tổng điểm từ 80 nhưng một tiêu chí dưới 70 vẫn được xác định là chưa đạt. Quy tắc này áp dụng cho cả đánh giá lần đầu và đánh giá lại.

**Đề xuất khi chưa đạt.** Trưởng ca phải chọn ít nhất một đề xuất và nhập nội dung chi tiết tương ứng. Các hình thức hiện có gồm đào tạo bổ sung, học lại bài học, theo dõi ca và đánh giá lại. Đề xuất cần nêu nội dung cải thiện và thông tin phù hợp với hình thức đã chọn, làm căn cứ để quản lý xem xét và giao kế hoạch; không đồng nghĩa yêu cầu đào tạo lại đã được duyệt ngay tại thời điểm chấm.

### 2.3.4 Quy tắc phê duyệt và đào tạo lại

**Phê duyệt áp dụng cho mọi đánh giá.** Kết quả do Trưởng ca gửi chỉ trở thành kết quả chính thức sau khi quản lý phê duyệt, bao gồm đánh giá lần đầu và đánh giá lại. Phê duyệt xác nhận hồ sơ đánh giá đủ căn cứ; không đồng nghĩa nhân viên đã đạt. Quản lý có thể duyệt một kết quả chưa đạt kèm đề xuất xử lý đầy đủ, nhưng không duyệt hồ sơ chưa có kết quả theo tiêu chí hoặc thiếu đề xuất cần thiết.

**Trả lại và gửi lại.** Quản lý trả lại phải cung cấp lý do. Trưởng ca bổ sung hoặc chỉnh sửa cùng bản ghi rồi gửi lại để xem xét; không chuyển trực tiếp từ trả lại sang đã duyệt. Luồng dữ liệu tương ứng là ChoDanhGia → ChoPheDuyet; quản lý quyết định DaDuyet hoặc TraLai; sau bổ sung, TraLai → ChoPheDuyet. Bản ghi đang chờ duyệt hoặc đã duyệt không được sửa như bản ghi đang chờ chấm.

**Tạo yêu cầu từ đánh giá chưa đạt đã duyệt.** Khi quản lý duyệt đánh giá chưa đạt có đề xuất, hệ thống tạo yêu cầu đào tạo lại liên kết với đánh giá nguồn tại trang_thai = DaDuyet và tien_trinh = ChoGiao. Luồng này không cần duyệt yêu cầu thêm lần nữa. Một đánh giá nguồn không được tạo nhiều yêu cầu trùng. Phần phân tích không bổ sung một luồng tạo đề xuất độc lập khi luồng đó chưa được xác nhận trong phạm vi chức năng đang sử dụng.

**Giao vòng và lưu lịch sử.** Một yêu cầu có thể có nhiều vòng đào tạo lại. Quản lý chủ động giao nội dung, người phụ trách, thời hạn và yêu cầu học/quiz; mỗi vòng có số thứ tự trong yêu cầu. Khi điều chỉnh vòng đang thực hiện, không mặc định tạo thêm vòng mới. Vòng tiếp theo được giao sau khi kết quả chưa đạt của vòng trước đã được phê duyệt và yêu cầu trở về chờ giao. Các vòng đã kết thúc được giữ lại cùng kết quả, người duyệt và thời điểm duyệt.

**Phân công và xác nhận.** Người phụ trách phải thuộc phạm vi cửa hàng và không phải chính nhân viên đang được đào tạo. Chỉ người được phân công cho vòng mới có quyền xác nhận hoàn thành. Xác nhận này ghi nhận nội dung thực hành hoặc theo dõi đã thực hiện, không thay thế đánh giá năng lực. Khi đổi người phụ trách hoặc nội dung hướng dẫn của vòng đang thực hiện, xác nhận cũ được xóa để yêu cầu xác nhận lại.

**Điều kiện đánh giá lại.** Quản lý chỉ tạo lịch khi vòng đang thực hiện đáp ứng các nội dung học/quiz được yêu cầu và đã có xác nhận của người phụ trách. Với bài học được giao học lại, dữ liệu tiến độ phải được cập nhật trong vòng; với quiz, cần lượt thực hiện mới thuộc vòng hiện tại và đạt DiemChuan của từng bài. Kết quả đạt cũ trước khi giao vòng không đủ để thay thế việc thực hiện lại. Nếu vòng không có yêu cầu học hoặc quiz tương ứng, không tự thêm điều kiện đó cho vòng. Chưa đủ điều kiện thì tiếp tục hoàn thành vòng hiện tại, không tạo lịch hoặc tạo vòng mới để bỏ qua điều kiện.

**Phân biệt trả lại với chưa đạt.** Nếu quản lý trả lại đánh giá lại, Trưởng ca sửa và gửi lại cùng bản ghi; yêu cầu vẫn đang xử lý, chưa có kết quả chính thức để kết thúc hoặc giao vòng tiếp theo. Chỉ khi quản lý duyệt kết quả chưa đạt, hệ thống lưu kết quả vòng và đưa yêu cầu về chờ giao kế hoạch. Do đó, trả lại đánh giá không phải là một lần đào tạo lại mới.

**Kết thúc hoặc tiếp tục yêu cầu.** Khi đánh giá lại đạt và đã được duyệt, vòng có status = DaDuyet, result = Dat; yêu cầu chuyển trang_thai = HoanThanh, tien_trinh = HoanTat. Nếu chưa đạt đã duyệt, vòng có status = DaDuyet, result = ChuaDat; yêu cầu chuyển trang_thai = DaDuyet, tien_trinh = ChoGiao để quản lý giao vòng tiếp theo. Không giới hạn số vòng trong luồng thiết kế. Tạo lịch, hoàn thành bài học hoặc xác nhận của người phụ trách đều chưa đủ để đóng yêu cầu.

**Tách biệt dự đoán và quyết định nghiệp vụ.** Thông tin nguy cơ chỉ hỗ trợ quản lý tham khảo, không được gộp vào điểm đánh giá hoặc dùng để tự phê duyệt và phân công đào tạo lại. Mô hình hiện tại sử dụng nhãn giả lập. Khi phát triển với dữ liệu thật, nhãn phải lấy từ kết quả đánh giá đã được quản lý duyệt, loại bỏ đánh giá đang chờ duyệt hoặc bị trả lại khỏi nguồn nhãn chính thức và xác định biến đầu vào trước thời điểm kết quả dùng làm nhãn.

Các yêu cầu và quy tắc trên làm căn cứ xây dựng mô hình chức năng, dòng dữ liệu tại mục 2.4 và thiết kế xử lý ở Chương 3. Việc giữ riêng điều kiện học tập, kết quả năng lực, quyết định phê duyệt và tiến trình đào tạo lại giúp tránh nhầm lẫn khi đối chiếu sơ đồ với dữ liệu và giao diện.
