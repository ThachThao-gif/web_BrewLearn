from pathlib import Path
import xml.etree.ElementTree as E
import re, html, json
from PIL import Image, ImageDraw, ImageFont

B=Path(__file__).parent
tree=E.parse(B/'BrewLearn-ERD-mot-trang-quan-he-ro.xml')
root=tree.find('.//root'); cells={c.get('id'):c for c in root}
names={'users':'nguoi_dung','user_lesson_progresses':'tien_do_bai_hoc','retraining_rounds':'vong_dao_tao_lai','notifications':'thong_bao'}
spec=[('vung','cua_hang','vung_id','Thuộc vùng'),('cua_hang','users','cua_hang_id','Thuộc cửa hàng'),('cua_hang','ca_lam_viec','cua_hang_id','Có ca làm việc'),('ca_lam_viec','users','ca_lam_viec_id','Được phân ca'),('chuong_trinh_dao_tao','bai_hoc','FK_MaCTDT','Gồm bài học'),('users','nhan_vien_khoa_hoc','user_id','Học khóa học'),('chuong_trinh_dao_tao','nhan_vien_khoa_hoc','MaCTDT','Tiến độ khóa học'),('users','user_lesson_progresses','user_id','Học bài'),('bai_hoc','user_lesson_progresses','lesson_id','Tiến độ bài học'),('bai_hoc','bai_kiem_tra','MaBaiHoc','Có bài kiểm tra'),('bai_kiem_tra','cau_hoi_kiem_tra','MaBaiKiemTra','Gồm câu hỏi'),('users','ket_qua_kiem_tra','user_id','Làm kiểm tra'),('bai_kiem_tra','ket_qua_kiem_tra','MaBaiKiemTra','Có lượt làm bài'),('users','danh_gia_nang_luc','user_id','Được đánh giá'),('users','danh_gia_nang_luc','NguoiDanhGia_id','Chấm đánh giá'),('users','danh_gia_nang_luc','nguoi_duyet_id','Duyệt đánh giá'),('chuong_trinh_dao_tao','danh_gia_nang_luc','MaCTDT','Nội dung đánh giá'),('users','loi_nghiep_vu','user_id','Nhân viên liên quan'),('users','loi_nghiep_vu','nguoi_ghi_nhan_id','Ghi nhận lỗi'),('users','du_doan_nguy_co','user_id','Được dự đoán'),('cua_hang','du_doan_nguy_co','cua_hang_id','Phạm vi dự đoán'),('users','yeu_cau_dao_tao_lai','user_id','Được đào tạo lại'),('users','yeu_cau_dao_tao_lai','nguoi_de_xuat_id','Đề xuất đào tạo lại'),('users','yeu_cau_dao_tao_lai','nguoi_duyet_id','Duyệt yêu cầu'),('users','yeu_cau_dao_tao_lai','nguoi_phu_trach_id','Phụ trách kế hoạch'),('du_doan_nguy_co','yeu_cau_dao_tao_lai','du_doan_id','Tham chiếu dự đoán'),('danh_gia_nang_luc','yeu_cau_dao_tao_lai','assessment_id','Đánh giá nguồn'),('danh_gia_nang_luc','yeu_cau_dao_tao_lai','danh_gia_lai_id','Đánh giá lại hiện hành'),('yeu_cau_dao_tao_lai','retraining_rounds','request_id','Có vòng đào tạo lại'),('users','retraining_rounds','assigned_by','Giao vòng'),('users','retraining_rounds','owner_id','Phụ trách vòng'),('danh_gia_nang_luc','retraining_rounds','assessment_id','Đánh giá lại của vòng'),('users','retraining_rounds','approved_by','Duyệt kết quả vòng'),('users','retraining_rounds','confirmed_by','Xác nhận hoàn thành'),('users','notifications','user_id','Nhận thông báo'),('users','yeu_cau_ho_tro','user_id','Gửi yêu cầu hỗ trợ')]
index={(a,b,c):(i+1,d) for i,(a,b,c,d) in enumerate(spec)}
tables={c.get('id'):c for c in root if c.get('id') in json.loads((B/'erd-schema.json').read_text('utf-8'))}
rects={k:tuple(float(c.find('mxGeometry').get(v)) for v in ['x','y','width','height']) for k,c in tables.items()}
for k in tables:
 cells[k+'-head'].set('value',names.get(k,k))
 cells[k+'-head'].set('style',cells[k+'-head'].get('style','')+'fillColor=#244b68;fontColor=#ffffff;fontSize=23;fontStyle=1;')
for k in ['title','legend']:
 if k in cells: root.remove(cells[k])
for c in list(root):
 if c.get('vertex')=='1' and c.get('parent')=='1' and c.get('id') not in tables:
  root.remove(c)
def node(id,value,x,y,w,h,style):
 c=E.SubElement(root,'mxCell',id=id,value=value,vertex='1',parent='1',style=style)
 E.SubElement(c,'mxGeometry',x=str(x),y=str(y),width=str(w),height=str(h),attrib={'as':'geometry'})
 return c
node('review-title','BREWLEARN — ERD TỔNG THỂ',45,20,2600,48,'text;fontSize=34;fontStyle=1;align=left;fontColor=#17374f;')
node('review-note','18 thực thể • 36 quan hệ trực tiếp • Số trên đường nối tương ứng bảng quan hệ bên phải',45,76,3300,36,'text;fontSize=21;align=left;')
routes=[]; seen=set()
badge_positions=[]
for edge in list(root):
 if edge.get('edge')!='1':continue
 a,b=edge.get('source'),edge.get('target'); col=edge.get('value').split(' · ')[0]
 num,role=index[a,b,col]; seen.add(num)
 style=edge.get('style'); opt='startArrow=ERzeroToOne' in style; unique='endArrow=ERzeroToOne' in style
 edge.set('value','')
 edge.set('tooltip',f'{num:02d}. {names.get(a,a)} → {names.get(b,b)}: {role}; cột triển khai: {col}')
 edge.set('style',style+'strokeColor=#52677b;strokeWidth=2;jumpStyle=arc;jumpSize=12;fontSize=17;fontStyle=1;labelBackgroundColor=#ffffff;labelBorderColor=#b8c9d6;')
 def frac(key):return float(re.search(key+r'=([^;]+)',style).group(1))
 x,y,w,h=rects[a]; xx,yy,ww,hh=rects[b]
 pts=[(x+w*frac('exitX'),y+h*frac('exitY'))]+[(float(p.get('x')),float(p.get('y'))) for p in edge.findall('.//mxPoint')]+[(xx+ww*frac('entryX'),yy+hh*frac('entryY'))]
 routes.append((num,pts,opt,unique))
 candidates=[]
 for p,q in zip(pts,pts[1:]):
  length=abs(p[0]-q[0])+abs(p[1]-q[1])
  if length<65:continue
  for f in [.5,.35,.65,.2,.8]:
   bx=p[0]+(q[0]-p[0])*f;by=p[1]+(q[1]-p[1])*f
   if any(x-24<bx<x+w+24 and y-20<by<y+h+20 for x,y,w,h in rects.values()):continue
   sep=min([abs(bx-u)+abs(by-v) for u,v in badge_positions] or [200])
   candidates.append((min(sep,180)+min(length,500)*.05,bx,by))
 assert candidates,(num,'no label space')
 _,bx,by=max(candidates);badge_positions.append((bx,by))
 node(f'badge-{num}',f'{num:02d}',bx-18,by-14,36,28,'rounded=1;arcSize=15;align=center;verticalAlign=middle;fillColor=#ffffff;strokeColor=#b8c9d6;fontSize=17;fontStyle=1;')
assert seen==set(range(1,37)) and len(tables)==18
hits=[];segments=[];overlaps=[]
for num,pts,_,_ in routes:
 for a,b in zip(pts,pts[1:]):
  segments.append((num,a,b))
  for t,(x,y,w,h) in rects.items():
   if a[0]==b[0] and x+.1<a[0]<x+w-.1 and max(min(a[1],b[1]),y+.1)<min(max(a[1],b[1]),y+h-.1):hits.append((num,t))
   if a[1]==b[1] and y+.1<a[1]<y+h-.1 and max(min(a[0],b[0]),x+.1)<min(max(a[0],b[0]),x+w-.1):hits.append((num,t))
for i,(n,a,b) in enumerate(segments):
 for m,c,e in segments[i+1:]:
  if n==m:continue
  if a[0]==b[0]==c[0]==e[0] and max(min(a[1],b[1]),min(c[1],e[1]))<min(max(a[1],b[1]),max(c[1],e[1])):overlaps.append((n,m))
  if a[1]==b[1]==c[1]==e[1] and max(min(a[0],b[0]),min(c[0],e[0]))<min(max(a[0],b[0]),max(c[0],e[0])):overlaps.append((n,m))
print('Table intersections:',hits,'Shared segments:',overlaps)
assert not hits and not overlaps
LX=4160
node('key-title','ĐỐI CHIẾU QUAN HỆ',LX,150,1120,50,'text;fontSize=28;fontStyle=1;align=left;fontColor=#244b68;')
for i,(a,b,c,role) in enumerate(spec):
 num=i+1
 text=f'{num:02d}   {names.get(a,a)} → {names.get(b,b)}\n        {role}'
 node(f'key-{num}',text,LX,215+i*69,1130,64,'rounded=0;whiteSpace=wrap;align=left;spacingLeft=12;fontSize=18;fillColor='+('#eef4f8' if i%2==0 else '#ffffff')+';strokeColor=none;')
notes=['PK: khóa chính; FK: tham chiếu logic; NN: bắt buộc; NULL: cho phép trống.', 'Crow’s Foot: vòng tròn = 0; vạch = 1; chân quạ = nhiều. Cầu vượt KHÔNG phải điểm nối.', '27 và 32: một–một tùy chọn. 28: một–nhiều theo schema (chưa có UNIQUE).', 'Ba quan hệ N:N đã được biểu diễn qua bảng liên kết; không vẽ thêm đường trực tiếp.', 'Tên thực thể Việt hóa trên ERD; liên kết logic không đồng nghĩa đã có FOREIGN KEY trong MySQL.', 'Tham chiếu dự đoán ở quan hệ 26 không có nghĩa hệ thống tự tạo yêu cầu đào tạo lại.']
for i,t in enumerate(notes):node(f'note-{i}',t,LX,2750+i*40,1130,38,'text;align=left;fontSize=17;whiteSpace=wrap;')
model=tree.find('.//mxGraphModel');model.set('pageWidth','5350');model.set('pageHeight','3200')
tree.getroot().set('pages','1');tree.getroot().find('diagram').set('name','ERD tổng thể — 18 thực thể, 36 quan hệ')
E.indent(tree);out=B/'BrewLearn-ERD-fix-review.xml';tree.write(out,encoding='utf-8',xml_declaration=True)
(B/'BrewLearn-ERD-fix-review.drawio').write_bytes(out.read_bytes())
# Render from the exact saved geometry for visual inspection.
im=Image.new('RGB',(5350,3200),'white');d=ImageDraw.Draw(im)
def font(n,b=False):return ImageFont.truetype('C:/Windows/Fonts/'+('arialbd.ttf' if b else 'arial.ttf'),n)
def segcross(a,b,c,e):
 if a[1]==b[1] and c[0]==e[0] and min(a[0],b[0])+13<c[0]<max(a[0],b[0])-13 and min(c[1],e[1])+13<a[1]<max(c[1],e[1])-13:return c[0],a[1]
crossings=0
for num,pts,opt,unique in routes:
 for a,b in zip(pts,pts[1:]):d.line([a,b],fill='#52677b',width=2)
for num,pts,opt,unique in routes:
 for a,b in zip(pts,pts[1:]):
  for num2,other,_,_ in routes:
   if num==num2:continue
   for c,e in zip(other,other[1:]):
    p=segcross(a,b,c,e)
    if p:
     x,y=p;crossings+=1;d.line([(x-11,y),(x+11,y)],fill='white',width=7);d.arc((x-11,y-11,x+11,y+11),180,360,fill='#52677b',width=2)
def endmark(p,q,optional,many):
 import math
 dx,dy=q[0]-p[0],q[1]-p[1];n=math.hypot(dx,dy);ux,uy=dx/n,dy/n
 def point(t,o=0):return p[0]+ux*t-uy*o,p[1]+uy*t+ux*o
 if many:
  for o in [-9,0,9]:d.line([point(18),point(0,o)],fill='#52677b',width=2)
 else:d.line([point(10,-8),point(10,8)],fill='#52677b',width=2)
 if optional:
  x,y=point(28);d.ellipse((x-5,y-5,x+5,y+5),fill='white',outline='#52677b',width=2)
 else:d.line([point(23,-8),point(23,8)],fill='#52677b',width=2)
for _,pts,opt,unique in routes:endmark(pts[0],pts[1],opt,False);endmark(pts[-1],pts[-2],True,not unique)
for c in root:
 if c.get('vertex')!='1' or c.get('id') in tables:continue
 g=c.find('mxGeometry')
 if g is None:continue
 x,y,w,h=[float(g.get(k,0)) for k in ['x','y','width','height']]
 if c.get('parent') in rects:
  px,py,_,_=rects[c.get('parent')];x+=px;y+=py
 style=c.get('style','');val=html.unescape(re.sub('<[^>]*>','',c.get('value','')))
 fc=re.search(r'fillColor=(#[0-9a-fA-F]+)',style)
 if fc:d.rectangle((x,y,x+w,y+h),fill=fc.group(1))
 if c.get('parent') in rects and not c.get('id').endswith('-head'):d.rectangle((x,y,x+w,y+h),outline='#d6e2ee')
 sz=int(float(re.findall(r'fontSize=([\d.]+)',style)[-1])) if 'fontSize=' in style else 17
 color='white' if c.get('id').endswith('-head') else '#17374f'
 if c.get('id').endswith('-head'):
  sz=23
  while d.textlength(val,font=font(sz,True))>w-16:sz-=1
 d.multiline_text((x+10,y+6),val,font=font(sz,'fontStyle=1' in style),fill=color,spacing=5)
for (num,pts,_,_),(x,y) in zip(routes,badge_positions):
 d.rounded_rectangle((x-17,y-13,x+17,y+13),radius=4,fill='white',outline='#b8c9d6');d.text((x-11,y-10),f'{num:02d}',font=font(17,True),fill='#17374f')
im.save(B/'BrewLearn-ERD-fix-review.png')
im.resize((2006,1200)).save(B/'BrewLearn-ERD-fix-review-preview.png')
print(f'Validated: {len(tables)} entities; {len(routes)} distinct relationships; {crossings} bridge crossings. XML and PNG exported.')
