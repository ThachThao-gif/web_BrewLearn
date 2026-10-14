from pathlib import Path
import xml.etree.ElementTree as E

out=Path(__file__).parent
file=E.Element('mxfile',host='app.diagrams.net',version='26.0.0',type='device')
diagram=E.SubElement(file,'diagram',id='asis-general',name='AS-IS đào tạo nhân viên')
model=E.SubElement(diagram,'mxGraphModel',grid='1',gridSize='10',page='1',pageWidth='1960',pageHeight='1080')
root=E.SubElement(model,'root')
E.SubElement(root,'mxCell',id='0')
E.SubElement(root,'mxCell',id='1',parent='0')
def vertex(id,label,x,y,w,h,style,parent='1'):
    c=E.SubElement(root,'mxCell',id=id,value=label,style=style,vertex='1',parent=parent)
    E.SubElement(c,'mxGeometry',x=str(x),y=str(y),width=str(w),height=str(h),attrib={'as':'geometry'})
    return c
base='html=1;whiteSpace=wrap;fontFamily=Arial;fontSize=14;strokeColor=#333333;fillColor=#FFFFFF;'
vertex('pool','BREWLEARN — QUY TRÌNH ĐÀO TẠO NHÂN VIÊN AS-IS',30,40,1870,900,
       'swimlane;horizontal=0;startSize=35;collapsible=0;'+base)
for id,label,y,h in [('hr','HR',0,170),('employee','NHÂN VIÊN',170,200),('leader','CA TRƯỞNG',370,240),('manager','QUẢN LÝ CỬA HÀNG',610,290)]:
    vertex(id,label,35,y,1835,h,'swimlane;horizontal=0;startSize=45;collapsible=0;'+base,'pool')
task='rounded=1;arcSize=12;'+base
event='ellipse;aspect=fixed;'+base
vertex('start','',90,65,30,30,event,'hr')
vertex('identify','Xác định nhân viên cần đào tạo\n(mới hoặc đang làm việc)',150,40,205,80,task,'hr')
vertex('plan','Xác định nội dung và\nphân công người hướng dẫn',360,55,210,80,task,'manager')
vertex('guide','Hướng dẫn trực tiếp;\ncung cấp tài liệu nghiệp vụ',610,55,200,80,task,'leader')
vertex('practice','Học và thực hành\ntheo nội dung được hướng dẫn',850,45,200,80,task,'employee')
vertex('observe','Quan sát, ghi nhận kết quả\nvà đề xuất xử lý',1100,55,205,80,task,'leader')
vertex('review','Xem xét kết quả\nvà đề xuất của ca trưởng',1320,55,190,80,task,'manager')
vertex('gate','',1550,70,50,50,'rhombus;'+base,'manager')
vertex('finish','Xác nhận hoàn thành\nvà thông báo kết quả',1640,55,160,80,task,'manager')
vertex('end','',1810,80,30,30,'ellipse;aspect=fixed;strokeWidth=3;fillColor=#222222;strokeColor=#222222;','manager')
vertex('extra','Yêu cầu đào tạo bổ sung;\nxác định nội dung cần cải thiện',1350,185,210,70,task,'manager')

def edge(id,source,target,label='',points=(),ports=''):
    c=E.SubElement(root,'mxCell',id=id,value=label,source=source,target=target,edge='1',parent='1',
       style='edgeStyle=orthogonalEdgeStyle;rounded=0;html=1;endArrow=block;endFill=1;strokeColor=#333333;fontSize=13;labelBackgroundColor=#FFFFFF;'+ports)
    g=E.SubElement(c,'mxGeometry',relative='1',attrib={'as':'geometry'})
    if points:
        a=E.SubElement(g,'Array',attrib={'as':'points'})
        for x,y in points:E.SubElement(a,'mxPoint',x=str(x),y=str(y))
edge('e1','start','identify',ports='exitX=1;exitY=0.5;entryX=0;entryY=0.5;')
edge('e2','identify','plan',points=[(455,120),(455,745)],ports='exitX=1;exitY=0.5;entryX=0;entryY=0.5;')
edge('e3','plan','guide',points=[(665,745),(665,505)],ports='exitX=1;exitY=0.5;entryX=0;entryY=0.5;')
edge('e4','guide','practice',points=[(905,505),(905,295)],ports='exitX=1;exitY=0.5;entryX=0;entryY=0.5;')
edge('e5','practice','observe',points=[(1155,295),(1155,505)],ports='exitX=1;exitY=0.5;entryX=0;entryY=0.5;')
edge('e6','observe','review',points=[(1375,505),(1375,745)],ports='exitX=1;exitY=0.5;entryX=0;entryY=0.5;')
edge('e7','review','gate',ports='exitX=1;exitY=0.5;entryX=0;entryY=0.5;')
edge('e8','gate','finish','Đạt yêu cầu',ports='exitX=1;exitY=0.5;entryX=0;entryY=0.5;')
edge('e9','finish','end',ports='exitX=1;exitY=0.5;entryX=0;entryY=0.5;')
edge('e10','gate','extra','Chưa đạt',points=[(1640,870)],ports='exitX=0.5;exitY=1;entryX=1;entryY=0.5;')
edge('e11','extra','plan','Tiếp tục đào tạo',points=[(1520,925),(530,925)],ports='exitX=0.5;exitY=1;entryX=0.5;entryY=1;')
vertex('note','Bối cảnh giả định, áp dụng cho nhân viên mới và nhân viên đang làm việc.\nThông tin được ghi nhận thủ công bằng tài liệu/biểu mẫu; chưa thể hiện học trực tuyến, chấm tự động hoặc dự đoán.\nNhánh chưa đạt quay lại giao nội dung và phân công; không phải quyết định tuyển dụng hay chấm dứt thử việc.',80,970,1790,85,
       'rounded=1;html=1;whiteSpace=wrap;align=left;spacing=12;fillColor=#FFF2CC;strokeColor=#D6B656;fontSize=14;')
E.indent(file,space='  ')
data=E.tostring(file,encoding='utf-8',xml_declaration=True)
for name in ['BrewLearn-AS-IS-nhan-vien.drawio','BrewLearn-AS-IS-nhan-vien.xml']:
    (out/name).write_bytes(data)
    parsed=E.fromstring(data)
    ids={c.get('id') for c in parsed.iter('mxCell')}
    for c in parsed.iter('mxCell'):
        if c.get('edge')=='1':assert c.get('source') in ids and c.get('target') in ids
print('Created AS-IS drawio and XML: 4 lanes, 11 flows, 1 retraining loop.')
