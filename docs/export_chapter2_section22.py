from pathlib import Path
import sys
import re
from docx import Document
from docx.shared import Cm, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH, WD_COLOR_INDEX
from docx.oxml.ns import qn

root=Path(__file__).parent
section=sys.argv[1] if len(sys.argv)>1 else '2.2'
source=root/f'Chuong-2-Muc-{section}-BrewLearn.md'
doc=Document()
sec=doc.sections[0]
sec.page_width,sec.page_height=Cm(21),Cm(29.7)
sec.left_margin,sec.right_margin=Cm(3),Cm(2)
sec.top_margin,sec.bottom_margin=Cm(2.5),Cm(2.5)
for name,size in [('Normal',13),('Heading 1',14),('Heading 2',13)]:
    st=doc.styles[name]
    st.font.name='Times New Roman'
    st.font.size=Pt(size)
    st.font.color.rgb=RGBColor(0,0,0)
    st.element.get_or_add_rPr().rFonts.set(qn('w:eastAsia'),'Times New Roman')
    st.paragraph_format.line_spacing=1.3
    st.paragraph_format.space_after=Pt(6)
    if name!='Normal':
        st.font.bold=True
        st.paragraph_format.keep_with_next=True
        st.paragraph_format.space_before=Pt(10)
doc.styles['Normal'].paragraph_format.first_line_indent=Cm(1)
doc.styles['Normal'].paragraph_format.widow_control=True
for line in source.read_text(encoding='utf-8').splitlines():
    line=line.strip()
    if not line:continue
    if line.startswith('### '):doc.add_paragraph(line[4:],style='Heading 2');continue
    if line.startswith('## '):doc.add_paragraph(line[3:],style='Heading 1');continue
    p=doc.add_paragraph()
    p.alignment=WD_ALIGN_PARAGRAPH.JUSTIFY
    for i,text in enumerate(re.split(r'\*\*(.*?)\*\*',line)):
        r=p.add_run(text)
        if i%2:r.bold=True
        if line.startswith('[CHÈN'):r.font.highlight_color=WD_COLOR_INDEX.YELLOW
    if line.startswith(('[CHÈN','Hình ')):
        p.paragraph_format.first_line_indent=Cm(0)
        p.alignment=WD_ALIGN_PARAGRAPH.CENTER
        if line.startswith('[CHÈN'):p.paragraph_format.keep_with_next=True
doc.core_properties.title=f'Mục {section} BrewLearn'
out=root/f'Chuong-2-Muc-{section}-BrewLearn.docx'
doc.save(out)
check=Document(out)
assert len([p for p in check.paragraphs if p.style.name=='Heading 2'])==4
expected_images=3 if section=='2.2' else 0
assert len([p for p in check.paragraphs if p.text.startswith('[CHÈN')])==expected_images
assert len([p for p in check.paragraphs if p.text.startswith('Hình ')])==expected_images
print(f'Created {out}; 4 subsections; {len(check.paragraphs)} paragraphs')
