from pathlib import Path
import re
from docx import Document
from docx.shared import Cm, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.oxml.ns import qn

root = Path(__file__).parent
text = (root/'Pham-vi-du-lieu-va-gioi-han-BrewLearn.md').read_text(encoding='utf-8')
doc = Document()
s = doc.sections[0]
s.page_width, s.page_height = Cm(21), Cm(29.7)
s.left_margin, s.right_margin = Cm(3), Cm(2)
s.top_margin, s.bottom_margin = Cm(2.5), Cm(2.5)
for name in ['Normal','Title','Heading 1','List Bullet']:
    st = doc.styles[name]
    st.font.name = 'Times New Roman'
    st.font.size = Pt(13)
    st.font.color.rgb = RGBColor(0,0,0)
    st.element.get_or_add_rPr().rFonts.set(qn('w:eastAsia'),'Times New Roman')
    st.paragraph_format.line_spacing = 1.3
    st.paragraph_format.space_after = Pt(6)
doc.styles['Normal'].paragraph_format.first_line_indent = Cm(1)
doc.styles['Title'].font.size = Pt(14)
doc.styles['Title'].font.bold = True
doc.styles['Heading 1'].font.bold = True
for line in text.splitlines():
    line = line.strip()
    if not line: continue
    if line.startswith('# '):
        p=doc.add_paragraph(line[2:],style='Title')
    elif line.startswith('## '):
        p=doc.add_paragraph(line[3:],style='Heading 1')
    else:
        p=doc.add_paragraph(style='List Bullet' if line.startswith('- ') else 'Normal')
        p.alignment=WD_ALIGN_PARAGRAPH.JUSTIFY
        line = line[2:] if line.startswith('- ') else line
        for i,chunk in enumerate(re.split(r'\*\*(.*?)\*\*',line)):
            r=p.add_run(chunk)
            r.bold=bool(i%2)
        if line.startswith('**'):
            p.paragraph_format.first_line_indent=Cm(0)
            p.paragraph_format.keep_with_next=True
out=root/'Pham-vi-du-lieu-va-gioi-han-BrewLearn.docx'
doc.save(out)
check=Document(out)
assert len([p for p in check.paragraphs if p.style.name=='Heading 1'])==4
print(out)
