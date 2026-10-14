# ĐỀ CƯƠNG ĐỒ ÁN TỐT NGHIỆP

**Tên đề tài:** Phân tích và thiết kế hệ thống thông tin quản lý đào tạo và dự đoán nguy cơ nhân viên chưa đạt chuẩn năng lực tại chuỗi cửa hàng cà phê BrewLearn.

**Sinh viên thực hiện:** [Điền họ và tên sinh viên]

**Mã số sinh viên:** [Điền mã số sinh viên]

**Ngành/chuyên ngành:** [Điền theo thông tin đào tạo]

**Giảng viên hướng dẫn:** [Điền tên giảng viên]

**Thời gian thực hiện:** 07/09/2026–14/11/2026, theo kế hoạch trong bản đồ án hiện có; cập nhật nếu lịch được giảng viên điều chỉnh.

## 1. Lý do chọn đề tài

Trong hoạt động của chuỗi cửa hàng cà phê, năng lực của nhân viên pha chế, thu ngân và phục vụ ảnh hưởng trực tiếp đến chất lượng sản phẩm và trải nghiệm khách hàng. Đào tạo cần gắn với vị trí công việc, đồng thời có cơ chế theo dõi tiến độ, kiểm tra kiến thức và đánh giá khả năng thực hiện công việc. Khi nhân viên chưa đáp ứng yêu cầu, cửa hàng cần tổ chức đào tạo lại và ghi nhận quá trình cải thiện.

Trong bối cảnh doanh nghiệp giả định của đề tài, việc lưu trữ thông tin phân tán và tổng hợp thủ công có thể gây khó khăn khi xác định nội dung nhân viên đã học, kết quả kiểm tra, thời điểm đến hạn đánh giá và trách nhiệm thực hiện đào tạo lại. Nếu kết quả đánh giá và các vòng đào tạo lại không được liên kết, người quản lý khó theo dõi lịch sử và căn cứ của quyết định.

Đề tài xây dựng BrewLearn nhằm tổ chức dữ liệu và quy trình trên một hệ thống thống nhất. Bên cạnh các chức năng quản lý đào tạo, đề tài thử nghiệm mô hình dự đoán nguy cơ từ dữ liệu học tập và kiểm tra để hỗ trợ quản lý ưu tiên xem hồ sơ. Kết quả dự đoán được phân biệt với kết quả đánh giá thực tế đã được phê duyệt. Đề tài sử dụng bối cảnh giả định và dữ liệu mô phỏng, không trình bày như một khảo sát đã thực hiện tại doanh nghiệp có thật.

## 2. Mục tiêu thực hiện

### 2.1. Mục tiêu tổng quát

Phân tích và thiết kế hệ thống thông tin quản lý đào tạo nhân viên tại chuỗi cửa hàng cà phê BrewLearn; xây dựng nguyên mẫu web hỗ trợ học tập, kiểm tra, đánh giá năng lực, phê duyệt và đào tạo lại; thử nghiệm mô hình dự đoán nguy cơ chưa đạt chuẩn năng lực để hỗ trợ theo dõi nhân viên.

### 2.2. Mục tiêu cụ thể

- Mô tả quy trình hiện trạng AS-IS, xác định hạn chế và đề xuất quy trình TO-BE; phân định trách nhiệm bằng pool và lane trong sơ đồ quy trình.
- Xác định yêu cầu chức năng, yêu cầu phi chức năng và phạm vi truy cập của nhân viên, trưởng ca, quản lý cửa hàng.
- Trình bày BFD, use case, DFD ngữ cảnh và DFD mức 0, kèm thuyết minh để kết nối chức năng, tác nhân, luồng và kho dữ liệu.
- Thiết kế cơ sở dữ liệu, ERD, lược đồ quan hệ và từ điển dữ liệu cho học tập, kiểm tra, đánh giá và đào tạo lại nhiều vòng.
- Trình bày kiến trúc tổng thể và cơ chế trao đổi dữ liệu giữa ứng dụng web, cơ sở dữ liệu và chương trình dự đoán.
- Xây dựng và so sánh các mô hình trên dữ liệu giả lập, trình bày quy trình huấn luyện và tích hợp kết quả dự đoán.
- Trình bày giao diện theo ba nhóm vai trò, triển khai nguyên mẫu và kiểm thử các chức năng, quy tắc nghiệp vụ chính.

## 3. Các vấn đề cần giải quyết

1. Quy trình đào tạo cần được tổ chức như thế nào để theo dõi từ nội dung học đến kiểm tra, đánh giá và đào tạo lại?
2. Các tác nhân cần quyền hạn và thông tin gì để thực hiện đúng trách nhiệm trong cửa hàng?
3. Cấu trúc dữ liệu nào có thể lưu tiến độ, mọi lượt kiểm tra, quyết định phê duyệt và lịch sử từng vòng đào tạo lại?
4. Dữ liệu học tập và kiểm tra có thể được khai thác ra sao để thử nghiệm dự đoán nguy cơ trên dữ liệu giả lập?
5. Kết quả dự đoán cần được trình bày và sử dụng thế nào để hỗ trợ theo dõi nhưng không thay thế quyết định đánh giá?

## 4. Đối tượng và phạm vi

### 4.1. Đối tượng thực hiện

Đối tượng gồm quy trình đào tạo, kiểm tra kiến thức, đánh giá năng lực, phê duyệt và đào tạo lại của nhân viên cửa hàng cà phê; cấu trúc dữ liệu và phương pháp phân tích, thiết kế hệ thống; phương pháp xây dựng và tích hợp mô hình dự đoán từ dữ liệu học tập.

### 4.2. Phạm vi người dùng và chức năng

| Nhóm người dùng | Phạm vi chức năng |
| --- | --- |
| Nhân viên | Xem nội dung đào tạo theo chức vụ; học và theo dõi tiến độ; làm bài kiểm tra; theo dõi kết quả cá nhân; thực hiện nội dung học lại; sử dụng tài khoản, thông báo và hỗ trợ |
| Trưởng ca | Theo dõi nhân viên thuộc phạm vi phụ trách; nhắc việc, ghi nhận lỗi nghiệp vụ; tạo lịch khi đủ điều kiện; chấm điểm, nhận xét và đề xuất cải thiện; bổ sung đánh giá bị trả lại; xác nhận nội dung khi được giao phụ trách đào tạo lại |
| Quản lý cửa hàng | Xem tổng quan và hồ sơ; quản lý ca và trạng thái làm việc; phê duyệt hoặc trả lại đánh giá; phân công và theo dõi các vòng đào tạo lại; tạo lịch đánh giá lại khi đủ điều kiện; xem kết quả dự đoán đã lưu |

### 4.3. Phạm vi dữ liệu và giới hạn

Đề tài sử dụng dữ liệu giả lập để huấn luyện và đánh giá mô hình. Dữ liệu nguyên mẫu phục vụ minh họa và kiểm thử, chưa đại diện cho vận hành dài hạn tại doanh nghiệp thực tế. Các chỉ số mô hình không được diễn giải thành độ chính xác dự báo nhân viên thực tế sau một hoặc ba tháng.

Phạm vi chức năng của hệ thống TO-BE không bao gồm tiền lương, chấm công, toàn bộ quản trị nhân sự, tài khoản nghiệp vụ HR hoặc quản lý vùng. HR vẫn được thể hiện trong quy trình AS-IS để mô tả hoạt động hiện trạng; việc không xây dựng vai trò HR trong nguyên mẫu không đồng nghĩa HR không tồn tại trong tổ chức. Nội dung khóa học, bài học và câu hỏi được chuẩn bị để minh họa; không mở rộng đề cương thành hệ thống biên soạn nội dung khi chức năng đó chưa nằm trong nguyên mẫu. Mô hình không tự phê duyệt đánh giá hoặc tự quyết định đào tạo lại.

## 5. Phương pháp thực hiện

### 5.1. Phân tích nghiệp vụ và mô hình hóa

Xác định quy trình và yêu cầu trong bối cảnh giả định của đề tài; biểu diễn hiện trạng bằng AS-IS và phương án cải tiến bằng TO-BE. Sử dụng pool để thể hiện phạm vi bên tham gia, lane để thể hiện trách nhiệm của vai trò hoặc bộ phận bên trong cùng pool. Thuyết minh các điểm chuyển giao, điều kiện rẽ nhánh và kết quả của quy trình.

Sử dụng BFD để mô tả cấu trúc chức năng, use case để mô tả mục tiêu tương tác của tác nhân, DFD để mô tả dòng và kho dữ liệu. Đối chiếu các sơ đồ với nhau và với nguyên mẫu để bảo đảm thống nhất tên gọi, phạm vi và điều kiện nghiệp vụ.

### 5.2. Thiết kế dữ liệu và nguyên mẫu

Xác định thực thể, thuộc tính, khóa và quan hệ; trình bày ERD, lược đồ quan hệ và từ điển dữ liệu. Đối chiếu mô hình thiết kế với các bảng thực tế, phân biệt liên kết được ứng dụng sử dụng với ràng buộc khóa ngoại đã khai báo trong cơ sở dữ liệu. Xây dựng nguyên mẫu web bằng PHP, Laravel, Blade và cơ sở dữ liệu phục vụ nghiệp vụ.

### 5.3. Xây dựng mô hình dự đoán

Mô tả nguồn và cách sinh dữ liệu giả lập, định nghĩa nhãn, biến đầu vào và quy trình chia dữ liệu. Thử nghiệm Logistic Regression, Decision Tree và Random Forest; so sánh Accuracy, Precision, Recall và F1-score. Trình bày các bước chuẩn hóa, lưu mô hình và suy luận. Khi tái lập, kiểm tra mô hình, bộ chuẩn hóa và thứ tự biến thực sự trong tệp đã lưu.

### 5.4. Kiểm thử và đánh giá

Kiểm thử các luồng hợp lệ, dữ liệu thiếu hoặc sai, quyền truy cập, phạm vi cửa hàng, xử lý trùng và chuyển trạng thái. Đánh giá tính nhất quán giữa tiến độ học, kết quả kiểm tra và điều kiện đánh giá lại. Báo cáo kết quả thực tế cùng lỗi và giới hạn; kiểm thử trình duyệt hoặc tích hợp bổ sung phải được tách khỏi các ca đã chạy.

## 6. Đề cương nội dung báo cáo

### PHẦN MỞ ĐẦU

Trình bày lý do chọn đề tài, mục tiêu, đối tượng, phạm vi, phương pháp và kết cấu báo cáo. Các phần hành chính gồm trang bìa, nhiệm vụ đồ án nếu trường yêu cầu, lời cam đoan, lời cảm ơn, tóm tắt, mục lục, danh mục hình, bảng và từ viết tắt được hoàn thiện theo mẫu của cơ sở đào tạo.

### CHƯƠNG 1. TỔNG QUAN ĐỀ TÀI VÀ CƠ SỞ LÝ THUYẾT

**1.1. Bối cảnh và sự cần thiết của hệ thống BrewLearn**

Giới thiệu bối cảnh cửa hàng cà phê giả định, nhu cầu quản lý đào tạo và vấn đề theo dõi năng lực nhân viên.

**1.2. Mục tiêu, đối tượng, phạm vi và phương pháp thực hiện**

Xác định phạm vi ba vai trò, dữ liệu giả lập và sản phẩm nguyên mẫu; giới hạn các kết luận của đề tài.

**1.3. Cơ sở lý thuyết về quản lý đào tạo và đánh giá năng lực**

Trình bày đào tạo theo chức vụ, kiểm tra kiến thức, đánh giá thực hành, phê duyệt và đào tạo lại; phân biệt điểm kiểm tra với kết quả đánh giá năng lực.

**1.4. Cơ sở lý thuyết về phân tích và thiết kế hệ thống**

Giới thiệu mục đích và ký pháp sử dụng cho BFD, use case, DFD, ERD và sơ đồ quy trình có pool, lane; phân biệt mô hình chức năng, dòng dữ liệu và trình tự công việc.

**1.5. Cơ sở lý thuyết và công nghệ mô hình dự đoán**

Trình bày phân loại nhị phân, xác suất dự đoán, Logistic Regression, Decision Tree, Random Forest và các chỉ số đánh giá. Giới thiệu công nghệ web và xử lý dữ liệu được sử dụng.

**1.6. Tiểu kết chương**

### CHƯƠNG 2. PHÂN TÍCH HỆ THỐNG

**2.1. Mô tả bối cảnh và tác nhân nghiệp vụ**

Xác định nhân viên, trưởng ca, quản lý cửa hàng và trách nhiệm của từng vai trò; giải thích quan hệ với phạm vi ca, cửa hàng và nội dung được phân công.

**2.2. Phân tích quy trình hiện trạng AS-IS**

AS-IS phản ánh quy trình vận hành hiện trạng được mô tả trong bối cảnh đề tài, có bốn làn: HR, Nhân viên thử việc, Ca trưởng và Quản lý cửa hàng. Phạm vi hệ thống TO-BE thu gọn còn ba vai trò Nhân viên, Trưởng ca và Quản lý cửa hàng là quyết định thiết kế, được giải thích tại mục 2.3–2.4; số làn hiện trạng và số vai trò sử dụng hệ thống vì vậy không mâu thuẫn với nhau.

Trình bày sơ đồ AS-IS đã xây dựng và thuyết minh quá trình giao nội dung, ghi nhận học tập, kiểm tra, đánh giá và xử lý chưa đạt trong bối cảnh giả định. Mô tả cách phân chia pool, lane theo đúng sơ đồ thực tế, chỉ rõ nơi lưu thông tin và các điểm chuyển giao thủ công. Không trình bày như dữ liệu khảo sát doanh nghiệp đã được xác nhận.

[CHÈN SƠ ĐỒ AS-IS TẠI ĐÂY — ghi tên pool, lane và thuyết minh trách nhiệm theo bản đã vẽ]

**2.3. Phân tích hạn chế và yêu cầu cải tiến**

Lập bảng liên kết hạn chế hiện trạng với yêu cầu cải tiến: lưu trữ tập trung, theo dõi tiến độ, điều kiện tạo lịch, phê duyệt, lưu vòng đào tạo lại và bổ sung thông tin nguy cơ.

Giải thích việc giới hạn nguyên mẫu vào hoạt động đào tạo tại cửa hàng với ba vai trò sử dụng. Các hoạt động HR trong AS-IS được ghi nhận làm bối cảnh hoặc điều kiện đầu vào, còn chức năng dành riêng cho HR nằm ngoài phạm vi triển khai; không tự chuyển toàn bộ trách nhiệm HR cho quản lý cửa hàng.

**2.4. Quy trình đề xuất TO-BE**

Trình bày sơ đồ TO-BE đã vẽ cho học tập và kiểm tra; đánh giá và phê duyệt; đào tạo lại và đánh giá lại. Thuyết minh pool, lane, điểm bắt đầu và kết thúc, bước xử lý, điều kiện rẽ nhánh, người phụ trách và thông tin chuyển giao. Phân biệt tác vụ tự động của hệ thống với việc thực hành hoặc quan sát ngoài phần mềm.

Đối chiếu bốn làn của AS-IS với ba vai trò của TO-BE: Nhân viên thử việc được mô tả trong nhóm Nhân viên, Ca trưởng tương ứng Trưởng ca, Quản lý cửa hàng giữ vai trò quản lý; HR không được xây dựng thành vai trò truy cập nguyên mẫu. Nếu sơ đồ có làn Hệ thống để biểu diễn tác vụ tự động, làn này không phải vai trò người dùng thứ tư.

[CHÈN SƠ ĐỒ TO-BE TẠI ĐÂY — có thể chia thành các hình theo từng quy trình]

**2.5. Yêu cầu hệ thống và quy tắc nghiệp vụ**

Trình bày yêu cầu chức năng theo ba vai trò, yêu cầu phi chức năng, phạm vi dữ liệu và trạng thái xử lý. Các quy tắc trọng tâm gồm điều kiện học/quiz trước đánh giá, công thức điểm năm tiêu chí, yêu cầu đề xuất khi chưa đạt, phê duyệt trước công bố và hoàn tất đào tạo lại sau kết quả đạt được duyệt.

**2.6. Sơ đồ phân rã chức năng BFD**

Trình bày BFD đã vẽ và thuyết minh các nhóm chức năng của hệ thống. Đối chiếu các chức năng với yêu cầu và use case để tránh bổ sung chức năng ngoài phạm vi nguyên mẫu.

[CHÈN SƠ ĐỒ BFD TẠI ĐÂY]

**2.7. Mô hình use case và đặc tả**

Trình bày use case tổng quát, sau đó các nhóm học tập, đánh giá, quản lý cửa hàng, đào tạo lại và chức năng chung. Đặc tả các use case trọng tâm theo tác nhân, mục tiêu, tiền điều kiện, luồng chính, luồng thay thế, ngoại lệ và hậu điều kiện. Các chức năng dùng chung không tạo thêm chức danh nghiệp vụ mới.

[CHÈN USE CASE TỔNG QUÁT VÀ CÁC USE CASE CHI TIẾT TẠI ĐÂY]

**2.8. DFD ngữ cảnh**

Trình bày một tiến trình đại diện BrewLearn và các luồng dữ liệu với ba tác nhân bên ngoài. Mô tả tên, hướng và nội dung trao đổi; kiểm tra ranh giới hệ thống.

[CHÈN DFD NGỮ CẢNH TẠI ĐÂY]

**2.9. DFD mức 0**

Theo quy ước hiện có của dự án, DFD mức 0 phân rã tiến trình hệ thống thành các tiến trình 1.0–8.0 và các kho dữ liệu logic. Thuyết minh đầu vào, đầu ra và kho dữ liệu của từng tiến trình; kiểm tra cân bằng với DFD ngữ cảnh. Các trang trình bày cùng mức không được gọi thành các mức phân rã khác nhau.

[CHÈN DFD MỨC 0 TẠI ĐÂY — giữ đầy đủ các trang nếu sơ đồ được chia trang]

**2.10. Tiểu kết chương**

### CHƯƠNG 3. THIẾT KẾ HỆ THỐNG VÀ MÔ HÌNH DỰ ĐOÁN

**3.1. Thiết kế kiến trúc tổng thể**

Trình bày sơ đồ kiến trúc đã vẽ gồm trình duyệt, ứng dụng Laravel, cơ sở dữ liệu và chương trình Python. Thuyết minh luồng yêu cầu web, ghi nhận học tập, tổng hợp dữ liệu, chạy dự đoán và đọc kết quả để hiển thị. Nêu rõ chương trình dự đoán chạy riêng, chưa mặc định là API suy luận thời gian thực.

[CHÈN SƠ ĐỒ KIẾN TRÚC TỔNG THỂ TẠI ĐÂY]

**3.2. Thiết kế cơ sở dữ liệu**

Xác định các nhóm thực thể: nhân sự/cửa hàng/ca; nội dung đào tạo; tiến độ; kiểm tra; đánh giá; yêu cầu và vòng đào tạo lại; dự đoán; thông báo và hỗ trợ. Trình bày thuộc tính, khóa chính, liên kết và yêu cầu lưu lịch sử. Tên bảng, cột và quan hệ đối chiếu với thiết kế CSDL đã thực hiện.

**3.3. ERD và lược đồ quan hệ**

Đưa ERD tổng thể, các hình theo nhóm nếu cần, số lượng tham gia và quan hệ giữa các thực thể. Trình bày lược đồ quan hệ và từ điển dữ liệu; ghi rõ liên kết logic và ràng buộc vật lý có trong triển khai.

[CHÈN ERD TỔNG THỂ VÀ ERD THEO NHÓM TẠI ĐÂY]

**3.4. Thiết kế xử lý nghiệp vụ và phân quyền**

Mô tả xử lý tiến độ, lưu lượt kiểm tra, đánh giá năm tiêu chí, phê duyệt hoặc trả lại, giao vòng đào tạo lại, xác nhận hoàn thành và đánh giá lại. Trình bày kiểm soát trạng thái, xử lý trùng và giới hạn dữ liệu theo tài khoản, ca hoặc cửa hàng phù hợp từng chức năng.

Phê duyệt hoặc trả lại áp dụng cho mọi kết quả đánh giá năng lực: đánh giá lần đầu, đánh giá định kỳ và đánh giá lại sau đào tạo. Kết quả trưởng ca vừa chấm chỉ ở bước chờ phê duyệt, chưa là kết quả chính thức. Quản lý có thể duyệt hoặc trả lại để bổ sung; chỉ kết quả đã được duyệt mới được sử dụng làm kết quả nghiệp vụ chính thức và nguồn nhãn thực tế cho mô hình.

**3.5. Thiết kế dữ liệu đầu vào và nhãn mô hình**

Trình bày cách tạo 1.000 quan sát giả lập và năm biến: progress_percent, avg_quiz_score, quiz_attempts, failed_rate, learning_visits. Định nghĩa competency_status là nhãn đích, với 0 biểu thị đạt chuẩn và 1 biểu thị chưa đạt chuẩn; phân biệt nguồn nhãn mô phỏng trong thử nghiệm hiện tại với quy tắc lấy nhãn từ dữ liệu đánh giá thực tế.

Khi xây dựng tập huấn luyện từ dữ liệu nghiệp vụ, nhãn competency_status phải lấy từ kết quả đánh giá đã được quản lý cửa hàng phê duyệt. Không lấy nhãn ngay khi trưởng ca vừa chấm; loại bản ghi chờ phê duyệt hoặc bị trả lại. Hồ sơ được trả lại rồi bổ sung chỉ được sử dụng khi kết quả cuối cùng đã được duyệt. Quy tắc này áp dụng cho đánh giá lần đầu, định kỳ và đánh giá lại; dữ liệu đầu vào phải gắn với mốc trước kết quả đánh giá dùng làm nhãn để tránh rò rỉ thông tin.

Trong thử nghiệm hiện tại, train_model.py sinh nhãn theo quy tắc giả lập, không trích nhãn từ đánh giá đã được quản lý duyệt. Vì vậy, quy tắc phê duyệt nêu trên là yêu cầu thiết kế đối với nguồn nhãn nghiệp vụ khi sử dụng dữ liệu thực tế, không phải tuyên bố rằng tập 1.000 quan sát hiện có được tạo từ hồ sơ đánh giá thật.

Giải thích điều kiện chưa đủ dữ liệu và giới hạn ngưỡng kiểm tra: dữ liệu mô phỏng sử dụng giả định đồng nhất về mốc 80 khi biểu diễn lượt kiểm tra chưa đạt, trong khi nghiệp vụ thực tế xác định đạt theo bai_kiem_tra.DiemChuan của từng bài. failed_attempts trong mã sinh dữ liệu được tạo giả lập, không được tính lại từ điểm và ngưỡng của từng bài thật. Do đó, mô hình huấn luyện hiện chưa phản ánh sự đa dạng của ngưỡng đạt giữa các bài kiểm tra; khi dùng dữ liệu thực tế cần xác định failed_rate theo ngưỡng tương ứng và tái đánh giá mô hình.

**3.6. Quy trình huấn luyện và lựa chọn mô hình**

Trình bày sơ đồ huấn luyện đã vẽ: chuẩn bị dữ liệu, chia huấn luyện/kiểm tra, chuẩn hóa khi cần, huấn luyện các mô hình, tính chỉ số, so sánh và lưu mô hình cùng thông tin đi kèm. Đối chiếu với quy trình chia 80/20, phân tầng nhãn và random_state=42 trong mã hiện tại. Phân biệt kết quả lưu sẵn với kết quả đã được tái lập.

[CHÈN SƠ ĐỒ QUY TRÌNH HUẤN LUYỆN MÔ HÌNH TẠI ĐÂY]

**3.7. Thiết kế tích hợp và sử dụng kết quả dự đoán**

Mô tả chương trình đọc dữ liệu, tạo biến, tải mô hình/bộ chuẩn hóa và lưu kết quả. Trình bày mức thấp, trung bình, cao, chưa đủ dữ liệu và thời điểm dự đoán. Kết quả hỗ trợ ưu tiên xem hồ sơ, không thay thế chấm điểm hoặc quyết định phê duyệt.

**3.8. Tiểu kết chương**

### CHƯƠNG 4. THIẾT KẾ GIAO DIỆN, TRIỂN KHAI VÀ KIỂM THỬ HỆ THỐNG

**4.1. Thiết kế giao diện**

4.1.1. Mục tiêu và nguyên tắc thiết kế.

4.1.2. Bố cục và điều hướng chung.

4.1.3. Giao diện đăng nhập dùng chung.

4.1.4. Giao diện dành cho nhân viên: tổng quan học tập, khóa học, bài học, kiểm tra, kết quả, tài khoản, thông báo và hỗ trợ.

4.1.5. Giao diện dành cho trưởng ca: tổng quan, danh sách nhân viên, tạo lịch, chấm điểm/đề xuất và đào tạo lại được phân công.

4.1.6. Giao diện dành cho quản lý cửa hàng: tổng quan, quản lý nhân viên, hồ sơ, phê duyệt, giao đào tạo lại, lịch sử các vòng và thông tin nguy cơ.

[CHÈN ẢNH GIAO DIỆN THEO TỪNG VAI TRÒ — sử dụng các vị trí đã đánh dấu trong bản Word Chương 4]

**4.2. Triển khai nguyên mẫu**

Trình bày môi trường, công nghệ, cấu hình dữ liệu, tài nguyên, quy trình chạy web, tích hợp Python và tác vụ nhắc hạn. Phân biệt hướng dẫn tái lập nguyên mẫu với minh chứng đã triển khai máy chủ công khai.

**4.3. Kiểm thử hệ thống**

Trình bày mục tiêu, môi trường, kịch bản, dữ liệu, kết quả và phân tích ca không đạt. Sử dụng báo cáo JUnit và file Excel đã xuất làm minh chứng. Lần chạy ngày 05/10/2026 hiện có 60 ca, 59 đạt, 1 không đạt; chưa trình bày là 100% đạt khi chưa có kết quả chạy lại sau sửa.

[CHÈN MINH CHỨNG KIỂM THỬ TẠI ĐÂY]

**4.4. Đánh giá và tiểu kết chương**

Đánh giá mức đáp ứng các mục tiêu, giới hạn kiểm thử và hạn chế của mô hình dữ liệu giả lập. Không dùng thời gian chạy kiểm thử làm thời gian phản hồi hệ thống.

### CHƯƠNG 5. KẾT LUẬN VÀ HƯỚNG PHÁT TRIỂN

**5.1. Kết quả đạt được**

Đối chiếu từng mục tiêu với sơ đồ, thiết kế dữ liệu, nguyên mẫu và minh chứng kiểm thử. Nêu đóng góp trong việc kết nối học tập, kiểm tra, đánh giá, phê duyệt và đào tạo lại nhiều vòng.

**5.2. Hạn chế của đồ án**

Nêu bối cảnh giả định, dữ liệu giả lập, giới hạn các ca kiểm thử và nội dung chưa có thực nghiệm tại doanh nghiệp. Ghi rõ kết quả mô hình chưa chứng minh khả năng dự báo ngoài bộ dữ liệu mô phỏng.

Nêu rõ mô hình hiện huấn luyện trên dữ liệu với giả định ngưỡng kiểm tra đồng nhất 80, chưa phản ánh các ngưỡng DiemChuan khác nhau của từng bài kiểm tra thực tế. Nhãn hiện tại cũng là nhãn sinh giả lập, chưa phải nhãn trích từ kết quả đánh giá được quản lý duyệt; vì vậy chưa thể xem các chỉ số thử nghiệm là minh chứng trên dữ liệu năng lực chính thức của nhân viên.

**5.3. Hướng phát triển**

Đề xuất thu thập dữ liệu thực tế có nhãn được duyệt, đánh giá mô hình trên dữ liệu độc lập, hoàn thiện tích hợp và vận hành, bổ sung kiểm thử trình duyệt, tải và quyền truy cập theo phạm vi. Các nội dung này là hướng phát triển, không tính là chức năng đã hoàn thành.

### TÀI LIỆU THAM KHẢO VÀ PHỤ LỤC

Tài liệu tham khảo gồm nguồn lý thuyết, ký pháp mô hình, công nghệ, phương pháp học máy và ba đồ án dùng tham khảo cách trình bày. Hoàn thiện định dạng theo yêu cầu của trường; không đưa nguồn chưa đọc vào danh mục như tài liệu đã sử dụng.

Phụ lục gồm sơ đồ khổ lớn, đặc tả use case, từ điển dữ liệu, kịch bản kiểm thử, kết quả Excel/JUnit, bảng chỉ số mô hình và hướng dẫn chạy nguyên mẫu. Khi sơ đồ có nhiều trang, giữ đầy đủ và xác định rõ các trang thuộc cùng mô hình.

## 7. Danh mục sản phẩm và vị trí trình bày

| Sản phẩm đã thực hiện theo thông tin sinh viên | Vị trí chính | Nội dung cần thuyết minh |
| --- | --- | --- |
| Sơ đồ AS-IS | Mục 2.2 | Hiện trạng giả định, trách nhiệm, cách xử lý và hạn chế |
| Sơ đồ TO-BE | Mục 2.4 | Quy trình cải tiến, điều kiện rẽ nhánh, chuyển giao và kết quả |
| Pool và lane | Trong mục 2.2 và 2.4 | Ranh giới bên tham gia và trách nhiệm; giải thích theo từng sơ đồ |
| BFD | Mục 2.6 | Nhóm chức năng và cấu trúc phân rã |
| Use case tổng quát/chi tiết | Mục 2.7 | Tác nhân, mục tiêu và đặc tả luồng |
| DFD ngữ cảnh | Mục 2.8 | Hệ thống, tác nhân bên ngoài và luồng dữ liệu |
| DFD mức 0 | Mục 2.9 | Các tiến trình, kho dữ liệu và cân bằng luồng |
| Sơ đồ kiến trúc tổng thể | Mục 3.1 | Thành phần triển khai và cách trao đổi dữ liệu |
| Thiết kế CSDL | Mục 3.2–3.3 | Thực thể, thuộc tính, khóa, quan hệ, lược đồ và từ điển dữ liệu |
| ERD | Mục 3.3 | Quan hệ, số lượng tham gia và liên kết giữa các nhóm dữ liệu |
| Sơ đồ và mô hình huấn luyện | Mục 3.5–3.7 | Dữ liệu, nhãn, chia tập, huấn luyện, đánh giá và tích hợp |
| Word giao diện/triển khai/kiểm thử | Chương 4 | Giao diện theo vai trò và minh chứng thực nghiệm |
| File Excel kiểm thử | Mục 4.3 và phụ lục | Tổng hợp, từng ca và phân tích lỗi |

Lưu ý: Pool và lane là thành phần của sơ đồ quy trình, không trình bày như một mức phân rã của DFD. Các sản phẩm được sinh viên xác nhận đã vẽ cần được chèn bằng bản cuối cùng và kiểm tra thống nhất tên, phạm vi trước khi nộp.

## 8. Quy ước trình bày pool và lane trong phần thuyết minh

Mỗi sơ đồ AS-IS hoặc TO-BE cần có bảng giải thích pool/lane theo đúng tên đã vẽ. Pool xác định một bên tham gia hoặc ranh giới quy trình; lane phân chia trách nhiệm trong pool. Nếu các vai trò thuộc cùng quy trình nội bộ cửa hàng, có thể đặt trong cùng pool và phân lane theo nhân viên, trưởng ca, quản lý cửa hàng. Phần xử lý tự động có thể có lane hệ thống khi phù hợp với mô hình TO-BE.

Đây là cách tổ chức đề xuất để thuyết minh, không xác nhận rằng mọi sơ đồ hiện có đã sử dụng đúng bộ lane này. Cần đối chiếu bản đã vẽ: mô tả các vai trò được thể hiện, xác định ai thực hiện mỗi bước và giải thích các điểm chuyển giao. Luồng giữa lane trong cùng pool thể hiện trình tự nội bộ; trao đổi giữa các pool phải được phân biệt với trình tự đó.

Trong AS-IS, ghi rõ thao tác học, đánh giá thực hành và tổng hợp thủ công. Trong TO-BE, ghi rõ thao tác người dùng, kiểm tra điều kiện của hệ thống, lưu dữ liệu, gửi thông báo và các việc ngoài phần mềm. Người phụ trách đào tạo lại được xác định theo phân công, không mặc định là nhân viên tự xác nhận hoàn tất yêu cầu.

## 9. Kế hoạch hoàn thiện và sản phẩm dự kiến

| Giai đoạn | Công việc trọng tâm | Sản phẩm/tiêu chí hoàn thành |
| --- | --- | --- |
| 1. Chốt đề cương | Thống nhất tên đề tài, phạm vi ba vai trò và bố cục năm chương | Đề cương được giảng viên góp ý |
| 2. Hoàn thiện phân tích | Rà soát AS-IS, TO-BE, pool/lane, BFD, use case và hai mức DFD | Bộ sơ đồ thống nhất với yêu cầu, có thuyết minh |
| 3. Hoàn thiện thiết kế | Rà soát kiến trúc, CSDL, ERD và xử lý nghiệp vụ | Thiết kế đối chiếu được với nguyên mẫu |
| 4. Hoàn thiện mô hình | Kiểm tra dữ liệu, quy trình huấn luyện, tệp mô hình và các chỉ số | Bảng kết quả có nguồn, ghi rõ dữ liệu giả lập |
| 5. Hoàn thiện thực nghiệm | Chèn ảnh theo vai trò, xác minh triển khai, rà soát ca kiểm thử | Chương 4 và phụ lục minh chứng; ghi nhận kết quả chạy lại nếu có |
| 6. Hoàn thiện báo cáo | Viết kết luận, rà soát trích dẫn, số hình/bảng và chuẩn bị trình bày | Báo cáo Word hoàn chỉnh và nội dung bảo vệ |

Các giai đoạn trên là kế hoạch hoàn thiện phần còn lại từ các sản phẩm đã làm, không yêu cầu vẽ lại toàn bộ sơ đồ. Mốc chi tiết được điều chỉnh theo lịch làm việc với giảng viên trong khoảng thời gian thực hiện của đề tài.

## 10. Kết quả và ý nghĩa dự kiến

Đồ án tạo bộ hồ sơ phân tích và thiết kế có thể đối chiếu từ yêu cầu đến quy trình, chức năng, dòng dữ liệu, cơ sở dữ liệu và nguyên mẫu. Việc tổ chức nhiều vòng đào tạo lại và gắn kết quả với phê duyệt giúp minh họa cách theo dõi quá trình cải thiện có lịch sử và trách nhiệm cụ thể.

Phần mô hình dự đoán cung cấp minh họa về việc khai thác dữ liệu học tập và kiểm tra để hỗ trợ quản lý. Giá trị của đồ án được đánh giá ở mức hoàn thiện mô hình hệ thống và thực nghiệm nguyên mẫu, cùng việc trình bày minh bạch nguồn dữ liệu, kết quả kiểm thử và giới hạn; chưa khẳng định lợi ích vận hành định lượng tại doanh nghiệp khi chưa có khảo sát thực tế.
