import sys
import re
from pathlib import Path
sys.path.insert(0, str(Path(__file__).parent / 'report-libs'))
from docx import Document
from docx.shared import Cm, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.oxml import OxmlElement
from docx.oxml.ns import qn

base = Path(__file__).parent
source = (base / 'Chuong-4-Giao-dien-Trien-khai-Kiem-thu-BrewLearn.md').read_text(encoding='utf-8')
doc = Document()
sec = doc.sections[0]
sec.page_width, sec.page_height = Cm(21), Cm(29.7)
sec.top_margin, sec.bottom_margin = Cm(2.5), Cm(2.5)
sec.left_margin, sec.right_margin = Cm(3), Cm(2)
for name in ['Normal', 'Title', 'Heading 1', 'Heading 2', 'Heading 3', 'Caption']:
    style = doc.styles[name]
    style.font.name = 'Times New Roman'
    style.font.size = Pt(13)
    style.font.color.rgb = RGBColor(0, 0, 0)
    style.element.get_or_add_rPr().rFonts.set(qn('w:eastAsia'), 'Times New Roman')
doc.styles['Title'].font.size = Pt(16)
doc.styles['Title'].font.bold = True
for name in ['Heading 1', 'Heading 2', 'Heading 3']:
    doc.styles[name].font.bold = True
    doc.styles[name].paragraph_format.keep_with_next = True
    doc.styles[name].paragraph_format.space_before = Pt(12)
    doc.styles[name].paragraph_format.space_after = Pt(6)
normal = doc.styles['Normal'].paragraph_format
normal.line_spacing = 1.5
normal.space_after = Pt(6)
normal.first_line_indent = Cm(1)
normal.widow_control = True

figures = {
    '4.1.3.1.': ['Giao diện đăng nhập hệ thống BrewLearn'],
    '4.1.4.1.': ['Giao diện tổng quan học tập của nhân viên', 'Giao diện danh sách khóa học của nhân viên'],
    '4.1.4.2.': ['Giao diện chi tiết khóa học', 'Giao diện bài học và ghi nhận tiến độ'],
    '4.1.4.3.': ['Giao diện danh sách bài kiểm tra', 'Giao diện làm bài kiểm tra', 'Giao diện kết quả bài kiểm tra'],
    '4.1.4.4.': ['Giao diện hồ sơ và cài đặt tài khoản nhân viên', 'Giao diện thông báo của nhân viên', 'Giao diện gửi yêu cầu hỗ trợ'],
    '4.1.5.1.': ['Giao diện tổng quan của trưởng ca', 'Giao diện danh sách nhân viên do trưởng ca phụ trách'],
    '4.1.5.2.': ['Giao diện tạo lịch đánh giá năng lực', 'Giao diện chấm điểm và đề xuất cải thiện'],
    '4.1.5.3.': ['Giao diện đào tạo lại tôi phụ trách của trưởng ca được phân công'],
    '4.1.6.1.': ['Giao diện tổng quan quản lý cửa hàng'],
    '4.1.6.2.': ['Giao diện quản lý danh sách nhân viên', 'Giao diện hồ sơ chi tiết nhân viên'],
    '4.1.6.3.': ['Giao diện phê duyệt hoặc trả lại đánh giá'],
    '4.1.6.4.': ['Giao diện giao đào tạo lại của quản lý cửa hàng', 'Giao diện lịch sử các vòng đào tạo lại'],
    '4.1.6.5.': ['Giao diện hiển thị mức nguy cơ và trạng thái chưa đủ dữ liệu'],
    '4.3.3.': ['Minh chứng kết quả chạy bộ kiểm thử tự động'],
}
figure_number = 0
current_section = None

def text_runs(p, text):
    for i, piece in enumerate(text.split('**')):
        run = p.add_run(piece.replace('`', ''))
        run.bold = bool(i % 2)

def insert_figures():
    global figure_number
    for caption in figures.get(current_section, []):
        figure_number += 1
        table = doc.add_table(rows=1, cols=1)
        table.style = 'Table Grid'
        cell = table.cell(0, 0)
        shade = OxmlElement('w:shd')
        shade.set(qn('w:fill'), 'FFF2CC')
        cell._tc.get_or_add_tcPr().append(shade)
        p = cell.paragraphs[0]
        p.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p.paragraph_format.first_line_indent = Cm(0)
        p.paragraph_format.space_before = Pt(20)
        p.paragraph_format.space_after = Pt(20)
        run = p.add_run(f'[CHÈN HÌNH 4.{figure_number} TẠI ĐÂY]\n{caption}')
        run.bold = True
        run.font.size = Pt(12)
        p = doc.add_paragraph(f'Hình 4.{figure_number}. {caption}.', 'Caption')
        p.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p.paragraph_format.first_line_indent = Cm(0)
        p.paragraph_format.keep_with_next = False
        p = doc.add_paragraph('Nguồn: Ảnh chụp hệ thống BrewLearn do tác giả bổ sung.')
        p.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p.paragraph_format.first_line_indent = Cm(0)
        p.runs[0].italic = True
        p.runs[0].font.size = Pt(11)

lines = source.splitlines()
i = 0
while i < len(lines):
    line = lines[i].strip()
    if not line:
        i += 1
        continue
    if line.startswith('#'):
        insert_figures()
        current_section = None
        level = len(line) - len(line.lstrip('#'))
        title = line[level:].strip()
        if level in (3, 4):
            current_section = title.split(' ', 1)[0]
        p = doc.add_paragraph(title, 'Title' if level == 1 else f'Heading {level - 1}')
        p.paragraph_format.first_line_indent = Cm(0)
        if level == 1:
            p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    elif line.startswith('|'):
        rows = []
        while i < len(lines) and lines[i].strip().startswith('|'):
            cells = [s.strip() for s in lines[i].strip().strip('|').split('|')]
            if not all(re.fullmatch(r':?-+:?', s) for s in cells):
                rows.append(cells)
            i += 1
        table = doc.add_table(rows=0, cols=len(rows[0]))
        table.style = 'Table Grid'
        for index, row in enumerate(rows):
            cells = table.add_row().cells
            for cell, value in zip(cells, row):
                p = cell.paragraphs[0]
                p.paragraph_format.first_line_indent = Cm(0)
                p.paragraph_format.line_spacing = 1.15
                p.paragraph_format.space_after = Pt(3)
                text_runs(p, value)
                for run in p.runs:
                    run.font.size = Pt(10)
                    if index == 0:
                        run.bold = True
            trPr = cells[0]._tc.getparent().get_or_add_trPr()
            avoid_split = OxmlElement('w:cantSplit')
            trPr.append(avoid_split)
            if index == 0:
                repeat = OxmlElement('w:tblHeader')
                trPr.append(repeat)
        doc.add_paragraph().paragraph_format.space_after = Pt(0)
        continue
    elif line.startswith('```'):
        i += 1
        code = []
        while i < len(lines) and not lines[i].startswith('```'):
            code.append(lines[i])
            i += 1
        p = doc.add_paragraph('\n'.join(code))
        p.paragraph_format.first_line_indent = Cm(0)
        p.paragraph_format.line_spacing = 1
        p.runs[0].font.size = Pt(10)
    else:
        p = doc.add_paragraph()
        p.alignment = WD_ALIGN_PARAGRAPH.JUSTIFY
        text_runs(p, line)
        if line.startswith('**Bảng'):
            p.alignment = WD_ALIGN_PARAGRAPH.CENTER
            p.paragraph_format.first_line_indent = Cm(0)
            p.paragraph_format.keep_with_next = True
        elif line.startswith('Nguồn:') or line.startswith('['):
            p.paragraph_format.first_line_indent = Cm(0)
    i += 1
insert_figures()

footer = sec.footer.paragraphs[0]
footer.alignment = WD_ALIGN_PARAGRAPH.CENTER
footer.paragraph_format.first_line_indent = Cm(0)
field = OxmlElement('w:fldSimple')
field.set(qn('w:instr'), 'PAGE')
footer._p.append(field)
doc.core_properties.title = 'Chương 4. Thiết kế giao diện, triển khai và kiểm thử hệ thống BrewLearn'
doc.core_properties.subject = 'Chương báo cáo nguyên mẫu, có đánh dấu vị trí chèn hình'
output = base / 'Chuong-4-BrewLearn-Phan-chia-giao-dien-theo-vai-tro.docx'
doc.save(output)
check = Document(output)
placeholders = [cell.text for table in check.tables for row in table.rows for cell in row.cells if '[CHÈN HÌNH' in cell.text]
assert len(placeholders) == 24, len(placeholders)
for title in ['4.1.4. Giao diện dành cho nhân viên', '4.1.5. Giao diện dành cho trưởng ca', '4.1.6. Giao diện dành cho quản lý cửa hàng']:
    assert any(p.text == title and p.style.name == 'Heading 2' for p in check.paragraphs), title
assert any('59/60' in p.text for p in check.paragraphs)
print(f'Created: {output}\nFigures: {len(placeholders)}\nTables: {len(check.tables) - len(placeholders)}\nSize: {output.stat().st_size} bytes')
