from pathlib import Path
import re
from docx import Document
from docx.shared import Cm, Pt, RGBColor
from docx.enum.table import WD_CELL_VERTICAL_ALIGNMENT
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.oxml import OxmlElement
from docx.oxml.ns import qn

base=Path(__file__).parent
src=Document(base/'Chuong-2-Muc-2.4.2-Use-case-BrewLearn.docx')
titles=[p.text for p in src.paragraphs if re.match(r'^UC-\w+\d+ — ',p.text)]
spec={title.split(' — ')[0]: (title.split(' — ')[1],{r.cells[0].text:r.cells[1].text for r in t.rows[1:]}) for title,t in zip(titles,src.tables[5:])}
chosen=['UC-NV02','UC-NV03','UC-TC02','UC-TC03','UC-TC05','UC-QL05','UC-QL07','UC-CH08','UC-QL09']
rules={
'UC-NV02':['Chỉ ghi tiến độ cho đúng tài khoản và bài học được phép truy cập.','Trạng thái hoàn thành và tiến độ tổng hợp phải tuân theo cùng quy tắc học tập; không tự suy ra kết quả đánh giá năng lực từ tiến độ.'],
'UC-NV03':['Điểm kiểm tra được tính từ tỷ lệ câu đúng, quy đổi về thang 100 và so với DiemChuan từng bài; dùng 80 khi chưa có giá trị cấu hình.','Một mã lượt làm bài chỉ tạo một kết quả; các lượt mới được lưu riêng và giữ lịch sử.'],
'UC-TC02':['Nhân viên phải đang làm việc, thuộc phạm vi phụ trách và hoàn thành bài học, quiz yêu cầu.','Không tạo lịch ban đầu khi có hồ sơ đang chờ xử lý hoặc yêu cầu đào tạo lại đang mở; lịch đánh giá lại được tạo từ vòng tương ứng.'],
'UC-TC03':['Điểm tổng = 20% nghiệp vụ + 30% kỹ năng + 20% quy trình + 15% phục vụ + 15% tác phong; làm tròn một chữ số thập phân.','Đạt khi điểm tổng từ 80 và từng tiêu chí từ 70; chưa đạt bắt buộc có đề xuất xử lý.','Gửi đánh giá chỉ chuyển sang Chờ phê duyệt; hồ sơ bị trả lại phải bổ sung rồi gửi lại, không chuyển trực tiếp sang Đã duyệt.'],
'UC-TC05':['Kết quả chưa đạt phải có ít nhất một đề xuất hợp lệ trước khi gửi phê duyệt.','Đào tạo bổ sung, học lại bài học, theo dõi ca và đánh giá lại là các nội dung xử lý trong cùng quy trình; đề xuất chưa đồng nghĩa đã giao vòng đào tạo.'],
'UC-QL05':['Chỉ quản lý có quyền với nhân viên thuộc cửa hàng được xử lý hồ sơ Chờ phê duyệt; trả lại bắt buộc nhập lý do.','Kết quả chỉ chính thức và gửi cho nhân viên sau phê duyệt, áp dụng cả đánh giá ban đầu và đánh giá lại.','Đánh giá lại Đạt đã duyệt mới hoàn tất yêu cầu; Chưa đạt đã duyệt giữ yêu cầu mở để giao vòng sau.','Phê duyệt đánh giá ban đầu chưa đạt có đề xuất có thể tạo yêu cầu đào tạo lại đã duyệt, không bắt buộc thêm lần duyệt đề xuất.'],
'UC-QL07':['Chỉ phân công yêu cầu đã duyệt ở trạng thái cho phép; người phụ trách thuộc cửa hàng và khác người được đào tạo.','Ngày đánh giá lại dự kiến không trước hạn hoàn thành; mốc dự kiến chưa thay thế thao tác tạo lịch đánh giá lại khi đủ điều kiện.','Vòng mới giữ lịch sử vòng cũ; sửa vòng đang thực hiện không tự tạo vòng mới. Đổi người phụ trách hoặc hướng dẫn phải xác nhận lại.'],
'UC-CH08':['Chỉ tài khoản được giao phụ trách vòng hiện tại được xác nhận.','Các bài học và quiz yêu cầu phải đáp ứng điều kiện của vòng; phải có ghi chú xác nhận.','Xác nhận nội dung không thay thế đánh giá năng lực và không đóng yêu cầu đào tạo lại.'],
'UC-QL09':['Chỉ tạo lịch từ vòng đang thực hiện của yêu cầu đã duyệt, khi đủ bài học, quiz mới đạt chuẩn và xác nhận của người phụ trách.','Kết quả quiz trước vòng không thay thế lượt mới theo yêu cầu; hồ sơ đánh giá phải liên kết đúng vòng.','Kết quả đánh giá lại vẫn phải qua trưởng ca chấm và quản lý phê duyệt; chỉ Đạt đã duyệt mới hoàn tất yêu cầu.']}
nfr={
'UC-NV02':['Tiến độ đã lưu phải được hiển thị nhất quán khi người học mở lại bài hoặc khóa.','Dữ liệu cập nhật phải được kiểm tra phía máy chủ; lỗi lưu không được hiển thị như đã hoàn thành.'],
'UC-NV03':['Chấm điểm phải dựa trên đáp án lưu ở phía máy chủ, không tin điểm do trình duyệt gửi.','Gửi lặp cùng lượt không tạo kết quả trùng; lỗi lưu không được thông báo thành công.'],
'UC-TC02':['Hệ thống phải chỉ rõ điều kiện còn thiếu để trưởng ca biết lý do không thể lập lịch.','Quyền, phạm vi và trạng thái phải được kiểm tra tại thời điểm ghi để hạn chế lịch trùng hoặc không hợp lệ.'],
'UC-TC03':['Các điểm thành phần, điểm tổng, đề xuất và trạng thái phải được lưu nhất quán.','Điểm được kiểm tra phía máy chủ; hồ sơ chờ duyệt hoặc đã duyệt phải được bảo vệ khỏi chỉnh sửa không đúng trạng thái.'],
'UC-TC05':['Biểu mẫu phải chỉ rõ trường bắt buộc tương ứng từng loại đề xuất.','Chi tiết đề xuất phải gắn đúng hồ sơ và được bảo vệ theo quyền truy cập.'],
'UC-QL05':['Quyết định, trạng thái đánh giá và cập nhật vòng/yêu cầu liên quan phải nhất quán khi lưu.','Phải lưu người duyệt và thời điểm duyệt; thao tác lặp không tạo thêm quyết định nghiệp vụ.'],
'UC-QL07':['Nội dung, người phụ trách, thời hạn và mốc dữ liệu kiểm tra của vòng phải được lưu nhất quán.','Lịch sử các vòng phải tra cứu được; thông báo giao việc phải gắn đúng người nhận.'],
'UC-CH08':['Phải lưu tài khoản xác nhận, thời điểm và ghi chú để truy vết trách nhiệm.','Quyền xác nhận và trạng thái vòng phải được kiểm tra phía máy chủ khi gửi yêu cầu.'],
'UC-QL09':['Hệ thống phải liệt kê rõ các điều kiện chưa hoàn thành.','Lịch, liên kết hồ sơ với vòng và trạng thái tiến trình phải được lưu nhất quán; không xác nhận thành công khi lưu lỗi.']}
doc=Document();s=doc.sections[0]
s.page_width,s.page_height=Cm(21),Cm(29.7)
s.left_margin,s.right_margin=Cm(3),Cm(2)
s.top_margin,s.bottom_margin=Cm(2),Cm(2)
for name,size in [('Normal',13),('Title',14),('Heading 1',13),('Heading 2',13)]:
 st=doc.styles[name];st.font.name='Times New Roman';st.font.size=Pt(size);st.font.color.rgb=RGBColor(0,0,0)
 st.element.get_or_add_rPr().rFonts.set(qn('w:eastAsia'),'Times New Roman')
 st.paragraph_format.space_after=Pt(6);st.paragraph_format.line_spacing=1.15
 if name!='Normal':st.font.bold=True;st.paragraph_format.keep_with_next=True

def para(text,style=None):
 p=doc.add_paragraph(text,style);p.paragraph_format.widow_control=True
 if style is None:p.alignment=WD_ALIGN_PARAGRAPH.JUSTIFY
 return p
def table(rows,widths):
 t=doc.add_table(rows=0,cols=len(widths));t.autofit=False
 for col,w in zip(t.columns,widths):col.width=Cm(w)
 borders=OxmlElement('w:tblBorders')
 for k in ['top','left','bottom','right','insideH','insideV']:
  el=OxmlElement('w:'+k);el.set(qn('w:val'),'single');el.set(qn('w:sz'),'4');el.set(qn('w:color'),'D9D9D9');borders.append(el)
 t._tbl.tblPr.append(borders)
 for i,row in enumerate(rows):
  r=t.add_row();r._tr.get_or_add_trPr().append(OxmlElement('w:cantSplit'))
  if i==0:r._tr.get_or_add_trPr().append(OxmlElement('w:tblHeader'))
  for c,text,w in zip(r.cells,row,widths):
   c.width=Cm(w);c.vertical_alignment=WD_CELL_VERTICAL_ALIGNMENT.CENTER
   mar=OxmlElement('w:tcMar')
   for k in ['top','left','bottom','right']:
    el=OxmlElement('w:'+k);el.set(qn('w:w'),'80');el.set(qn('w:type'),'dxa');mar.append(el)
   c._tc.get_or_add_tcPr().append(mar)
   for j,line in enumerate(text.split('\n')):
    p=c.paragraphs[0] if j==0 else c.add_paragraph()
    p.paragraph_format.space_after=Pt(2);p.paragraph_format.space_before=Pt(1);p.paragraph_format.line_spacing=1.0
    run=p.add_run(line);run.font.size=Pt(12);run.bold=(i==0 or c==r.cells[0])
   if i==0:
    sh=OxmlElement('w:shd');sh.set(qn('w:fill'),'E7E6E6');c._tc.get_or_add_tcPr().append(sh)
 return t

para('2.4.2 Use Case tổng quát và đặc tả trọng tâm','Title')
para('a. Mô hình use case tổng quát','Heading 1')
para('Mô hình use case của BrewLearn xác định các mục tiêu tương tác của ba tác nhân: Nhân viên, Trưởng ca và Quản lý cửa hàng. Nhân viên học và làm bài kiểm tra theo chức danh, theo dõi kết quả cá nhân và thực hiện nội dung học lại. Trưởng ca theo dõi nhân viên thuộc phạm vi phụ trách, tạo lịch, đánh giá và đề xuất cải thiện. Quản lý cửa hàng phê duyệt kết quả, tổ chức các vòng đào tạo lại và xem thông tin nguy cơ dự đoán đã lưu.')
para('Các chức năng tài khoản, thông báo, tra cứu và hỗ trợ phục vụ quy trình chính. Người phụ trách đào tạo lại là nhân viên hoặc trưởng ca được phân công, không phải chức danh mới. HR và Quản lý vùng không thuộc phạm vi hệ thống TO-BE. Kết quả dự đoán hỗ trợ theo dõi, không thay thế kết quả đánh giá năng lực đã duyệt.')
p=para('[Chèn sơ đồ use case tổng quát của hệ thống BrewLearn tại đây]');p.alignment=WD_ALIGN_PARAGRAPH.CENTER
para('Sơ đồ tổng quát thể hiện các mục tiêu nghiệp vụ chính. Các sơ đồ chi tiết, danh mục đầy đủ và đặc tả các use case còn lại được trình bày tại phụ lục Use case và đặc tả chi tiết. Mã và tên use case được sử dụng thống nhất giữa chương này và phụ lục.')
para('b. Danh sách use case trọng tâm','Heading 1')
table([['Use case ID','Use case name','Actor(s)']]+[[k,spec[k][0],spec[k][1]['Tác nhân']] for k in chosen],[2.5,8,5.5])
para('UC-TC03 bao gồm nghiệp vụ chấm điểm theo năm tiêu chí UC-TC04. UC-TC05 mở rộng việc đánh giá khi kết quả chưa đạt; trong trường hợp này, đề xuất là bắt buộc trước khi gửi duyệt. Đánh giá lại sử dụng cùng nghiệp vụ đánh giá và phê duyệt, trên hồ sơ liên kết với vòng đào tạo tương ứng. Đăng nhập là tiền điều kiện của các chức năng được bảo vệ.')
para('c. Đặc tả các use case trọng tâm','Heading 1')
para('Các bảng sau tập trung vào học tập, kiểm tra, đánh giá, phê duyệt và đào tạo lại. Yêu cầu phi chức năng là yêu cầu thiết kế cần đáp ứng, không phải kết luận về kết quả kiểm thử hoặc hiệu năng đã đo của nguyên mẫu.')
mapping=[('Actor(s)','Tác nhân'),('Trigger','Kích hoạt'),('Pre-Condition(s)','Tiền điều kiện'),('Post-Condition(s)','Hậu điều kiện'),('Basic Flow','Luồng chính'),('Alternative Flow','Luồng thay thế'),('Exception Flow','Ngoại lệ')]
for k in chosen:
 doc.add_page_break();name,fields=spec[k]
 para(k+' '+name,'Heading 2')
 actor=fields['Tác nhân'].split(';')[0].rstrip('.')
 description=f'Là {actor.lower()}, tôi muốn {name[0].lower()+name[1:]} để {fields["Mục tiêu"][0].lower()+fields["Mục tiêu"][1:]}'
 rows=[['Thuộc tính','Đặc tả'],['Use case name',name],['Use case ID',k],['Description',description]]
 rows += [[en,fields[vn]] for en,vn in mapping]
 rows += [['Business Rule','\n'.join(f'BR-{k[3:]}-{i+1}: {v}' for i,v in enumerate(rules[k]))],['Non-Functional Requirement','\n'.join(f'NFR-{k[3:]}-{i+1}: {v}' for i,v in enumerate(nfr[k]))]]
 table(rows,[3.6,12.4])
doc.core_properties.title='2.4.2 Use Case tổng quát và đặc tả trọng tâm BrewLearn'
out=base/'Chuong-2-Muc-2.4.2-Use-case-trong-tam-da-sua.docx';doc.save(out)
check=Document(out)
assert len(check.tables)==10
expected=['Use case name','Use case ID','Description']+[x[0] for x in mapping]+['Business Rule','Non-Functional Requirement']
for t in check.tables[1:]:assert [r.cells[0].text for r in t.rows[1:]]==expected
print(f'Created {out}; 9 specifications; all 12 required fields verified.')
