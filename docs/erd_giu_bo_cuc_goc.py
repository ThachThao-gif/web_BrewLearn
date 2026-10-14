from pathlib import Path
import xml.etree.ElementTree as E
import json
B=Path(__file__).parent
tree=E.parse(B/'BrewLearn-ERD-logic-nghiep-vu-day-du.xml')
cells={c.get('id'):c for c in tree.iter('mxCell')}
meta=json.loads((B/'erd-schema.json').read_text('utf-8'))
labels={};titles={}
for t,m in meta.items():
    titles[t]=cells[t+'-head'].get('value')
    for col in m['columns']:
        key=t+'-'+col['name']
        if key in cells:labels[(t,col['name'])]=cells[key].get('value')
source=(B/'render_erd_connected.py').read_text('utf-8')
needle="data=json.loads((B/'erd-schema.json').read_text('utf-8'))"
source=source.replace(needle,needle+"\nfor t,m in data.items():m['columns']=[c for c in m['columns'] if (t,c['name']) in labels]\n")
# Some columns are intentionally omitted from this business ERD.
source=source.replace("for k,v in logical.items():links.setdefault(k,(*v,'RL'))","for k,v in logical.items():\n    if k in labels:links.setdefault(k,(*v,'RL'))")
source=source.replace("for f in m['foreign_keys']:links[(t,f['col'])]=(f['target'],f['target_col'],'FK')","for f in m['foreign_keys']:\n        if (t,f['col']) in labels:links[(t,f['col'])]=(f['target'],f['target_col'],'FK')")
source=source.replace("node(t+'-head',t,", "node(t+'-head',titles[t],")
source=source.replace("tw=draw.textlength(t,font=f(23,True));txt(x+(w-tw)/2,y+10,t,23,True)","tw=draw.textlength(titles[t],font=f(23,True));txt(x+(w-tw)/2,y+10,titles[t],23,True)")
start=source.index("        display=f'{flag+")
end=source.index('\n        ry=',start)
source=source[:start]+"        display=labels[(t,name)]\n        font_size=16\n        while draw.textlength(display,font=f(font_size,bool(flags)))>w-24:font_size-=1"+source[end:]
source=source.replace("'text;align=left;spacingLeft=12;fontSize=18;'","f'text;align=left;spacingLeft=12;fontSize={font_size};'")
source=source.replace("txt(x+12,y+ry+5,display,18,bool(flags)","txt(x+12,y+ry+5,display,font_size,bool(flags)")
source=source.replace("draw.textlength(display,font=f(18,bool(flags)))","draw.textlength(display,font=f(font_size,bool(flags)))")
source=source.replace('18 bảng xuất hiện một lần · Liên kết trực tiếp · Crow’s Foot · PK / FK / UQ · RL: liên kết theo code · ?: nullable','PK: khóa chính · FK: tham chiếu ở mức logic · NULL: cho phép rỗng · NOT NULL: bắt buộc')
start=source.index('notes=[');end=source.index('\nfor i,s in enumerate(notes)',start)
source=source[:start]+'''notes=['Một trang, 18 thực thể; giữ bố cục tổng thể ban đầu. Nhân viên, trưởng ca và quản lý lưu chung trong users.',
'Ngày vào làm được giữ trong thực thể Nhân viên / tài khoản; cột thực tế là joined_at.',
'FK thể hiện tham chiếu ở mức logic; một số quan hệ hiện được ứng dụng sử dụng nhưng chưa có ràng buộc trong MySQL.',
'Cặp request_id + round_number là duy nhất. Đường giao nhau có cầu vượt, không phải điểm nối.']'''+source[end:]
source=source.replace('BrewLearn-ERD-lien-ket-tong-the','BrewLearn-ERD-bo-cuc-goc-da-sua')
source=source.replace('#fff0d3','#3787c9').replace('#fffcf3','#f8fafc').replace('#c79a42','#98afc0').replace('#a23f24','#1d3344')
source=source.replace('fontSize=23;fontStyle=1;align=center;','fontSize=23;fontStyle=1;fontColor=#ffffff;align=center;')
source=source.replace('y+10,titles[t],23,True)','y+10,titles[t],23,True,"#ffffff")')
exec(compile(source,'original-layout-renderer','exec'),{'__file__':str(B/'render_erd_connected.py'),'labels':labels,'titles':titles})
out=B/'BrewLearn-ERD-bo-cuc-goc-da-sua.drawio'
(B/'BrewLearn-ERD-bo-cuc-goc-da-sua.xml').write_bytes(out.read_bytes())
print('Generated original-layout XML, drawio and PNG.')
