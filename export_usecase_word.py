from pathlib import Path
import re
from docx import Document
from docx.shared import Cm, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT, WD_CELL_VERTICAL_ALIGNMENT
from docx.oxml import OxmlElement
from docx.oxml.ns import qn

base = Path(__file__).parent
source = base / 'docs/Chuong-2-Muc-2.4.2-Use-case-BrewLearn.md'
output = base / 'Chuong-2-Muc-2.4.2-Use-case-BrewLearn.docx'
doc = Document()
sec = doc.sections[0]
sec.page_width, sec.page_height = Cm(21), Cm(29.7)
sec.left_margin, sec.right_margin = Cm(3), Cm(2)
sec.top_margin, sec.bottom_margin = Cm(2), Cm(2)
for name, size in [('Normal',13),('Title',16),('Heading 1',14),('Heading 2',13),('Heading 3',13)]:
    s=doc.styles[name]
    s.font.name='Times New Roman'; s.font.size=Pt(size); s.font.color.rgb=RGBColor(0,0,0)
    s.element.get_or_add_rPr().rFonts.set(qn('w:eastAsia'),'Times New Roman')
    s.paragraph_format.line_spacing=1.15
    s.paragraph_format.space_after=Pt(6)
    if name!='Normal':
        s.font.bold=True; s.paragraph_format.keep_with_next=True
        s.paragraph_format.space_before=Pt(10)

def runs(p,text):
    for j, part in enumerate(re.split(r'\*\*(.*?)\*\*', text)):
        r=p.add_run(part.replace('`','')); r.bold=bool(j%2)

def cell_text(cell,text,header=False):
    cell.vertical_alignment=WD_CELL_VERTICAL_ALIGNMENT.CENTER
    for i, part in enumerate(text.split('<br>')):
        p=cell.paragraphs[0] if i==0 else cell.add_paragraph()
        p.paragraph_format.space_after=Pt(3); p.paragraph_format.space_before=Pt(2)
        p.paragraph_format.line_spacing=1.05
        runs(p,part)
        for r in p.runs:
            r.font.size=Pt(12)
            if header: r.bold=True

def table(rows):
    t=doc.add_table(rows=0, cols=len(rows[0])); t.alignment=WD_TABLE_ALIGNMENT.CENTER; t.autofit=False
    widths=[3.5,12.5] if len(rows[0])==2 else [2.3,4.4,9.3]
    for col,w in zip(t.columns,widths): col.width=Cm(w)
    pr=t._tbl.tblPr
    borders=OxmlElement('w:tblBorders')
    for side in ['top','left','bottom','right','insideH','insideV']:
        el=OxmlElement('w:'+side); el.set(qn('w:val'),'single'); el.set(qn('w:sz'),'4'); el.set(qn('w:color'),'D9D9D9'); borders.append(el)
    pr.append(borders)
    margins=OxmlElement('w:tblCellMar')
    for side in ['top','left','bottom','right']:
        el=OxmlElement('w:'+side);el.set(qn('w:w'),'90');el.set(qn('w:type'),'dxa');margins.append(el)
    pr.append(margins)
    for i, values in enumerate(rows):
        row=t.add_row()
        rp=row._tr.get_or_add_trPr(); rp.append(OxmlElement('w:cantSplit'))
        if i==0:rp.append(OxmlElement('w:tblHeader'))
        for c,text,w in zip(row.cells,values,widths):
            c.width=Cm(w);cell_text(c,text,i==0)
            if i==0:
                shade=OxmlElement('w:shd');shade.set(qn('w:fill'),'E7E6E6');c._tc.get_or_add_tcPr().append(shade)
    doc.add_paragraph().paragraph_format.space_after=Pt(2)

lines=source.read_text(encoding='utf-8').splitlines(); i=0; spec_count=0
while i<len(lines):
    line=lines[i].strip();i+=1
    if not line:continue
    if line.startswith('|'):
        rows=[]
        while True:
            if not re.match(r'^\|[\s:|\-]+\|$',line):rows.append([c.strip() for c in line.strip('|').split('|')])
            if i>=len(lines) or not lines[i].strip().startswith('|'):break
            line=lines[i].strip();i+=1
        table(rows);continue
    if line.startswith('#### '):
        doc.add_page_break();spec_count+=1
        doc.add_paragraph(line[5:],style='Heading 2');continue
    if line.startswith('### '):
        if line.startswith('### e.'):doc.add_page_break()
        doc.add_paragraph(line[4:],style='Heading 1');continue
    if line.startswith('## '):
        doc.add_paragraph(line[3:],style='Title');continue
    p=doc.add_paragraph()
    p.paragraph_format.widow_control=True
    p.alignment=WD_ALIGN_PARAGRAPH.JUSTIFY
    runs(p,line[2:] if line.startswith('- ') else line)
    if line.startswith('- '):p.paragraph_format.left_indent=Cm(.5)
doc.core_properties.title='2.4.2 Use case và đặc tả use case BrewLearn'
doc.core_properties.subject='Mô hình use case theo ba vai trò và đặc tả nghiệp vụ'
doc.save(output)
check=Document(output)
assert spec_count==18
assert len(check.tables)==23
print(f'Created {output}; {len(check.tables)} tables; {spec_count} specifications')
