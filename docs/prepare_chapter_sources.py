import sys
from pathlib import Path
sys.path.insert(0, str(Path(__file__).parent / 'report-libs'))
from pypdf import PdfReader
from docx import Document

base = Path(__file__).parent
files = ['Nguyễn_Quốc_Hưng_030238220081_KLTN.pdf', '030238220087_NguyenThaiGiaKhai.pdf', 'Lương Thị Tường Vy.pdf']
for i, name in enumerate(files, 1):
    reader = PdfReader(Path('D:/download') / name)
    text = '\n'.join(f'\n=== PDF PAGE {j} ===\n{page.extract_text()}' for j, page in enumerate(reader.pages, 1))
    (base / f'chapter-source-{i}.txt').write_text(text, encoding='utf-8')
    print(f'{i}: {name}: {len(reader.pages)} pages')
doc = Document(base / 'ĐỒ ÁN_MIS.docx')
text = '\n'.join(p.text for p in doc.paragraphs)
(base / 'chapter-project-context.txt').write_text(text, encoding='utf-8')
for p in doc.paragraphs:
    if p.style.name.startswith('Heading') or p.text.startswith('CHƯƠNG'):
        print(p.text)
