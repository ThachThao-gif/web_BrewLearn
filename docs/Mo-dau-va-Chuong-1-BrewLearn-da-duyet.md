# MỞ ĐẦU

## 1 Tính cấp thiết của đề tài

Đào tạo nhân viên là một hoạt động gắn với việc duy trì chất lượng thực hiện công việc tại cửa hàng cà phê. Nhân viên pha chế, thu ngân và phục vụ cần nắm được nội dung nghiệp vụ, thực hiện đúng quy trình và đáp ứng các yêu cầu về kỹ năng, phục vụ và tác phong. Việc theo dõi đào tạo vì vậy cần kết hợp thông tin học tập, kết quả kiểm tra kiến thức và đánh giá thực hành, thay vì chỉ xác định nhân viên đã tham gia một khóa học hay chưa.

Trong bối cảnh giả định của chuỗi cửa hàng BrewLearn, thông tin đào tạo được lưu ở nhiều nơi và trao đổi giữa người học, trưởng ca và quản lý. Khi chưa có cấu trúc lưu trữ thống nhất, việc xác định tiến độ, nội dung còn thiếu, kết quả đang chờ duyệt hoặc kế hoạch đào tạo lại có thể gặp khó khăn. Đặc biệt, một nhân viên có thể cần nhiều vòng đào tạo lại; mỗi vòng cần giữ nội dung thực hiện, người phụ trách và kết quả để có căn cứ theo dõi quá trình cải thiện.

Đề tài “Phân tích và thiết kế hệ thống thông tin quản lý đào tạo và dự đoán nguy cơ nhân viên chưa đạt chuẩn năng lực tại chuỗi cửa hàng cà phê BrewLearn” được thực hiện để giải quyết bài toán tổ chức quy trình và dữ liệu trong bối cảnh này. Trọng tâm là phân tích, thiết kế hệ thống và giao diện minh họa. Phần dự đoán bằng Python trên dữ liệu giả lập cung cấp thêm một thử nghiệm phương pháp khai thác dữ liệu học tập; kết quả không thay thế đánh giá thực tế hoặc quyết định của quản lý.

## 2 Mục đích nghiên cứu

Đề tài hướng đến thiết kế hệ thống thông tin hỗ trợ quy trình học tập, kiểm tra, đánh giá năng lực, phê duyệt và đào tạo lại tại cửa hàng. Các chức năng được tổ chức theo trách nhiệm của nhân viên, trưởng ca và quản lý cửa hàng; dữ liệu được thiết kế để theo dõi tiến độ và lưu lịch sử xử lý.

Đồng thời, đề tài xây dựng và chạy thử mô hình phân loại nguy cơ bằng Python trên dữ liệu giả lập, trình bày kết quả cùng các giới hạn của thử nghiệm. Các chức năng web đã xây dựng được sử dụng để minh họa thiết kế, không đặt thành yêu cầu hoàn thiện toàn bộ hệ thống vận hành trong phạm vi đồ án.

## 3 Đối tượng và phạm vi nghiên cứu

Đối tượng nghiên cứu là quy trình quản lý đào tạo nhân viên cửa hàng cà phê và thông tin phát sinh trong học tập, kiểm tra, đánh giá, phê duyệt và đào tạo lại. Đề tài còn xem xét cách biểu diễn dữ liệu học tập thành biến đầu vào cho một mô hình dự đoán minh họa.

Phạm vi được xây dựng trong bối cảnh chuỗi cửa hàng giả định BrewLearn, không phải kết quả khảo sát được xác nhận tại doanh nghiệp cụ thể. Hệ thống đề xuất phục vụ ba vai trò: Nhân viên, Trưởng ca và Quản lý cửa hàng. Các hoạt động HR xuất hiện trong mô tả hiện trạng nhưng không được phát triển thành vai trò truy cập nguyên mẫu. Dữ liệu huấn luyện và minh họa là dữ liệu giả lập; phần triển khai web chỉ phục vụ minh họa các chức năng đã hoàn thành.

## 4 Ý nghĩa khoa học và tính thực tiễn

Về phương pháp, đề tài vận dụng các công cụ phân tích và thiết kế hệ thống để kết nối yêu cầu nghiệp vụ với quy trình, chức năng, dữ liệu và giao diện. Phần thực nghiệm mô hình làm rõ các bước xác định biến, xây dựng nhãn giả lập và so sánh kết quả phân loại. Đây là ứng dụng phương pháp đã có vào bài toán minh họa, không đặt mục tiêu đề xuất thuật toán mới.

Về khả năng ứng dụng, thiết kế BrewLearn cung cấp cách tổ chức thông tin đào tạo và phân định trách nhiệm trong phê duyệt, đào tạo lại. Bộ tài liệu và giao diện có thể làm cơ sở cho việc tiếp tục phát triển hoặc khảo sát thực tế. Đề tài chưa có dữ liệu để khẳng định mức giảm chi phí, tăng năng suất hoặc cải thiện chất lượng dịch vụ sau triển khai.

## 5 Phương pháp nghiên cứu

Đề tài sử dụng nghiên cứu tài liệu để xác định các khái niệm và phương pháp cần thiết, sau đó xây dựng bối cảnh nghiệp vụ giả định. Các công cụ mô hình hóa được dùng để phân tích hiện trạng, đề xuất quy trình và thiết kế chức năng, dữ liệu. Giao diện và các chức năng nguyên mẫu đã xây dựng được đối chiếu với thiết kế để minh họa cách sử dụng.

Phần thử nghiệm mô hình sử dụng Python để tạo dữ liệu giả lập, chia dữ liệu, huấn luyện các thuật toán được lựa chọn và tính các chỉ số đánh giá. Kết quả được diễn giải trong giới hạn của dữ liệu mô phỏng. Hoạt động chạy thử mô hình khác với kiểm thử toàn bộ website; kiểm thử hệ thống hoàn chỉnh không thuộc sản phẩm bắt buộc của phạm vi đã xác định.

## 6 Thời gian thực hiện

Đồ án được tổ chức trong thời lượng tối đa 12 tuần theo yêu cầu học phần. Công việc gồm xác định phạm vi, hoàn thiện phân tích nghiệp vụ, thiết kế hệ thống và dữ liệu, thử nghiệm mô hình, chuẩn bị giao diện minh họa và hoàn thiện báo cáo. Phân bổ các nhóm công việc được trình bày tại mục 1.4.2.

## 7 Kết quả dự kiến đạt được

Các sản phẩm chính gồm hồ sơ phân tích và thiết kế hệ thống BrewLearn; thiết kế cơ sở dữ liệu và các quan hệ; bộ giao diện theo ba vai trò; chương trình thử nghiệm dự đoán bằng Python cùng kết quả đánh giá trên dữ liệu giả lập; báo cáo đồ án và các phụ lục liên quan. Các chức năng web đã thực hiện được dùng làm minh họa bổ sung, không mở rộng cam kết sang vận hành doanh nghiệp thực tế.

## 8 Kết cấu báo cáo

Báo cáo gồm năm chương: Tổng quan đề tài; Phân tích hệ thống; Thiết kế hệ thống; Thiết kế giao diện và minh họa; Kết luận và hướng phát triển. Mở đầu giới thiệu ngắn vấn đề và phạm vi; Chương 1 cụ thể hóa mục tiêu, phương pháp, nền tảng lý thuyết và kế hoạch thực hiện.

# CHƯƠNG 1 TỔNG QUAN ĐỀ TÀI

## 1.1 Lý do chọn đề tài

### 1.1.1 Bối cảnh đào tạo tại cửa hàng cà phê

Hoạt động đào tạo tại cửa hàng cà phê liên quan đến nhiều nhóm nội dung như kiến thức sản phẩm, thao tác nghiệp vụ, tuân thủ quy trình, phục vụ và tác phong. Nội dung cần phù hợp với vị trí công việc, trong khi việc theo dõi phải giúp người phụ trách nhận biết nhân viên đã hoàn thành yêu cầu nào và còn cần bổ sung nội dung nào. Điểm kiểm tra kiến thức và kết quả quan sát thực hành có ý nghĩa khác nhau, vì vậy cần được lưu và diễn giải riêng.

BrewLearn được xây dựng như một bối cảnh giả định để phân tích bài toán này. Trong mô tả hiện trạng AS-IS, HR tham gia cùng Nhân viên thử việc, Ca trưởng và Quản lý cửa hàng. Bốn làn thể hiện trách nhiệm trong quy trình hiện trạng. Ở hệ thống đề xuất TO-BE, phạm vi sử dụng giới hạn ba vai trò Nhân viên, Trưởng ca và Quản lý cửa hàng nhằm tập trung vào quản lý đào tạo tại cửa hàng. Nhân viên thử việc thuộc nhóm Nhân viên và Ca trưởng tương ứng Trưởng ca; HR vẫn là thành phần của bối cảnh nghiệp vụ, không được xây dựng thành vai trò sử dụng hệ thống trong đề tài.

Quy trình đề xuất liên kết học tập với kiểm tra, đánh giá năng lực và xử lý sau đánh giá. Khi nhân viên chưa đạt, nội dung cải thiện cần có người phụ trách, thời hạn và kết quả đánh giá lại. Đào tạo lại có thể diễn ra qua nhiều vòng, nên thiết kế cần giữ lịch sử thay vì ghi đè toàn bộ thông tin cũ khi giao vòng tiếp theo.

### 1.1.2 Vấn đề cần giải quyết

Vấn đề thứ nhất là tổ chức thông tin học tập. Thiết kế cần xác định nội dung áp dụng theo chức vụ, tiến độ từng bài, mức độ hoàn thành khóa và lịch sử kiểm tra. Việc lưu thông tin có cấu trúc giúp đối chiếu giữa hoạt động cá nhân và dữ liệu tổng hợp mà trưởng ca hoặc quản lý sử dụng.

Vấn đề thứ hai là phân biệt kết quả đánh giá với trạng thái xử lý. Trưởng ca ghi điểm và nhận xét, còn quản lý phê duyệt hoặc trả lại để bổ sung. Kết quả vừa được chấm chưa phải kết quả chính thức. Thiết kế cần biểu diễn rõ bước chờ duyệt, trả lại và đã duyệt cho cả đánh giá lần đầu, định kỳ và đánh giá lại.

Vấn đề thứ ba là theo dõi đào tạo lại. Mỗi vòng cần lưu kế hoạch, người phụ trách, xác nhận và kết quả. Nếu đánh giá lại chưa đạt được duyệt, quản lý chủ động giao vòng mới; việc trả lại hồ sơ để bổ sung không tự tạo vòng mới. Yêu cầu chỉ hoàn thành khi kết quả đánh giá lại đạt được quản lý duyệt.

Vấn đề cuối cùng là cách sử dụng thông tin dự đoán. Dữ liệu học tập và kiểm tra có thể được chuyển thành biến đầu vào để thử nghiệm phân loại nguy cơ. Tuy nhiên, xác suất do mô hình tạo ra không đồng nhất với điểm đánh giá nghiệp vụ. Vì vậy, thiết kế giao diện cần phân biệt nguy cơ dự đoán, trạng thái chưa đủ dữ liệu và kết quả thực tế đã được duyệt.

## 1.2 Mục tiêu và phạm vi

### 1.2.1 Mục tiêu thực hiện

Mục tiêu tổng quát là phân tích và thiết kế hệ thống thông tin quản lý đào tạo BrewLearn, thiết kế giao diện minh họa và xây dựng, chạy thử mô hình dự đoán riêng bằng Python trên dữ liệu giả lập. Các chức năng web có thể thao tác đã hoàn thành được sử dụng để minh họa bổ sung cho thiết kế.

Các mục tiêu cụ thể gồm:

- Phân tích hiện trạng giả định, xác định hạn chế và đề xuất quy trình học tập, đánh giá, phê duyệt và đào tạo lại.
- Xác định tác nhân, yêu cầu chức năng và quy tắc nghiệp vụ trong phạm vi ba vai trò; mô hình hóa bằng BPMN, BFD, Use Case và DFD.
- Thiết kế kiến trúc, cơ sở dữ liệu, ERD và xử lý nghiệp vụ, chú trọng lưu lịch sử đánh giá và các vòng đào tạo lại.
- Thiết kế giao diện theo ba vai trò, làm rõ thông tin hiển thị và các thao tác trọng tâm.
- Thử nghiệm Logistic Regression, Decision Tree và Random Forest trên dữ liệu giả lập; so sánh các chỉ số và phân tích giới hạn sử dụng kết quả.

Đề tài không đặt mục tiêu hoàn thiện toàn bộ website, triển khai tại doanh nghiệp hoặc kiểm thử hệ thống chuyên sâu trong 12 tuần. Việc sử dụng nguyên mẫu bổ sung nhằm minh họa thiết kế và không thay đổi phạm vi sản phẩm chính.

### 1.2.2 Đối tượng và phạm vi chức năng

Đối tượng nghiên cứu gồm quy trình đào tạo nhân viên, dữ liệu học tập và kiểm tra, đánh giá năng lực, phê duyệt và đào tạo lại. Phần dữ liệu dự đoán được xem xét ở mức thử nghiệm phương pháp phân loại trên dữ liệu mô phỏng.

Nhân viên sử dụng hệ thống đề xuất để xem nội dung phù hợp chức vụ, học bài, theo dõi tiến độ, làm kiểm tra và xem thông tin kết quả cá nhân. Khi được giao học lại, nhân viên thực hiện nội dung tương ứng. Các chức năng tài khoản, thông báo và hỗ trợ phục vụ việc sử dụng hệ thống.

Trưởng ca theo dõi nhân viên thuộc phạm vi phụ trách, lập lịch khi đủ điều kiện, chấm điểm và nhập nhận xét, đề xuất cải thiện khi chưa đạt, bổ sung hồ sơ bị trả lại. Trưởng ca còn có thể tự học và làm kiểm tra; trong phạm vi đề tài, trưởng ca không thuộc nhóm nhân viên được đánh giá năng lực hoặc dự đoán nguy cơ. Khi được phân công phụ trách đào tạo lại, tài khoản thực hiện ghi nhận và xác nhận theo nhiệm vụ được giao.

Quản lý cửa hàng xem tổng quan và hồ sơ, phê duyệt hoặc trả lại đánh giá, giao và theo dõi các vòng đào tạo lại, tạo lịch đánh giá lại khi đủ điều kiện. Thông tin dự đoán hỗ trợ ưu tiên xem hồ sơ. Phạm vi giao diện quản lý không bao gồm tự học và kiểm tra cá nhân; phân ca và trạng thái làm việc là dữ liệu hỗ trợ, không mở rộng thành phân hệ quản trị nhân sự toàn diện.

### 1.2.3 Phạm vi dữ liệu và giới hạn

Đề tài sử dụng bối cảnh doanh nghiệp giả định và dữ liệu giả lập để xây dựng, đánh giá mô hình cũng như minh họa giao diện. Dữ liệu không đại diện cho quá trình vận hành được khảo sát dài hạn, nên chưa đủ căn cứ để suy luận hiệu quả tại một chuỗi cửa hàng thực tế. Đầu ra mô hình phản ánh xác suất phân loại từ các biến đầu vào tại thời điểm chạy, không chứng minh khả năng dự báo kết quả năng lực sau một hoặc ba tháng.

Nhãn competency_status trong thử nghiệm hiện tại được sinh theo quy tắc mô phỏng. Nếu phát triển với dữ liệu nghiệp vụ thật, nhãn cần lấy từ kết quả đánh giá đã được quản lý phê duyệt; loại dữ liệu chờ duyệt hoặc bị trả lại và xác định đầu vào trước mốc kết quả dùng làm nhãn. Dữ liệu mô phỏng còn sử dụng giả định ngưỡng kiểm tra đồng nhất 80, chưa phản ánh các ngưỡng DiemChuan khác nhau của từng bài kiểm tra.

Phạm vi không bao gồm tiền lương, chấm công, vai trò HR/Quản lý vùng trên hệ thống hoặc chức năng biên soạn, xuất bản toàn bộ nội dung đào tạo. Chương trình dự đoán chạy riêng bằng Python; chưa đặt yêu cầu vận hành tự động qua Scheduler hoặc API thời gian thực. Mô hình không tự phê duyệt đánh giá, tự giao đào tạo lại hoặc tự đóng yêu cầu.

## 1.3 Phương pháp và cơ sở lý thuyết

### 1.3.1 Phương pháp thực hiện

Nghiên cứu tài liệu được sử dụng để tìm hiểu các khái niệm quản lý đào tạo, phân tích thiết kế hệ thống và phân loại dữ liệu. Từ đó, đề tài xây dựng bối cảnh giả định, xác định vấn đề, tác nhân và quy tắc phù hợp với phạm vi đã lựa chọn. Không sử dụng cách diễn đạt hàm ý đã thực hiện phỏng vấn, khảo sát hoặc thu thập hồ sơ doanh nghiệp khi chưa có hoạt động đó.

Phân tích và thiết kế được thực hiện theo hướng liên kết các mô hình: quy trình cho biết trách nhiệm và trình tự, Use Case xác định mục tiêu tương tác, DFD mô tả dòng dữ liệu, ERD biểu diễn cấu trúc lưu trữ. Các mô hình được đối chiếu về tên gọi, phạm vi và quy tắc để tạo thành bộ hồ sơ nhất quán.

Thực nghiệm mô hình sử dụng dữ liệu giả lập để tạo biến và nhãn, chia tập huấn luyện/kiểm tra, chuẩn hóa khi phương pháp yêu cầu, huấn luyện và so sánh các thuật toán. Hoạt động chạy thử tập trung vào chương trình Python và kết quả phân loại. Giao diện, dữ liệu và các chức năng web đã hoàn thành được sử dụng để minh họa luồng sử dụng, không đặt kiểm thử toàn bộ website thành một phần độc lập của báo cáo.

### 1.3.2 Cơ sở lý thuyết

Trong phạm vi đề tài, quản lý đào tạo được hiểu là tổ chức nội dung theo vị trí công việc và theo dõi việc thực hiện của người học. Kiểm tra kiến thức cung cấp kết quả trả lời câu hỏi, còn đánh giá năng lực ghi nhận khả năng thực hiện công việc theo tiêu chí nghiệp vụ. Hai nguồn thông tin có thể hỗ trợ lẫn nhau nhưng không được dùng thay thế hoàn toàn.

Phê duyệt là bước xác lập kết quả chính thức theo quy trình thiết kế. Điểm đánh giá và trạng thái phê duyệt cần được lưu riêng để phân biệt “chưa đạt” với “chưa được duyệt”. Khi kết quả chưa đạt, đào tạo lại tập trung vào nội dung cần cải thiện, có phân công và đánh giá lại. Số vòng và lịch sử giúp theo dõi các lần thực hiện; điều kiện dừng là kết quả đạt đã được duyệt, không chỉ là hết thời hạn hoặc đã có xác nhận thực hiện.

Hệ thống thông tin trong đề tài kết nối dữ liệu, xử lý và người sử dụng. Kiến trúc MVC được dùng để tổ chức nguyên mẫu: Model liên quan đến dữ liệu, View trình bày thông tin, Controller tiếp nhận và điều phối yêu cầu. Đây là cách tổ chức thành phần, khác với các sơ đồ mô tả nghiệp vụ. Đối với BrewLearn, cơ sở dữ liệu còn là điểm trao đổi giữa phần web và chương trình Python chạy riêng.

Phân loại có giám sát sử dụng biến đầu vào và nhãn đã biết để học quy tắc phân biệt các lớp. Bài toán thử nghiệm sử dụng hai lớp, trong đó 0 biểu thị đạt và 1 biểu thị chưa đạt theo nhãn giả lập. Xác suất lớp 1 được dùng để biểu diễn nguy cơ tham khảo; xác suất không phải điểm chấm theo năm tiêu chí. Do nhãn và dữ liệu được mô phỏng, kết quả chỉ minh họa phương pháp.

### 1.3.3 Công cụ phân tích thiết kế và phương pháp dự đoán

**BPMN và trách nhiệm quy trình.** BPMN cung cấp ký pháp mô tả sự kiện, hoạt động, cổng điều kiện và luồng. Pool thể hiện bên tham gia, lane phân chia trách nhiệm trong pool; luồng trình tự và luồng thông điệp có mục đích khác nhau. Đề tài sử dụng BPMN cho AS-IS và TO-BE để làm rõ chuyển giao công việc giữa các vai trò [1].

**BFD và Use Case.** BFD tổ chức chức năng tổng thể thành các nhóm nhỏ, không thể hiện trình tự thực hiện. Use Case mô tả mục tiêu tương tác của tác nhân với hệ thống. Quan hệ include chỉ hành vi được đưa vào, extend mô tả hành vi mở rộng theo điều kiện; chúng không thay thế luồng công việc BPMN [2].

**DFD.** DFD thể hiện tiến trình, luồng, tác nhân ngoài và kho dữ liệu. Khi phân rã cần giữ cân bằng đầu vào/đầu ra với mức trên [3]. Theo quy ước của BrewLearn, ngữ cảnh có một tiến trình đại diện hệ thống; mức 0 phân rã thành các tiến trình nghiệp vụ. Kho logic có thể bao gồm nhiều bảng vật lý.

**ERD và thiết kế quan hệ.** ERD mô tả thực thể, thuộc tính và quan hệ cùng số lượng tham gia. Khi chuyển sang cấu trúc quan hệ, thiết kế cần xác định khóa và các liên kết. BrewLearn đối chiếu quan hệ thiết kế với bảng thực tế, phân biệt liên kết được ứng dụng sử dụng và ràng buộc vật lý đã khai báo.

**Trạng thái, tuần tự và kiến trúc.** Sơ đồ trạng thái mô tả trạng thái của đối tượng và điều kiện chuyển; sơ đồ tuần tự mô tả thứ tự thông điệp giữa các thành phần [2]. Trạng thái yêu cầu đào tạo lại cần phân biệt với trạng thái từng vòng. Kiến trúc tổng thể giải thích các thành phần và kết nối, còn sơ đồ tuần tự bổ sung cách chúng tương tác. Các hình bổ sung được đặt ở phụ lục khi không cần thiết trong nội dung chính.

**Logistic Regression.** Đây là phương pháp phân loại dù tên gọi có từ “regression”; mô hình liên kết biến đầu vào với xác suất lớp thông qua hàm logistic [4]. Trong thử nghiệm, xác suất lớp chưa đạt được dùng làm đầu ra tham khảo.

**Decision Tree.** Cây quyết định chia dữ liệu theo các điều kiện để tạo các nhánh phân loại. Cấu trúc cây có thể diễn giải theo đường đi từ gốc đến lá; độ phức tạp cần được kiểm soát để hạn chế quá khớp [5].

**Random Forest.** Phương pháp kết hợp nhiều cây được xây dựng với yếu tố ngẫu nhiên và tổng hợp dự đoán của chúng [6]. Đề tài sử dụng phương pháp này để so sánh với Logistic Regression và một cây quyết định trên bộ dữ liệu mô phỏng.

**Chỉ số đánh giá.** Accuracy đo tỷ lệ phân loại đúng; Precision đo tỷ lệ đúng trong các trường hợp được dự đoán là lớp dương; Recall đo tỷ lệ phát hiện được trong các trường hợp thực sự thuộc lớp dương; F1-score là trung bình điều hòa của Precision và Recall [7]. Trong đề tài, lớp dương là lớp 1, chưa đạt theo nhãn mô phỏng. Các chỉ số được đọc cùng nhau và không được diễn giải thành độ tin cậy đã xác nhận trên nhân viên thực tế.

## 1.4 Sản phẩm và kết cấu báo cáo

### 1.4.1 Sản phẩm dự kiến

Sản phẩm chính thứ nhất là bộ hồ sơ phân tích, gồm hiện trạng và quy trình đề xuất, yêu cầu, quy tắc nghiệp vụ, BFD, Use Case trọng tâm, DFD ngữ cảnh và mức 0. Các sơ đồ và đặc tả chi tiết được đặt ở phụ lục để phần chính tập trung vào quy trình cốt lõi.

Sản phẩm thứ hai là thiết kế kiến trúc, cơ sở dữ liệu, ERD, lược đồ và từ điển dữ liệu, cùng thiết kế xử lý học tập, kiểm tra, đánh giá và đào tạo lại. Thiết kế tập trung vào lưu lịch sử và phân biệt trạng thái xử lý với kết quả đạt/chưa đạt.

Sản phẩm thứ ba là bộ giao diện minh họa theo ba vai trò, có mô tả thông tin và thao tác. Các chức năng web đã hoàn thành được sử dụng để minh họa cách áp dụng thiết kế, không cam kết hoàn thiện toàn bộ website hoặc triển khai công khai.

Sản phẩm thứ tư là chương trình và kết quả thử nghiệm mô hình Python trên dữ liệu giả lập, gồm mô tả biến, nhãn, quy trình huấn luyện, bảng so sánh và giới hạn. Báo cáo hoàn chỉnh liên kết các sản phẩm này trong phạm vi đồ án; không coi bộ kiểm thử toàn hệ thống là sản phẩm bắt buộc.

### 1.4.2 Kế hoạch và kết cấu đồ án

Kế hoạch được phân bổ trong 12 tuần theo các nhóm công việc sau. Các sản phẩm đã thực hiện được rà soát và hoàn thiện thay vì làm lại; phân bổ này là kế hoạch công việc, không phải xác nhận đã hoàn thành từng giai đoạn.

| Thời gian | Công việc trọng tâm | Sản phẩm |
| --- | --- | --- |
| Tuần 1–2 | Xác định vấn đề, phạm vi và xây dựng bối cảnh | Đề cương và mô tả hiện trạng |
| Tuần 3–4 | Phân tích quy trình, chức năng và dòng dữ liệu | AS-IS/TO-BE, yêu cầu, BFD, Use Case, DFD |
| Tuần 5–6 | Thiết kế kiến trúc, dữ liệu và xử lý | Kiến trúc, ERD, lược đồ, trạng thái và quy tắc |
| Tuần 7–8 | Hoàn thiện chương trình Python và thử nghiệm | Dữ liệu giả lập, mô hình và bảng chỉ số |
| Tuần 9–10 | Hoàn thiện giao diện và chuẩn bị minh họa | Ảnh giao diện, dữ liệu và luồng minh họa |
| Tuần 11–12 | Rà soát nội dung, phụ lục và chuẩn bị trình bày | Báo cáo và nội dung bảo vệ |

**Chương 1 – Tổng quan đề tài:** trình bày lý do lựa chọn, mục tiêu, phạm vi, phương pháp, cơ sở lý thuyết và sản phẩm.

**Chương 2 – Phân tích hệ thống:** trình bày AS-IS, TO-BE, yêu cầu, quy tắc, BFD, Use Case và hai mức DFD.

**Chương 3 – Thiết kế hệ thống:** trình bày kiến trúc, cơ sở dữ liệu, ERD, xử lý học tập/kiểm tra, đánh giá/phê duyệt/đào tạo lại và thử nghiệm mô hình.

**Chương 4 – Thiết kế giao diện và minh họa:** trình bày môi trường, dữ liệu và cách chạy minh họa, giao diện chung và giao diện cho Nhân viên, Trưởng ca, Quản lý cửa hàng. Không tổ chức mục kiểm thử hệ thống riêng.

**Chương 5 – Kết luận và hướng phát triển:** đối chiếu sản phẩm với mục tiêu, nêu giới hạn bối cảnh, dữ liệu, mô hình và mức độ minh họa; đề xuất các bước phát triển ngoài phạm vi đã thực hiện.

Các phụ lục cung cấp sơ đồ khổ lớn hoặc chi tiết, đặc tả bổ sung, từ điển dữ liệu và minh chứng liên quan. Cách bố trí này giúp báo cáo chính giữ trọng tâm trong phạm vi 12 tuần và vẫn cho phép đối chiếu thiết kế khi cần.

# TÀI LIỆU THAM KHẢO CHO PHẦN VIẾT

[1] Object Management Group. Business Process Model and Notation, Version 2.0.2. https://www.omg.org/spec/BPMN/2.0.2/. Truy cập ngày 07/10/2026.

[2] Object Management Group. Unified Modeling Language, Version 2.5.1. https://www.omg.org/spec/UML/2.5.1/. Truy cập ngày 07/10/2026.

[3] University of Cape Town, Computer Science Department. MSc-IT Study Material, Chapter 6: Data-Flow Diagrams, Validation. https://www.cs.uct.ac.za/mit_notes/software/htmls/ch06s13.html. Truy cập ngày 07/10/2026.

[4] scikit-learn. Linear Models, Logistic Regression. https://scikit-learn.org/stable/modules/linear_model.html#logistic-regression. Truy cập ngày 07/10/2026.

[5] scikit-learn. Decision Trees. https://scikit-learn.org/stable/modules/tree.html. Truy cập ngày 07/10/2026.

[6] scikit-learn. Ensembles, Random Forests. https://scikit-learn.org/stable/modules/ensemble.html#forest. Truy cập ngày 07/10/2026.

[7] scikit-learn. Metrics and scoring: quantifying the quality of predictions. https://scikit-learn.org/stable/modules/model_evaluation.html. Truy cập ngày 07/10/2026.
