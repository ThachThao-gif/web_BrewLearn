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
lines = (base / 'De-cuong-do-an-tot-nghiep-BrewLearn.md').read_text(encoding='utf-8').splitlines()
doc = Document()
sec = doc.sections[0]
sec.page_width, sec.page_height = Cm(21), Cm(29.7)
sec.top_margin, sec.bottom_margin = Cm(2.5), Cm(2.5)
sec.left_margin, sec.right_margin = Cm(3), Cm(2)
for name in ['Normal', 'Title', 'Heading 1', 'Heading 2']:
    st = doc.styles[name]
    st.font.name = 'Times New Roman'
    st.font.size = Pt(13)
    st.font.color.rgb = RGBColor(0, 0, 0)
    st.element.get_or_add_rPr().rFonts.set(qn('w:eastAsia'), 'Times New Roman')
    if name != 'Normal':
        st.font.bold = True
        st.paragraph_format.keep_with_next = True
        st.paragraph_format.space_before = Pt(12)
doc.styles['Title'].font.size = Pt(16)
normal = doc.styles['Normal'].paragraph_format
normal.line_spacing = 1.5
normal.space_after = Pt(6)
normal.first_line_indent = Cm(1)
normal.widow_control = True

def add_runs(p, text):
    for index, part in enumerate(text.split('**')):
        r = p.add_run(part)
        r.bold = bool(index % 2)

i = 0
while i < len(lines):
    line = lines[i].strip()
    if not line:
        i += 1
        continue
    if line.startswith('#'):
        level = len(line) - len(line.lstrip('#'))
        title = line[level:].strip()
        p = doc.add_paragraph(title, 'Title' if level == 1 else f'Heading {level - 1}')
        p.paragraph_format.first_line_indent = Cm(0)
        if level == 1:
            p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    elif line.startswith('|'):
        rows = []
        while i < len(lines) and lines[i].strip().startswith('|'):
            cells = [v.strip() for v in lines[i].strip().strip('|').split('|')]
            if not all(re.fullmatch(r':?-+:?', v) for v in cells):
                rows.append(cells)
            i += 1
        table = doc.add_table(rows=0, cols=len(rows[0]))
        table.style = 'Table Grid'
        for ri, values in enumerate(rows):
            cells = table.add_row().cells
            for cell, value in zip(cells, values):
                p = cell.paragraphs[0]
                p.paragraph_format.first_line_indent = Cm(0)
                p.paragraph_format.line_spacing = 1.15
                add_runs(p, value)
                for r in p.runs:
                    r.font.size = Pt(11)
                    r.bold = ri == 0
            trpr = cells[0]._tc.getparent().get_or_add_trPr()
            trpr.append(OxmlElement('w:cantSplit'))
            if ri == 0:
                trpr.append(OxmlElement('w:tblHeader'))
        doc.add_paragraph()
        continue
    elif line.startswith('[CHÈN'):
        p = doc.add_paragraph(line)
        p.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p.paragraph_format.first_line_indent = Cm(0)
        p.paragraph_format.space_before = Pt(12)
        p.paragraph_format.space_after = Pt(12)
        p.runs[0].bold = True
        shade = OxmlElement('w:shd')
        shade.set(qn('w:fill'), 'FFF2CC')
        p._p.get_or_add_pPr().append(shade)
    else:
        p = doc.add_paragraph()
        p.alignment = WD_ALIGN_PARAGRAPH.JUSTIFY
        if line.startswith('- '):
            line = '• ' + line[2:]
            p.paragraph_format.first_line_indent = Cm(0)
            p.paragraph_format.left_indent = Cm(0.5)
        add_runs(p, line)
        if line.startswith('**'):
            p.paragraph_format.first_line_indent = Cm(0)
            p.paragraph_format.keep_with_next = True
    i += 1
footer = sec.footer.paragraphs[0]
footer.alignment = WD_ALIGN_PARAGRAPH.CENTER
footer.paragraph_format.first_line_indent = Cm(0)
field = OxmlElement('w:fldSimple')
field.set(qn('w:instr'), 'PAGE')
footer._p.append(field)
doc.core_properties.title = 'Đề cương đồ án tốt nghiệp BrewLearn'
doc.core_properties.subject = 'Đề cương năm chương; quy trình, phân tích, thiết kế, mô hình, nguyên mẫu và kiểm thử'
out = base / 'De-cuong-do-an-tot-nghiep-BrewLearn-cap-nhat.docx'
doc.save(out)
check = Document(out)
text = '\n'.join(p.text for p in check.paragraphs)
assert sum(p.text.startswith('CHƯƠNG ') for p in check.paragraphs) == 5
for term in ['AS-IS', 'TO-BE', 'BFD', 'use case', 'DFD ngữ cảnh', 'DFD mức 0', 'ERD', 'pool', 'lane', 'huấn luyện', 'kiến trúc tổng thể']:
    assert term in text, term
assert len(check.tables) == 3
for phrase in ['có bốn làn: HR', 'Phê duyệt hoặc trả lại áp dụng cho mọi kết quả đánh giá', 'nhãn competency_status phải lấy từ kết quả đánh giá đã được quản lý', 'giả định ngưỡng kiểm tra đồng nhất 80', '60 ca, 59 đạt, 1 không đạt']:
    assert phrase in text, phrase
print(f'Created: {out}\n5 chapters; 3 tables; {sum(p.text.startswith("[CHÈN") for p in check.paragraphs)} figure markers; {out.stat().st_size} bytes')
