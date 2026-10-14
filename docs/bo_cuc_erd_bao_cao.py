from pathlib import Path
import xml.etree.ElementTree as E
import json,html
B=Path(__file__).parent
src=E.parse(B/'BrewLearn-ERD-logic-nghiep-vu-day-du.xml')
cells={c.get('id'):c for c in src.iter('mxCell')}
data=json.loads((B/'erd-schema.json').read_text('utf-8'))
groups=[
('01 — Nhân sự và tổ chức',['users','cua_hang','ca_lam_viec','vung']),
('02 — Nội dung đào tạo',['chuong_trinh_dao_tao','bai_hoc','bai_kiem_tra','cau_hoi_kiem_tra']),
('03 — Học tập và kiểm tra',['nhan_vien_khoa_hoc','user_lesson_progresses','ket_qua_kiem_tra']),
('04 — Đánh giá và đào tạo lại',['danh_gia_nang_luc','yeu_cau_dao_tao_lai','retraining_rounds']),
('05 — Dự đoán và lỗi nghiệp vụ',['du_doan_nguy_co','loi_nghiep_vu']),
('06 — Thông báo và hỗ trợ',['notifications','yeu_cau_ho_tro'])]
out=E.Element('mxfile',host='app.diagrams.net',type='device')
def add(root,id,label,x,y,w,h,style):
    c=E.SubElement(root,'mxCell',id=id,value=label,vertex='1',parent='1',style=style)
    E.SubElement(c,'mxGeometry',x=str(x),y=str(y),width=str(w),height=str(h),attrib={'as':'geometry'})
    return c
for num,(title,tables) in enumerate(groups,1):
    dia=E.SubElement(out,'diagram',id=f'page{num}',name=title)
    model=E.SubElement(dia,'mxGraphModel',page='1',pageWidth='1160',pageHeight='1640',grid='1')
    r=E.SubElement(model,'root');E.SubElement(r,'mxCell',id='0');E.SubElement(r,'mxCell',id='1',parent='0')
    add(r,'title','BREWLEARN — '+title.upper(),40,28,1080,48,'text;fontFamily=Arial;fontSize=25;fontStyle=1;align=center;')
    add(r,'sub','PK: khóa chính     FK: cột tham chiếu ở mức logic     NULL / NOT NULL: khả năng để rỗng',40,80,1080,32,'text;fontFamily=Arial;fontSize=16;align=center;')
    ys=[142,142];layout={}
    for idx,t in enumerate(tables):
        col=idx%2;x=40+col*560;y=ys[col];w=520
        attrs=[cells[t+'-'+c['name']] for c in data[t]['columns'] if t+'-'+c['name'] in cells]
        h=48+len(attrs)*29
        add(r,t,cells[t+'-head'].get('value'),x,y,w,48,'rounded=0;fillColor=#215875;strokeColor=#215875;fontColor=#ffffff;fontFamily=Arial;fontStyle=1;fontSize=22;')
        for j,a in enumerate(attrs):
            # Split identifier and type into aligned columns for scanning.
            name,typ=a.get('value').split(' : ',1)
            dtype,null=typ.split(' | ')
            yy=y+48+j*29;fill='#f0f5f8' if j%2==0 else '#ffffff'
            add(r,t+'-n'+str(j),name,x,yy,310,29,f'rounded=0;fillColor={fill};strokeColor=#d5dfe5;align=left;spacingLeft=9;fontFamily=Arial;fontSize=16;')
            add(r,t+'-t'+str(j),dtype,x+310,yy,130,29,f'rounded=0;fillColor={fill};strokeColor=#d5dfe5;align=left;spacingLeft=5;fontFamily=Arial;fontSize=15;')
            add(r,t+'-u'+str(j),'Có' if null=='NULL' else 'Không',x+440,yy,80,29,f'rounded=0;fillColor={fill};strokeColor=#d5dfe5;align=center;fontFamily=Arial;fontSize=15;')
        ys[col]=y+h+60;layout[t]=(x,y,w,h)
    # Draw local relationships in the gutters between tables.
    for k,edge in enumerate(src.iter('mxCell')):
        p=edge.get('source');c=edge.get('target')
        if edge.get('edge')!='1' or p not in layout or c not in layout:continue
        px,py,pw,ph=layout[p];cx,cy,cw,ch=layout[c]
        same=px==cx
        lane=px-18 if same else 580
        sidep=0 if same or px>cx else 1
        sidec=0 if same or cx>px else 1
        import re
        start=re.search(r'startArrow=([^;]+)',edge.get('style'))[1]
        end=re.search(r'endArrow=([^;]+)',edge.get('style'))[1]
        e=E.SubElement(r,'mxCell',id='edge'+str(k),edge='1',parent='1',source=p,target=c,tooltip=edge.get('tooltip',''),style=f'edgeStyle=none;rounded=0;exitX={sidep};exitY=0.5;entryX={sidec};entryY=0.5;startArrow={start};endArrow={end};strokeColor=#456b82;strokeWidth=1.5;jumpStyle=arc;')
        g=E.SubElement(e,'mxGeometry',relative='1',attrib={'as':'geometry'});a=E.SubElement(g,'Array',attrib={'as':'points'})
        E.SubElement(a,'mxPoint',x=str(lane),y=str(py+24));E.SubElement(a,'mxPoint',x=str(lane),y=str(cy+24))
    bottom=max(ys)+8
    add(r,'nullnote','Cột cuối: “Có” = cho phép NULL; “Không” = NOT NULL. Tên thật của cột xem tooltip trong bản đầy đủ.',40,bottom,1080,34,'text;fontFamily=Arial;fontSize=15;align=left;')
    # All relationship roles are written explicitly rather than lost between split pages.
    rel=[]
    for edge in src.iter('mxCell'):
        if edge.get('edge')=='1' and edge.get('target') in tables:
            parent=edge.get('source');child=edge.get('target')
            fields=edge.get('tooltip','')
            rel.append(f'{cells[child+"-head"].get("value")} → {cells[parent+"-head"].get("value")}: {fields}')
    add(r,'relhead','QUAN HỆ DỮ LIỆU (giữ tên cột thực tế để đối chiếu)',40,bottom+42,1080,30,'text;fontFamily=Arial;fontStyle=1;fontSize=17;align=left;')
    for j,line in enumerate(rel):
        add(r,'rel'+str(j),line,40,bottom+76+j*30,1080,30,'text;whiteSpace=wrap;fontFamily=Arial;fontSize=15;align=left;')
    height=max(1640,bottom+125+len(rel)*30)
    model.set('pageHeight',str(height))
E.indent(out);E.ElementTree(out).write(B/'BrewLearn-ERD-bo-cuc-bao-cao.xml',encoding='utf-8',xml_declaration=True)
print('Created 6 editable report pages with aligned attribute/type/null columns and all incoming relationship roles.')
