from pathlib import Path
import json, math, textwrap
import xml.etree.ElementTree as ET
import copy, zipfile
from PIL import Image, ImageDraw, ImageFont

BASE=Path(__file__).parent
schema=json.loads((BASE/'erd-schema.json').read_text('utf-8'))
FONT='C:/Windows/Fonts/arial.ttf'; BOLD='C:/Windows/Fonts/arialbd.ttf'
root=ET.Element('mxfile',host='app.diagrams.net',type='device')
links={}
for table,meta in schema.items():
    for f in meta['foreign_keys']: links[(table,f['col'])]=(f['target'],f['target_col'],'FK')
logical={
('ca_lam_viec','cua_hang_id'):('cua_hang','id'),
('user_lesson_progresses','lesson_id'):('bai_hoc','MaBaiHoc'),
('danh_gia_nang_luc','nguoi_duyet_id'):('users','id'),
('loi_nghiep_vu','nguoi_ghi_nhan_id'):('users','id'),
('yeu_cau_dao_tao_lai','assessment_id'):('danh_gia_nang_luc','id'),
('yeu_cau_dao_tao_lai','nguoi_phu_trach_id'):('users','id'),
('yeu_cau_dao_tao_lai','danh_gia_lai_id'):('danh_gia_nang_luc','id'),
('retraining_rounds','request_id'):('yeu_cau_dao_tao_lai','id'),
('retraining_rounds','assigned_by'):('users','id'),
('retraining_rounds','owner_id'):('users','id'),
('retraining_rounds','assessment_id'):('danh_gia_nang_luc','id'),
('retraining_rounds','approved_by'):('users','id'),
('retraining_rounds','confirmed_by'):('users','id'),
}
for key,target in logical.items():
    if key not in links: links[key]=(*target,'RL')
seen=[]; edge_geometries=[]
def font(size=20,bold=False): return ImageFont.truetype(BOLD if bold else FONT,size)
def text(x,y,s,size=20,bold=False,fill='#111111'): draw.text((x,y),s,font=font(size,bold),fill=fill)
def cell(id,value,x,y,w,h,style='text;align=left;verticalAlign=middle;spacingLeft=12;'):
    if 'fontSize=' not in style: style+='fontSize=20;'
    c=ET.SubElement(graph,'mxCell',id=id,value=value,style=style+'fontFamily=Arial;whiteSpace=wrap;',vertex='1',parent='1')
    ET.SubElement(c,'mxGeometry',x=str(x),y=str(y),width=str(w),height=str(h),attrib={'as':'geometry'})
    return c
def page(slug,title,h=1800):
    global graph,im,draw,current_slug,page_h
    current_slug=slug;page_h=h
    dia=ET.SubElement(root,'diagram',id=slug,name=title)
    model=ET.SubElement(dia,'mxGraphModel',grid='1',page='1',pageWidth='2460',pageHeight=str(h))
    graph=ET.SubElement(model,'root');ET.SubElement(graph,'mxCell',id='0');ET.SubElement(graph,'mxCell',id='1',parent='0')
    im=Image.new('RGB',(2460,h),'white');draw=ImageDraw.Draw(im)
    cell('title',title,60,25,2340,55,'text;align=center;fontStyle=1;');text(60,30,title,32,True)
    note='Crow’s Foot  |  PK: khóa chính  |  UQ: duy nhất  |  FK: khóa ngoại MySQL  |  RL: liên kết theo code'
    cell('legend',note,60,100,2340,45);text(60,105,note,22)
    note='Ô “THAM CHIẾU” là cùng thực thể được lặp lại để tránh giao cắt; không phải bảng mới. ○: tùy chọn, |: một, chân quạ: nhiều.'
    cell('repeat',note,60,150,2340,45);text(60,155,note,20)
def save():
    im.save(BASE/f'ERD-BrewLearn-{current_slug}.png',dpi=(300,300))
def uniques(table):
    groups={}
    for idx in schema[table]['indexes']:
        if not idx['non_unique'] and idx['name']!='PRIMARY':groups.setdefault(idx['name'],[]).append(idx['col'])
    return list(groups.values())
def marker(x,y,kind,direction):
    # Symbols are entirely inside the connector corridor, never inside fields.
    if kind=='one':
        for dist in [9,17]:draw.line((x+direction*dist,y-10,x+direction*dist,y+10),fill='black',width=2)
    elif kind=='zeroone':
        draw.line((x+direction*9,y-10,x+direction*9,y+10),fill='black',width=2)
        cx=x+direction*25;draw.ellipse((cx-6,y-6,cx+6,y+6),fill='white',outline='black',width=2)
    elif kind=='zeromany':
        px=x+direction*20
        draw.line((px,y,x,y-12),fill='black',width=2);draw.line((px,y,x,y+12),fill='black',width=2)
        cx=x+direction*34;draw.ellipse((cx-6,y-6,cx+6,y+6),fill='white',outline='black',width=2)
def edge(s,t,x1,x2,y,parent_optional,child_unique,label):
    parent='ERzeroToOne' if parent_optional else 'ERmandOne';child='ERzeroToOne' if child_unique else 'ERzeroToMany'
    c=ET.SubElement(graph,'mxCell',id=f'e-{s}-{t}',value='',edge='1',parent='1',source=s,target=t,
        style=f'endArrow={child};startArrow={parent};startFill=0;endFill=0;strokeColor=#000000;strokeWidth=1.5;exitX=1;exitY=0.5;exitPerimeter=0;entryX=0;entryY=0.5;entryPerimeter=0;')
    ET.SubElement(c,'mxGeometry',relative='1',attrib={'as':'geometry'})
    draw.line((x1,y,x2,y),fill='black',width=2)
    marker(x1,y,'zeroone' if parent_optional else 'one',1);marker(x2,y,'zeroone' if child_unique else 'zeromany',-1)
    cell(f'label-{s}',label,x1+40,y-39,x2-x1-80,29,'text;align=center;fontSize=18;')
    tw=draw.textlength(label,font=font(18));text((x1+x2-tw)/2,y-30,label,18)
    edge_geometries.append((current_slug,x1,x2,y))
def reference(id,table,pk,x,y):
    cell(id,f'{table}\nTHAM CHIẾU · PK {pk}',x,y,325,66,'rounded=0;fillColor=#ffffff;strokeColor=#333333;align=center;')
    draw.rectangle((x,y,x+325,y+66),outline='#333333',width=2)
    text(x+12,y+9,table,20,True);text(x+12,y+38,f'THAM CHIẾU · PK {pk}',17,fill='#444444')
def entity(table,x,y,refx):
    seen.append(table);cols=schema[table]['columns'];width=620
    titleid=f'entity-header-{table}';cell(titleid,table,x,y,width,54,'rounded=0;fillColor=#edf1f5;strokeColor=#333333;fontStyle=1;')
    draw.rectangle((x,y,x+width,y+54),fill='#edf1f5',outline='#333333',width=2);text(x+15,y+14,table,25,True)
    cursor=y+54
    uq=uniques(table);single={g[0] for g in uq if len(g)==1}
    for col in cols:
        name=col['name'];relation=links.get((table,name));height=104 if relation else 34
        dtype=col['type'];lines=textwrap.wrap(dtype,26) or [''];height=max(height,28*len(lines)+12)
        flags=[]
        if col['colkey']=='PRI':flags.append('PK')
        if relation:flags.append(relation[2])
        if name in single:flags.append('UQ')
        if col['nullable']=='YES':flags.append('NULL')
        value=name+('  ['+', '.join(flags)+']' if flags else '')+'\n'+dtype
        id=f'{table}-{name}'
        field_label=name+('  ['+', '.join(flags)+']' if flags else '')
        if relation: field_label=name+'\n['+', '.join(flags)+']'
        cell(id,field_label,x,cursor,380,height,'rounded=0;fillColor=#ffffff;strokeColor=#b8bec5;align=left;spacingLeft=14;fontSize=18;')
        cell(id+'-type',dtype,x+380,cursor,240,height,'rounded=0;fillColor=#ffffff;strokeColor=#b8bec5;align=left;spacingLeft=8;fontSize=17;')
        draw.rectangle((x,cursor,x+width,cursor+height),outline='#b8bec5',width=1)
        if relation:
            text(x+14,cursor+17,name,21,True)
            text(x+14,cursor+49,'['+', '.join(flags)+']',18,fill='#444444')
            for j,line in enumerate(lines):text(x+380,cursor+20+j*25,line,17,fill='#444444')
            target,pk,kind=relation;rid=f'ref-{table}-{name}';mid=cursor+height/2
            reference(rid,target,pk,refx,mid-33)
            edge(rid,id,refx+325,x,mid,col['nullable']=='YES',name in single,kind)
        else:
            txt=name+('  ['+', '.join(flags)+']' if flags else '')
            text(x+14,cursor+(height-24)/2,txt,18,col['colkey']=='PRI')
            for j,line in enumerate(lines):text(x+380,cursor+7+j*25,line,17,fill='#444444')
        cursor+=height
    if uq:
        info='UQ: '+'; '.join('('+', '.join(g)+')' for g in uq)
        lines=textwrap.wrap(info,88)
        cell(table+'-unique',info,x,cursor+12,width,32*len(lines),'text;fontSize=18;')
        for j,line in enumerate(lines):text(x,cursor+15+j*25,line,17)
        cursor+=40+25*len(lines)
    assert cursor<page_h-135,(table,cursor,page_h)
    return cursor

groups=[
('01-nhan-su','01  ERD NHÂN SỰ VÀ CA LÀM VIỆC','users','ca_lam_viec',2050),
('02-cua-hang','02  ERD DANH MỤC VÙNG VÀ CỬA HÀNG','vung','cua_hang',1100),
('03-noi-dung','03  ERD CHƯƠNG TRÌNH VÀ BÀI HỌC','chuong_trinh_dao_tao','bai_hoc',1100),
('04-tien-do','04  ERD TIẾN ĐỘ KHÓA HỌC VÀ BÀI HỌC','nhan_vien_khoa_hoc','user_lesson_progresses',1200),
('05-kiem-tra','05  ERD BÀI KIỂM TRA VÀ CÂU HỎI','bai_kiem_tra','cau_hoi_kiem_tra',1100),
('06-ket-qua','06  ERD KẾT QUẢ KIỂM TRA VÀ LỖI NGHIỆP VỤ','ket_qua_kiem_tra','loi_nghiep_vu',1250),
('07-danh-gia','07  ERD ĐÁNH GIÁ NĂNG LỰC VÀ DỰ ĐOÁN','danh_gia_nang_luc','du_doan_nguy_co',1800),
('08-dao-tao-lai','08  ERD YÊU CẦU VÀ CÁC VÒNG ĐÀO TẠO LẠI','yeu_cau_dao_tao_lai','retraining_rounds',2400),
('09-ho-tro','09  ERD THÔNG BÁO VÀ YÊU CẦU HỖ TRỢ','notifications','yeu_cau_ho_tro',1100),
]
for slug,title,a,b,h in groups:
    page(slug,title,h);entity(a,530,250,60);entity(b,1770,250,1300)
    if a=='vung':note='nguoi_quan_ly_id là cột còn tồn tại; chưa xác nhận FK hoặc luồng quản lý vùng nên không vẽ quan hệ giả định.'
    elif a=='yeu_cau_dao_tao_lai':note='required_lessons / required_quizzes là danh sách JSON; không vẽ chúng như khóa ngoại đơn. Giữ lịch sử riêng từng vòng.'
    else:note='Tất cả cột và kiểu dữ liệu giữ nguyên từ MySQL. Chân quạ ở phía bảng chứa cột liên kết; đầu đối diện xác định bảng được tham chiếu.'
    cell('footer',note,60,h-90,2340,55);text(60,h-85,note,20)
    save()

# A compact overview shows the three N:N relationships resolved by their actual bridges.
page('00-quan-he-NN','00  ERD TỔNG QUAN CÁC QUAN HỆ NHIỀU NHIỀU',1240)
def summary(id,table,fields,x,y,w=580):
    label=table+'\n'+'\n'.join(fields)
    cell(id,label,x,y,w,160,'rounded=0;fillColor=#ffffff;strokeColor=#333333;align=left;spacing=15;')
    draw.rectangle((x,y,x+w,y+160),outline='#333333',width=2);draw.rectangle((x,y,x+w,y+48),fill='#edf1f5',outline='#333333',width=2)
    text(x+15,y+13,table,24,True)
    for j,f in enumerate(fields):text(x+15,y+65+j*27,f,20)
for i,(bridge,target,pk,col) in enumerate([
('nhan_vien_khoa_hoc','chuong_trinh_dao_tao','MaCTDT','MaCTDT'),
('user_lesson_progresses','bai_hoc','MaBaiHoc','lesson_id'),
('ket_qua_kiem_tra','bai_kiem_tra','MaBaiKiemTra','MaBaiKiemTra')]):
    y=270+i*285
    summary(f'user{i}','users',['PK id','Tham chiếu cùng bảng users'],60,y,420)
    summary(f'bridge{i}',bridge,['PK id','user_id → users.id',f'{col} → {target}.{pk}'],790,y,700)
    summary(f'target{i}',target,[f'PK {pk}','Các thuộc tính: xem trang chi tiết'],1800,y,580)
    edge(f'user{i}',f'bridge{i}',480,790,y+80,False,False,'1 : N')
    # Right reference to bridge drawn in reverse spatial direction, with parent symbols on the right.
    draw.line((1490,y+80,1800,y+80),fill='black',width=2);marker(1490,y+80,'zeromany',1);marker(1800,y+80,'one',-1)
    c=ET.SubElement(graph,'mxCell',id=f'right{i}',source=f'bridge{i}',target=f'target{i}',edge='1',parent='1',style='startArrow=ERzeroToMany;endArrow=ERmandOne;startFill=0;endFill=0;exitX=1;exitY=0.5;entryX=0;entryY=0.5;strokeColor=#000000;')
    ET.SubElement(c,'mxGeometry',relative='1',attrib={'as':'geometry'})
    text(1575,y+48,'N : 1',18)
    text(790,y+185,'Quan hệ N:N được triển khai bằng hai liên kết 1:N qua bảng trung gian.',20)
cell('footer','Các ô ở trang này chỉ hiển thị khóa liên kết. Toàn bộ thuộc tính và tính tùy chọn nằm ở các trang 01–09.',60,1140,2340,60)
text(60,1145,'Các ô ở trang này chỉ hiển thị khóa liên kết. Toàn bộ thuộc tính và tính tùy chọn nằm ở các trang 01–09.',21)
save()
# Move overview to the first tab.
overview=root[-1];root.remove(overview);root.insert(0,overview)
assert set(seen)==set(schema) and len(seen)==18
# Straight connectors occupy disjoint horizontal lanes within each domain page.
for i,a in enumerate(edge_geometries):
    for b in edge_geometries[i+1:]:
        if a[0]==b[0] and abs(a[3]-b[3])<0.1:
            assert max(a[1],b[1])>=min(a[2],b[2]),('Overlapping lines',a,b)
# Assemble one complete poster from the domain panels without adding crossing connectors.
row_heights=[max(g[4] for g in groups[i:i+3]) for i in range(0,9,3)]
poster=Image.new('RGB',(7380,120+sum(row_heights)),'white')
pd=ImageDraw.Draw(poster)
pd.text((60,30),'ERD BREWLEARN — TOÀN BỘ 18 THỰC THỂ VÀ CÁC LIÊN KẾT',font=font(44,True),fill='black')
dia=ET.Element('diagram',id='full-erd',name='ERD đầy đủ trên một trang lớn')
model=ET.SubElement(dia,'mxGraphModel',grid='1',page='1',pageWidth='7380',pageHeight=str(poster.height))
dest=ET.SubElement(model,'root');ET.SubElement(dest,'mxCell',id='0');ET.SubElement(dest,'mxCell',id='1',parent='0')
head=ET.SubElement(dest,'mxCell',id='poster-title',value='ERD BREWLEARN — TOÀN BỘ 18 THỰC THỂ VÀ CÁC LIÊN KẾT',vertex='1',parent='1',style='text;align=left;fontFamily=Arial;fontSize=44;fontStyle=1;')
ET.SubElement(head,'mxGeometry',x='60',y='25',width='7200',height='70',attrib={'as':'geometry'})
for i,(slug,*_) in enumerate(groups):
    x=(i%3)*2460;y=120+sum(row_heights[:i//3])
    with Image.open(BASE/f'ERD-BrewLearn-{slug}.png') as pic:poster.paste(pic,(x,y))
    old=next(d for d in root if d.get('id')==slug)
    for original in old.find('mxGraphModel/root'):
        if original.get('id') in ('0','1'):continue
        c=copy.deepcopy(original);c.set('id',slug+'-'+c.get('id'))
        for attr in ['source','target']:
            if c.get(attr):c.set(attr,slug+'-'+c.get(attr))
        geo=c.find('mxGeometry')
        if c.get('vertex')=='1':geo.set('x',str(float(geo.get('x'))+x));geo.set('y',str(float(geo.get('y'))+y))
        dest.append(c)
root.insert(0,dia)
poster.save(BASE/'ERD-BrewLearn-day-du.png',dpi=(300,300))
ET.indent(root)
ET.ElementTree(root).write(BASE/'BrewLearn-ERD-chuan.drawio',encoding='utf-8',xml_declaration=True)
with zipfile.ZipFile(BASE/'BrewLearn-ERD-day-du.zip','w',zipfile.ZIP_DEFLATED) as z:
    z.write(BASE/'BrewLearn-ERD-chuan.drawio','BrewLearn-ERD-chuan.drawio')
    z.write(BASE/'ERD-BrewLearn-day-du.png','ERD-BrewLearn-day-du.png')
    for filename in sorted(BASE.glob('ERD-BrewLearn-0*.png')):z.write(filename,filename.name)
print(f'PASS: 18 full entities, {len(links)} direct relationships, full poster plus 10 detail/overview pages; no crossing or overlapping connector lanes.')
