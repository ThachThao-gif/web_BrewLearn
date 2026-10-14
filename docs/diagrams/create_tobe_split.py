from pathlib import Path
import xml.etree.ElementTree as E
from PIL import Image, ImageDraw, ImageFont
import math

OUT=Path(__file__).parent
BASE='html=1;whiteSpace=wrap;fontFamily=Arial;fontSize=16;strokeColor=#334155;fillColor=#FFFFFF;'
FONT='C:/Windows/Fonts/arial.ttf'
class Chart:
    def __init__(self,id,title,lanes,height):
        self.file=E.Element('mxfile',host='app.diagrams.net',version='26.0.0',type='device')
        diagram=E.SubElement(self.file,'diagram',id=id,name=title)
        model=E.SubElement(diagram,'mxGraphModel',grid='1',gridSize='10',page='1',pageWidth='1800',pageHeight=str(height))
        self.root=E.SubElement(model,'root')
        E.SubElement(self.root,'mxCell',id='0');E.SubElement(self.root,'mxCell',id='1',parent='0')
        self.nodes={};self.edges=[];self.lanes=lanes;self.height=height
        self.raw('pool',title,30,60,1740,lanes[-1][2]-60,'swimlane;horizontal=0;startSize=45;collapsible=0;'+BASE)
        for name,y,bottom,label in lanes:
            self.raw(name,label,45,y-60,1695,bottom-y,'swimlane;horizontal=0;startSize=45;collapsible=0;'+BASE,'pool')
    def raw(self,id,label,x,y,w,h,style,parent='1'):
        c=E.SubElement(self.root,'mxCell',id=id,value=label,style=style,vertex='1',parent=parent)
        E.SubElement(c,'mxGeometry',x=str(x),y=str(y),width=str(w),height=str(h),attrib={'as':'geometry'})
    def n(self,id,label,x,y,w=190,h=80,kind='task',lane=None):
        lane=lane or next(n for n,a,b,l in self.lanes if a<=y<b)
        oy=next(a for n,a,b,l in self.lanes if n==lane)
        style={'task':'rounded=1;arcSize=12;','gate':'rhombus;','start':'ellipse;aspect=fixed;','end':'ellipse;aspect=fixed;strokeWidth=3;','link':'ellipse;aspect=fixed;double=1;'}[kind]+BASE
        if kind=='end':style+='fillColor=#334155;'
        if kind=='link':style+='fontSize=13;fontStyle=1;'
        self.raw(id,'×' if kind=='gate' else '' if kind=='link' else label,x-75,y-oy,w,h,style,lane)
        if kind=='link':
            fill='#FFFFFF' if id.startswith('catch') else '#334155'
            self.raw(id+'-arrow','',x-75+w/2-9,y-oy+h/2-6,18,12,'shape=singleArrow;direction=east;fillColor='+fill+';strokeColor=#334155;',lane)
            self.raw(id+'-label',label,x-75-10,y-oy-23,w+20,20,'text;html=1;align=center;fontSize=14;fontStyle=1;',lane)
        self.nodes[id]=(x,y,w,h,label,kind)
    def question(self,id,text):
        x,y,w,h,_,_=self.nodes[id]
        self.raw(id+'-question',text,x+w/2-90,y-28,180,25,'text;html=1;align=center;fontFamily=Arial;fontSize=14;fontStyle=1;')
    def e(self,id,s,t,sp,tp,label='',points=()):
        c=E.SubElement(self.root,'mxCell',id=id,source=s,target=t,value=label,edge='1',parent='1',
            style=f'edgeStyle=orthogonalEdgeStyle;rounded=0;html=1;endArrow=block;endFill=1;fontSize=14;labelBackgroundColor=#FFFFFF;strokeColor=#334155;exitX={sp[0]};exitY={sp[1]};entryX={tp[0]};entryY={tp[1]};')
        g=E.SubElement(c,'mxGeometry',relative='1',attrib={'as':'geometry'})
        if points:
            a=E.SubElement(g,'Array',attrib={'as':'points'})
            for x,y in points:E.SubElement(a,'mxPoint',x=str(x),y=str(y))
        def port(n,p):
            x,y,w,h,_,_=self.nodes[n];return(x+w*p[0],y+h*p[1])
        path=[port(s,sp),*points,port(t,tp)]
        for a,b in zip(path,path[1:]):assert a[0]==b[0] or a[1]==b[1],(id,a,b)
        self.edges.append((id,path,label,s,t))
    def note(self,text,y):
        self.raw('note',text,80,y,1640,self.height-y-20,'rounded=1;whiteSpace=wrap;html=1;align=left;spacing=12;fillColor=#FFF2CC;strokeColor=#D6B656;fontSize=15;')
        self.notetext=text;self.notey=y
    def save(self,name):
        errors=[]
        for id,path,label,s,t in self.edges:
            for (ax,ay),(bx,by) in zip(path,path[1:]):
                for nid,(x,y,w,h,_,_) in self.nodes.items():
                    if nid in (s,t):continue
                    if ax==bx and x<ax<x+w and max(min(ay,by),y)<min(max(ay,by),y+h):errors.append((id,'through',nid))
                    if ay==by and y<ay<y+h and max(min(ax,bx),x)<min(max(ax,bx),x+w):errors.append((id,'through',nid))
        for i,(id,path,_,s,t) in enumerate(self.edges):
            for jd,q,_,u,v in self.edges[i+1:]:
                if {s,t}&{u,v}:continue
                for (ax,ay),(bx,by) in zip(path,path[1:]):
                    for (cx,cy),(dx,dy) in zip(q,q[1:]):
                        if ax==bx and cy==dy and min(cx,dx)<ax<max(cx,dx) and min(ay,by)<cy<max(ay,by):errors.append((id,'cross',jd))
                        if ay==by and cx==dx and min(ax,bx)<cx<max(ax,bx) and min(cy,dy)<ay<max(cy,dy):errors.append((id,'cross',jd))
                        if ax==bx==cx==dx and max(min(ay,by),min(cy,dy))<min(max(ay,by),max(cy,dy)):errors.append((id,'overlap',jd))
                        if ay==by==cy==dy and max(min(ax,bx),min(cx,dx))<min(max(ax,bx),max(cx,dx)):errors.append((id,'overlap',jd))
        assert not errors,errors
        E.indent(self.file,space='  ')
        (OUT/(name+'.xml')).write_bytes(E.tostring(self.file,encoding='utf-8',xml_declaration=True))
        self.render(name)
    def render(self,name):
        im=Image.new('RGB',(1800,self.height),'white');d=ImageDraw.Draw(im)
        f=ImageFont.truetype(FONT,16);small=ImageFont.truetype(FONT,14)
        for n,y,b,l in self.lanes:
            d.rectangle((30,y,1770,b),outline='#64748b',width=2)
            d.text((35,y+10),l,font=small,fill='#334155')
        d.text((80,20),self.file.find('diagram').get('name'),font=ImageFont.truetype(FONT,22),fill='#0f172a')
        for id,path,label,s,t in self.edges:
            d.line(path,fill='#334155',width=2)
            a,b=path[-2:];theta=math.atan2(b[1]-a[1],b[0]-a[0]);p=[b,(b[0]-11*math.cos(theta-.45),b[1]-11*math.sin(theta-.45)),(b[0]-11*math.cos(theta+.45),b[1]-11*math.sin(theta+.45))];d.polygon(p,fill='#334155')
            if label:
                a,b=max(zip(path,path[1:]),key=lambda ab:abs(ab[0][0]-ab[1][0])+abs(ab[0][1]-ab[1][1]));x=(a[0]+b[0])/2;y=(a[1]+b[1])/2
                box=d.textbbox((x,y),label,font=small);d.rectangle((box[0]-3,box[1]-3,box[2]+3,box[3]+3),fill='white');d.text((x,y),label,font=small,fill='#334155')
        for id,(x,y,w,h,label,kind) in self.nodes.items():
            if kind=='gate':d.polygon([(x+w/2,y),(x+w,y+h/2),(x+w/2,y+h),(x,y+h/2)],fill='white',outline='#334155');d.text((x+w/2-6,y+h/2-10),'X',font=f,fill='#334155')
            elif kind in ['start','end','link']:
                d.ellipse((x,y,x+w,y+h),fill='#334155' if kind=='end' else 'white',outline='#334155',width=3 if kind=='end' else 2)
                if kind=='link':d.ellipse((x+4,y+4,x+w-4,y+h-4),outline='#334155')
                if kind=='link':
                    cx=x+w/2;cy=y+h/2
                    d.line((cx-8,cy,cx+8,cy),fill='#334155',width=3)
                    d.polygon([(cx+8,cy),(cx+1,cy-5),(cx+1,cy+5)],fill='#334155')
                    d.text((x,y-22),label,font=small,fill='#334155')
            else:d.rounded_rectangle((x,y,x+w,y+h),radius=10,fill='white',outline='#334155',width=2)
            if label and kind not in ['gate','link']:
                lines=label.split('\n');lineh=19
                for j,l in enumerate(lines):
                    width=d.textlength(l,font=small if kind=='link' else f)
                    d.text((x+(w-width)/2,y+(h-len(lines)*lineh)/2+j*lineh),l,font=small if kind=='link' else f,fill='#334155')
        d.rounded_rectangle((80,self.notey,1720,self.height-20),radius=10,fill='#fff2cc',outline='#d6b656')
        d.multiline_text((95,self.notey+12),self.notetext,font=small,fill='#334155',spacing=5)
        im.save(OUT/(name+'-preview.png'))

R=(1,.5);L=(0,.5);T=(.5,0);B=(.5,1)
a=Chart('evaluation','2.2.3 TO-BE — Đánh giá và phê duyệt',[('employee',60,230,'NHÂN VIÊN'),('leader',230,620,'TRƯỞNG CA'),('manager',620,1010,'QUẢN LÝ CỬA HÀNG')],1160)
a.n('start','',120,315,30,30,'start');a.n('ready','',220,305,50,50,'gate')
a.n('schedule','Tạo lịch đánh giá\nLưu ChoDanhGia\nvà thông báo',340,290,190,80)
a.n('receive','Nhận thông báo;\ntham gia đúng lịch',620,110,190,80)
a.n('score','Chấm 5 tiêu chí; nhận xét\nChưa đạt: kèm đề xuất',880,290,200,80)
a.n('submit','Gửi kết quả\nChoPheDuyet',1120,290,190,80)
a.n('wait','Chưa tạo lịch; chờ NV\nhoàn thành học / kiểm tra',150,465,220,80)
a.n('waitend','',405,490,30,30,'end')
a.n('review','Xem xét kết quả\nvà nội dung đề xuất',1120,650,190,80)
a.n('approve','',1390,665,50,50,'gate')
a.n('return','Bổ sung đánh giá\nbị trả lại',1320,470,190,80)
a.n('approved','Phê duyệt: DaDuyet\nThông báo kết quả cho NV',1310,770,210,80)
a.n('result','',1390,900,50,50,'gate')
a.n('passed','Kết quả đạt\nKết thúc lượt đánh giá',1510,885,190,80)
a.n('end','',1720,910,30,30,'end')
a.n('request','Tạo yêu cầu đào tạo lại\nDaDuyet / ChoGiao',1100,885,220,80)
a.n('next','',990,910,30,30,'end')
a.e('e1','start','ready',R,L)
a.e('e2','ready','schedule',R,L,'Có')
a.e('e3','ready','wait',B,T,'Chưa đủ',[(245,430),(260,430)])
a.e('e4','wait','waitend',R,L)
a.e('e5','schedule','receive',R,L,points=[(575,330),(575,150)])
a.e('e6','receive','score',R,T,points=[(980,150)])
a.e('e7','score','submit',R,L)
a.e('e8','submit','review',B,T)
a.e('e10','review','approve',R,L)
a.e('e11','approve','return',T,B,'Trả lại + lý do (TraLai)')
a.e('e12','return','submit',T,T,'Gửi lại ChoPheDuyet',[(1415,250),(1215,250)])
a.e('e13','approve','approved',B,T,'Duyệt')
a.e('e14','approved','result',B,T)
a.e('e15','result','passed',R,L,'Đạt')
a.e('e16','passed','end',R,L)
a.e('e17','result','request',L,R,'Chưa đạt')
a.e('e18','request','next',L,R)
a.question('ready','Đủ điều kiện tạo lịch?')
a.question('approve','Duyệt hay trả lại?')
a.question('result','Kết quả đã duyệt đạt?')
a.note('Gateway đầu: đủ điều kiện học / quiz, đúng phạm vi, không có lượt đánh giá đang xử lý.\nĐiểm đạt: tổng từ 80 và từng tiêu chí từ 70. Duyệt khác với đạt; chưa đạt đã duyệt mới tạo yêu cầu đào tạo lại.\nTrả lại: trưởng ca bổ sung rồi gửi lại ChoPheDuyet; không chuyển trực tiếp TraLai → DaDuyet.\nYêu cầu DaDuyet / ChoGiao chuyển sang quy trình 2.2.4. NV chỉ xem kết quả chính thức sau duyệt.',1030)
a.save('BrewLearn-TOBE-2.2.3-Danh-gia-Phe-duyet')

b=Chart('retraining','2.2.4 TO-BE — Đào tạo lại và đánh giá lại',[('employee',60,270,'NHÂN VIÊN'),('leader',270,680,'TRƯỞNG CA / NGƯỜI PHỤ TRÁCH'),('manager',680,1130,'QUẢN LÝ CỬA HÀNG')],1300)
b.n('start','',120,750,30,30,'start');b.n('assign','Giao kế hoạch vòng n\nNgười phụ trách, hạn,\nnội dung học / quiz',230,725,210,80)
b.n('learn','Học lại / thực hành\nLàm quiz được yêu cầu',520,125,200,80)
b.n('confirm','Người được phân công\nxác nhận hoàn thành',800,420,200,80)
b.n('ready','',1080,740,50,50,'gate')
b.n('incomplete','Bổ sung phần còn thiếu\nChưa tạo lịch đánh giá lại',1010,550,200,70)
b.n('schedule','Tạo lịch đánh giá lại\nVòng: ChoDanhGiaLai\nĐánh giá: ChoDanhGia',1190,725,190,80)
b.n('score','Chấm đánh giá lại;\nchưa đạt: kèm đề xuất\nGửi ChoPheDuyet',1480,410,200,90)
b.n('review','Xem xét kết quả\nđánh giá lại',1480,880,200,80)
b.n('approve','',1330,905,50,50,'gate')
b.n('returned','Bổ sung đánh giá bị trả lại\nGửi lại ChoPheDuyet',1250,550,200,70)
b.n('approved','Duyệt đánh giá: DaDuyet\nLưu kết quả của vòng',1070,880,180,80)
b.n('result','',950,905,50,50,'gate')
b.n('continue','Ghi nhận kết quả chưa đạt;\nchuyển yêu cầu về chờ giao\nkế hoạch vòng tiếp theo',650,880,220,80)
b.n('complete','Vòng n: DaDuyet / Dat\nYêu cầu: HoanThanh / HoanTat',870,1010,250,80)
b.n('end','',1160,1035,30,30,'end')
b.e('e1','start','assign',R,L)
b.e('e3','assign','learn',R,L,points=[(465,765),(465,165)])
b.e('e5','learn','confirm',R,T,points=[(900,165)])
b.e('e6','confirm','ready',B,L,points=[(900,765)])
b.e('e7','ready','incomplete',T,B,'Chưa đủ',[(1105,655),(1110,655)])
b.e('e8','incomplete','learn',T,T,'Tiếp tục hoàn thành vòng hiện tại',[(1110,95),(620,95)])
b.e('e9','ready','schedule',R,L,'Có')
b.e('e10','schedule','score',T,T,points=[(1285,700),(1230,700),(1230,320),(1580,320)])
b.e('e12','score','review',B,T)
b.e('e13','review','approve',L,R,points=[(1420,920),(1420,930)])
b.e('e14','approve','returned',T,B,'Trả lại + lý do (TraLai)',[(1355,850),(1420,850),(1420,650),(1350,650)])
b.e('e15','returned','score',T,L,'Gửi lại cùng đánh giá',[(1350,455)])
b.e('e16','approve','approved',L,R,'Duyệt',[(1290,930),(1290,920)])
b.e('e17','approved','result',L,R,points=[(1030,920),(1030,930)])
b.e('e18','result','continue',L,R,'Chưa đạt',[(915,930),(915,920)])
b.e('e19','continue','assign',L,B,'QL chủ động giao vòng tiếp theo',[(335,920)])
b.e('e20','result','complete',B,T,'Đạt',[(975,990),(995,990)])
b.e('e21','complete','end',R,L)
b.question('ready','Đủ điều kiện đánh giá lại?')
b.question('approve','Duyệt hay trả lại?')
b.question('result','Kết quả đã duyệt đạt?')
b.note('Đầu vào: yêu cầu từ 2.2.3 đã được tạo tại DaDuyet / ChoGiao; không cần duyệt yêu cầu thêm lần nữa.\nĐiều kiện đánh giá lại: học / quiz mới theo yêu cầu vòng và xác nhận đúng người phụ trách; quiz đạt theo DiemChuan từng bài.\nChưa đủ điều kiện: hoàn thành vòng hiện tại. Trả lại: sửa cùng đánh giá, không tạo vòng mới. Chưa đạt đã duyệt: QL giao vòng tiếp theo.\nKhông giới hạn số vòng; chỉ đóng yêu cầu khi kết quả đạt đã được QL duyệt. Đổi người phụ trách / nội dung có thể phải xác nhận lại.\nNgười được phân công xác nhận; riêng chấm đánh giá lại thuộc Trưởng ca. status / result là trường của retraining_rounds.',1150)
b.save('BrewLearn-TOBE-2.2.4-Dao-tao-lai-Danh-gia-lai')
combined=E.Element('mxfile',host='app.diagrams.net',version='26.0.0',type='device')
for chart in [a,b]:combined.append(chart.file.find('diagram'))
E.indent(combined,space='  ')
(OUT/'BrewLearn-TOBE-2-BPMN-tach-rieng.drawio').write_bytes(E.tostring(combined,encoding='utf-8',xml_declaration=True))
print('Created 2 XML files, combined drawio, and internal previews. All edge segments orthogonal.')
