from pathlib import Path
import xml.etree.ElementTree as E
import json,re,html
B=Path(__file__).parent
ns={'__file__':str(B/'erd_giu_bo_cuc_goc.py')}
exec((B/'erd_giu_bo_cuc_goc.py').read_text('utf-8').split('exec(compile',1)[0],ns)
source=ns['source'];labels=ns['labels']
english_tables={'users':'nguoi_dung','notifications':'thong_bao','user_lesson_progresses':'tien_do_bai_hoc','retraining_rounds':'vong_dao_tao_lai'}
titles={t:english_tables.get(t,t) for t in ns['meta']}
source=source.replace('#3787c9','#335d83')
start=source.index('for t,(x,y,w,h) in rects.items():')
end=source.index("\ntxt(80,30,",start)
source=source[:start]+'''for t,(x,y,w,h) in rects.items():
    node(t,'',x,y,w,h,'group;')
    node(t+'-head',titles[t],0,0,w,HEADER,'rounded=0;fillColor=#335d83;strokeColor=#335d83;fontColor=#ffffff;fontSize=23;fontStyle=1;align=center;',t)
    draw.rectangle((x,y,x+w,y+HEADER),fill='#335d83')
    tw=draw.textlength(titles[t],font=f(23,True));txt(x+(w-tw)/2,y+10,titles[t],23,True,'#ffffff')
    for i,col in enumerate(data[t]['columns']):
        name=col['name'];value=labels[(t,name)];left,tail=value.split(' : ');dtype,null=tail.split(' | ')
        ry=HEADER+i*ROW;fill='#eef4fa' if i%2==0 else '#ffffff'
        draw.rectangle((x,y+ry,x+w,y+ry+ROW),fill=fill,outline='#d6e2ee')
        for xx in (x+325,x+465):draw.line([(xx,y+ry),(xx,y+ry+ROW)],fill='#c3d3e2')
        size=16
        while draw.textlength(left,font=f(size))>310:size-=1
        assert size>=10,(t,name,left)
        for suffix,label,xx,ww,fs in [('name',left,0,325,size),('type',dtype,325,140,14),('null','NULL' if null=='NULL' else 'NN',465,55,14)]:
            node(t+'-'+name+'-'+suffix,label,xx,ry,ww,ROW,f'rounded=0;fillColor={fill};strokeColor=#d6e2ee;align=left;spacingLeft=7;fontSize={fs};',t)
            txt(x+xx+7,y+ry+6,label,fs,False,'#172e46')
        # Native names remain accessible for audit without changing display labels.
        for cell in graph.findall('mxCell')[-3:]:cell.set('tooltip',t+'.'+name+' : '+col['type']+' | '+null)
''' +source[end:]
source=source.replace('PK: khóa chính · FK: tham chiếu ở mức logic · NULL: cho phép rỗng · NOT NULL: bắt buộc','PK: khóa chính · FK: tham chiếu logic · NN: NOT NULL · NULL: cho phép rỗng · Cầu vượt không phải điểm nối')
source=source.replace('Ngày vào làm được giữ trong thực thể Nhân viên / tài khoản; cột thực tế là joined_at.','nguoi_dung.ngay_vao_lam là nhãn của users.joined_at; tên bảng và cột tiếng Việt gốc được giữ nguyên.')
source=source.replace('BrewLearn-ERD-bo-cuc-goc-da-sua','BrewLearn-ERD-chot-dinh-danh')
env={'__file__':str(B/'render_erd_connected.py'),'labels':labels,'titles':titles}
exec(compile(source,'final-erd','exec'),env)
out=B/'BrewLearn-ERD-chot-dinh-danh.drawio';(B/'BrewLearn-ERD-chot-dinh-danh.xml').write_bytes(out.read_bytes())
# Scalable export with matching layout and bridging at horizontal crossings.
W,H=env['W'],env['H'];routes=env['routes'];rects=env['rects']
svg=[f'<svg xmlns="http://www.w3.org/2000/svg" width="{W}" height="{H}" viewBox="0 0 {W} {H}"><rect width="100%" height="100%" fill="white"/>']
def text(x,y,s,size=16,color='#172e46',anchor='start',bold=False):
    svg.append(f'<text x="{x}" y="{y}" text-anchor="{anchor}" font-family="Arial" font-size="{size}" fill="{color}" font-weight="{"bold" if bold else "normal"}">{html.escape(s)}</text>')
def endmark(p,q,optional,many):
    import math
    dx,dy=q[0]-p[0],q[1]-p[1];n=math.hypot(dx,dy);ux,uy=dx/n,dy/n
    def point(d,o=0):return (p[0]+ux*d-uy*o,p[1]+uy*d+ux*o)
    def line(a,b):svg.append(f'<path d="M{a[0]} {a[1]} L{b[0]} {b[1]}" stroke="#454545" fill="none" stroke-width="2"/>')
    if many:
        for o in (-10,0,10):line(point(18),point(0,o))
        c=point(29)
    else:line(point(9,-9),point(9,9));c=point(22)
    if optional:svg.append(f'<circle cx="{c[0]}" cy="{c[1]}" r="5" fill="white" stroke="#454545" stroke-width="2"/>')
    elif not many:line(point(17,-9),point(17,9))
for key,fields,pts in routes:
    for a,b in zip(pts,pts[1:]):
        crosses=[]
        if a[1]==b[1]:
            for _,_,other in routes:
                if other is pts:continue
                for c,d in zip(other,other[1:]):
                    if c[0]==d[0] and min(a[0],b[0])+10<c[0]<max(a[0],b[0])-10 and min(c[1],d[1])<a[1]<max(c[1],d[1]):crosses.append(c[0])
        path=f'M{a[0]} {a[1]}'
        sign=1 if b[0]>a[0] else -1
        for x in sorted(set(crosses),reverse=sign<0):path+=f' L{x-8*sign} {a[1]} Q{x} {a[1]-13} {x+8*sign} {a[1]}'
        path+=f' L{b[0]} {b[1]}'
        svg.append(f'<path d="{path}" stroke="white" stroke-width="7" fill="none"/><path d="{path}" stroke="#555" stroke-width="2" fill="none"/>')
    endmark(pts[0],pts[1],key[2],False);endmark(pts[-1],pts[-2],True,not key[3])
for t,(x,y,w,h) in rects.items():
    svg.append(f'<rect x="{x}" y="{y}" width="{w}" height="44" fill="#335d83"/>');text(x+w/2,y+30,titles[t],23,'white','middle',True)
    for i,col in enumerate(env['data'][t]['columns']):
        left,tail=labels[t,col['name']].split(' : ');dtype,null=tail.split(' | ');yy=y+44+i*27;fill='#eef4fa' if i%2==0 else '#fff'
        svg.append(f'<rect x="{x}" y="{yy}" width="{w}" height="27" fill="{fill}" stroke="#d6e2ee"/>')
        for xx in (x+325,x+465):svg.append(f'<path d="M{xx} {yy} v27" stroke="#c3d3e2"/>')
        size=16
        while env['draw'].textlength(left,font=env['f'](size))>310:size-=1
        text(x+7,yy+19,left,size);text(x+332,yy+19,dtype,14);text(x+472,yy+19,'NULL' if null=='NULL' else 'NN',14)
text(80,65,'SƠ ĐỒ MỐI QUAN HỆ THỰC THỂ BREWLEARN',38,bold=True)
text(80,115,'PK: khóa chính · FK: tham chiếu logic · NN: NOT NULL · NULL: cho phép rỗng',23)
for i,s in enumerate(env['notes']):text(80,3810+i*31,s,20)
svg.append('</svg>');(B/'BrewLearn-ERD-chot-dinh-danh.svg').write_text(''.join(svg),encoding='utf-8')
# Validate original Vietnamese identifiers and explicit data types.
rendered=E.parse(out);allcells={c.get('id'):c for c in rendered.iter('mxCell')}
for t in env['data']:
    assert allcells[t+'-head'].get('value')==english_tables.get(t,t)
assert 'DiemChuan' in allcells['bai_kiem_tra-DiemChuan-name'].get('value')
assert 'ngay_vao_lam' in allcells['users-joined_at-name'].get('value')
assert all('?' not in c.get('value','') for c in allcells.values())
print('PASS: native Vietnamese table identifiers preserved, three columns, original layout, PNG/XML/SVG exported.')
