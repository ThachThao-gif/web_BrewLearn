from PIL import Image,ImageDraw,ImageFont
import json
pages=json.load(open('D:/wamp64/Brewlearn/docs/diagrams/tobe-clean-preview-data.json',encoding='utf-8'))
fp='C:/Windows/Fonts/arial.ttf';bp='C:/Windows/Fonts/arialbd.ttf'
def wrap(d,t,f,w):
 lines=[]
 for para in t.split('\n'):
  line=''
  for word in para.split():
   test=(line+' '+word).strip()
   if d.textlength(test,font=f)>w and line:lines.append(line);line=word
   else:line=test
  lines.append(line)
 return '\n'.join(lines)
for index,p in enumerate(pages):
 im=Image.new('RGB',(p['width']+60,1330),'white');d=ImageDraw.Draw(im)
 d.rectangle((30,30,p['width']+30,1110),outline='#684719',width=2)
 d.rectangle((30,30,p['width']+30,70),fill='#fff8ec',outline='#684719')
 d.text((150,42),'BREWLEARN — '+p['name'],font=ImageFont.truetype(bp,18),fill='#291b0a')
 for lane,t in enumerate(['HỆ THỐNG BREWLEARN','NHÂN VIÊN','TRƯỞNG CA','QUẢN LÝ CỬA HÀNG']):
  yy=70+lane*260;d.rectangle((30,yy,p['width']+30,yy+260),fill='#fffdf8' if lane%2 else 'white',outline='#bca984')
  d.text((38,yy+110),t,font=ImageFont.truetype(bp,10),fill='#291b0a')
 nodes={n['id']:n for n in p['ns']}
 for e in p['es']:
  a,b=nodes[e['a']],nodes[e['b']];ex,ey,tx,ty=e['ports']
  start=(a['x']+a['w']*ex,a['y']+a['h']*ey);end=(b['x']+b['w']*tx,b['y']+b['h']*ty)
  pts=[start]+[tuple(t) for t in e['pts']]+[end]
  route=[pts[0]]
  for point in pts[1:]:
   prev=route[-1]
   if point[0]!=prev[0] and point[1]!=prev[1]:route.append((point[0],prev[1]))
   route.append(point)
  route=[(x+30,y+30) for x,y in route]
  d.line(route,fill='#4a4a4a',width=2)
  x,y=route[-1];px,py=route[-2]
  if x!=px:
   s=1 if x>px else -1;d.polygon([(x,y),(x-s*12,y-5),(x-s*12,y+5)],fill='#4a4a4a')
  else:
   s=1 if y>py else -1;d.polygon([(x,y),(x-5,y-s*12),(x+5,y-s*12)],fill='#4a4a4a')
  if e['t']:
   longest=max(zip(route,route[1:]),key=lambda ab:abs(ab[0][0]-ab[1][0])+abs(ab[0][1]-ab[1][1]))
   (x1,y1),(x2,y2)=longest
   f=ImageFont.truetype(fp,13);tw=d.textlength(e['t'],font=f)
   xx=(x1+x2-tw)/2;yy=(y1+y2)/2-19
   d.rectangle((xx-3,yy-2,xx+tw+3,yy+17),fill='white')
   d.text((xx,yy),e['t'],font=f,fill='#291b0a')
 for n in p['ns']:
  x,y,w,h=n['x']+30,n['y']+30,n['w'],n['h'];kind=n['k']
  if kind=='xor':d.polygon([(x+w/2,y),(x+w,y+h/2),(x+w/2,y+h),(x,y+h/2)],fill='#fff2cc',outline='#684719')
  elif kind in ['start','end']:d.ellipse((x,y,x+w,y+h),fill='white',outline='#684719',width=4 if kind=='end' else 2)
  else:d.rounded_rectangle((x,y,x+w,y+h),radius=10,fill='white',outline='#684719',width=2)
  f=ImageFont.truetype(fp,15);t=wrap(d,n['t'],f,w-18);bb=d.multiline_textbbox((0,0),t,font=f,spacing=4,align='center');tw,th=bb[2]-bb[0],bb[3]-bb[1]
  d.multiline_text((x+(w-tw)/2,y+(h-th)/2-3),t,font=f,fill='#291b0a',spacing=4,align='center')
  if kind=='sub':
   d.rectangle((x+w/2-8,y+h-20,x+w/2+8,y+h-4),outline='#684719');d.text((x+w/2-4,y+h-21),'+',font=f,fill='#684719')
 d.rectangle((140,1150,p['width']-80,1290),fill='#fff2cc',outline='#bca984')
 f=ImageFont.truetype(fp,16);d.multiline_text((155,1170),wrap(d,p['note'],f,p['width']-250),font=f,fill='#291b0a',spacing=8)
 im.save('D:/wamp64/Brewlearn/docs/diagrams/TO-BE-clean-'+str(index+2)+'.png')

