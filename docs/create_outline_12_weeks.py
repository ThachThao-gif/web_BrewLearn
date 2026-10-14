from pathlib import Path
from docx import Document
from docx.shared import Cm, Pt
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.oxml import OxmlElement
from docx.oxml.ns import qn

base = Path(__file__).parent
doc = Document()
section = doc.sections[0]
section.page_width, section.page_height = Cm(21), Cm(29.7)
section.left_margin, section.right_margin = Cm(3), Cm(2)
section.top_margin, section.bottom_margin = Cm(2), Cm(2)
for name in ['Normal', 'Title', 'Heading 1', 'Heading 2']:
    st=doc.styles[name]
    st.font.name='Times New Roman'
    st.font.size=Pt(13)
    st.element.get_or_add_rPr().rFonts.set(qn('w:eastAsia'),'Times New Roman')
    if name!='Normal':
        st.font.bold=True
        st.paragraph_format.keep_with_next=True
        st.paragraph_format.space_before=Pt(9)
        st.paragraph_format.space_after=Pt(5)
doc.styles['Title'].font.size=Pt(16)
normal=doc.styles['Normal'].paragraph_format
normal.line_spacing=1.15
normal.space_after=Pt(6)
normal.widow_control=True

def p(text, bold=False):
    para=doc.add_paragraph(text)
    para.alignment=WD_ALIGN_PARAGRAPH.JUSTIFY
    if bold:
        para.runs[0].bold=True
        para.paragraph_format.keep_with_next=True
    return para
def h(text,level=1):
    return doc.add_paragraph(text,f'Heading {level}')
def page(): doc.add_page_break()
def table(headers,rows,widths):
    t=doc.add_table(rows=1,cols=len(headers))
    t.style='Table Grid'
    t.autofit=False
    for c,w in zip(t.columns,widths): c.width=Cm(w)
    for c,text in zip(t.rows[0].cells,headers):
        c.text=text
        for r in c.paragraphs[0].runs: r.bold=True
    t.rows[0]._tr.get_or_add_trPr().append(OxmlElement('w:tblHeader'))
    for values in rows:
        for c,text,w in zip(t.add_row().cells,values,widths):
            c.width=Cm(w)
            c.text=text
    for row in t.rows:
        row._tr.get_or_add_trPr().append(OxmlElement('w:cantSplit'))
        for cell in row.cells:
            tcpr=cell._tc.get_or_add_tcPr()
            margins=OxmlElement('w:tcMar')
            for side in ['top','left','bottom','right']:
                val=OxmlElement('w:'+side)
                val.set(qn('w:w'),'90')
                val.set(qn('w:type'),'dxa')
                margins.append(val)
            tcpr.append(margins)
            for para in cell.paragraphs:
                para.paragraph_format.line_spacing=1.0
                para.paragraph_format.space_after=Pt(4)
                for r in para.runs:r.font.size=Pt(11)

title=doc.add_paragraph('ĐỀ CƯƠNG ĐỒ ÁN TỐT NGHIỆP BREWLEARN','Title')
title.alignment=WD_ALIGN_PARAGRAPH.CENTER
p('Tên đề tài: Phân tích và thiết kế hệ thống thông tin quản lý đào tạo và dự đoán nguy cơ nhân viên chưa đạt chuẩn năng lực tại chuỗi cửa hàng cà phê BrewLearn.',True)
p('Phạm vi thực hiện trong 12 tuần: nguyên mẫu quản lý đào tạo tại cửa hàng; dự đoán nguy cơ là phần thử nghiệm bổ sung trên dữ liệu giả lập.')
h('Mục tiêu và phạm vi thực hiện')
p('Mục tiêu chính là phân tích, thiết kế và xây dựng nguyên mẫu minh họa quy trình học tập → kiểm tra kiến thức → đánh giá năng lực → quản lý phê duyệt → đào tạo lại khi chưa đạt. Hệ thống lưu lịch sử kết quả và các vòng đào tạo lại; chỉ hoàn tất yêu cầu khi kết quả đánh giá lại đạt được quản lý duyệt.')
p('Phạm vi người dùng gồm Nhân viên, Trưởng ca và Quản lý cửa hàng. Nhân viên và trưởng ca có hoạt động học, kiểm tra; quản lý cửa hàng tập trung theo dõi, phê duyệt và giao đào tạo lại. Phân ca, hồ sơ, tài khoản, thông báo và hỗ trợ là chức năng hỗ trợ, không đặt thành các phân hệ nghiên cứu độc lập.')
p('Bối cảnh nghiệp vụ là giả định. AS-IS có bốn làn HR, Nhân viên thử việc, Ca trưởng và Quản lý cửa hàng; TO-BE giới hạn ba vai trò sử dụng nguyên mẫu. HR được giữ trong mô tả hiện trạng, không xây dựng thành vai trò truy cập hệ thống.')
p('Phần dự đoán sử dụng năm chỉ số học tập, kiểm tra và dữ liệu giả lập để minh họa phương pháp phân loại. Không cam kết dự báo năng lực thực tế theo thời gian, vận hành AI tự động hoặc đánh giá mô hình bằng dữ liệu doanh nghiệp.')
h('Sản phẩm chính')
p('Báo cáo năm chương; bộ sơ đồ phân tích và thiết kế cốt lõi; CSDL và nguyên mẫu web; giao diện theo ba vai trò; minh chứng kiểm thử; một mục thử nghiệm dự đoán cùng kết quả và giới hạn. Sơ đồ bổ sung, đặc tả đầy đủ và bảng kết quả chi tiết được đặt ở phụ lục.')

page()
h('CHƯƠNG 1 TỔNG QUAN ĐỀ TÀI')
h('1.1 Lý do chọn đề tài',2)
p('Nêu nhu cầu tổ chức đào tạo, lưu kết quả và theo dõi cải thiện năng lực trong bối cảnh cửa hàng cà phê giả định.')
h('1.2 Mục tiêu và phạm vi',2)
p('Xác định quy trình cốt lõi, ba vai trò và giới hạn nguyên mẫu. Phân biệt mục tiêu quản lý đào tạo với phần thử nghiệm dự đoán bổ sung.')
h('1.3 Phương pháp và cơ sở lý thuyết cần thiết',2)
p('Nghiên cứu tài liệu, mô hình hóa nghiệp vụ, thiết kế dữ liệu, xây dựng nguyên mẫu và kiểm thử. Giới thiệu ngắn các ký pháp được dùng và phương pháp phân loại; không trình bày thành khảo cứu rộng về AI.')
h('1.4 Sản phẩm dự kiến và cấu trúc báo cáo',2)
p('Trình bày sản phẩm cần hoàn thiện trong 12 tuần và bố cục năm chương.')
h('CHƯƠNG 2 PHÂN TÍCH HỆ THỐNG')
h('2.1 Bối cảnh và quy trình hiện trạng AS IS',2)
p('Chèn sơ đồ AS-IS đã vẽ, giải thích bốn làn và các điểm lưu trữ, chuyển giao thông tin. Lập bảng ngắn liên kết hạn chế với yêu cầu cải tiến.')
h('2.2 Quy trình đề xuất TO BE',2)
p('Chèn ba nhóm BPMN: học tập và kiểm tra; đánh giá và phê duyệt; đào tạo lại và đánh giá lại. Giải thích việc giới hạn ba vai trò, trách nhiệm trong pool/lane và điều kiện rẽ nhánh. Không yêu cầu thêm BPMN dự đoán riêng.')
h('2.3 Yêu cầu và quy tắc nghiệp vụ',2)
p('Tổng hợp chức năng theo vai trò, yêu cầu phi chức năng thiết yếu và quy tắc đánh giá, phê duyệt, đào tạo lại. Không mở mục User Story riêng nếu trùng yêu cầu và đặc tả Use Case.')
h('2.4 Mô hình chức năng và dòng dữ liệu',2)
p('Chèn BFD, Use Case tổng quát, DFD ngữ cảnh và DFD mức 0. Thuyết minh ranh giới, tác nhân, luồng và kho dữ liệu; chỉ đặc tả ngắn các Use Case trọng tâm. Bản đầy đủ đưa phụ lục.')
h('2.5 Tiểu kết chương',2)

page()
h('CHƯƠNG 3 THIẾT KẾ HỆ THỐNG')
h('3.1 Kiến trúc tổng thể',2)
p('Chèn kiến trúc trình duyệt – Laravel – MySQL – chương trình Python chạy riêng. Giải thích MVC và trao đổi dữ liệu trong cùng mục; sơ đồ tuần tự chi tiết đặt phụ lục. Hiện tại chưa có Scheduler/Artisan gọi Python dự đoán.')
h('3.2 Thiết kế cơ sở dữ liệu',2)
p('Trình bày thực thể trọng tâm, khóa, quan hệ, ERD tổng thể và các ràng buộc lưu lịch sử. Các bảng trọng tâm gồm dữ liệu học tập, kết quả kiểm tra, danh_gia_nang_luc, yeu_cau_dao_tao_lai và retraining_rounds. Từ điển dữ liệu đầy đủ, ERD theo nhóm đặt phụ lục.')
h('3.3 Thiết kế xử lý học tập và kiểm tra',2)
p('Mô tả hiển thị nội dung phù hợp chức vụ, ghi nhận tiến độ, chấm điểm theo đáp án và DiemChuan của từng bài, giữ lịch sử thi và xử lý gửi lặp. Không mở rộng thành quản trị biên soạn khóa học hoặc ngân hàng câu hỏi.')
h('3.4 Thiết kế đánh giá phê duyệt và đào tạo lại',2)
p('Trình bày điều kiện tạo lịch, điểm theo năm tiêu chí, phê duyệt hoặc trả lại cho mọi đánh giá và vòng đào tạo lại. Giữ sơ đồ trạng thái đào tạo lại nhiều vòng trong nội dung chính; sơ đồ trạng thái đánh giá bổ sung đặt phụ lục.')
p('Phân biệt trạng thái yêu cầu với từng vòng. Khi QL duyệt đánh giá lại đạt, vòng giữ status = DaDuyet, result = Dat; yêu cầu chuyển trang_thai = HoanThanh, tien_trinh = HoanTat. Khi chưa đạt đã duyệt, QL chủ động giao vòng kế tiếp. Trả lại chỉ tác động bản ghi đánh giá, không tự tạo vòng mới.')
p('Yêu cầu mới từ đánh giá chưa đạt được QL duyệt có thể tạo thẳng ở DaDuyet/ChoGiao. Nhánh ChoDuyet chỉ được mô tả là xử lý bản ghi chờ duyệt có sẵn; không khẳng định chức năng tạo đề xuất độc lập khi chưa triển khai.')
h('3.5 Thử nghiệm mô hình dự đoán nguy cơ',2)
p('Giới hạn trong một mục: năm biến đầu vào, 1.000 quan sát giả lập, cách chia tập, sơ đồ huấn luyện đã có và so sánh Logistic Regression, Decision Tree, Random Forest bằng các chỉ số đã lưu. Không bổ sung thuật toán mới hoặc nghiên cứu tối ưu sâu.')
p('Nhãn competency_status hiện sinh giả lập. Khi dùng dữ liệu nghiệp vụ, chỉ lấy nhãn từ đánh giá đã được QL duyệt; loại chờ duyệt/bị trả lại và tránh dùng dữ liệu tương lai. Giả định ngưỡng quiz đồng nhất 80 trong mô phỏng chưa phản ánh các DiemChuan khác nhau; nêu rõ giới hạn này.')
h('3.6 Tiểu kết chương',2)

page()
h('CHƯƠNG 4 XÂY DỰNG NGUYÊN MẪU VÀ KIỂM THỬ')
h('4.1 Môi trường và cách chạy nguyên mẫu',2)
p('Nêu công nghệ, cấu hình và cách chạy web, CSDL, script Python. Phân biệt môi trường cục bộ đã dùng với phương án triển khai công khai chưa được xác minh.')
h('4.2 Giao diện chức năng chung',2)
p('Mô tả ngắn đăng nhập, tài khoản, thông báo và hỗ trợ. Không cần mỗi chức năng một phần phân tích dài.')
h('4.3 Giao diện dành cho nhân viên',2)
p('Chèn ảnh tổng quan học tập, khóa/bài học, bài kiểm tra và thông tin kết quả cá nhân. Thuyết minh thao tác và dữ liệu hiển thị.')
h('4.4 Giao diện dành cho trưởng ca',2)
p('Chèn ảnh theo dõi nhân viên, tự học/kiểm tra, tạo lịch, chấm điểm và nhiệm vụ đào tạo lại được phân công. Trưởng ca không thuộc đối tượng đánh giá năng lực nhân viên trong phạm vi đề tài.')
h('4.5 Giao diện dành cho quản lý cửa hàng',2)
p('Chèn ảnh tổng quan, hồ sơ, phê duyệt, giao đào tạo lại và lịch sử vòng. Dashboard/nguy cơ được mô tả ở đây; không mở chương thiết kế Dashboard riêng. Phân ca và trạng thái làm việc chỉ trình bày ngắn như chức năng hỗ trợ.')
h('4.6 Kiểm thử và đánh giá kết quả',2)
p('Tập trung quyền/phạm vi dữ liệu, học và quiz, đánh giá – phê duyệt, chuyển trạng thái và hoàn tất đào tạo lại. Dùng kết quả đã thực thi: 60 ca, 59 đạt, 1 không đạt ngày 05/10/2026. Nếu sửa và chạy lại thì thay bằng minh chứng mới; không tự ghi 100% đạt. File Excel/JUnit chi tiết đặt phụ lục.')
h('4.7 Tiểu kết chương',2)
h('CHƯƠNG 5 KẾT LUẬN VÀ HƯỚNG PHÁT TRIỂN')
h('5.1 Kết quả đạt được',2)
p('Đối chiếu mục tiêu với thiết kế, nguyên mẫu và kiểm thử; nêu mức hoàn thành quy trình cốt lõi.')
h('5.2 Hạn chế',2)
p('Bối cảnh giả định, dữ liệu/nhãn mô phỏng, giả định ngưỡng 80, giới hạn kiểm thử và chưa có đánh giá vận hành doanh nghiệp.')
h('5.3 Hướng phát triển',2)
p('Dữ liệu thật có nhãn đã duyệt, tích hợp dự đoán tự động, kiểm thử tải/bảo mật chuyên sâu và triển khai thực tế là hướng phát triển ngoài cam kết 12 tuần.')

page()
h('TÀI LIỆU THAM KHẢO VÀ PHỤ LỤC')
p('Tài liệu tham khảo chỉ gồm nguồn đã sử dụng cho nghiệp vụ, ký pháp, công nghệ và mô hình. Ba bài mẫu được dùng tham khảo cách trình bày, không dùng kết quả của bài mẫu làm kết quả BrewLearn.')
p('Phụ lục giữ Use Case chi tiết và đặc tả đầy đủ, từ điển dữ liệu, ERD theo nhóm, hai sơ đồ tuần tự, sơ đồ trạng thái đánh giá, ảnh bổ sung và bảng kiểm thử. Các sơ đồ đã làm vẫn được bảo toàn, không yêu cầu vẽ lại để thu gọn đề cương.')
h('KẾ HOẠCH THỰC HIỆN TRONG 12 TUẦN')
table(['Tuần','Công việc','Sản phẩm'],[
('1–2','Chốt vấn đề, phạm vi và rà soát AS-IS/TO-BE','Đề cương và quy trình nghiệp vụ'),
('3–4','Hoàn thiện yêu cầu, Use Case, BFD và DFD','Hồ sơ phân tích nhất quán'),
('5–6','Đối chiếu kiến trúc, ERD, CSDL và quy tắc xử lý','Thiết kế hệ thống'),
('7–8','Hoàn thiện nguyên mẫu và minh họa dự đoán đã có','Luồng cốt lõi và kết quả mô hình'),
('9–10','Kiểm thử, xử lý lỗi trọng tâm, thu minh chứng','Kết quả có thể đối chiếu'),
('11–12','Hoàn thiện báo cáo, phụ lục và chuẩn bị bảo vệ','Báo cáo và nội dung trình bày')],[2,8,6])
p('Kế hoạch phân bổ công việc trong 12 tuần, không khẳng định các hạng mục đã hoàn thành theo từng tuần. Với sản phẩm đã có, tập trung rà soát và hoàn thiện thay vì làm lại.')
h('NHỮNG NỘI DUNG ĐÃ THU GỌN ĐỂ XEM XÉT')
p('Gộp câu hỏi nghiên cứu vào mục tiêu; không mở User Story riêng; gộp yêu cầu mô hình vào mục 3.5; gộp MVC vào kiến trúc; gộp Dashboard vào giao diện quản lý; gộp kiểm soát quyền/trạng thái vào thiết kế nghiệp vụ và kiểm thử. Chuyển sơ đồ kỹ thuật bổ sung sang phụ lục.')
p('Không đưa quản trị nhân sự toàn diện, biên soạn nội dung, AI vận hành tự động, đo hiệu quả doanh nghiệp, kiểm thử tải hoặc bảo mật chuyên sâu vào sản phẩm bắt buộc. Việc rút gọn mục lục không làm giảm phần đã xây dựng; nếu cần giảm thêm khối lượng thực hiện, trao đổi với giảng viên để giới hạn phần dự đoán ở mức minh họa tối thiểu hoặc điều chỉnh tên đề tài.')

footer=section.footer.paragraphs[0]
footer.alignment=WD_ALIGN_PARAGRAPH.CENTER
field=OxmlElement('w:fldSimple');field.set(qn('w:instr'),'PAGE');footer._p.append(field)
doc.core_properties.title='Đề cương đồ án tốt nghiệp BrewLearn phạm vi 12 tuần'
out=base/'De-cuong-BrewLearn-thu-gon-12-tuan.docx'
doc.save(out)
check=Document(out)
assert len(check.tables)==1
assert sum(x.text.startswith('CHƯƠNG ') for x in check.paragraphs)==5
print(out)
