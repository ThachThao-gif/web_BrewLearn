from pathlib import Path
import xml.etree.ElementTree as E
import json,re
B=Path(__file__).parent
src=E.parse(B/'BrewLearn-ERD-logic-nghiep-vu-day-du.xml');old={c.get('id'):c for c in src.iter('mxCell')}
data=json.loads((B/'erd-schema.json').read_text('utf-8'))
rows=[['vung','cua_hang','ca_lam_viec','users','notifications'],['chuong_trinh_dao_tao','nhan_vien_khoa_hoc','user_lesson_progresses','yeu_cau_ho_tro','loi_nghiep_vu'],['bai_hoc','bai_kiem_tra','ket_qua_kiem_tra','du_doan_nguy_co'],['cau_hoi_kiem_tra','danh_gia_nang_luc','yeu_cau_dao_tao_lai','retraining_rounds']]
W=3300;BW=570;GAP=80;ROW=32;HEADER=50
root=E.Element('mxfile',host='app.diagrams.net',type='device');d=E.SubElement(root,'diagram',id='onepage',name='ERD BrewLearn — một trang')
m=E.SubElement(d,'mxGraphModel',page='1',pageWidth=str(W),pageHeight='3400',grid='1');r=E.SubElement(m,'root');E.SubElement(r,'mxCell',id='0');E.SubElement(r,'mxCell',id='1',parent='0')
def node(id,value,x,y,w,h,style,parent='1'):
    c=E.SubElement(r,'mxCell',id=id,value=value,parent=parent,vertex='1',style=style+'fontFamily=Arial;')
    E.SubElement(c,'mxGeometry',x=str(x),y=str(y),width=str(w),height=str(h),attrib={'as':'geometry'})
    return c
positions={};y=150
for ri,row in enumerate(rows):
    maxh=0
    for ci,t in enumerate(row):
        attrs=[old[t+'-'+c['name']] for c in data[t]['columns'] if t+'-'+c['name'] in old]
        x=45+ci*(BW+GAP);h=HEADER+ROW*len(attrs);maxh=max(maxh,h);positions[t]=(x,y,BW,h)
        node(t,'',x,y,BW,h,'group;')
        node(t+'-head',old[t+'-head'].get('value'),0,0,BW,HEADER,'fillColor=#245e7e;strokeColor=#245e7e;fontColor=#ffffff;fontSize=24;fontStyle=1;',t)
        for i,a in enumerate(attrs):
            name,typ=a.get('value').split(' : ');dtype,null=typ.split(' | ')
            # Dense but aligned table; full NULL wording remains in tooltip.
            fill='#edf3f7' if i%2==0 else '#ffffff';yy=HEADER+i*ROW
            c=node(t+'-n'+str(i),name,0,yy,340,ROW,f'fillColor={fill};strokeColor=#d9e2e8;align=left;spacingLeft=8;fontSize=18;',t);c.set('tooltip',a.get('tooltip',''))
            node(t+'-d'+str(i),dtype,340,yy,160,ROW,f'fillColor={fill};strokeColor=#d9e2e8;align=left;spacingLeft=5;fontSize=17;',t)
            node(t+'-u'+str(i),'NULL' if null=='NULL' else 'NN',500,yy,70,ROW,f'fillColor={fill};strokeColor=#d9e2e8;fontSize=16;',t)
    y+=maxh+110
# Route through gutters; retain original cardinalities and column tooltip.
for i,e in enumerate(src.iter('mxCell')):
    if e.get('edge')!='1':continue
    p=e.get('source');c=e.get('target');px,py,pw,ph=positions[p];cx,cy,cw,ch=positions[c]
    start=re.search('startArrow=([^;]+)',e.get('style'))[1];end=re.search('endArrow=([^;]+)',e.get('style'))[1]
    if py==cy:
        sx=tx=.5;sy=ty=0;lane=py-25-(i%5)*10
        pts=[(px+pw/2,lane),(cx+cw/2,lane)]
    elif px==cx:
        sx=tx=0;sy=ty=.5;lane=px-20-(i%4)*8
        pts=[(lane,py+ph/2),(lane,cy+ch/2)]
    else:
        sx=1 if px<cx else 0;tx=0 if px<cx else 1;sy=ty=.5
        lane=px+pw+25+(i%4)*8 if px<cx else px-25-(i%4)*8
        # Horizontal segments stay in row gutters to avoid crossing table bodies.
        mid=min(py,cy)-35-(i%5)*9
        targetlane=cx-20 if tx==0 else cx+cw+20
        pts=[(lane,py+ph/2),(lane,mid),(targetlane,mid),(targetlane,cy+ch/2)]
    edge=E.SubElement(r,'mxCell',id='rel'+str(i),parent='1',edge='1',source=p,target=c,tooltip=e.get('tooltip',''),style=f'edgeStyle=none;exitX={sx};exitY={sy};entryX={tx};entryY={ty};startArrow={start};endArrow={end};startFill=0;endFill=0;strokeWidth=1.6;strokeColor=#61798b;jumpStyle=arc;jumpSize=8;rounded=0;')
    g=E.SubElement(edge,'mxGeometry',relative='1',attrib={'as':'geometry'});a=E.SubElement(g,'Array',attrib={'as':'points'})
    for xx,yy in pts:E.SubElement(a,'mxPoint',x=str(xx),y=str(yy))
node('title','MÔ HÌNH DỮ LIỆU LOGIC — BREWLEARN',40,20,W-80,55,'text;fontSize=36;fontStyle=1;align=center;')
node('legend','PK: khóa chính · FK: tham chiếu logic · NULL: cho phép rỗng · NN: NOT NULL · Tên cột thực tế xem tooltip',40,82,W-80,36,'text;fontSize=21;align=center;')
node('note','Nhân viên / tài khoản lưu chung trong users. Giữ ngày vào làm (joined_at). Cặp request_id + round_number là duy nhất.',40,y-40,W-80,40,'text;fontSize=20;align=center;')
m.set('pageHeight',str(y+30));E.indent(root);E.ElementTree(root).write(B/'BrewLearn-ERD-mot-trang-bao-cao.xml',encoding='utf-8',xml_declaration=True)
assert len(positions)==18
print('Single page:',W,y+30,'18 entities; all selected business attributes retained.')
