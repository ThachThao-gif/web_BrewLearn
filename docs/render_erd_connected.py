from pathlib import Path
import json, re, math, heapq, itertools
import xml.etree.ElementTree as ET
from PIL import Image, ImageDraw, ImageFont

B=Path(__file__).parent
data=json.loads((B/'erd-schema.json').read_text('utf-8'))
# Read relationship definitions without executing the earlier renderer.
source=(B/'render_erd.py').read_text('utf-8')
logical=eval(source.split('logical=',1)[1].split('\nfor key,target',1)[0],{'__builtins__':{}})
links={}
for t,m in data.items():
    for f in m['foreign_keys']:links[(t,f['col'])]=(f['target'],f['target_col'],'FK')
for k,v in logical.items():links.setdefault(k,(*v,'RL'))
positions={
'users':(1840,1460), 'vung':(1840,180), 'cua_hang':(1840,650),
'ca_lam_viec':(2480,1080), 'chuong_trinh_dao_tao':(680,850),
'bai_hoc':(680,1390), 'bai_kiem_tra':(680,1910),
'cau_hoi_kiem_tra':(80,2200), 'nhan_vien_khoa_hoc':(1180,400),
'user_lesson_progresses':(1240,2750), 'ket_qua_kiem_tra':(680,2750),
'danh_gia_nang_luc':(2840,1680), 'du_doan_nguy_co':(2840,630),
'yeu_cau_dao_tao_lai':(3740,1680), 'retraining_rounds':(3740,2840),
'loi_nghiep_vu':(2840,2790), 'notifications':(1840,2980),
'yeu_cau_ho_tro':(1840,3470)}
W,H=4460,3950; STEP=20; ROW=27; HEADER=44; BW=520
rects={t:(x,y,BW,HEADER+ROW*len(data[t]['columns'])) for t,(x,y) in positions.items()}
groups={}
for (child,col),(parent,pk,kind) in links.items():
    c=next(c for c in data[child]['columns'] if c['name']==col)
    indexes={}
    for idx in data[child]['indexes']:
        if not idx['non_unique']:indexes.setdefault(idx['name'],[]).append(idx['col'])
    uq=[col] in indexes.values()
    groups.setdefault((parent,child,c['nullable']=='YES',uq),[]).append((col,pk,kind))

blocked=set()
for x,y,w,h in rects.values():
    for gx in range(math.floor((x-16)/STEP),math.ceil((x+w+16)/STEP)+1):
        for gy in range(math.floor((y-16)/STEP),math.ceil((y+h+16)/STEP)+1):blocked.add((gx,gy))
used_segments=set();used_nodes={};route_ports=set()
maxx,maxy=W//STEP-2,H//STEP-3
def candidates(table):
    x,y,w,h=rects[table];out=[]
    for side in ['L','R','T','B']:
        for frac in [.1,.2,.3,.4,.5,.6,.7,.8,.9]:
            if side in ['L','R']:
                yy=round((y+h*frac)/STEP)*STEP;xx=x if side=='L' else x+w
                p=(round((x-40 if side=='L' else x+w+40)/STEP),yy//STEP)
            else:
                xx=round((x+w*frac)/STEP)*STEP;yy=y if side=='T' else y+h
                p=(xx//STEP,round((y-40 if side=='T' else y+h+40)/STEP))
            if p not in blocked and p not in route_ports:out.append((p,(xx,yy),side))
    return out
def segment(a,b):return tuple(sorted((a,b)))
def astar(start,goal):
    heap=[(0,0,start,-1)];cost={(start,-1):0};prev={};best=None
    while heap:
        _,g,p,d=heapq.heappop(heap)
        if g!=cost.get((p,d)):continue
        if p==goal:best=(p,d);break
        for nd,(dx,dy) in enumerate([(1,0),(-1,0),(0,1),(0,-1)]):
            q=(p[0]+dx,p[1]+dy)
            if not(2<=q[0]<=maxx and 7<=q[1]<=maxy) or q in blocked:continue
            if segment(p,q) in used_segments:continue
            orientation=nd//2
            if q in used_nodes:
                dirs=used_nodes[q]
                if len(dirs)>1 or orientation in dirs:continue
            # No turns at a crossing: a bridge can show the distinct routes.
            if p in used_nodes and d!=-1 and d//2!=orientation:continue
            added=1+(4 if d!=-1 and d!=nd else 0)+(180 if q in used_nodes else 0)
            ng=g+added;state=(q,nd)
            if ng<cost.get(state,10**12):
                cost[state]=ng;prev[state]=(p,d)
                heuristic=abs(q[0]-goal[0])+abs(q[1]-goal[1])
                heapq.heappush(heap,(ng+heuristic,ng,q,nd))
    if best is None:return None
    path=[];state=best
    while state in prev:path.append(state[0]);state=prev[state]
    path.append(start);path.reverse();return cost[best],path
routes=[]
# Route constrained local links first, then links to the central users table.
items=sorted(groups.items(),key=lambda e:(e[0][0]=='users', abs(positions[e[0][0]][0]-positions[e[0][1]][0])+abs(positions[e[0][0]][1]-positions[e[0][1]][1])))
for key,fields in items:
    parent,child,optional,unique=key
    pairs=[]
    for s in candidates(parent):
        for t in candidates(child):
            dist=abs(s[0][0]-t[0][0])+abs(s[0][1]-t[0][1])
            pairs.append((dist,s,t))
    pairs.sort(key=lambda v:v[0]);best=None
    for _,s,t in pairs[:8]:
        result=astar(s[0],t[0])
        if result:
            score,path=result
            if best is None or score<best[0]:best=(score,path,s,t)
            if score<180:break
    assert best is not None,('No route',key)
    _,path,s,t=best
    route_ports.update([s[0],t[0]])
    for a,b in zip(path,path[1:]):used_segments.add(segment(a,b))
    for i,p in enumerate(path):
        dirs=set()
        for q in path[max(0,i-1):i]+path[i+1:i+2]:dirs.add(0 if q[1]==p[1] else 1)
        used_nodes.setdefault(p,set()).update(dirs)
    points=[s[1]]+[(x*STEP,y*STEP) for x,y in path]+[t[1]]
    simplified=[points[0]]
    for a,b,c in zip(points,points[1:],points[2:]):
        if not(a[0]==b[0]==c[0] or a[1]==b[1]==c[1]):simplified.append(b)
    simplified.append(points[-1]);routes.append((key,fields,simplified))
    print('Routed',parent,'->',child,flush=True)

im=Image.new('RGB',(W,H),'white');draw=ImageDraw.Draw(im)
def f(size=20,bold=False):return ImageFont.truetype('C:/Windows/Fonts/arialbd.ttf' if bold else 'C:/Windows/Fonts/arial.ttf',size)
def txt(x,y,s,size=20,bold=False,color='#111111'):draw.text((x,y),s,font=f(size,bold),fill=color)
mx=ET.Element('mxfile',host='app.diagrams.net',type='device')
dia=ET.SubElement(mx,'diagram',id='connected-erd',name='ERD tổng thể liên kết trực tiếp')
model=ET.SubElement(dia,'mxGraphModel',grid='1',page='1',pageWidth=str(W),pageHeight=str(H))
graph=ET.SubElement(model,'root');ET.SubElement(graph,'mxCell',id='0');ET.SubElement(graph,'mxCell',id='1',parent='0')
def node(id,label,x,y,w,h,style,parent='1'):
    c=ET.SubElement(graph,'mxCell',id=id,value=label,vertex='1',parent=parent,style=style+'fontFamily=Arial;whiteSpace=wrap;')
    ET.SubElement(c,'mxGeometry',x=str(x),y=str(y),width=str(w),height=str(h),attrib={'as':'geometry'})
def crow(p,q,optional=False,many=False):
    dx=q[0]-p[0];dy=q[1]-p[1];length=math.hypot(dx,dy);ux,uy=dx/length,dy/length
    def point(d,o=0):return(p[0]+ux*d-uy*o,p[1]+uy*d+ux*o)
    if many:
        for o in [-10,0,10]:draw.line([point(18),point(0,o)],fill='#454545',width=2)
        circ=point(29)
    else:
        draw.line([point(9,-9),point(9,9)],fill='#454545',width=2);circ=point(22)
    if optional:draw.ellipse((circ[0]-5,circ[1]-5,circ[0]+5,circ[1]+5),fill='white',outline='#454545',width=2)
    elif not many:draw.line([point(17,-9),point(17,9)],fill='#454545',width=2)

for n,(key,fields,pts) in enumerate(routes):
    parent,child,optional,unique=key
    # Actual keys are labelled in the child table. Edge metadata lists every role.
    label='; '.join(col for col,_,_ in fields)
    p_rect=rects[parent];c_rect=rects[child]
    sx=(pts[0][0]-p_rect[0])/p_rect[2];sy=(pts[0][1]-p_rect[1])/p_rect[3]
    tx=(pts[-1][0]-c_rect[0])/c_rect[2];ty=(pts[-1][1]-c_rect[1])/c_rect[3]
    c=ET.SubElement(graph,'mxCell',id=f'rel-{n}',value='',edge='1',parent='1',source=parent,target=child,
        tooltip=label,style=f'edgeStyle=none;rounded=0;startArrow={"ERzeroToOne" if optional else "ERmandOne"};endArrow={"ERzeroToOne" if unique else "ERzeroToMany"};startFill=0;endFill=0;strokeColor=#454545;strokeWidth=1.5;jumpStyle=arc;jumpSize=10;exitX={sx};exitY={sy};exitPerimeter=0;entryX={tx};entryY={ty};entryPerimeter=0;')
    geo=ET.SubElement(c,'mxGeometry',relative='1',attrib={'as':'geometry'});arr=ET.SubElement(geo,'Array',attrib={'as':'points'})
    for x,y in pts[1:-1]:ET.SubElement(arr,'mxPoint',x=str(x),y=str(y))
    draw.line(pts,fill='#555555',width=2);crow(pts[0],pts[1],optional);crow(pts[-1],pts[-2],True,not unique)

# Make remaining perpendicular intersections unambiguous with a white bridge gap.
crossings=[]
for i,(_,_,a) in enumerate(routes):
    for j,(_,_,b) in enumerate(routes[:i]):
        for p,q in zip(a,a[1:]):
            for r,s in zip(b,b[1:]):
                if p[1]==q[1] and r[0]==s[0] and min(p[0],q[0])<r[0]<max(p[0],q[0]) and min(r[1],s[1])<p[1]<max(r[1],s[1]):
                    x,y=r[0],p[1];draw.rectangle((x-7,y-5,x+7,y+5),fill='white');draw.arc((x-8,y-8,x+8,y+8),180,360,fill='#555555',width=2);crossings.append((x,y))
                elif p[0]==q[0] and r[1]==s[1] and min(p[1],q[1])<r[1]<max(p[1],q[1]) and min(r[0],s[0])<p[0]<max(r[0],s[0]):
                    x,y=p[0],r[1];draw.rectangle((x-5,y-7,x+5,y+7),fill='white');draw.arc((x-8,y-8,x+8,y+8),270,450,fill='#555555',width=2);crossings.append((x,y))

for t,(x,y,w,h) in rects.items():
    node(t,'',x,y,w,h,'group;')
    node(t+'-head',t,0,0,w,HEADER,'rounded=0;fillColor=#fff0d3;strokeColor=#c79a42;fontSize=23;fontStyle=1;align=center;',t)
    draw.rectangle((x,y,x+w,y+h),fill='#fffcf3',outline='#c79a42',width=2);draw.rectangle((x,y,x+w,y+HEADER),fill='#fff0d3',outline='#c79a42',width=2)
    tw=draw.textlength(t,font=f(23,True));txt(x+(w-tw)/2,y+10,t,23,True)
    for i,col in enumerate(data[t]['columns']):
        name=col['name'];flags=[]
        if col['colkey']=='PRI':flags.append('PK')
        if (t,name) in links:flags.append(links[(t,name)][2])
        if col['colkey']=='UNI':flags.append('UQ')
        flag='/'.join(flags)
        dtype=col['type'];displaytype='enum' if dtype.startswith('enum(') else dtype
        display=f'{flag+" " if flag else ""}{name} : {displaytype}'+(' ?' if col['nullable']=='YES' else '')
        ry=HEADER+i*ROW
        node(t+'-'+name,display,0,ry,w,ROW,'text;align=left;spacingLeft=12;fontSize=18;',t)
        txt(x+12,y+ry+5,display,18,bool(flags), '#a23f24' if flags else '#222222')
        assert draw.textlength(display,font=f(18,bool(flags)))<w-20,(t,name,display)
txt(80,30,'SƠ ĐỒ MỐI QUAN HỆ THỰC THỂ BREWLEARN',38,True)
txt(80,90,'18 bảng xuất hiện một lần · Liên kết trực tiếp · Crow’s Foot · PK / FK / UQ · RL: liên kết theo code · ?: nullable',23)
node('title','SƠ ĐỒ MỐI QUAN HỆ THỰC THỂ BREWLEARN',80,20,4200,60,'text;align=left;fontSize=38;fontStyle=1;')
node('legend','18 bảng xuất hiện một lần · Liên kết trực tiếp · Crow’s Foot · PK / FK / UQ · RL: liên kết theo code · ?: nullable',80,85,4200,45,'text;align=left;fontSize=23;')
notes=['Quan hệ N:N được triển khai qua nhan_vien_khoa_hoc, user_lesson_progresses và ket_qua_kiem_tra.',
       'Một đường có thể đại diện nhiều cột cùng cặp bảng và cùng lực lượng quan hệ; xem tooltip trong Draw.io để biết các cột.',
       'Dự đoán không tự giao đào tạo lại. Cột nguoi_quan_ly_id ở vung còn tồn tại nhưng chưa xác nhận quan hệ nghiệp vụ.',
       f'Các đường có điểm vượt nhau dùng cầu nhảy, không phải điểm nối. Số vị trí vượt nhau: {len(set(crossings))}.']
for i,s in enumerate(notes):txt(80,3790+i*31,s,20);node('note'+str(i),s,80,3785+i*31,4250,31,'text;align=left;fontSize=20;')
ET.indent(mx);ET.ElementTree(mx).write(B/'BrewLearn-ERD-lien-ket-tong-the.drawio',encoding='utf-8',xml_declaration=True)
im.save(B/'BrewLearn-ERD-lien-ket-tong-the.png',dpi=(300,300))
print('Entities:',len(rects),'direct column relations:',len(links),'connector groups:',len(routes),'crossings:',len(set(crossings)))
