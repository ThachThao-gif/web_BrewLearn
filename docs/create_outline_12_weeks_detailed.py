from pathlib import Path
from docx import Document
from docx.shared import Cm, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.oxml import OxmlElement
from docx.oxml.ns import qn

chapters = [
('CHƯƠNG 1 TỔNG QUAN ĐỀ TÀI', [
('1.1 Lý do chọn đề tài', [
('1.1.1 Bối cảnh đào tạo tại cửa hàng cà phê', 'Mô tả nhu cầu đào tạo và theo dõi năng lực nhân viên trong bối cảnh giả định của BrewLearn.'),
('1.1.2 Vấn đề cần giải quyết', 'Nêu khó khăn khi lưu thông tin phân tán, tổng hợp học tập, xử lý đánh giá và theo dõi đào tạo lại.')]),
('1.2 Mục tiêu và phạm vi', [
('1.2.1 Mục tiêu thực hiện', 'Xây dựng nguyên mẫu quản lý học tập, kiểm tra, đánh giá, phê duyệt và đào tạo lại; thử nghiệm dự đoán là nội dung bổ sung.'),
('1.2.2 Đối tượng và phạm vi chức năng', 'Giới hạn ba vai trò: Nhân viên, Trưởng ca, Quản lý cửa hàng. Phân ca, tài khoản, thông báo và hỗ trợ phục vụ quy trình cốt lõi.'),
('1.2.3 Phạm vi dữ liệu và giới hạn', 'Bối cảnh và dữ liệu giả lập; không cam kết triển khai doanh nghiệp, dự đoán theo thời gian trên dữ liệu thật hoặc quản trị nhân sự toàn diện.')]),
('1.3 Phương pháp và cơ sở lý thuyết cần thiết', [
('1.3.1 Phương pháp thực hiện', 'Nghiên cứu tài liệu, xây dựng bối cảnh, phân tích nghiệp vụ, thiết kế dữ liệu, xây dựng nguyên mẫu và kiểm thử.'),
('1.3.2 Cơ sở về đào tạo và đánh giá năng lực', 'Phân biệt học tập, kiểm tra kiến thức, đánh giá thực hành, phê duyệt và đào tạo lại.'),
('1.3.3 Công cụ phân tích thiết kế và phương pháp dự đoán', 'Giới thiệu ngắn BPMN, BFD, Use Case, DFD, ERD, trạng thái và kiến trúc; nêu các phương pháp phân loại thực sự sử dụng.')]),
('1.4 Sản phẩm và kết cấu báo cáo', [
('1.4.1 Sản phẩm dự kiến', 'Bộ hồ sơ phân tích thiết kế, CSDL, nguyên mẫu web, kết quả kiểm thử và thử nghiệm mô hình trên dữ liệu giả lập.'),
('1.4.2 Kế hoạch và kết cấu đồ án', 'Trình bày phân bổ 12 tuần và nội dung năm chương; không mở thêm mục câu hỏi nghiên cứu hoặc ý nghĩa dài, trùng mục tiêu.')])]),
('CHƯƠNG 2 PHÂN TÍCH HỆ THỐNG', [
('2.1 Bối cảnh và quy trình hiện trạng AS IS', [
('2.1.1 Bối cảnh và các bên tham gia', 'AS-IS có bốn làn HR, Nhân viên thử việc, Ca trưởng, Quản lý cửa hàng. Đây là hiện trạng mô tả trong bối cảnh giả định, không phải khảo sát doanh nghiệp đã xác nhận.'),
('2.1.2 Quy trình hiện trạng', 'Chèn BPMN AS-IS đã vẽ; thuyết minh pool/lane, hoạt động, nơi lưu thông tin và các điểm chuyển giao.'),
('2.1.3 Hạn chế và nhu cầu cải tiến', 'Lập bảng ngắn: vấn đề hiện trạng, ảnh hưởng và yêu cầu xử lý tương ứng.')]),
('2.2 Quy trình đề xuất TO BE', [
('2.2.1 Ranh giới quy trình và trách nhiệm', 'Giải thích TO-BE giới hạn ba vai trò sử dụng nguyên mẫu; HR ngoài phạm vi triển khai. Làn Hệ thống nếu có không phải vai trò người dùng thứ tư.'),
('2.2.2 Quy trình học tập và kiểm tra', 'Chèn BPMN: xem nội dung phù hợp chức vụ, học, ghi tiến độ, làm kiểm tra và xác định điều kiện đánh giá.'),
('2.2.3 Quy trình đánh giá và phê duyệt', 'Chèn BPMN: tạo lịch, trưởng ca đánh giá, gửi chờ duyệt; quản lý duyệt hoặc trả lại. Phê duyệt áp dụng cả đánh giá đầu, định kỳ và đánh giá lại.'),
('2.2.4 Quy trình đào tạo lại và đánh giá lại', 'Chèn BPMN: giao kế hoạch, người phụ trách xác nhận, tạo lịch lại, duyệt kết quả; chưa đạt thì quản lý chủ động giao vòng tiếp theo.')]),
('2.3 Yêu cầu và quy tắc nghiệp vụ', [
('2.3.1 Yêu cầu chức năng theo vai trò', 'Tổng hợp chức năng chính cho nhân viên, trưởng ca và quản lý; không tách User Story nếu lặp yêu cầu/Use Case.'),
('2.3.2 Yêu cầu phi chức năng thiết yếu', 'Nêu yêu cầu dễ sử dụng, nhất quán dữ liệu và kiểm soát truy cập; không đưa chỉ tiêu tải hoặc bảo mật chưa có cơ sở kiểm chứng.'),
('2.3.3 Quy tắc học tập kiểm tra và đánh giá', 'Điều kiện bài học/quiz trước đánh giá; điểm tổng theo năm tiêu chí từ 80, từng tiêu chí từ 70; kiểm tra đạt theo DiemChuan từng bài.'),
('2.3.4 Quy tắc phê duyệt và đào tạo lại', 'Đánh giá chưa đạt cần đề xuất; kết quả chỉ chính thức khi duyệt; giữ lịch sử vòng, trả lại không tạo vòng mới, chỉ đạt đã duyệt mới hoàn tất yêu cầu.')]),
('2.4 Mô hình chức năng và dòng dữ liệu', [
('2.4.1 Sơ đồ phân rã chức năng BFD', 'Chèn BFD tổng thể, giải thích các nhóm phục vụ phạm vi đã chốt.'),
('2.4.2 Use Case tổng quát và đặc tả trọng tâm', 'Chèn Use Case tổng quát; đặc tả ngắn học/kiểm tra, đánh giá, phê duyệt và đào tạo lại. Các sơ đồ và đặc tả đầy đủ đặt phụ lục.'),
('2.4.3 DFD ngữ cảnh', 'Chèn sơ đồ, mô tả tác nhân bên ngoài và luồng dữ liệu với hệ thống.'),
('2.4.4 DFD mức 0', 'Chèn sơ đồ, mô tả tiến trình và kho logic; kiểm tra cân bằng với ngữ cảnh. Các trang cùng mức không gọi thành các mức phân rã mới.')]),
('2.5 Tiểu kết chương', [])]),
('CHƯƠNG 3 THIẾT KẾ HỆ THỐNG', [
('3.1 Kiến trúc tổng thể', [
('3.1.1 Các thành phần và tổ chức ứng dụng', 'Chèn kiến trúc trình duyệt, Laravel MVC, MySQL và Python; giải thích vai trò từng thành phần trong cùng mục.'),
('3.1.2 Luồng xử lý và trao đổi dữ liệu', 'Mô tả yêu cầu web và đọc kết quả dự đoán đã lưu. Python chạy riêng; chưa có Scheduler/Artisan gọi dự đoán. Sơ đồ tuần tự chi tiết dẫn chiếu phụ lục.')]),
('3.2 Thiết kế cơ sở dữ liệu', [
('3.2.1 Các thực thể và thuộc tính trọng tâm', 'Trình bày nhóm nhân sự/cửa hàng, nội dung, tiến độ, kiểm tra, đánh giá, yêu cầu/vòng đào tạo lại và dự đoán.'),
('3.2.2 ERD và quan hệ giữa các thực thể', 'Chèn ERD tổng thể; nêu số lượng tham gia và liên kết. ERD theo nhóm chuyển phụ lục.'),
('3.2.3 Khóa ràng buộc và lưu lịch sử', 'Giải thích khóa chính, liên kết, xử lý trùng và quan hệ yêu cầu–vòng–đánh giá; phân biệt ràng buộc logic và khóa ngoại thực tế.'),
('3.2.4 Lược đồ quan hệ và từ điển dữ liệu', 'Trình bày ngắn bảng trọng tâm; từ điển dữ liệu đầy đủ đặt phụ lục. Giữ tên bảng/cột đúng triển khai, hoặc ghi bảng ánh xạ nếu dùng tên thiết kế khác.')]),
('3.3 Thiết kế xử lý học tập và kiểm tra', [
('3.3.1 Hiển thị nội dung và ghi nhận tiến độ', 'Mô tả nội dung theo chức vụ, tiến độ từng bài và tổng hợp khóa; bảo đảm hiển thị nhất quán.'),
('3.3.2 Chấm điểm và lưu lượt kiểm tra', 'Tính điểm từ đáp án, so với DiemChuan, lưu lịch sử thi lại và ngăn tạo kết quả trùng do gửi lặp.')]),
('3.4 Thiết kế đánh giá phê duyệt và đào tạo lại', [
('3.4.1 Tạo lịch và ghi nhận đánh giá', 'Lưu lịch trong danh_gia_nang_luc.NgayHen; kiểm tra điều kiện và phạm vi; tính điểm, nhận xét và đề xuất khi chưa đạt.'),
('3.4.2 Phê duyệt hoặc trả lại kết quả', 'ChoDanhGia → ChoPheDuyet; quản lý chuyển DaDuyet hoặc TraLai. Trưởng ca bổ sung rồi gửi lại ChoPheDuyet; không đi thẳng TraLai → DaDuyet.'),
('3.4.3 Giao vòng và xác nhận nội dung đào tạo lại', 'Mô tả người phụ trách, nội dung, thời hạn, điều kiện học/quiz mới và xác nhận. Đổi người/nội dung có thể yêu cầu xác nhận lại.'),
('3.4.4 Đánh giá lại và kết thúc yêu cầu', 'Chèn sơ đồ trạng thái đào tạo lại nhiều vòng. Vòng status=DaDuyet, result=Dat thì yêu cầu mới HoanThanh/HoanTat. Chưa đạt đã duyệt thì giữ lịch sử và chờ quản lý giao vòng mới; không giới hạn số vòng trong logic hiện tại.')]),
('3.5 Thử nghiệm mô hình dự đoán nguy cơ', [
('3.5.1 Dữ liệu và biến đầu vào', 'Mô tả 1.000 quan sát giả lập và năm biến progress_percent, avg_quiz_score, quiz_attempts, failed_rate, learning_visits.'),
('3.5.2 Nhãn mô hình và giới hạn mô phỏng', 'competency_status hiện sinh giả lập. Nhãn nghiệp vụ khi dùng dữ liệu thật chỉ lấy từ đánh giá đã QL duyệt, loại chờ duyệt/bị trả lại; dữ liệu đầu vào trước kết quả làm nhãn. Mốc 80 giả định chưa phản ánh DiemChuan khác nhau.'),
('3.5.3 Quy trình huấn luyện và kết quả so sánh', 'Chèn sơ đồ huấn luyện đã có; nêu chia tập, chuẩn hóa nếu cần và bảng Accuracy/Precision/Recall/F1 của ba mô hình. Phân biệt kết quả lưu sẵn với thực nghiệm tái lập; không thêm thuật toán mới.'),
('3.5.4 Tích hợp kết quả vào nguyên mẫu', 'Chạy Python riêng, tổng hợp biến, dự đoán khi đủ dữ liệu, ghi du_doan_nguy_co; hiển thị mức nguy cơ và thời điểm. Không thay thế đánh giá chính thức.')]),
('3.6 Tiểu kết chương', [])]),
('CHƯƠNG 4 XÂY DỰNG NGUYÊN MẪU VÀ KIỂM THỬ', [
('4.1 Môi trường và cách chạy nguyên mẫu', [
('4.1.1 Công nghệ và môi trường sử dụng', 'Nêu PHP/Laravel, MySQL, giao diện và Python; chỉ ghi phiên bản đã xác minh.'),
('4.1.2 Các bước chạy và chuẩn bị minh họa', 'Mô tả cấu hình và dữ liệu thử; phân biệt chạy cục bộ với triển khai công khai chưa xác nhận.')]),
('4.2 Giao diện chức năng chung', [
('4.2.1 Đăng nhập và tài khoản', 'Chèn ảnh tiêu biểu; mô tả xác thực và thiết lập tài khoản ngắn gọn.'),
('4.2.2 Thông báo và hỗ trợ', 'Nêu chức năng hỗ trợ; thông báo đọc khi tải trang, không khẳng định push thời gian thực.')]),
('4.3 Giao diện dành cho nhân viên', [
('4.3.1 Tổng quan học tập và khóa học', 'Chèn ảnh, mô tả nội dung được áp dụng và tiến độ.'),
('4.3.2 Bài học và bài kiểm tra', 'Chèn ảnh học bài, làm kiểm tra và kết quả.'),
('4.3.3 Theo dõi kết quả và nội dung học lại', 'Mô tả thông tin cá nhân, kết quả được công bố sau duyệt và nội dung được giao học lại theo màn hình thực tế.')]),
('4.4 Giao diện dành cho trưởng ca', [
('4.4.1 Tổng quan và theo dõi nhân viên', 'Chèn ảnh danh sách trong phạm vi phụ trách và tình trạng học/đánh giá.'),
('4.4.2 Tự học và làm kiểm tra', 'Nêu trưởng ca có hoạt động học/quiz, không thuộc đối tượng đánh giá năng lực nhân viên trong phạm vi đề tài.'),
('4.4.3 Tạo lịch và đánh giá năng lực', 'Chèn ảnh tạo lịch, chấm điểm, đề xuất và bổ sung kết quả bị trả lại.'),
('4.4.4 Đào tạo lại được phân công', 'Chèn ảnh nhiệm vụ và xác nhận; quyền xác nhận gắn với người được phân công.')]),
('4.5 Giao diện dành cho quản lý cửa hàng', [
('4.5.1 Tổng quan và hồ sơ nhân viên', 'Chèn ảnh Dashboard/hồ sơ; phân ca và trạng thái làm việc chỉ là chức năng hỗ trợ.'),
('4.5.2 Phê duyệt kết quả đánh giá', 'Chèn ảnh duyệt hoặc trả lại kèm lý do.'),
('4.5.3 Giao đào tạo lại và lịch sử vòng', 'Chèn ảnh giao kế hoạch và theo dõi kết quả nhiều vòng.'),
('4.5.4 Thông tin dự đoán nguy cơ', 'Chèn ảnh nếu có dữ liệu phù hợp; tách nguy cơ dự đoán khỏi kết quả đánh giá đã duyệt.')]),
('4.6 Kiểm thử và đánh giá kết quả', [
('4.6.1 Phạm vi môi trường và tình huống kiểm thử', 'Tập trung chức năng, quyền/phạm vi dữ liệu, điều kiện đánh giá và chuyển trạng thái; không cam kết kiểm thử tải/bảo mật chuyên sâu.'),
('4.6.2 Kết quả kiểm thử và ca không đạt', 'Lần chạy 05/10/2026: 60 ca, 59 đạt, 1 không đạt. Cập nhật nếu có lần chạy mới; không tự ghi 100%. Excel/JUnit đầy đủ đặt phụ lục.'),
('4.6.3 Đánh giá mức đáp ứng và giới hạn', 'Đối chiếu mục tiêu, chức năng và minh chứng; nêu nội dung chưa kiểm tra trình duyệt/tích hợp. Thời gian chạy test không là thời gian phản hồi hệ thống.')]),
('4.7 Tiểu kết chương', [])]),
('CHƯƠNG 5 KẾT LUẬN VÀ HƯỚNG PHÁT TRIỂN', [
('5.1 Kết quả đạt được', [
('5.1.1 Kết quả phân tích và thiết kế', 'Tóm tắt sự nhất quán yêu cầu, quy trình, chức năng, dữ liệu và kiến trúc.'),
('5.1.2 Kết quả nguyên mẫu và thực nghiệm', 'Nêu mức hoàn thành quy trình cốt lõi, kiểm thử và phần dự đoán bổ sung; không khẳng định lợi ích doanh nghiệp chưa đo.')]),
('5.2 Hạn chế của đồ án', [
('5.2.1 Giới hạn bối cảnh và phạm vi kiểm thử', 'Bối cảnh giả định, chưa vận hành doanh nghiệp; ca kiểm thử chỉ chứng minh tình huống đã thực thi.'),
('5.2.2 Giới hạn dữ liệu và mô hình', 'Nhãn giả lập và mốc 80 đồng nhất; chưa kiểm chứng trên dữ liệu thật có đánh giá được duyệt.')]),
('5.3 Hướng phát triển', [
('5.3.1 Hoàn thiện nguyên mẫu và vận hành', 'Đề xuất dữ liệu thật, kiểm thử trình duyệt/tải/bảo mật và triển khai thực tế ngoài cam kết 12 tuần.'),
('5.3.2 Hoàn thiện thử nghiệm dự đoán', 'Đánh giá độc lập bằng nhãn đã duyệt, biến theo ngưỡng từng quiz và tích hợp chạy tự động nếu cần.')])])]

doc=Document()
sec=doc.sections[0]
sec.page_width,sec.page_height=Cm(21),Cm(29.7)
sec.left_margin,sec.right_margin=Cm(3),Cm(2)
sec.top_margin,sec.bottom_margin=Cm(2),Cm(2)
for name in ['Normal','Title','Heading 1','Heading 2','Heading 3']:
    st=doc.styles[name];st.font.name='Times New Roman';st.font.size=Pt(13);st.font.color.rgb=RGBColor(0,0,0)
    st.element.get_or_add_rPr().rFonts.set(qn('w:eastAsia'),'Times New Roman')
    if name!='Normal':
        st.font.bold=True;st.paragraph_format.keep_with_next=True
        st.paragraph_format.space_before=Pt(8);st.paragraph_format.space_after=Pt(4)
doc.styles['Title'].font.size=Pt(16)
doc.styles['Normal'].paragraph_format.line_spacing=1.15
doc.styles['Normal'].paragraph_format.space_after=Pt(6)
doc.styles['Normal'].paragraph_format.widow_control=True
title=doc.add_paragraph('ĐỀ CƯƠNG CHI TIẾT ĐỒ ÁN TỐT NGHIỆP BREWLEARN','Title');title.alignment=WD_ALIGN_PARAGRAPH.CENTER
doc.add_paragraph('Phân tích và thiết kế hệ thống thông tin quản lý đào tạo và dự đoán nguy cơ nhân viên chưa đạt chuẩn năng lực tại chuỗi cửa hàng cà phê BrewLearn').runs[0].bold=True
doc.add_paragraph('Phạm vi 12 tuần: tập trung nguyên mẫu quy trình đào tạo tại cửa hàng. Dự đoán nguy cơ là thử nghiệm bổ sung trên dữ liệu giả lập. Các mục nhỏ dưới đây hướng dẫn tổ chức bài viết, không bổ sung phân hệ hoặc công việc kỹ thuật mới.')
doc.add_paragraph('Quy trình cốt lõi: học tập → kiểm tra → đánh giá → quản lý phê duyệt → đào tạo lại khi chưa đạt. Phân ca, tài khoản, thông báo và hỗ trợ chỉ phục vụ quy trình này.')
for title,sections in chapters:
    heading=doc.add_paragraph(title,'Heading 1')
    heading.paragraph_format.page_break_before=True
    for main,subs in sections:
        doc.add_paragraph(main,'Heading 2')
        for sub,description in subs:
            doc.add_paragraph(sub,'Heading 3')
            p=doc.add_paragraph(description);p.alignment=WD_ALIGN_PARAGRAPH.JUSTIFY
doc.add_paragraph('TÀI LIỆU THAM KHẢO VÀ PHỤ LỤC','Heading 1').paragraph_format.page_break_before=True
doc.add_paragraph('Tài liệu tham khảo chỉ liệt kê nguồn đã dùng về nghiệp vụ, ký pháp, công nghệ và phương pháp mô hình. Ba đồ án mẫu dùng tham khảo cách trình bày; không sử dụng kết quả của bài mẫu làm kết quả BrewLearn.')
for title,text in [
('Phụ lục A Sơ đồ và đặc tả bổ sung','Use Case chi tiết, đặc tả đầy đủ, các sơ đồ BPMN/DFD khổ lớn hoặc nhiều trang.'),
('Phụ lục B Hồ sơ dữ liệu','ERD theo nhóm, lược đồ quan hệ và từ điển dữ liệu đầy đủ.'),
('Phụ lục C Tương tác và trạng thái bổ sung','Trạng thái đánh giá; tuần tự chạy Python và tạo lịch/thông báo. Giữ trạng thái đào tạo lại nhiều vòng trong mục 3.4.4.'),
('Phụ lục D Minh chứng thực nghiệm','Excel/JUnit kiểm thử, dữ liệu và bảng chỉ số mô hình, ảnh bổ sung và hướng dẫn chạy nguyên mẫu.')]:
    doc.add_paragraph(title,'Heading 2');doc.add_paragraph(text)
doc.add_paragraph('LƯU Ý KHI ÁP DỤNG ĐỀ CƯƠNG','Heading 1')
doc.add_paragraph('Mỗi mục nhỏ trình bày ngắn nội dung cần chứng minh, gắn sơ đồ/bảng/ảnh khi có; không bắt buộc mỗi mục một sơ đồ riêng. Các mục dùng chung một hình nên dẫn chiếu để tránh lặp. Mục tiểu kết không cần chia thêm cấp.')
doc.add_paragraph('Quản lý cửa hàng không có hoạt động học/quiz trong phạm vi giao diện và Use Case đã chốt; mã phía máy chủ chưa chặn hoàn toàn truy cập trực tiếp các tuyến học/quiz nên không khẳng định quyền này đã được cưỡng chế đầy đủ khi chưa xác minh sửa và kiểm thử.')
doc.add_paragraph('Bản đề cương này dùng để giảng viên nhận xét phạm vi. Nếu cần giảm thêm khối lượng kỹ thuật, ưu tiên trao đổi về mức thử nghiệm dự đoán hoặc tên đề tài; không coi chuyển sơ đồ sang phụ lục là đã giảm chức năng.')
footer=sec.footer.paragraphs[0];footer.alignment=WD_ALIGN_PARAGRAPH.CENTER
field=OxmlElement('w:fldSimple');field.set(qn('w:instr'),'PAGE');footer._p.append(field)
out=Path(__file__).parent/'De-cuong-BrewLearn-12-tuan-chi-tiet-muc-nho.docx'
doc.core_properties.title='Đề cương chi tiết đồ án tốt nghiệp BrewLearn phạm vi 12 tuần'
doc.save(out)
check=Document(out)
assert sum(p.text.startswith('CHƯƠNG ') for p in check.paragraphs)==5
assert sum(p.style.name=='Heading 3' for p in check.paragraphs)==sum(len(s) for _,groups in chapters for _,s in groups)
print(f'{out}\nFive chapters; {sum(p.style.name=="Heading 3" for p in check.paragraphs)} numbered subsections verified.')
