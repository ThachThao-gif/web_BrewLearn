from pathlib import Path
import re
from docx import Document
from docx.shared import Cm, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.oxml import OxmlElement
from docx.oxml.ns import qn

base = Path(__file__).parent
source = (base / 'Thiet-ke-CSDL-hien-tai.md').read_text(encoding='utf-8')
doc = Document()
sec = doc.sections[0]
sec.page_width, sec.page_height = Cm(21), Cm(29.7)
sec.top_margin, sec.bottom_margin = Cm(2), Cm(2)
sec.left_margin, sec.right_margin = Cm(3), Cm(2)
for name in ['Normal', 'Title', 'Heading 1', 'Heading 2', 'Heading 3']:
    st = doc.styles[name]
    st.font.name = 'Times New Roman'
    st.font.color.rgb = RGBColor(0,0,0)
    st.font.size = Pt(13)
    st.element.rPr.rFonts.set(qn('w:eastAsia'), 'Times New Roman')
doc.styles['Title'].font.size = Pt(16)
doc.styles['Title'].font.bold = True
normal = doc.styles['Normal'].paragraph_format
normal.line_spacing = 1.3
normal.space_after = Pt(6)
normal.widow_control = True
for name in ['Heading 1','Heading 2','Heading 3']:
    fmt=doc.styles[name].paragraph_format
    fmt.space_before, fmt.space_after = Pt(10), Pt(5)
    fmt.keep_with_next = True
    doc.styles[name].font.bold = True

def plain(s):
    return re.sub(r'\[([^\]]+)\]\([^)]*\)', r'\1', s).replace('`','').replace('**','')

def para(s, keep=False):
    p=doc.add_paragraph()
    p.alignment=WD_ALIGN_PARAGRAPH.JUSTIFY
    p.paragraph_format.keep_together=keep
    # Primary key is bold, all physical field names remain unchanged.
    for i,part in enumerate(s.split('**')):
        r=p.add_run(plain(part));r.bold=(i%2==1)
    return p

doc.add_paragraph('THIẾT KẾ CƠ SỞ DỮ LIỆU BREWLEARN', 'Title')
para('Cơ sở dữ liệu BrewLearn lưu trữ thông tin nhân sự, cửa hàng, học tập, kiểm tra, đánh giá năng lực, dự đoán nguy cơ và đào tạo lại. Các chức năng nghiệp vụ phục vụ nhân viên, trưởng ca và quản lý cửa hàng. Tên thực thể và thuộc tính được trình bày theo cấu trúc triển khai của hệ thống.')
doc.add_heading('1 Xác định thực thể và thuộc tính', level=1)
para('Ký hiệu PK xác định khóa chính. Các cột liên kết giữ nguyên tên trong cơ sở dữ liệu. created_at và updated_at lần lượt lưu thời điểm tạo và cập nhật bản ghi tại những bảng có hai thuộc tính này.')
block=source.split('### 1.1.',1)[1].split('## 2.',1)[0]
block='### 1.1.'+block
for chunk in re.split(r'\n\s*\n',block.strip()):
    s=chunk.strip()
    if s.startswith('### '):
        doc.add_heading(plain(s[4:]).replace('. ', ' ',1),level=2)
    elif s.startswith('**'):
        p=doc.add_paragraph(plain(s));p.runs[0].bold=True
        p.paragraph_format.keep_with_next=True
        p.paragraph_format.space_before=Pt(7)
    elif re.match(r'^\w+ \(',s):
        para(s,keep=True)
    else:
        # Rewrite implementation commentary as report prose.
        if s.startswith('Bảng này vẫn được'):
            s='Bảng vung là danh mục vùng đang được cửa hàng tham chiếu qua cua_hang.vung_id. Thuộc tính nguoi_quan_ly_id còn tồn tại trong cấu trúc triển khai; chức năng quản lý vùng không thuộc phạm vi nghiệp vụ của báo cáo.'
        s=s.replace('trong database đang chạy','trong cơ sở dữ liệu hiện tại').replace('của bản cũ','').replace('Database hiện tại','Cơ sở dữ liệu hiện tại')
        para(s)

doc.add_heading('2 Quan hệ giữa các thực thể',level=1)
para('Các quan hệ sau thể hiện liên kết dữ liệu được cấu trúc bảng hoặc mã nguồn sử dụng. Một số quan hệ chưa được khai báo ràng buộc FOREIGN KEY ở mức cơ sở dữ liệu.')
table_src=source.split('| Quan hệ | Cột liên kết |',1)[1].split('## 3.',1)[0]
rows=[]
for line in table_src.splitlines():
    if line.startswith('|') and not line.startswith('|---'):
        rows.append([plain(x.strip()) for x in line.strip('|').split('|')])
t=doc.add_table(rows=1,cols=2)
t.autofit=False
t.columns[0].width=Cm(6.2);t.columns[1].width=Cm(9.8)
for c,txt in zip(t.rows[0].cells,['Quan hệ','Cột liên kết']):
    c.text=txt
    for r in c.paragraphs[0].runs:r.bold=True
    shade=OxmlElement('w:shd');shade.set(qn('w:fill'),'EDEDED');c._tc.get_or_add_tcPr().append(shade)
repeat=OxmlElement('w:tblHeader');t.rows[0]._tr.get_or_add_trPr().append(repeat)
for row in rows:
    cells=t.add_row().cells
    for c,txt in zip(cells,row):c.text=txt
for row in t.rows:
    cant=OxmlElement('w:cantSplit');row._tr.get_or_add_trPr().append(cant)
    for c in row.cells:
        props=c._tc.get_or_add_tcPr()
        borders=OxmlElement('w:tcBorders')
        for side in ['top','left','bottom','right']:
            e=OxmlElement('w:'+side);e.set(qn('w:val'),'single');e.set(qn('w:sz'),'4');e.set(qn('w:color'),'D9D9D9');borders.append(e)
        props.append(borders)
        for p in c.paragraphs:
            p.paragraph_format.space_after=Pt(4)
            p.paragraph_format.space_before=Pt(4)
            p.paragraph_format.line_spacing=1.15
            for r in p.runs:r.font.size=Pt(12)
doc.add_heading('3 Dữ liệu tổng hợp trên dashboard',level=1)
para('Biểu đồ đường trên dashboard quản lý cửa hàng được tổng hợp từ danh_gia_nang_luc, không tạo bảng dữ liệu riêng. Hệ thống lấy kết quả đánh giá đã duyệt cuối cùng của từng nhân viên trong mỗi tháng, thống kê tỷ lệ đạt và chưa đạt trong sáu tháng gần nhất theo phạm vi bộ lọc nhân viên đang làm. Biểu đồ nguy cơ sử dụng dự đoán mới nhất đã lưu trong du_doan_nguy_co. Hai nguồn dữ liệu được trình bày độc lập.')
doc.add_heading('4 Các bảng phục vụ hạ tầng',level=1)
para('Ngoài các thực thể nghiệp vụ, hệ thống có các bảng migrations, sessions, password_reset_tokens, cache, cache_locks, jobs, job_batches và failed_jobs để phục vụ vận hành Laravel. Các bảng này được tách khỏi mô hình thực thể nghiệp vụ.')
doc.core_properties.title='Thiết kế cơ sở dữ liệu BrewLearn'
doc.core_properties.author=''
doc.core_properties.subject='Thực thể thuộc tính và quan hệ dữ liệu'
target=base/'Thiet-ke-CSDL-BrewLearn.docx'
doc.save(target)
print(target)
print('Tables:',len(doc.tables),'Paragraphs:',len(doc.paragraphs))
