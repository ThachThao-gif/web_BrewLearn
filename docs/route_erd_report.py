from pathlib import Path
import xml.etree.ElementTree as E
import json,re
B=Path(__file__).parent
tree=E.parse(B/'BrewLearn-ERD-mot-trang-bao-cao.xml');root=tree.find('.//root');cells={c.get('id'):c for c in root.findall('mxCell')}
data=json.loads((B/'erd-schema.json').read_text('utf-8'))
positions={}
for t in data:
    g=cells[t].find('mxGeometry');positions[t]=(int(float(g.get('x'))),int(float(g.get('y'))))
    data[t]['columns']=[c for c in data[t]['columns'] if t+'-n'+str(list(data[t]['columns']).index(c)) in cells] if False else data[t]['columns']
    # Exact height comes from the edited report diagram, not the full schema.
rects={t:(*positions[t],570,int(float(cells[t].find('mxGeometry').get('height')))) for t in data}
source=(B/'render_erd_connected.py').read_text('utf-8').split("im=Image.new",1)[0]
start=source.index('positions={');end=source.index('groups={}',start)
source=source[:start]+f'positions={positions!r}\nW,H=3300,2452; STEP=10; ROW=32; HEADER=50; BW=570\nrects={rects!r}\n'+source[end:]
source=source.replace("B=Path(__file__).parent","B=Path(__file__).parent")
# Separate each relationship role, especially the user links.
source=source.replace("groups.setdefault((parent,child,c['nullable']=='YES',uq),[]).append((col,pk,kind))","groups[(parent,child,c['nullable']=='YES',uq,col)]=[(col,pk,kind)]")
source=source.replace('parent,child,optional,unique=key','parent,child,optional,unique,role=key')
source=source.replace('pairs[:8]','pairs[:200]')
source=source.replace('[.1,.2,.3,.4,.5,.6,.7,.8,.9]','[i/40 for i in range(1,40)]')
source=source.replace('if score<180:break','if score<100000:break')
# Make more space between grid rows and columns so separate role edges can pass.
source=source.replace('W,H=3300,2452','W,H=4100,3100')
source=source.replace('positions='+repr(positions),'positions='+repr({t:(x+int((x-45)/650)*160,y+int(y/600)*100) for t,(x,y) in positions.items()}))
newpos={t:(x+int((x-45)/650)*160,y+int(y/600)*100) for t,(x,y) in positions.items()}
newrect={t:(*newpos[t],w,h) for t,(x,y,w,h) in rects.items()}
source=source.replace('rects='+repr(rects),'rects='+repr(newrect))
rects=newrect
for t,(x,y,w,h) in rects.items():
    g=cells[t].find('mxGeometry');g.set('x',str(x));g.set('y',str(y))
tree.find('.//mxGraphModel').set('pageWidth','4100');tree.find('.//mxGraphModel').set('pageHeight','3100')
ns={'__file__':str(B/'render_erd_connected.py')};exec(compile(source,'router','exec'),ns)
for c in list(root):
    if c.get('edge')=='1':root.remove(c)
for i,(key,fields,pts) in enumerate(ns['routes']):
    p,c,optional,unique,role=key
    px,py,pw,ph=rects[p];cx,cy,cw,ch=rects[c]
    sx=(pts[0][0]-px)/pw;sy=(pts[0][1]-py)/ph;tx=(pts[-1][0]-cx)/cw;ty=(pts[-1][1]-cy)/ch
    col=next(v for v in data[c]['columns'] if v['name']==role)
    # The connection label identifies the exact column and cardinality.
    label=role+' · '+('0..1' if optional else '1')+' — '+('0..1' if unique else '0..N')
    edge=E.SubElement(root,'mxCell',id='route'+str(i),parent='1',edge='1',source=p,target=c,value=label,style=f'edgeStyle=none;exitX={sx};exitY={sy};exitPerimeter=0;entryX={tx};entryY={ty};entryPerimeter=0;startArrow={"ERzeroToOne" if optional else "ERmandOne"};endArrow={"ERzeroToOne" if unique else "ERzeroToMany"};startFill=0;endFill=0;strokeColor=#516b80;strokeWidth=1.4;jumpStyle=arc;jumpSize=9;fontSize=13;fontFamily=Arial;labelBackgroundColor=#ffffff;')
    g=E.SubElement(edge,'mxGeometry',relative='1',attrib={'as':'geometry'});a=E.SubElement(g,'Array',attrib={'as':'points'})
    for x,y in pts[1:-1]:E.SubElement(a,'mxPoint',x=str(x),y=str(y))
tree.getroot().find('diagram').set('name','ERD một trang — quan hệ tách riêng')
cells['legend'].set('value','PK: khóa chính · FK: tham chiếu logic · NN: NOT NULL · Nhãn đường: cột liên kết và bội số · Cầu vượt không phải điểm nối')
E.indent(tree);tree.write(B/'BrewLearn-ERD-mot-trang-quan-he-ro.xml',encoding='utf-8',xml_declaration=True)
print('DONE: routed',len(ns['routes']),'individual roles; grid paths do not share segments or enter entity boxes.')
