# CHƯƠNG 4. THIẾT KẾ GIAO DIỆN, TRIỂN KHAI VÀ KIỂM THỬ HỆ THỐNG

Trên cơ sở kết quả phân tích và thiết kế ở các chương trước, chương này trình bày giao diện, phương thức triển khai và kết quả kiểm thử nguyên mẫu BrewLearn. Hệ thống được xây dựng nhằm hỗ trợ quản lý đào tạo và theo dõi năng lực nhân viên tại chuỗi cửa hàng cà phê trong bối cảnh doanh nghiệp giả định. Nội dung tập trung vào ba nhóm người dùng gồm nhân viên, trưởng ca và quản lý cửa hàng; đồng thời làm rõ cách tích hợp kết quả dự đoán nguy cơ chưa đạt chuẩn năng lực vào hoạt động theo dõi nhân viên.

Phần thiết kế giao diện được tổ chức theo vai trò và chức năng, kết hợp mô tả đối tượng sử dụng, thành phần thông tin và thao tác trên từng màn hình. Cách tổ chức này được tham khảo từ phần giao diện của các đồ án Nguyễn Quốc Hưng, Nguyễn Thái Gia Khải và Lương Thị Tường Vy [1–3]. Phần triển khai và kiểm thử sử dụng cấu trúc, mã nguồn và kết quả thực nghiệm của BrewLearn để phản ánh đúng phạm vi nguyên mẫu.

## 4.1. Thiết kế giao diện hệ thống

### 4.1.1. Mục tiêu và nguyên tắc thiết kế

Giao diện BrewLearn được thiết kế để hỗ trợ người dùng thực hiện các công việc liên quan đến học tập, kiểm tra kiến thức, đánh giá năng lực và đào tạo lại trên một hệ thống thống nhất. Đối với nhân viên, giao diện cần giúp nhận biết nội dung được phân công, mức độ hoàn thành và kết quả kiểm tra. Đối với trưởng ca, giao diện cần hỗ trợ theo dõi nhân viên thuộc phạm vi phụ trách, tổ chức đánh giá và ghi nhận đề xuất cải thiện. Đối với quản lý cửa hàng, giao diện cần tổng hợp thông tin, hỗ trợ phê duyệt và theo dõi kết quả đào tạo lại.

Nguyên tắc thứ nhất là thống nhất về bố cục và cách biểu diễn thông tin. Các trang sử dụng chung vùng điều hướng, vùng nội dung và khu vực tài khoản; các biểu mẫu có nhãn trường dữ liệu, nút thao tác và thông báo phản hồi. Danh sách được trình bày bằng bảng hoặc thẻ thông tin tùy tính chất nội dung. Thanh tiến độ thể hiện mức độ hoàn thành học tập, trong khi nhãn trạng thái thể hiện tình trạng xử lý đánh giá và đào tạo lại.

Nguyên tắc thứ hai là tổ chức thông tin theo vai trò. Sau khi đăng nhập, người dùng được chuyển đến trang phù hợp với công việc của mình. Các mục chức năng được hiển thị theo nhóm quyền trong giao diện; việc kiểm tra quyền và giới hạn phạm vi dữ liệu còn được thực hiện tại các chức năng xử lý phía máy chủ. Như vậy, khả năng truy cập một chức năng phải được xem xét cả ở mức giao diện và mức xử lý yêu cầu.

Nguyên tắc thứ ba là phân biệt thông tin chưa có với kết quả đã được xác lập. Nhân viên chưa có kết quả kiểm tra hoặc đánh giá cần được thể hiện bằng trạng thái tương ứng, tránh hiểu nhầm thành điểm bằng không. Tương tự, dữ liệu dự đoán được trình bày riêng với kết quả đánh giá đã được quản lý phê duyệt. Cách biểu diễn này giúp người dùng nhận biết nguồn gốc và ý nghĩa của từng loại thông tin.

Nguyên tắc thứ tư là hỗ trợ phản hồi và phòng ngừa thao tác không hợp lệ. Khi thiếu dữ liệu bắt buộc, sai định dạng hoặc chưa đáp ứng điều kiện nghiệp vụ, hệ thống cần thông báo để người dùng điều chỉnh. Các thao tác phê duyệt, giao đào tạo lại và tạo lịch đánh giá lại phải gắn với trạng thái hiện hành, hạn chế việc xử lý trùng hoặc chuyển trạng thái không đúng quy trình.

Về nhận diện, bố cục chung sử dụng nền kem, màu nâu cà phê và màu cam làm điểm nhấn, phù hợp với bối cảnh hệ thống đào tạo nhân viên cửa hàng cà phê. Mã giao diện có cơ chế thu gọn thanh điều hướng và điều chỉnh bố cục theo kích thước màn hình. Đây là đặc điểm đã triển khai trong mã nguồn; mức độ phù hợp trên từng thiết bị cần tiếp tục được kiểm tra bằng trình duyệt thực tế.

### 4.1.2. Bố cục và điều hướng chung

Giao diện sau đăng nhập gồm thanh điều hướng bên trái và vùng nội dung chính. Thanh điều hướng chứa nhận diện BrewLearn cùng các nhóm chức năng phù hợp với tài khoản. Vùng nội dung trình bày tiêu đề trang, dữ liệu nghiệp vụ, bộ lọc và các nút xử lý. Người dùng có thể truy cập thông tin tài khoản và thông báo để theo dõi các công việc liên quan.

Với màn hình rộng, thanh điều hướng được bố trí cố định nhằm hỗ trợ chuyển đổi giữa các chức năng. Với màn hình nhỏ, thanh điều hướng có thể mở hoặc ẩn để dành không gian cho nội dung. Mã nguồn bố cục sử dụng điểm chuyển đổi tại chiều rộng 768 px. Các danh sách nhiều thông tin vẫn cần được kiểm tra khả năng đọc, cuộn và thao tác trên thiết bị di động trước khi đưa vào vận hành.

**Bảng 4.1. Các nhóm giao diện theo vai trò**

| Vai trò | Nhóm giao diện chính | Mục đích sử dụng |
| --- | --- | --- |
| Nhân viên | Trang học tập, khóa học, bài học, bài kiểm tra, hồ sơ, thông báo, hỗ trợ | Thực hiện đào tạo và theo dõi kết quả cá nhân |
| Trưởng ca | Tổng quan trưởng ca, danh sách nhân viên, lịch đánh giá, biểu mẫu chấm điểm, đào tạo lại được phân công | Theo dõi nhân viên, đánh giá năng lực và xác nhận nội dung phụ trách |
| Quản lý cửa hàng | Tổng quan cửa hàng, nhân viên, hồ sơ chi tiết, phê duyệt đánh giá, giao đào tạo lại, thông báo | Phê duyệt kết quả và điều phối hoạt động đào tạo trong cửa hàng |

Nguồn: Tổng hợp từ cấu trúc giao diện và các chức năng xử lý của BrewLearn.

### 4.1.3. Giao diện đăng nhập dùng chung

#### 4.1.3.1. Giao diện đăng nhập

Đối tượng sử dụng gồm nhân viên, trưởng ca và quản lý cửa hàng. Màn hình đăng nhập cung cấp các trường email, mật khẩu và lựa chọn ghi nhớ đăng nhập. Người dùng nhập thông tin tài khoản đã được cấp và thực hiện xác thực để truy cập hệ thống. Nguyên mẫu không có luồng đăng ký tài khoản công khai trong các tuyến chức năng hiện tại.

Khi dữ liệu đầu vào thiếu hoặc email không hợp lệ, hệ thống phản hồi lỗi kiểm tra dữ liệu. Khi email hoặc mật khẩu không đúng, hệ thống hiển thị thông báo đăng nhập không thành công. Khi xác thực thành công, phiên đăng nhập được tạo lại và người dùng được chuyển đến trang học tập, trang trưởng ca hoặc trang quản lý cửa hàng theo chức vụ. Thao tác đăng xuất hủy phiên hiện hành và đưa người dùng về trang đăng nhập.

Giao diện này đóng vai trò điểm truy cập chung của hệ thống. Việc điều hướng theo vai trò giúp người dùng tiếp cận ngay nhóm công việc thường xuyên thực hiện. Các nhánh vai trò khác còn tồn tại trong mã nguồn cần được rà soát riêng; phạm vi trình bày của đề tài tập trung vào ba vai trò nêu trên.

### 4.1.4. Giao diện dành cho nhân viên

Nhóm giao diện dành cho nhân viên được tổ chức theo trình tự học tập: theo dõi nội dung được phân công, học từng bài, làm bài kiểm tra và xem kết quả cá nhân. Các màn hình hồ sơ, thông báo và hỗ trợ được trình bày trong nhóm này để mô tả đầy đủ quá trình sử dụng của nhân viên; trưởng ca và quản lý cũng có thể sử dụng các chức năng chung tương ứng theo quyền tài khoản.

#### 4.1.4.1. Giao diện trang học tập và danh sách khóa học

Đối tượng sử dụng chính là nhân viên; trưởng ca có thể tham gia học tập theo nội dung phù hợp với chức vụ. Trang học tập tổng hợp thông tin khóa học, tiến độ và các nội dung cần tiếp tục thực hiện. Danh sách khóa học giúp người dùng nhận biết chương trình đào tạo được áp dụng theo vị trí công việc và các chương trình dùng chung.

Mỗi khóa học được trình bày bằng thông tin nhận diện và mức độ hoàn thành. Người dùng lựa chọn khóa học để xem chi tiết, sau đó truy cập từng bài học. Tiến độ khóa học được tổng hợp từ dữ liệu bài học thông qua thành phần xử lý CourseProgress, giúp liên kết dữ liệu học tập cá nhân với thông tin hiển thị cho trưởng ca và quản lý.

Khi có hoạt động học mới, tiến độ cần được ghi nhận vào cơ sở dữ liệu và phản ánh nhất quán sau khi tải lại trang. Nguyên mẫu có thêm cơ chế giữ mức tiến độ nền của dữ liệu minh họa; cơ chế này phục vụ bộ dữ liệu demo và cần được phân biệt với cách tính tiến độ từ hoạt động học phát sinh thực tế.

#### 4.1.4.2. Giao diện chi tiết khóa học và bài học

Giao diện chi tiết khóa học trình bày thông tin chương trình và các bài học liên quan. Từ danh sách này, người dùng có thể mở bài đang học hoặc chọn bài tiếp theo. Việc đặt danh sách bài học cùng nội dung chương trình hỗ trợ người dùng nhận biết vị trí của bài học trong toàn bộ khóa đào tạo.

Giao diện bài học có vùng phát video, nội dung đào tạo và danh sách bài học trong chương trình. Video được phục vụ từ thư mục tài nguyên công khai của ứng dụng. Trong quá trình học, mã giao diện gửi thông tin tiến độ về máy chủ và hỗ trợ tiếp tục tại vị trí đã ghi nhận. Trạng thái hoàn thành bài học là một căn cứ để kiểm tra điều kiện đánh giá năng lực.

Việc ghi nhận tiến độ giúp giảm nhu cầu tổng hợp thủ công, nhưng thời lượng xem hoặc tỷ lệ hoàn thành video chỉ thể hiện hoạt động trên hệ thống. Để xác định mức độ đáp ứng công việc, BrewLearn kết hợp kết quả kiểm tra kiến thức và đánh giá thực hành của trưởng ca.

#### 4.1.4.3. Giao diện bài kiểm tra và kết quả kiểm tra

Giao diện danh sách bài kiểm tra cho phép người dùng xem các bài phù hợp với chức vụ và các bài áp dụng chung. Khi mở bài kiểm tra, hệ thống trình bày câu hỏi và phương án trả lời để người dùng lựa chọn, sau đó gửi bài làm để chấm điểm.

Kết quả kiểm tra được xác định theo đáp án trong cơ sở dữ liệu. Điều kiện đạt dựa trên ngưỡng của từng bài kiểm tra; thành phần kiểm tra điều kiện sử dụng giá trị mặc định 80 khi chưa có ngưỡng riêng. Kết quả được lưu theo từng lượt làm để theo dõi lịch sử, thay vì chỉ giữ một điểm tổng hợp duy nhất.

Hệ thống có xử lý việc gửi lặp cùng một lượt làm bài, trả lại kết quả đã ghi nhận thay vì tạo thêm kết quả cho thao tác trùng. Các lượt thi lại hợp lệ được giữ trong lịch sử. Phân biệt hai trường hợp này giúp duy trì tính nhất quán của dữ liệu kiểm tra và của các thống kê dùng cho đào tạo.

#### 4.1.4.4. Giao diện hồ sơ, thông báo và hỗ trợ

Giao diện tài khoản cho phép người dùng cập nhật thông tin liên hệ, đổi mật khẩu và điều chỉnh một số thiết lập cá nhân. Khi đổi mật khẩu, hệ thống yêu cầu kiểm tra mật khẩu hiện tại trước khi lưu thông tin mới. Chức năng đăng xuất các thiết bị khác xử lý các phiên còn lại của chính tài khoản đang đăng nhập.

Giao diện thông báo giúp người dùng tiếp nhận thông tin liên quan đến đánh giá, phê duyệt và đào tạo lại. Các thông báo được gắn với tài khoản nhận và có trạng thái đã đọc. Đối với quản lý cửa hàng, nguồn thông báo không đưa các bản ghi loại nguy cơ hoặc dự đoán vào cùng luồng nhắc việc nghiệp vụ; thông tin nguy cơ được trình bày ở khu vực theo dõi riêng.

Giao diện hỗ trợ cho phép người dùng xem các yêu cầu của mình và gửi yêu cầu mới. Đây là chức năng tiếp nhận yêu cầu hỗ trợ trong nguyên mẫu. Phạm vi mô tả không mở rộng thành hệ thống xử lý phiếu hỗ trợ nhiều cấp khi chưa có minh chứng về toàn bộ quy trình đó.

### 4.1.5. Giao diện dành cho trưởng ca

Nhóm giao diện dành cho trưởng ca tập trung vào theo dõi nhân viên thuộc phạm vi phụ trách, tổ chức đánh giá năng lực và ghi nhận đề xuất cải thiện. Khi được phân công phụ trách đào tạo lại, trưởng ca còn sử dụng màn hình xác nhận nội dung thực hiện. Các giao diện được trình bày theo trình tự theo dõi, đánh giá và hỗ trợ cải thiện năng lực.

#### 4.1.5.1. Giao diện tổng quan và danh sách nhân viên

Đối tượng sử dụng là trưởng ca. Giao diện tổng quan hỗ trợ theo dõi nhân viên thuộc phạm vi phụ trách, tình trạng học tập và các công việc đánh giá. Danh sách nhân viên tập hợp thông tin tiến độ, điểm kiểm tra và trạng thái đánh giá để trưởng ca xác định trường hợp cần quan tâm.

Các kết quả chưa phát sinh được trình bày bằng trạng thái chưa có kết quả. Điều này giúp phân biệt nhân viên chưa được đánh giá với nhân viên đã có điểm thấp. Trạng thái đánh giá còn cần phản ánh bước xử lý, chẳng hạn chờ đánh giá, chờ phê duyệt hoặc đã có kết quả được duyệt.

Trưởng ca có thể theo dõi các mốc đến hạn, tổ chức đánh giá và xem hồ sơ nhân viên. Tuy nhiên, trưởng ca vẫn là một thành viên có thể học tập trên hệ thống, đồng thời được loại khỏi nhóm đối tượng đánh giá và dự đoán nguy cơ của luồng nhân viên trong phạm vi nguyên mẫu.

#### 4.1.5.2. Giao diện tạo lịch và đánh giá năng lực

Giao diện tạo lịch đánh giá hỗ trợ trưởng ca lựa chọn nhân viên, xác định lịch và ghi nhận thông tin liên quan. Trước khi tạo lịch đánh giá lần đầu, hệ thống kiểm tra việc hoàn thành các bài học được phân công và kết quả đạt của từng bài kiểm tra bắt buộc. Trường hợp còn thiếu điều kiện học tập, danh sách cần thể hiện nội dung chưa hoàn thành; việc chưa đủ điều kiện không loại bỏ nhu cầu nhắc hạn đánh giá.

Giao diện chấm điểm gồm năm nhóm tiêu chí: nghiệp vụ, kỹ năng, quy trình, phục vụ và tác phong. Mỗi nhóm được nhập theo thang điểm từ 0 đến 100. Điểm tổng hợp được tính như sau:

**Điểm tổng hợp = 0,20 × Điểm nghiệp vụ + 0,30 × Điểm kỹ năng + 0,20 × Điểm quy trình + 0,15 × Điểm phục vụ + 0,15 × Điểm tác phong.**

Theo quy tắc hiện tại, kết quả đạt yêu cầu khi điểm tổng hợp sau khi làm tròn một chữ số thập phân từ 80 trở lên và cả năm nhóm tiêu chí đều từ 70 trở lên. Điều kiện này giúp hạn chế trường hợp điểm cao ở một nhóm bù cho thiếu hụt lớn ở nhóm khác. Ví dụ, các điểm lần lượt là 90, 60, 90, 90 và 90 cho tổng điểm 81,0, nhưng kết quả vẫn chưa đạt do điểm kỹ năng dưới 70.

Khi kết quả chưa đạt, trưởng ca phải lựa chọn ít nhất một đề xuất và cung cấp thông tin phù hợp với đề xuất đó. Các nhóm đề xuất gồm đào tạo bổ sung, học lại bài học, theo dõi ca và đánh giá lại. Biểu mẫu sử dụng các trường chi tiết để làm rõ nội dung cần cải thiện. Sau khi lưu, đánh giá chuyển sang bước chờ quản lý phê duyệt; kết quả này chưa được xem là quyết định cuối cùng.

#### 4.1.5.3. Giao diện đào tạo lại tôi phụ trách

Khi được giao phụ trách một vòng đào tạo lại, trưởng ca sử dụng giao diện “Đào tạo lại tôi phụ trách” để xem nhân viên, số vòng, nội dung được giao và thời hạn. Màn hình có vùng ghi nhận nội dung thực hành hoặc hành vi quan sát, giúp người phụ trách cung cấp thông tin về việc thực hiện kế hoạch.

Người được phân công ghi nhận nội dung thực hành hoặc hành vi quan sát và xác nhận hoàn thành theo điều kiện của hệ thống. Nếu thay đổi người phụ trách hoặc nội dung thực hiện, yêu cầu xác nhận được kiểm tra lại. Sau khi đủ điều kiện, quản lý mới có thể tạo lịch đánh giá lại. Quyền xác nhận gắn với người được phân công; việc trình bày màn hình trong nhóm trưởng ca không có nghĩa mọi trưởng ca đều được xác nhận mọi vòng đào tạo.

### 4.1.6. Giao diện dành cho quản lý cửa hàng

Nhóm giao diện dành cho quản lý cửa hàng hỗ trợ theo dõi tổng thể nhân viên trong cửa hàng, xem hồ sơ, phê duyệt kết quả đánh giá và điều phối đào tạo lại. Thông tin dự đoán nguy cơ được đặt trong nhóm này để hỗ trợ ưu tiên xem xét hồ sơ. Các màn hình được trình bày theo trình tự tổng quan, quản lý nhân viên, phê duyệt và theo dõi quá trình cải thiện.

#### 4.1.6.1. Giao diện tổng quan quản lý cửa hàng

Đối tượng sử dụng là quản lý cửa hàng. Giao diện tổng quan tập hợp các chỉ số và công việc liên quan đến nhân viên của cửa hàng đang quản lý. Các bộ lọc hỗ trợ giới hạn thông tin theo những tiêu chí có trong giao diện, giúp người dùng chuyển từ tổng quan đến hồ sơ cần xem.

Biểu đồ kết quả đánh giá được tổng hợp từ các bản ghi đã được duyệt và sử dụng kết quả phù hợp với từng kỳ. Phần nguy cơ sử dụng kết quả dự đoán mới nhất của nhân viên. Việc tách hai nhóm thông tin giúp người dùng phân biệt kết luận nghiệp vụ được phê duyệt với tín hiệu tham khảo do mô hình tạo ra.

Danh sách ưu tiên xem hồ sơ được sắp xếp theo mức nguy cơ cao, trung bình, thấp và chưa đủ dữ liệu. Thông tin dự đoán giúp quản lý lựa chọn trường hợp cần xem xét sớm. Việc đánh giá năng lực và phê duyệt vẫn được thực hiện bằng luồng nghiệp vụ riêng, dựa trên dữ liệu học tập, kiểm tra và nhận xét của người đánh giá.

#### 4.1.6.2. Giao diện quản lý nhân viên và hồ sơ chi tiết

Giao diện danh sách nhân viên hỗ trợ quản lý xem thông tin nhân sự trong cửa hàng, lọc danh sách và điều chỉnh phân ca hoặc trạng thái làm việc theo chức năng đã triển khai. Phạm vi dữ liệu được giới hạn theo cửa hàng của tài khoản quản lý.

Khi mở hồ sơ chi tiết, quản lý có thể theo dõi thông tin học tập, số bài đã hoàn thành, lịch sử kiểm tra và đánh giá. Hồ sơ còn trình bày lịch sử các vòng đào tạo lại, giúp người quản lý xem quá trình cải thiện thay vì chỉ nhận biết trạng thái mới nhất.

Nhân viên tạm ngưng hoặc nghỉ việc được loại khỏi các luồng nhắc việc tương ứng nhưng lịch sử vẫn được giữ lại. Cách xử lý này hỗ trợ việc tra cứu dữ liệu đã phát sinh, đồng thời tránh đưa người không còn làm việc vào các công việc đào tạo hiện hành.

#### 4.1.6.3. Giao diện phê duyệt đánh giá

Giao diện phê duyệt tổ chức đánh giá theo trạng thái để quản lý nhận biết hồ sơ cần xử lý, hồ sơ đã duyệt và hồ sơ được trả lại. Thông tin phục vụ quyết định gồm nhân viên, điểm đánh giá, kết quả đạt hoặc chưa đạt, nhận xét và đề xuất cải thiện.

Quản lý có thể phê duyệt hoặc trả lại đánh giá để bổ sung. Khi trả lại, lý do là dữ liệu bắt buộc. Điểm đã ghi nhận được giữ lại để trưởng ca có căn cứ chỉnh sửa; hồ sơ được gửi lại sau khi bổ sung sẽ tiếp tục luồng phê duyệt. Hệ thống kiểm tra trạng thái trước khi xử lý để ngăn việc phê duyệt lại cùng một đánh giá.

Đối với đánh giá chưa đạt có đề xuất hợp lệ, việc phê duyệt có thể tạo yêu cầu đào tạo lại. Đối với hồ sơ thiếu kết quả hoặc thiếu thông tin đề xuất bắt buộc, hệ thống yêu cầu bổ sung thay vì cho phép xác lập quyết định từ dữ liệu chưa đầy đủ.

#### 4.1.6.4. Giao diện giao đào tạo lại và theo dõi các vòng

Giao diện giao đào tạo lại cho phép quản lý xác định kế hoạch thực hiện, người phụ trách và thời hạn. Một yêu cầu đào tạo lại có thể có nhiều vòng, được lưu trong cấu trúc retraining_rounds. Mỗi vòng có nội dung, người phụ trách, lịch thực hiện, đánh giá lại và kết quả duyệt riêng.

Khi kế hoạch có học lại bài liên quan, hệ thống đặt lại tiến độ của các bài được chọn và giữ thông tin trạng thái trước đó trong vòng đào tạo. Các bài ngoài kế hoạch và lịch sử kiểm tra đã có được bảo toàn. Điều kiện hoàn thành yêu cầu có lượt kiểm tra đạt mới phù hợp với nội dung và thời điểm giao vòng, tránh sử dụng kết quả cũ để chứng minh hoàn thành nhiệm vụ mới.

Quản lý theo dõi tiến độ thực hiện và xác nhận của người phụ trách được trình bày tại mục 4.1.5.3. Sau khi nội dung học lại, kiểm tra và xác nhận đáp ứng điều kiện của vòng, quản lý mới có thể tạo lịch đánh giá lại.

Nếu kết quả đánh giá lại chưa đạt và đã được duyệt, vòng hiện tại được giữ trong lịch sử, yêu cầu chuyển sang chờ giao vòng tiếp theo. Quản lý cần nhập kế hoạch và thời hạn mới. Khi đánh giá lại đạt và được phê duyệt, yêu cầu đào tạo lại mới được hoàn tất. Việc trả lại đánh giá để bổ sung không tự tạo vòng mới hoặc đóng yêu cầu.

#### 4.1.6.5. Giao diện thông tin dự đoán nguy cơ

Thông tin dự đoán nguy cơ được sử dụng để hỗ trợ quản lý ưu tiên xem hồ sơ nhân viên. Các mức hiển thị gồm thấp, trung bình, cao và chưa đủ dữ liệu. Nhãn chưa đủ dữ liệu được sử dụng khi nhân viên chưa có số lượt kiểm tra và số lượt học đáp ứng điều kiện chạy mô hình; trường hợp này không được xem là nguy cơ thấp.

Mã dự đoán phân loại xác suất dưới 0,30 là thấp; từ 0,30 đến dưới 0,60 là trung bình; từ 0,60 trở lên là cao. Đây là ngưỡng đang triển khai của nguyên mẫu, cần tiếp tục được đánh giá nếu sử dụng dữ liệu doanh nghiệp thực tế. Kết quả còn gắn với thời điểm dự đoán vì dữ liệu học tập có thể thay đổi sau khi mô hình đã chạy.

Hệ thống không sử dụng nguy cơ dự đoán để thay thế điểm đánh giá, tự phê duyệt kết quả hoặc tự kết luận nhân viên chưa đạt. Phân biệt này là yêu cầu quan trọng để bảo đảm ý nghĩa của dữ liệu trong quy trình quản lý đào tạo.

## 4.2. Triển khai hệ thống

### 4.2.1. Môi trường và công nghệ triển khai

BrewLearn được xây dựng dưới dạng ứng dụng web Laravel, tổ chức theo mô hình Model–View–Controller. View sử dụng Blade để trình bày dữ liệu và các thành phần HTML, CSS, JavaScript cho tương tác người dùng. Controller tiếp nhận yêu cầu, kiểm tra dữ liệu và điều phối các thao tác nghiệp vụ. Model và các thành phần xử lý liên quan được sử dụng để truy cập, tổng hợp và kiểm tra dữ liệu.

Phần dự đoán nguy cơ được triển khai bằng Python, đọc dữ liệu đào tạo, gọi mô hình đã lưu và ghi kết quả vào cơ sở dữ liệu để ứng dụng web sử dụng. Nguyên mẫu hiện sử dụng kết nối cơ sở dữ liệu cho quá trình trao đổi dữ liệu; chưa có căn cứ để mô tả thành một dịch vụ dự đoán trực tuyến có API riêng.

**Bảng 4.2. Công nghệ và môi trường của nguyên mẫu**

| Thành phần | Công nghệ hoặc cấu hình | Vai trò và căn cứ xác định |
| --- | --- | --- |
| Môi trường phát triển | Windows, thư mục dự án thuộc WampServer | Môi trường máy phát triển đang khảo sát; chưa xác minh cấu hình máy chủ công khai |
| Ngôn ngữ phía máy chủ | PHP 8.3.14 | Phiên bản kiểm tra bằng lệnh php -v |
| Framework | Laravel 13.32.0 | Phiên bản xác nhận bằng php artisan --version |
| Quản lý thư viện PHP | Composer 2.9.5 | Quản lý các phụ thuộc ứng dụng |
| Giao diện | Blade, Tailwind CSS, JavaScript, Alpine.js | Bố cục và tương tác trong mã giao diện; bố cục chung còn dùng tài nguyên CDN |
| Công cụ tài nguyên | Node.js 24.14.1, Vite | Môi trường Node đã kiểm tra; Vite khai báo trong package.json |
| Cơ sở dữ liệu nghiệp vụ | MySQL theo luồng kết nối Python | Lưu và trao đổi dữ liệu đào tạo, kiểm tra, dự đoán; phiên bản máy chủ chưa được xác minh trong lần khảo sát này |
| Cơ sở dữ liệu kiểm thử | SQLite trong bộ nhớ | Cấu hình trong phpunit.xml, tách khỏi cơ sở dữ liệu nghiệp vụ |
| Xử lý mô hình | Python, pandas, NumPy, scikit-learn, joblib | Các thư viện trong mã huấn luyện và dự đoán |
| Kết nối mô hình với dữ liệu | PyMySQL, python-dotenv | Được mã ket_noi_db.py sử dụng; cần cài bổ sung ngoài danh sách requirements.txt hiện tại |
| Kiểm thử tự động | PHPUnit qua môi trường Laravel | Thực hiện các ca kiểm thử sẵn có của dự án |

Nguồn: Khảo sát mã nguồn, cấu hình và môi trường ngày 05/10/2026. Các phiên bản Python và thư viện học máy đang dùng để phục vụ mô hình cần được ghi nhận riêng khi tái lập môi trường dự đoán.

### 4.2.2. Tổ chức triển khai và luồng xử lý

Luồng xử lý bắt đầu khi người dùng gửi yêu cầu từ trình duyệt. Laravel định tuyến yêu cầu đến chức năng tương ứng, kiểm tra phiên đăng nhập và áp dụng các điều kiện của chức năng. Dữ liệu được đọc hoặc cập nhật tại cơ sở dữ liệu, sau đó trả về giao diện hoặc phản hồi cho thao tác JavaScript.

Các nhóm dữ liệu chính gồm tài khoản và cửa hàng; khóa học và bài học; tiến độ học tập; bài kiểm tra và kết quả kiểm tra; đánh giá năng lực; yêu cầu và vòng đào tạo lại; thông báo và dự đoán nguy cơ. Liên kết các nhóm dữ liệu giúp theo dõi một nhân viên xuyên suốt từ học tập đến đánh giá lại.

Đối với mô hình, chương trình Python đọc cấu hình kết nối, tổng hợp các biến đầu vào, gọi hàm dự đoán và ghi kết quả. Laravel sử dụng kết quả đã lưu để hiển thị. Do quá trình này được thực hiện ở bước riêng, một hoạt động học hoặc kiểm tra mới không đồng nghĩa kết quả nguy cơ được cập nhật ngay. Để cập nhật nguy cơ từ dữ liệu mới, cần chạy lại chương trình dự đoán.

### 4.2.3. Quy trình chuẩn bị môi trường nguyên mẫu

Để tái lập nguyên mẫu, cần chuẩn bị PHP và các phần mở rộng theo yêu cầu phụ thuộc, Composer, cơ sở dữ liệu MySQL, Node.js và môi trường Python phù hợp với mô hình đã lưu. Các thư viện Python phục vụ kết nối dữ liệu phải được kiểm tra cùng các thư viện học máy. Tệp mô hình và tệp chuẩn hóa cần được sử dụng đồng bộ với phiên bản thư viện đã tạo ra chúng.

Bước đầu tiên là cài các phụ thuộc PHP bằng composer install từ tệp khóa của dự án. Tiếp theo, chuẩn bị tệp cấu hình môi trường từ mẫu, xác định khóa ứng dụng, kết nối cơ sở dữ liệu và cấu hình phiên. Mẫu .env.example hiện chọn SQLite mặc định, trong khi chương trình tích hợp dự đoán dùng MySQL; do đó cần cấu hình nhất quán khi triển khai luồng web cùng mô hình.

Bước chuẩn bị dữ liệu cần dựa trên nguồn khởi tạo được lựa chọn cho nguyên mẫu. Dự án có các migration và tệp brewlearn.sql; người triển khai cần kiểm tra tính tương thích trên một cơ sở dữ liệu mới trước khi chọn quy trình nhập dữ liệu. Không nên thực hiện đồng thời các thao tác khởi tạo có khả năng tạo trùng cấu trúc hoặc ghi đè dữ liệu. Các dữ liệu tài khoản, cửa hàng, chương trình đào tạo, bài học và bài kiểm tra phải được chuẩn bị trước khi minh họa nghiệp vụ.

Các tài nguyên giao diện được chuẩn bị theo package-lock.json và cấu hình công cụ tài nguyên. Trong môi trường trình diễn cục bộ có thể khởi động máy chủ phát triển Laravel; nếu sử dụng Apache của WampServer cần cấu hình thư mục phục vụ web là public. Các thư mục storage và bootstrap/cache phải có quyền ghi phù hợp để hệ thống lưu phiên, nhật ký và các dữ liệu phát sinh.

Trước khi trình diễn, cần đăng nhập bằng từng vai trò để kiểm tra luồng điều hướng, khả năng truy cập dữ liệu, tài nguyên video và các nội dung được phân công. Đây là quy trình triển khai nguyên mẫu được đề xuất từ cấu trúc ứng dụng; không được hiểu là biên bản xác nhận đã triển khai công khai trên máy chủ doanh nghiệp.

### 4.2.4. Tích hợp mô hình dự đoán nguy cơ

Mô hình sử dụng năm biến đầu vào: tiến độ đào tạo trung bình, điểm kiểm tra trung bình, số lượt kiểm tra, tỷ lệ lượt kiểm tra chưa đạt và số lượt học. Mục tiêu dự đoán là xác suất thuộc nhóm chưa đạt chuẩn năng lực theo mô hình được huấn luyện.

Chương trình kết nối dữ liệu chỉ lựa chọn nhân viên đang làm việc có thông tin cửa hàng, đồng thời loại các vai trò trưởng ca và quản lý. Điều kiện chạy mô hình yêu cầu có ít nhất một lượt kiểm tra và một lượt học. Nếu chưa đáp ứng, hệ thống ghi trạng thái chưa đủ dữ liệu và không gán điểm nguy cơ.

**Bảng 4.3. Các biến đầu vào của mô hình**

| Biến | Ý nghĩa | Lưu ý trong triển khai |
| --- | --- | --- |
| progress_percent | Tiến độ trung bình các chương trình phù hợp với chức vụ | Chương trình kết nối đọc dữ liệu tổng hợp trong nhan_vien_khoa_hoc; cần bảo đảm dữ liệu này được cập nhật |
| avg_quiz_score | Điểm trung bình các lượt kiểm tra của nhân viên | Phản ánh dữ liệu lịch sử được chương trình tổng hợp |
| quiz_attempts | Tổng số lượt kiểm tra | Phải lớn hơn 0 để chạy mô hình |
| failed_rate | Tỷ lệ lượt kiểm tra có điểm dưới 80 | Mã tích hợp dùng mốc cố định 80, trong khi điều kiện đạt nghiệp vụ có ngưỡng từng bài |
| learning_visits | Số lượt học của nhân viên | Phải lớn hơn 0 để chạy mô hình |

Nguồn: Chương trình app/ml/ket_noi_db.py và app/ml/predict.py.

Sự khác biệt giữa mốc 80 dùng để tạo biến failed_rate và ngưỡng đạt của từng bài kiểm tra cần được ghi nhận khi diễn giải kết quả. Nếu thay đổi ngưỡng nghiệp vụ, cần xem xét điều chỉnh cách tạo biến và tái đánh giá mô hình để duy trì tính nhất quán.

Dữ liệu huấn luyện hiện được tạo giả lập bằng chương trình train_model.py với 1.000 quan sát, chia 80% cho huấn luyện và 20% cho kiểm tra, có phân tầng theo nhãn và random_state bằng 42. Vì vậy, kết quả phản ánh khả năng thử nghiệm phương pháp trên dữ liệu giả lập, chưa chứng minh hiệu quả dự đoán đối với nhân viên của một doanh nghiệp thực tế.

### 4.2.5. Tác vụ nhắc hạn và quản lý dữ liệu minh họa

Hệ thống có tác vụ brewlearn:remind-assessments để tạo nhắc việc liên quan đến đánh giá. Mã cấu hình lịch đặt tác vụ chạy hằng ngày lúc 08 giờ theo múi giờ Asia/Ho_Chi_Minh. Để lịch này thực sự hoạt động, môi trường vận hành cần kích hoạt bộ lập lịch Laravel; việc có khai báo trong mã nguồn chưa đủ để xác nhận tác vụ đã chạy tự động trên máy chủ.

Hạn đánh giá lần đầu được tính từ ngày vào làm cộng ba tháng lịch; sau kết quả đã duyệt, chu kỳ tiếp theo căn cứ vào ngày đánh giá tương ứng. Nguyên mẫu xử lý các trường hợp sắp đến hạn, đến hạn, quá hạn và tình trạng không còn làm việc. Thời điểm hiển thị của dữ liệu demo thay đổi theo ngày chạy hệ thống, vì vậy ảnh minh họa cần ghi thời điểm chụp để tránh nhầm lẫn khi đối chiếu.

Bộ dữ liệu demo hỗ trợ minh họa học tập, kiểm tra, nguy cơ và đào tạo lại nhiều vòng. Lệnh chuẩn bị lại dữ liệu demo có thể xóa hoạt động thử nghiệm và tạo lại dữ liệu, nên chỉ sử dụng trên môi trường được dành cho minh họa. Trong lần khảo sát phục vụ chương này, không thực hiện lệnh khôi phục dữ liệu demo hoặc chạy chương trình dự đoán có thao tác ghi vào cơ sở dữ liệu nghiệp vụ.

## 4.3. Kiểm thử hệ thống

### 4.3.1. Mục tiêu và phạm vi kiểm thử

Kiểm thử nhằm xác định mức độ đáp ứng của nguyên mẫu đối với các yêu cầu đã triển khai, đặc biệt là tính nhất quán của dữ liệu và việc chuyển trạng thái trong quy trình đào tạo. Các nhóm chức năng được xem xét gồm tài khoản, học tập, kiểm tra kiến thức, đánh giá năng lực, phê duyệt, phân ca, đào tạo lại và nhắc hạn.

Kiểm thử còn xem xét một số tình huống bất thường như thiếu đề xuất khi đánh giá chưa đạt, chọn bài học không thuộc khóa, truy cập chức năng quản lý bằng tài khoản nhân viên, thao tác với dữ liệu của cửa hàng khác và xử lý lặp một đánh giá. Các tình huống này giúp kiểm tra khả năng ngăn dữ liệu không hợp lệ đi vào luồng nghiệp vụ.

Phạm vi thực nghiệm của chương là các kiểm thử Unit và Feature sẵn có trong dự án. Kiểm thử Feature gửi yêu cầu HTTP và xác nhận phản hồi, dữ liệu hoặc nội dung trả về trong môi trường Laravel. Kết quả này không thay thế kiểm thử toàn bộ thao tác trên trình duyệt, kiểm thử khả năng sử dụng, kiểm thử tải hoặc đánh giá bảo mật chuyên sâu.

### 4.3.2. Môi trường, dữ liệu và phương pháp kiểm thử

Bộ kiểm thử được thực hiện ngày 05/10/2026 với PHP 8.3.14 và môi trường ứng dụng của dự án. Cấu hình phpunit.xml sử dụng SQLite trong bộ nhớ, session và cache dạng array, cùng queue đồng bộ. Các lớp kiểm thử nghiệp vụ chuẩn bị bảng và dữ liệu phù hợp với tình huống cần xác nhận. Cơ sở dữ liệu nghiệp vụ được tách khỏi môi trường này.

Lệnh chạy tạo minh chứng của chương là:

```sh
php -d xdebug.mode=off vendor/phpunit/phpunit/phpunit --do-not-cache-result --log-junit docs/kiem-thu-brewlearn.xml
```

Kết quả được lưu theo định dạng JUnit để đối chiếu tổng số ca, số phép xác nhận và nội dung lỗi. Tổng số ca bao gồm các lần chạy được tách theo bộ dữ liệu của phương thức kiểm thử. Vì vậy, số ca thực thi có thể lớn hơn số phương thức được đặt tên trong mã nguồn.

Đối với các kiểm thử nghiệp vụ, kết quả đạt được xác định khi phản hồi và trạng thái dữ liệu phù hợp với các phép xác nhận của ca kiểm thử. Các ca không đạt được ghi nhận cùng lý do để phân biệt lỗi chức năng với kỳ vọng kiểm thử không còn phù hợp. Không suy rộng kết quả đạt thành kết luận hệ thống không còn lỗi ngoài phạm vi đã kiểm tra.

### 4.3.3. Kết quả kiểm thử tự động

**Bảng 4.4. Tổng hợp kết quả kiểm thử theo nhóm**

| Nhóm kiểm thử | Số ca | Đạt | Không đạt | Số phép xác nhận | Nội dung chính |
| --- | --- | --- | --- | --- | --- |
| AssessmentImprovementTest | 10 | 10 | 0 | 90 | Đề xuất cải thiện, dữ liệu chi tiết và điều kiện đánh giá |
| DemoWorkflowTest | 8 | 8 | 0 | 85 | Tài khoản, hỗ trợ, tiến độ và lịch sử kiểm tra |
| ManagerWorkflowTest | 33 | 33 | 0 | 317 | Phạm vi cửa hàng, phê duyệt, đào tạo lại và nhắc hạn |
| StaffAssessmentSummaryTest | 7 | 7 | 0 | 49 | Hiển thị điểm và trạng thái đánh giá nhân viên |
| Feature ExampleTest | 1 | 0 | 1 | 1 | Yêu cầu trang chủ bằng tài khoản chưa đăng nhập |
| Unit ExampleTest | 1 | 1 | 0 | 1 | Phép xác nhận mẫu của bộ kiểm thử |
| Tổng cộng | 60 | 59 | 1 | 543 | Không có ca lỗi thực thi hoặc ca bị bỏ qua |

Nguồn: Tệp docs/kiem-thu-brewlearn.xml, lần chạy ngày 05/10/2026. Các số liệu theo nhóm cần được đọc cùng tên lớp kiểm thử; ca Unit mẫu chỉ xác nhận giá trị đúng và không chứng minh một chức năng nghiệp vụ.

Kết quả ghi nhận 59 ca đạt trên tổng số 60 ca, tương đương khoảng 98,33%. Thời gian thực thi ghi trong tệp JUnit là khoảng 5,00 giây. Đây là thời gian chạy bộ kiểm thử trên môi trường khảo sát, không phải thời gian phản hồi trung bình của hệ thống đối với người dùng và không phải kết quả đo tải.

Bốn lớp kiểm thử nghiệp vụ đạt toàn bộ 58 ca của chúng. Kết quả cho thấy các tình huống được xây dựng trong những lớp này đáp ứng phép xác nhận về dữ liệu, phản hồi và chuyển trạng thái. Bộ kiểm thử tổng thể vẫn có trạng thái không đạt do một ca mẫu Feature chưa phù hợp với cơ chế xác thực hiện tại.

### 4.3.4. Các tình huống kiểm thử nghiệp vụ tiêu biểu

Bảng sau tóm tắt các tình huống từ bộ kiểm thử đã chạy. Mã KT được sử dụng để trình bày trong báo cáo, không phải tên phương thức của mã nguồn. Một hàng có thể đại diện cho nhiều phép xác nhận hoặc nhiều bộ dữ liệu; không cộng số hàng để thay thế tổng số ca ở Bảng 4.4.

**Bảng 4.5. Các tình huống kiểm thử tiêu biểu và kết quả**

| Mã | Tình huống và dữ liệu thực hiện | Kết quả mong đợi | Kết quả thực tế |
| --- | --- | --- | --- |
| KT01 | Nhân viên truy cập trang quản lý cửa hàng | Từ chối truy cập, có hướng xử lý đăng nhập phù hợp | Đạt trong ManagerWorkflowTest |
| KT02 | Quản lý truy cập danh sách và trang quản lý | Dữ liệu giới hạn theo cửa hàng của tài khoản | Đạt trong ManagerWorkflowTest |
| KT03 | Gán ca hoặc xử lý nhân viên thuộc cửa hàng khác | Không cập nhật dữ liệu ngoài phạm vi quản lý | Đạt trong ManagerWorkflowTest |
| KT04 | Đổi mật khẩu và cung cấp mật khẩu hiện tại | Kiểm tra mật khẩu cũ, giữ phiên đang sử dụng theo ca thử | Đạt trong DemoWorkflowTest |
| KT05 | Đăng xuất các thiết bị khác | Chỉ xóa các phiên khác của cùng tài khoản | Đạt trong DemoWorkflowTest |
| KT06 | Ghi tiến độ học một phần rồi tải lại và xem tổng hợp trưởng ca | Dữ liệu tiến độ hiển thị nhất quán | Đạt trong DemoWorkflowTest |
| KT07 | Thi lại và gửi lặp cùng lượt bài kiểm tra | Giữ lịch sử thi lại; gửi lặp trả kết quả đã lưu | Đạt trong DemoWorkflowTest |
| KT08 | Nhân viên chưa có kết quả kiểm tra hoặc đánh giá | Thể hiện chưa có kết quả, tránh hiển thị như điểm 0 | Đạt trong StaffAssessmentSummaryTest |
| KT09 | Đánh giá chưa đạt nhưng thiếu đề xuất hoặc chi tiết | Phản hồi lỗi, không lưu đánh giá thiếu dữ liệu | Đạt trong AssessmentImprovementTest |
| KT10 | Chọn bài học thuộc khóa khác trong đề xuất học lại | Từ chối tổ hợp khóa và bài học không hợp lệ | Đạt trong AssessmentImprovementTest |
| KT11 | Kết quả đạt trong hồ sơ có đề xuất cũ | Xóa đề xuất cũ và bỏ qua chi tiết ẩn không còn phù hợp | Đạt trong AssessmentImprovementTest |
| KT12 | Phê duyệt đánh giá chưa đạt và gửi lại thao tác | Tạo một yêu cầu đào tạo lại, chặn xử lý trùng | Đạt trong ManagerWorkflowTest |
| KT13 | Trả lại đánh giá để bổ sung | Bắt buộc lý do và giữ điểm đã ghi nhận | Đạt trong ManagerWorkflowTest |
| KT14 | Tạo lịch đánh giá đầu khi chưa đủ bài học hoặc quiz | Chặn tạo lịch; thông tin nhắc hạn vẫn được giữ | Đạt trong ManagerWorkflowTest |
| KT15 | Giao học lại bài mục tiêu trong vòng mới | Cần học và kiểm tra đạt mới theo vòng; giữ bài ngoài kế hoạch | Đạt trong ManagerWorkflowTest |
| KT16 | Tạo lịch đánh giá lại khi chưa có xác nhận hợp lệ | Chặn lịch; chỉ người phụ trách phù hợp được xác nhận | Đạt trong ManagerWorkflowTest |
| KT17 | Thực hiện nhiều vòng và duyệt kết quả đạt của đánh giá lại | Giữ lịch sử; chỉ hoàn tất sau khi kết quả đạt được duyệt | Đạt trong ManagerWorkflowTest |
| KT18 | Nhân viên quá hạn hơn 105 ngày hoặc ngày vào làm cuối tháng | Tiếp tục nhận diện quá hạn; tính mốc theo tháng lịch | Đạt trong ManagerWorkflowTest |
| KT19 | Nhân viên tạm ngưng hoặc nghỉ việc | Loại khỏi nhắc việc tương ứng, giữ lịch sử | Đạt trong ManagerWorkflowTest |
| KT20 | Dự đoán nguy cơ thay đổi trong khi đã có đánh giá được duyệt | Dự đoán không thay thế kết quả đánh giá nghiệp vụ | Đạt trong ManagerWorkflowTest |
| KT21 | Tạo yêu cầu hỗ trợ và xem danh sách bằng tài khoản đăng nhập | Dữ liệu gắn với người gửi và hiển thị đúng phạm vi ca thử | Đạt trong DemoWorkflowTest |
| KT22 | Truy cập / khi chưa đăng nhập trong Feature ExampleTest | Ca mẫu hiện yêu cầu HTTP 200 | Không đạt: nhận HTTP 302 |

Nguồn: Đối chiếu mã nguồn các lớp kiểm thử và kết quả JUnit của lần chạy đã nêu.

### 4.3.5. Phân tích trường hợp không đạt

Ca test_the_application_returns_a_successful_response trong Feature ExampleTest gửi yêu cầu đến đường dẫn trang chủ mà không tạo phiên đăng nhập, sau đó yêu cầu phản hồi HTTP 200. Trong cấu trúc tuyến hiện tại, trang chủ được bảo vệ bởi middleware auth. Lần chạy thực tế nhận HTTP 302, thể hiện phản hồi chuyển hướng đối với yêu cầu chưa xác thực.

Khi đối chiếu cấu trúc tuyến và chức năng đăng nhập, kỳ vọng của ca mẫu chưa phù hợp với yêu cầu bảo vệ trang chủ. Trường hợp này cần được điều chỉnh bằng một kiểm thử xác nhận khách chưa đăng nhập được chuyển đến trang đăng nhập. Kiểm thử trang chủ thành công cần chuẩn bị tài khoản, phiên đăng nhập và dữ liệu phù hợp trước khi yêu cầu HTTP 200.

Trong phạm vi biên soạn chương, ca mẫu được giữ nguyên để báo cáo đúng kết quả quan sát. Chưa có lần chạy lại sau khi sửa ca kiểm thử, nên không kết luận bộ kiểm thử đã đạt 60/60 ca. Đồng thời, chỉ dựa vào mã 302 chưa đủ để xác nhận toàn bộ luồng giao diện đăng nhập; đích chuyển hướng và hành vi trình duyệt nên được kiểm tra bằng ca riêng.

### 4.3.6. Đánh giá kết quả thử nghiệm mô hình

Tệp model_results.csv lưu các chỉ số của ba mô hình gồm Logistic Regression, Decision Tree và Random Forest. Các chỉ số được trình bày trong Bảng 4.6 nhằm mô tả kết quả thử nghiệm hiện có của dự án. Chương này không chạy lại huấn luyện hoặc thay thế các tệp mô hình khi thu thập minh chứng.

**Bảng 4.6. Kết quả mô hình được lưu trong dự án trên dữ liệu giả lập**

| Mô hình | Accuracy | Precision | Recall | F1-score |
| --- | --- | --- | --- | --- |
| Logistic Regression | 0,7350 | 0,7500 | 0,7706 | 0,7602 |
| Decision Tree | 0,6350 | 0,6765 | 0,6330 | 0,6540 |
| Random Forest | 0,6450 | 0,6759 | 0,6697 | 0,6728 |

Nguồn: app/ml/model_results.csv; mô tả cách tạo và chia dữ liệu được đối chiếu với app/ml/train_model.py. Đây là kết quả lưu sẵn, chưa được tái lập bằng một lần huấn luyện mới trong quá trình viết chương.

Trong các kết quả lưu hiện có, Logistic Regression có Accuracy và F1-score cao nhất. Điều này cung cấp căn cứ so sánh các mô hình trên bộ thử nghiệm giả lập của dự án. Tuy nhiên, dữ liệu và nhãn đều được sinh theo quy tắc mô phỏng, có thêm nhiễu; vì vậy không thể sử dụng các chỉ số này để khẳng định độ chính xác dự đoán năng lực của nhân viên trong điều kiện thực tế.

Mã dự đoán tải mô hình từ best_model.pkl và thông tin đi kèm từ model_info.pkl. Trước khi sử dụng trong một môi trường mới, cần kiểm tra tên mô hình, thứ tự biến đầu vào và bộ chuẩn hóa thực sự có trong các tệp này. Việc tồn tại bảng so sánh không tự chứng minh tệp mô hình đang phục vụ đã được xác nhận tương ứng với dòng có kết quả tốt nhất.

Để đánh giá khả năng ứng dụng thực tế, cần thu thập dữ liệu có kết quả đánh giá đã được duyệt làm nhãn tham chiếu, xác định mốc thời gian của các biến đầu vào và kiểm tra trên dữ liệu độc lập. Việc đánh giá cần xem xét cả trường hợp cảnh báo đúng và cảnh báo sai, đồng thời bảo đảm thông tin sau thời điểm đánh giá không bị đưa ngược vào dữ liệu dự đoán trước đánh giá.

### 4.3.7. Các nội dung cần kiểm thử bổ sung

Kết quả kiểm thử hiện tại chủ yếu phản ánh xử lý phía máy chủ với SQLite trong bộ nhớ. Khi triển khai luồng nghiệp vụ cùng MySQL và chương trình Python, cần kiểm tra tích hợp trên cơ sở dữ liệu thử riêng để xác nhận cấu trúc bảng, kiểu dữ liệu, tính toán biến đầu vào và việc ghi, đọc kết quả dự đoán.

Kiểm thử trình duyệt cần bao gồm đăng nhập theo từng vai trò, điều hướng, phát video, ghi tiến độ, chọn đáp án, gửi bài, mở biểu mẫu, sử dụng bộ lọc, xác nhận đào tạo lại và hiển thị trên màn hình nhỏ. Các tương tác này phụ thuộc JavaScript và tài nguyên giao diện, trong đó bố cục hiện còn sử dụng CDN. Các ca Feature hiện có không trực tiếp xác nhận mọi hành vi này.

Kiểm thử tải cần lựa chọn quy mô và tác vụ đại diện như nhiều người xem bài học, lưu tiến độ hoặc nộp bài cùng thời điểm. Kiểm thử bảo mật cần mở rộng phạm vi xác thực, quyền truy cập các tuyến, dữ liệu đầu vào và thông tin phiên. Chương chưa có kết quả đo thực tế cho hai nhóm này nên không đưa ra cam kết về số người dùng đồng thời, thời gian phản hồi hoặc mức độ an toàn toàn diện.

Đối với mô hình, cần kiểm tra riêng các trường hợp không có quiz, không có lượt học, dữ liệu được cập nhật sau dự đoán, các điểm sát ngưỡng 0,30 và 0,60, cùng trường hợp ngưỡng đạt của bài kiểm tra khác 80. Các trường hợp này được đề xuất bổ sung và chưa được gán kết quả đạt trong bảng kiểm thử đã thực hiện.

## 4.4. Đánh giá kết quả và tiểu kết chương

Nguyên mẫu BrewLearn đã tổ chức các nhóm giao diện theo ba vai trò để minh họa quy trình quản lý đào tạo. Nhân viên có thể tiếp cận nội dung học tập và kiểm tra; trưởng ca có công cụ theo dõi và ghi nhận đánh giá; quản lý cửa hàng có công cụ phê duyệt và điều phối đào tạo lại. Việc lưu lịch sử kiểm tra, đánh giá và các vòng đào tạo lại tạo cơ sở theo dõi quá trình cải thiện của nhân viên.

Phần triển khai làm rõ cấu trúc ứng dụng Laravel, cơ sở dữ liệu nghiệp vụ và chương trình Python phục vụ dự đoán nguy cơ. Kết quả dự đoán được trình bày như thông tin tham khảo để ưu tiên xem hồ sơ, đồng thời tách khỏi kết quả đánh giá thực tế đã được phê duyệt. Nguyên mẫu vẫn cần quy trình vận hành mô hình, lập lịch và kiểm tra tích hợp để bảo đảm dữ liệu hiển thị được cập nhật phù hợp.

Lần chạy bộ kiểm thử sẵn có ghi nhận 59/60 ca đạt với 543 phép xác nhận. Bốn lớp kiểm thử nghiệp vụ đạt 58/58 ca; một ca mẫu Feature không đạt do yêu cầu mã phản hồi chưa phù hợp với trang chủ có xác thực. Kết quả cho thấy các tình huống nghiệp vụ được kiểm tra có căn cứ thực nghiệm, nhưng chưa đủ để xác nhận toàn bộ giao diện, hiệu năng, bảo mật hoặc độ tin cậy dự đoán trong môi trường doanh nghiệp.

Nhìn chung, chương đã kết nối thiết kế giao diện với cách triển khai và các minh chứng kiểm thử của nguyên mẫu. Các kết quả là cơ sở để trình bày mức độ hoàn thành đề tài ở chương kết luận, đồng thời xác định những nội dung cần bổ sung khi tiếp tục phát triển hệ thống và thử nghiệm với dữ liệu thực tế.

## Tài liệu tham khảo sử dụng trong chương

[1] Nguyễn Quốc Hưng. Ứng dụng trí tuệ nhân tạo tạo sinh trong tự động hóa quy trình tuyển dụng. Khóa luận tốt nghiệp, Trường Đại học Ngân hàng Thành phố Hồ Chí Minh. Tham khảo mục 3.9 và Chương 4; trang in 42–58, tương ứng trang PDF 56–72.

[2] Nguyễn Thái Gia Khải (2025). Giải pháp số trong phát triển nền tảng tìm kiếm việc làm cho sinh viên Trường Đại học Ngân hàng TP. HCM có liên hệ AI. Đồ án chuyên ngành. Tham khảo mục 5.2, bắt đầu trang in 108, tương ứng trang PDF 125.

[3] Lương Thị Tường Vy (2025). Phân tích và thiết kế hệ thống website hỗ trợ du lịch tích hợp AI. Đồ án chuyên ngành, Trường Đại học Ngân hàng Thành phố Hồ Chí Minh. Tham khảo Chương 5; trang in 67–85, tương ứng trang PDF 81–99.

[4] Mã nguồn, cấu hình và tài liệu dự án BrewLearn: routes/web.php; routes/console.php; các Controller, Model và Blade liên quan; phpunit.xml; các lớp kiểm thử trong tests; app/ml/train_model.py; app/ml/predict.py; app/ml/ket_noi_db.py; app/ml/model_results.csv; DEMO-DATA.md. Khảo sát ngày 05/10/2026.

[5] Báo cáo thực thi bộ kiểm thử BrewLearn, docs/kiem-thu-brewlearn.xml, ngày 05/10/2026.
