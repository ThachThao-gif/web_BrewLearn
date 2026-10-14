from pathlib import Path
import re
from docx import Document
from docx.shared import Cm, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH, WD_COLOR_INDEX
from docx.oxml import OxmlElement
from docx.oxml.ns import qn

root=Path(__file__).parent
source=root/'Chuong-2-Muc-2.1-BrewLearn.md'
doc=Document()
s=doc.sections[0]
s.page_width,s.page_height=Cm(21),Cm(29.7)
s.left_margin,s.right_margin=Cm(3),Cm(2)
s.top_margin,s.bottom_margin=Cm(2.5),Cm(2.5)
for name,size in [('Normal',13),('Title',16),('Heading 1',14),('Heading 2',13)]:
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
doc.styles['Title'].paragraph_format.alignment=WD_ALIGN_PARAGRAPH.CENTER
lines=source.read_text(encoding='utf-8').splitlines()
i=0
while i<len(lines):
    line=lines[i].strip()
    i+=1
    if not line: continue
    if line.startswith('|'):
        rows=[]
        while True:
            row=[x.strip() for x in line.strip('|').split('|')]
            if not all(re.fullmatch(r'[-: ]+',x) for x in row): rows.append(row)
            if i>=len(lines) or not lines[i].strip().startswith('|'): break
            line=lines[i].strip()
            i+=1
        table=doc.add_table(rows=0,cols=3)
        table.style='Table Grid'
        table.autofit=False
        for n,row in enumerate(rows):
            cells=table.add_row().cells
            for cell,value,width in zip(cells,row,[4.8,5.1,6.1]):
                cell.width=Cm(width)
                p=cell.paragraphs[0]
                p.paragraph_format.first_line_indent=Cm(0)
                p.paragraph_format.line_spacing=1.1
                p.paragraph_format.space_after=Pt(4)
                r=p.add_run(value)
                r.font.size=Pt(11)
                r.bold=n==0
            trpr=table.rows[n]._tr.get_or_add_trPr()
            trpr.append(OxmlElement('w:cantSplit'))
            if n==0: trpr.append(OxmlElement('w:tblHeader'))
        continue
    m=re.match(r'^(#{1,3}) (.*)$',line)
    if m:
        doc.add_paragraph(m[2],style={1:'Title',2:'Heading 1',3:'Heading 2'}[len(m[1])])
        continue
    p=doc.add_paragraph()
    p.alignment=WD_ALIGN_PARAGRAPH.JUSTIFY
    for n,chunk in enumerate(re.split(r'\*\*(.*?)\*\*',line)):
        r=p.add_run(chunk)
        if n%2:r.bold=True
        if line.startswith('[CHÈN'):r.font.highlight_color=WD_COLOR_INDEX.YELLOW
    if line.startswith(('Hình 2.1','Bảng 2.1','[CHÈN')):
        p.alignment=WD_ALIGN_PARAGRAPH.CENTER
        p.paragraph_format.first_line_indent=Cm(0)
        if line.startswith('Bảng'):p.paragraph_format.keep_with_next=True
out=root/'Chuong-2-Muc-2.1-BrewLearn.docx'
doc.save(out)
check=Document(out)
assert len(check.tables)==1 and len(check.tables[0].rows)==7
assert len([p for p in check.paragraphs if p.style.name=='Heading 2'])==3
print(f'Created {out}; {len(check.paragraphs)} paragraphs; 1 table; 1 image placeholder')
