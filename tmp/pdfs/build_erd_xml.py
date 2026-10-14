import xml.etree.ElementTree as E
from pathlib import Path
import re, html

src=E.parse(r'D:/download/usecase1-ERD.final.drawio.svg').getroot()
doc=E.fromstring(src.attrib['content'])
page=next(p for p in doc.findall('diagram') if p.get('name')=='ERD.final')
cells=page.findall('./mxGraphModel/root/mxCell'); byid={c.get('id'):c for c in cells}
def ancestor(c):
    while c.get('parent') not in (None,'1'):
        c=byid[c.get('parent')]
    return c
tables={}
for c in cells:
    if c.get('parent')=='1' and c.get('vertex')=='1':
        children=[q for q in cells if q.get('parent')==c.get('id')]
        title=next((q.get('value','') for q in children if re.fullmatch('[a-z_]+',q.get('value',''))),None)
        if title: tables[title]={'id':c.get('id'),'rows':[]}
for c in cells:
    g=c.find('mxGeometry')
    if g is None or g.get('width')!='325': continue
    a=ancestor(c); name=next((n for n,t in tables.items() if t['id']==a.get('id')),None)
    if not name: continue
    siblings=[q for q in cells if q.get('parent')==c.get('parent') and q.find('mxGeometry') is not None and q.find('mxGeometry').get('y','0')==g.get('y','0')]
    typ=next((q.get('value','') for q in siblings if q.find('mxGeometry').get('x')=='325'),'')
    null=next((q.get('value','') for q in siblings if q.find('mxGeometry').get('x')=='465'),'NULL')
    y=float(g.get('y','0')); parent=byid.get(c.get('parent'))
    while parent is not None and parent.get('id')!=a.get('id'):
        pg=parent.find('mxGeometry'); y+=float(pg.get('y','0')) if pg is not None else 0
        parent=byid.get(parent.get('parent'))
    tables[name]['rows'].append((y,re.sub(' +',' ',c.get('value','')),typ.strip(),null))
for t in tables.values(): t['rows'].sort()
t=tables['danh_gia_nang_luc']; t['rows'].append((999,'chi_tiet_diem','JSON','NULL'))
for i,r in enumerate(t['rows']):
    if r[1]=='chi_tiet_de_xuat': t['rows'][i]=(r[0],r[1],'JSON','NULL')
targets={'cua_hang_id':'cua_hang','ca_lam_viec_id':'ca_lam_viec','vung_id':'vung','ma_ctdt':'chuong_trinh_dao_tao','ma_bai_hoc':'bai_hoc','ma_bai_kiem_tra':'bai_kiem_tra','du_doan_id':'du_doan_nguy_co','danh_gia_nguon_id':'danh_gia_nang_luc','danh_gia_lai_id':'danh_gia_nang_luc','yeu_cau_id':'yeu_cau_dao_tao_lai'}
relations=[]
for name,t in tables.items():
    for i,r in enumerate(t['rows']):
        if r[1].startswith('FK '):
            field=r[1][3:]; target=targets.get(field,'nguoi_dung')
            relations.append((target,name,field,r[3],i))
assert len(relations)==36,len(relations)
mx=E.Element('mxfile',host='app.diagrams.net',type='device',version='26.0.0')
colors={'nguoi_dung':'#17365D','vung':'#17365D','cua_hang':'#17365D','ca_lam_viec':'#17365D','danh_gia_nang_luc':'#6B4C9A','yeu_cau_dao_tao_lai':'#6B4C9A','vong_dao_tao_lai':'#6B4C9A','du_doan_nguy_co':'#9A641B'}
def make_page(title,layout):
    diagram=E.SubElement(mx,'diagram',name=title,id='page-'+str(len(mx)))
    model=E.SubElement(diagram,'mxGraphModel',grid='1',gridSize='20',guides='1',connect='1',arrows='1',fold='1',page='0',pageScale='1',background='#ffffff')
    root=E.SubElement(model,'root'); E.SubElement(root,'mxCell',id='0'); E.SubElement(root,'mxCell',id='1',parent='0')
    def vertex(id,value,style,x,y,w,h,parent='1'):
        c=E.SubElement(root,'mxCell',id=id,value=value,style=style,vertex='1',parent=parent)
        E.SubElement(c,'mxGeometry',x=str(x),y=str(y),width=str(w),height=str(h),attrib={'as':'geometry'}); return c
    maxx=max(p[0] for p in layout.values())+600
    vertex('title',title,'text;html=1;align=left;fontSize=30;fontStyle=1;fontColor=#17365D;',40,20,maxx,50)
    note='ERD LOGIC • Tên Việt hóa • NN: bắt buộc; NULL: tùy chọn • Mỗi đường là một vai trò/khóa tham chiếu. Quan hệ logic không đồng nghĩa có ràng buộc FK vật lý. Các thuộc tính trình bày theo bản thiết kế nguồn, không phải toàn bộ cột kỹ thuật. vung.nguoi_quan_ly_id không biểu diễn quan hệ.'
    vertex('legend',note,'text;html=1;whiteSpace=wrap;align=left;fontSize=15;fontColor=#475569;',40,78,maxx,60)
    for name,(x,y) in layout.items():
        rows=tables[name]['rows']; h=46+30*len(rows)
        vertex(name,'','rounded=0;fillColor=#FFFFFF;strokeColor=#CBD5E1;strokeWidth=1.5;container=1;collapsible=0;',x,y,560,h)
        vertex(name+'-head',name,'rounded=0;fillColor='+colors.get(name,'#216C73')+';strokeColor=none;fontColor=#FFFFFF;fontStyle=1;fontSize=19;',0,0,560,46,name)
        for i,(_,field,typ,null) in enumerate(rows):
            vertex(name+'-row-'+str(i),html.escape(field)+'  |  '+html.escape(typ)+'  |  '+null,'text;html=1;align=left;spacingLeft=12;fontFamily=Arial;fontSize=15;fillColor='+('#F0F5FA' if i%2==0 else '#FFFFFF')+';strokeColor=#E2E8F0;',0,46+30*i,560,30,name)
    counts={}; outgoing={}
    for a,b,f,n,i in relations:
        if a in layout and b in layout: outgoing[a]=outgoing.get(a,0)+1
    for k,(a,b,field,null,i) in enumerate(relations):
        if a not in layout or b not in layout: continue
        ax,ay=layout[a]; bx,by=layout[b]; ah=46+30*len(tables[a]['rows']); bh=46+30*len(tables[b]['rows'])
        counts[a]=counts.get(a,0)+1; sy=0.12+0.76*counts[a]/(outgoing[a]+1)
        start='ERmandOne' if null=='NN' else 'ERzeroToOne'
        right=bx>ax; ex=1 if right else 0; en=0 if right else 1
        if ax==bx: ex=en=1
        style=f'edgeStyle=orthogonalEdgeStyle;rounded=0;orthogonalLoop=1;jettySize=24;startArrow={start};endArrow=ERzeroToMany;startFill=0;endFill=0;strokeColor=#64748B;strokeWidth=1.3;jumpStyle=arc;jumpSize=8;fontSize=13;fontColor=#334155;labelBackgroundColor=#FFFFFF;exitX={ex};exitY={sy};entryX={en};entryY={(46+30*i+15)/bh};'
        c=E.SubElement(root,'mxCell',id='rel-'+str(k),value=field,style=style,edge='1',parent='1',source=a,target=b+'-row-'+str(i),tooltip=a+'.id → '+b+'.'+field)
        # The target is an actual attribute row, so its attachment is centered.
        c.set('style',style.replace('entryY='+str((46+30*i+15)/bh),'entryY=0.5'))
        g=E.SubElement(c,'mxGeometry',relative='1',attrib={'as':'geometry'})
        points=E.SubElement(g,'Array',attrib={'as':'points'})
        lane=(ax+560+bx)/2+(k%7-3)*24 if right else (bx+560+ax)/2+(k%7-3)*24
        if ax==bx: lane=ax+650+(k%7)*28
        E.SubElement(points,'mxPoint',x=str(lane),y=str(ay+ah*sy))
        E.SubElement(points,'mxPoint',x=str(lane),y=str(by+46+30*i+15))

make_page('01 · ERD tổng thể — 18 bảng, 36 quan hệ',{
'chuong_trinh_dao_tao':(40,190),'bai_hoc':(40,580),'bai_kiem_tra':(40,1020),'cau_hoi_kiem_tra':(40,1460),
'nhan_vien_khoa_hoc':(860,190),'tien_do_bai_hoc':(860,650),'ket_qua_kiem_tra':(860,1120),
'vung':(1680,190),'cua_hang':(1680,600),'ca_lam_viec':(1680,1050),'nguoi_dung':(1680,1500),
'du_doan_nguy_co':(2540,190),'danh_gia_nang_luc':(2540,780),'loi_nghiep_vu':(2540,1820),
'yeu_cau_dao_tao_lai':(3440,190),'vong_dao_tao_lai':(3440,1200),
'thong_bao':(860,1940),'yeu_cau_ho_tro':(1680,2220)})
make_page('02 · Tổ chức, nhân sự và hỗ trợ',{'vung':(40,190),'cua_hang':(850,190),'ca_lam_viec':(1660,190),'nguoi_dung':(850,700),'thong_bao':(40,1320),'loi_nghiep_vu':(850,1320),'yeu_cau_ho_tro':(1660,1320)})
make_page('03 · Học tập và kiểm tra',{'nguoi_dung':(40,190),'chuong_trinh_dao_tao':(40,800),'bai_hoc':(850,800),'bai_kiem_tra':(1660,800),'nhan_vien_khoa_hoc':(850,190),'tien_do_bai_hoc':(1660,190),'ket_qua_kiem_tra':(2470,190),'cau_hoi_kiem_tra':(2470,800)})
make_page('04 · Đánh giá và đề xuất đào tạo lại',{'nguoi_dung':(40,190),'chuong_trinh_dao_tao':(40,850),'cua_hang':(40,1270),'danh_gia_nang_luc':(1000,190),'du_doan_nguy_co':(1000,1100),'yeu_cau_dao_tao_lai':(2000,190)})
make_page('05 · Vòng đào tạo lại và phê duyệt',{'nguoi_dung':(40,190),'danh_gia_nang_luc':(40,850),'yeu_cau_dao_tao_lai':(1100,190),'vong_dao_tao_lai':(2200,190),'du_doan_nguy_co':(1100,1150)})
out=Path('output/erd');out.mkdir(parents=True,exist_ok=True)
E.indent(mx,space='  ')
payload=E.tostring(mx,encoding='utf-8',xml_declaration=True)
for ext in ('drawio','xml'): (out/('BrewLearn_ERD_hoan_chinh.'+ext)).write_bytes(payload)
print('Tables:',len(tables),'Relations:',len(relations),'Pages:',len(mx))
for d in mx:
    cs=d.findall('./mxGraphModel/root/mxCell'); ids={c.get('id') for c in cs}; edges=[c for c in cs if c.get('edge')=='1']
    assert len(ids)==len(cs)
    assert all(c.get('source') in ids and c.get('target') in ids for c in edges)
    print(d.get('name'),len(edges),'valid relationships')
