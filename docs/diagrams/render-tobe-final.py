from PIL import Image,ImageDraw,ImageFont
import xml.etree.ElementTree as ET
root=ET.parse('D:/wamp64/Brewlearn/docs/diagrams/BrewLearn-TO-BE.xml').getroot()
fp='C:/Windows/Fonts/arial.ttf';bp='C:/Windows/Fonts/arialbd.ttf'
def wrap(d,text,font,width):
 out=[]
 for para in text.split('\n'):
  line=''
  for word in para.split():
   temp=(line+' '+word).strip()
   if d.textlength(temp,font=font)>width and line:out.append(line);line=word
   else:line=temp
  out.append(line)
 return '\n'.join(out)
for index,page in enumerate(root.findall('diagram')):
 model=page.find('mxGraphModel');cells={c.get('id'):c for c in model.find('root')}
 W,H=int(model.get('pageWidth')),int(model.get('pageHeight'))
 im=Image.new('RGB',(W,H),'white');d=ImageDraw.Draw(im)
 def rect(cid):
  c=cells[cid];g=c.find('mxGeometry')
  if g is None:return 0,0,0,0
  x,y,w,h=[float(g.get(k,'0')) for k in ['x','y','width','height']]
  parent=c.get('parent')
  if parent not in [None,'0','1'] and parent in cells:
   px,py,_,_=rect(parent);x+=px;y+=py
  return x,y,w,h
 def style(c):
  return dict(t.split('=',1) for t in c.get('style','').split(';') if '=' in t)
 def drawtext(c,x,y,w,h,fill='#291b0a'):
  s=style(c);size=int(s.get('fontSize','15'));f=ImageFont.truetype(bp if s.get('fontStyle')=='1' else fp,size)
  txt=wrap(d,c.get('value',''),f,max(10,w-20));bb=d.multiline_textbbox((0,0),txt,font=f,spacing=5,align='center');tw,th=bb[2]-bb[0],bb[3]-bb[1]
  xx=x+10 if s.get('align')=='left' else x+(w-tw)/2
  d.multiline_text((xx,y+(h-th)/2-3),txt,font=f,fill=fill,spacing=5,align='left' if s.get('align')=='left' else 'center')
 for cid,c in cells.items():
  if c.get('vertex')!='1':continue
  if cid=='pool' or cid.startswith('lane'):
   x,y,w,h=rect(cid);s=style(c)
   d.rectangle((x,y,x+w,y+h),fill=s.get('swimlaneFillColor','#ffffff'),outline='#bca984',width=1)
   if cid=='pool':
    d.rectangle((x,y,x+w,y+40),fill='#fff8ec',outline='#bca984')
    d.text((x+120,y+10),c.get('value',''),font=ImageFont.truetype(bp,17),fill='#291b0a')
   else:
    d.rectangle((x,y,x+110,y+h),fill='#fffdf8',outline='#bca984')
    layer=Image.new('RGBA',(int(h),100),(255,255,255,0));ld=ImageDraw.Draw(layer);font=ImageFont.truetype(bp,14)
    text=c.get('value','');tw=ld.textlength(text,font=font);ld.text(((h-tw)/2,40),text,font=font,fill='#291b0a')
    im.paste(layer.rotate(90,expand=True),(int(x+5),int(y)),layer.rotate(90,expand=True))
 routes=[]
 for cid,c in cells.items():
  if c.get('edge')!='1':continue
  sx,sy,sw,sh=rect(c.get('source'));tx,ty,tw,th=rect(c.get('target'));s=style(c)
  start=(sx+sw*float(s.get('exitX','1')),sy+sh*float(s.get('exitY','.5')))
  end=(tx+tw*float(s.get('entryX','0')),ty+th*float(s.get('entryY','.5')))
  parent=c.get('parent');px,py,_,_=rect(parent) if parent in cells else (0,0,0,0)
  points=[start]+[(float(p.get('x'))+px,float(p.get('y'))+py) for p in c.findall('.//Array/mxPoint')]+[end]
  route=[points[0]]
  for pt in points[1:]:
   last=route[-1]
   if pt[0]!=last[0] and pt[1]!=last[1]:
    route.append((pt[0],last[1]))
   route.append(pt)
  route=[a for k,a in enumerate(route) if not k or a!=route[k-1]]
  routes.append((cid,route,c))
  d.line(route,fill='#4a4a4a',width=2)
  x,y=route[-1];xx,yy=route[-2]
  if x!=xx:
   sign=1 if x>xx else -1;d.polygon([(x,y),(x-sign*12,y-5),(x-sign*12,y+5)],fill='#4a4a4a')
  else:
   sign=1 if y>yy else -1;d.polygon([(x,y),(x-5,y-sign*12),(x+5,y-sign*12)],fill='#4a4a4a')
  if c.get('value'):
   a,b=max(zip(route,route[1:]),key=lambda t:abs(t[0][0]-t[1][0])+abs(t[0][1]-t[1][1]))
   f=ImageFont.truetype(fp,14);text=c.get('value');ww=d.textlength(text,font=f);xx=(a[0]+b[0]-ww)/2;yy=(a[1]+b[1])/2-19
   d.rectangle((xx-3,yy-2,xx+ww+3,yy+17),fill='white');d.text((xx,yy),text,font=f,fill='#291b0a')
 for cid,c in cells.items():
  if c.get('vertex')!='1' or cid=='pool' or cid.startswith('lane'):continue
  x,y,w,h=rect(cid);s=c.get('style','')
  if 'rhombus' in s:d.polygon([(x+w/2,y),(x+w,y+h/2),(x+w/2,y+h),(x,y+h/2)],fill='#fff2cc',outline='#684719')
  elif 'ellipse' in s:d.ellipse((x,y,x+w,y+h),fill='white',outline='#684719',width=4 if 'strokeWidth=4' in s else 2)
  elif 'shape=note' in s:d.rectangle((x,y,x+w,y+h),fill='#fff2cc',outline='#bca984')
  else:d.rounded_rectangle((x,y,x+w,y+h),radius=10 if 'rounded=1' in s else 0,fill='white',outline='#684719',width=2)
  drawtext(c,x,y,w,h)
 im.save('D:/wamp64/Brewlearn/docs/diagrams/TO-BE-final-page-'+str(index+1)+'.png')
 print(page.get('name')+': rendered')

