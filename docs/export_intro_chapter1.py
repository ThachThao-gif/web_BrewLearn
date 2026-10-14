from pathlib import Path
import re
from docx import Document
from docx.shared import Cm, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.oxml import OxmlElement
from docx.oxml.ns import qn

root = Path(__file__).parent
source = root / 'Mo-dau-va-Chuong-1-BrewLearn-da-duyet.md'
output = root / 'Mo-dau-va-Chuong-1-BrewLearn-da-duyet.docx'
doc = Document()
sec = doc.sections[0]
sec.page_width, sec.page_height = Cm(21), Cm(29.7)
sec.top_margin, sec.bottom_margin = Cm(2.5), Cm(2.5)
sec.left_margin, sec.right_margin = Cm(3), Cm(2)
for name in ['Normal', 'Title', 'Heading 1', 'Heading 2']:
    st = doc.styles[name]
    st.font.name = 'Times New Roman'
    st.font.color.rgb = RGBColor(0, 0, 0)
    st.element.rPr.rFonts.set(qn('w:eastAsia'), 'Times New Roman')
normal = doc.styles['Normal']
normal.font.size = Pt(13)
normal.paragraph_format.line_spacing = 1.3
normal.paragraph_format.space_after = Pt(6)
normal.paragraph_format.first_line_indent = Cm(1)
normal.paragraph_format.widow_control = True
for name, size in [('Title',16), ('Heading 1',14), ('Heading 2',13)]:
    st = doc.styles[name]
    st.font.size, st.font.bold = Pt(size), True
    st.paragraph_format.first_line_indent = Cm(0)
    st.paragraph_format.space_before = Pt(12)
    st.paragraph_format.space_after = Pt(6)
    st.paragraph_format.keep_with_next = True
doc.styles['Title'].paragraph_format.alignment = WD_ALIGN_PARAGRAPH.CENTER

def runs(p, text):
    for i, piece in enumerate(re.split(r'\*\*(.*?)\*\*', text)):
        r = p.add_run(piece)
        if i % 2: r.bold = True

lines = source.read_text(encoding='utf-8').splitlines()
i = 0
while i < len(lines):
    line = lines[i].strip()
    if not line:
        i += 1
        continue
    if line.startswith('|'):
        rows = []
        while i < len(lines) and lines[i].strip().startswith('|'):
            row = [x.strip() for x in lines[i].strip().strip('|').split('|')]
            if not all(re.fullmatch(r'[-: ]+', x) for x in row): rows.append(row)
            i += 1
        caption = doc.add_paragraph('Bảng 1.1 Kế hoạch thực hiện đồ án trong 12 tuần')
        caption.paragraph_format.first_line_indent = Cm(0)
        caption.paragraph_format.keep_with_next = True
        caption.alignment = WD_ALIGN_PARAGRAPH.CENTER
        table = doc.add_table(rows=1, cols=3)
        table.style = 'Table Grid'
        table.autofit = False
        for c, width in zip(table.columns, [2.4, 8.1, 5.5]): c.width = Cm(width)
        for n, values in enumerate(rows):
            cells = table.rows[0].cells if n == 0 else table.add_row().cells
            for cell, value, width in zip(cells, values, [2.4,8.1,5.5]):
                cell.width = Cm(width)
                p = cell.paragraphs[0]
                p.paragraph_format.first_line_indent = Cm(0)
                p.paragraph_format.line_spacing = 1.15
                p.paragraph_format.space_after = Pt(4)
                r = p.add_run(value)
                r.font.size = Pt(11)
                r.bold = n == 0
            trpr = table.rows[n]._tr.get_or_add_trPr()
            trpr.append(OxmlElement('w:cantSplit'))
            if n == 0: trpr.append(OxmlElement('w:tblHeader'))
        continue
    match = re.match(r'^(#{1,3}) (.*)$', line)
    if match:
        level, title = len(match[1]), match[2]
        p = doc.add_paragraph(style={1:'Title',2:'Heading 1',3:'Heading 2'}[level])
        if level == 1 and len(doc.paragraphs) > 1: p.paragraph_format.page_break_before = True
        runs(p, title)
    else:
        p = doc.add_paragraph()
        p.alignment = WD_ALIGN_PARAGRAPH.JUSTIFY
        if line.startswith('['):
            p.paragraph_format.first_line_indent = Cm(0)
            p.paragraph_format.line_spacing = 1.15
        runs(p, line)
    i += 1
footer = sec.footer.paragraphs[0]
footer.alignment = WD_ALIGN_PARAGRAPH.CENTER
footer.paragraph_format.first_line_indent = Cm(0)
field = OxmlElement('w:fldSimple')
field.set(qn('w:instr'), 'PAGE')
footer._p.append(field)
doc.core_properties.title = 'Mở đầu và Chương 1 đồ án BrewLearn'
doc.core_properties.subject = 'Phạm vi phân tích thiết kế đã được duyệt trong 12 tuần'
doc.save(output)
check = Document(output)
assert len(check.tables) == 1
assert len([p for p in check.paragraphs if p.style.name == 'Heading 2']) == 10
assert 'quản lý sinh viên' not in source.read_text(encoding='utf-8').lower()
print(f'Created {output}; paragraphs={len(check.paragraphs)}, tables={len(check.tables)}')
