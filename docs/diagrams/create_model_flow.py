from pathlib import Path
import xml.etree.ElementTree as ET

out = Path(__file__).parent
mxfile = ET.Element('mxfile', host='app.diagrams.net', version='24.7.17')
diagram = ET.SubElement(mxfile, 'diagram', id='model-flow', name='Huấn luyện và sử dụng mô hình')
graph = ET.SubElement(diagram, 'mxGraphModel', dx='1600', dy='1650', grid='1', gridSize='10', guides='1', tooltips='1', connect='1', arrows='1', fold='1', page='1', pageScale='1', pageWidth='1600', pageHeight='1650', math='0', shadow='0')
root = ET.SubElement(graph, 'root')
ET.SubElement(root, 'mxCell', id='0')
ET.SubElement(root, 'mxCell', id='1', parent='0')
base = 'whiteSpace=wrap;html=1;fontFamily=Times New Roman;fontSize=19;strokeWidth=1.5;spacing=10;'

def box(i, text, x, y, w, h, style=''):
    cell = ET.SubElement(root, 'mxCell', id=i, value=text, style=base+style, vertex='1', parent='1')
    ET.SubElement(cell, 'mxGeometry', x=str(x), y=str(y), width=str(w), height=str(h), **{'as':'geometry'})

def edge(i, a, b, label='', ports='exitX=0.5;exitY=1;entryX=0.5;entryY=0;', points=(), dashed=False, offset=None):
    style = 'edgeStyle=orthogonalEdgeStyle;rounded=0;html=1;endArrow=block;endFill=1;strokeColor=#334155;strokeWidth=1.5;fontFamily=Times New Roman;fontSize=17;labelBackgroundColor=#ffffff;'+ports
    if dashed: style += 'dashed=1;'
    cell = ET.SubElement(root, 'mxCell', id=i, value=label, style=style, edge='1', parent='1', source=a, target=b)
    geo = ET.SubElement(cell, 'mxGeometry', relative='1', **{'as':'geometry'})
    if points:
        arr = ET.SubElement(geo, 'Array', **{'as':'points'})
        for x,y in points: ET.SubElement(arr, 'mxPoint', x=str(x), y=str(y))
    if offset: ET.SubElement(geo, 'mxPoint', x=str(offset[0]), y=str(offset[1]), **{'as':'offset'})

process = 'rounded=0;fillColor=#eaf2fb;strokeColor=#375a7f;'
runtime = 'rounded=0;fillColor=#edf5ed;strokeColor=#4e7451;'
terminal = 'rounded=1;arcSize=50;fillColor=#ffffff;strokeColor=#334155;'
box('title','<b>QUY TRÌNH HUẤN LUYỆN VÀ SỬ DỤNG MÔ HÌNH DỰ ĐOÁN NGUY CƠ</b>',40,25,1520,65,'text;strokeColor=none;fillColor=none;fontSize=25;')
box('train-panel','<b>A. HUẤN LUYỆN VÀ LỰA CHỌN MÔ HÌNH</b>',40,120,650,1410,'rounded=0;fillColor=#f8fafc;strokeColor=#cbd5e1;dashed=1;verticalAlign=top;spacingTop=18;')
box('use-panel','<b>B. SỬ DỤNG MÔ HÌNH ĐÃ HUẤN LUYỆN</b>',770,120,790,1410,'rounded=0;fillColor=#f8fafc;strokeColor=#cbd5e1;dashed=1;verticalAlign=top;spacingTop=18;')

box('t-start','Bắt đầu huấn luyện',210,195,300,55,terminal)
box('dataset','Tạo 1.000 bản ghi giả lập<br>5 chỉ số đầu vào và nhãn competency_status',120,295,480,100,process)
box('split','Chia dữ liệu: 80% huấn luyện, 20% kiểm tra<br>Phân tầng theo nhãn; random_state = 42',120,440,480,100,process)
box('preprocess','Chuẩn hóa cho Logistic Regression<br>Fit scaler trên tập huấn luyện;<br>transform tập huấn luyện và kiểm tra',120,585,480,110,process)
box('train','Huấn luyện ba thuật toán<br>Logistic Regression · Decision Tree · Random Forest<br>Hai mô hình cây sử dụng dữ liệu không chuẩn hóa',120,740,480,115,process)
box('metrics','Đánh giá trên tập kiểm tra<br>Accuracy · Precision · Recall · F1-score<br>Ma trận nhầm lẫn',120,900,480,110,process)
box('select','Lựa chọn mô hình theo F1-score<br>trên tập kiểm tra hiện có',120,1055,480,90,process)
box('artifact','<b>Lưu các tệp phục vụ dự đoán</b><br>best_model.pkl · scaler.pkl · model_info.pkl<br>Lưu bảng kết quả so sánh mô hình',120,1190,480,120,'shape=note;size=20;fillColor=#fff4d6;strokeColor=#9b7625;')
box('t-end','Kết thúc huấn luyện',210,1365,300,55,terminal)
for n,(a,b) in enumerate(zip(['t-start','dataset','split','preprocess','train','metrics','select','artifact'],['dataset','split','preprocess','train','metrics','select','artifact','t-end'])):
    edge('train-edge-'+str(n),a,b)

box('u-start','Bắt đầu lượt xử lý độc lập',870,195,360,55,terminal)
box('read','Đọc dữ liệu MySQL<br>Nhân viên đang làm việc;<br>dữ liệu học tập và kết quả kiểm tra',870,295,360,110,runtime)
box('features','Tổng hợp 5 chỉ số đầu vào<br>Tiến độ · Điểm trung bình · Số lượt kiểm tra<br>Tỷ lệ không đạt · Số lượt học',870,450,360,110,runtime)
box('decision','Có lượt kiểm tra<br>và lượt học?',955,605,190,130,'rhombus;fillColor=#fff4d6;strokeColor=#9b7625;spacing=0;fontSize=18;')
box('load','Nạp mô hình và thông tin đã lưu<br>Chuẩn hóa đầu vào nếu mô hình yêu cầu',870,810,360,100,runtime)
box('predict','Thực hiện dự đoán nguy cơ<br>Tính điểm nguy cơ và xác định mức cảnh báo',870,955,360,100,runtime)
box('insufficient','Gán “Chưa đủ dữ liệu”<br>Điểm nguy cơ và<br>thông tin mô hình để trống',1290,620,220,110,'rounded=0;fillColor=#fff4d6;strokeColor=#9b7625;')
box('save','<b>Lưu vào du_doan_nguy_co trong MySQL</b><br>Điểm nguy cơ (nếu có) · Mức cảnh báo · Chỉ số đầu vào<br>Thời điểm dự đoán · Thông tin mô hình (nếu có)',870,1190,640,120,'shape=cylinder3;boundedLbl=1;backgroundOutline=1;size=15;fillColor=#fff4d6;strokeColor=#9b7625;spacingTop=20;')
box('u-end','Kết thúc lượt xử lý',1040,1365,300,55,terminal)
edge('use-edge-1','u-start','read')
edge('use-edge-2','read','features')
edge('use-edge-3','features','decision')
edge('use-edge-4','decision','load','Có',offset=(35,0))
edge('use-edge-5','decision','insufficient','Không','exitX=1;exitY=0.5;entryX=0;entryY=0.5;',offset=(0,-18))
edge('use-edge-6','load','predict')
edge('use-edge-7','predict','save',ports='exitX=0.5;exitY=1;entryX=0.28125;entryY=0;')
edge('use-edge-8','insufficient','save',ports='exitX=0.5;exitY=1;entryX=0.828125;entryY=0;',points=((1400,1110),))
edge('use-edge-9','save','u-end')
edge('model-transfer','artifact','load','Sử dụng các tệp đã lưu','exitX=1;exitY=0.5;entryX=0;entryY=0.5;',points=((730,1250),(730,860)),dashed=True,offset=(-25,0))
box('footnote','Đường nét đứt: chuyển giao các tệp đã huấn luyện. Hai phần được thực hiện ở những thời điểm riêng biệt.<br>Dữ liệu huấn luyện là giả lập; kết quả dự đoán hỗ trợ quản lý và không tự động phê duyệt đánh giá hoặc giao đào tạo lại.',60,1550,1480,75,'text;strokeColor=none;fillColor=none;fontSize=17;fontColor=#475569;')

cells = root.findall('mxCell')
ids = [c.attrib['id'] for c in cells]
assert len(ids) == len(set(ids))
for c in cells:
    if c.attrib.get('edge') == '1':
        assert c.attrib['source'] in ids and c.attrib['target'] in ids
ET.indent(mxfile, space='  ')
for ext in ('xml','drawio'):
    path = out / ('BrewLearn-Quy-trinh-huan-luyen-su-dung-mo-hinh.'+ext)
    ET.ElementTree(mxfile).write(path, encoding='utf-8', xml_declaration=True)
    ET.parse(path)
    print(path, path.stat().st_size)
