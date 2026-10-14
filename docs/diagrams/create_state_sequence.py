from pathlib import Path
import xml.etree.ElementTree as E
import html

BASE = Path(__file__).parent
FONT = 'fontFamily=Times New Roman;fontSize=18;fontColor=#17202a;'
BOX = 'rounded=1;whiteSpace=wrap;html=0;fillColor=#ffffff;strokeColor=#263238;strokeWidth=2;'+FONT
TEXT = 'text;html=0;whiteSpace=wrap;align=left;verticalAlign=middle;strokeColor=none;fillColor=none;'+FONT
EDGE = 'edgeStyle=none;rounded=0;html=0;endArrow=open;endFill=0;strokeColor=#263238;strokeWidth=1.6;fontFamily=Times New Roman;fontSize=16;labelBackgroundColor=#ffffff;'

class Page:
    def __init__(self, name, width, height):
        self.name, self.w, self.h = name, width, height
        self.model = E.Element('mxGraphModel', dx='1800', dy='1400', grid='1', gridSize='10', guides='1', tooltips='1', connect='1', arrows='1', fold='1', page='1', pageScale='1', pageWidth=str(width), pageHeight=str(height), math='0', shadow='0')
        self.root = E.SubElement(self.model, 'root')
        E.SubElement(self.root, 'mxCell', id='0')
        E.SubElement(self.root, 'mxCell', id='1', parent='0')
        self.items=[]
        self.num=0
    def box(self, label, x,y,w,h,style=BOX):
        self.num+=1
        id=f'n{self.num}'
        c=E.SubElement(self.root,'mxCell',id=id,value=label,style=style,vertex='1',parent='1')
        E.SubElement(c,'mxGeometry',x=str(x),y=str(y),width=str(w),height=str(h),attrib={'as':'geometry'})
        self.items.append(('box',label,x,y,w,h,style))
        return id
    def line(self, label, points, dashed=False, arrow=True, labelpos=None):
        self.num+=1
        style=EDGE+('dashed=1;' if dashed else '')+('endArrow=none;' if not arrow else '')
        c=E.SubElement(self.root,'mxCell',id=f'e{self.num}',value=label,style=style,edge='1',parent='1')
        g=E.SubElement(c,'mxGeometry',relative='1',attrib={'as':'geometry'})
        E.SubElement(g,'mxPoint',x=str(points[0][0]),y=str(points[0][1]),attrib={'as':'sourcePoint'})
        E.SubElement(g,'mxPoint',x=str(points[-1][0]),y=str(points[-1][1]),attrib={'as':'targetPoint'})
        if len(points)>2:
            arr=E.SubElement(g,'Array',attrib={'as':'points'})
            for x,y in points[1:-1]: E.SubElement(arr,'mxPoint',x=str(x),y=str(y))
        if labelpos:
            c.set('value','')
            self.box(label,*labelpos,TEXT+'fontSize=16;align=center;')
        self.items.append(('line',label if not labelpos else '',points,dashed,arrow))
    def title(self, text, subtitle):
        self.box(text,40,20,self.w-80,50,TEXT+'fontSize=27;fontStyle=1;align=center;')
        self.box(subtitle,40,75,self.w-80,65,TEXT+'fontSize=18;align=center;')
    def frame(self,x,y,w,h):
        self.box('',x,y,w,h,'rounded=0;fillColor=none;strokeColor=#78909c;strokeWidth=1;')
        cell=self.root[-1]
        self.root.remove(cell)
        self.root.insert(2,cell)
        item=self.items.pop()
        self.items.insert(0,item)
    def save(self, stem):
        mx=E.Element('mxfile',host='app.diagrams.net',version='24.7.17',compressed='false')
        d=E.SubElement(mx,'diagram',id=stem,name=self.name)
        d.append(self.model)
        E.indent(mx)
        E.ElementTree(mx).write(BASE/(stem+'.xml'),encoding='utf-8',xml_declaration=True)
        self.svg(stem)
    def svg(self,stem):
        s=[f'<svg xmlns="http://www.w3.org/2000/svg" width="{self.w}" height="{self.h}" viewBox="0 0 {self.w} {self.h}"><rect width="100%" height="100%" fill="white"/><defs><marker id="arrow" markerWidth="10" markerHeight="10" refX="9" refY="5" orient="auto"><path d="M1,1 L9,5 L1,9" fill="none" stroke="#263238" stroke-width="1.5"/></marker></defs>']
        for item in self.items:
            if item[0]=='line':
                _, label, pts, dashed, arrow=item
                s.append(f'<polyline points="'+ ' '.join(f'{x},{y}' for x,y in pts)+'" fill="none" stroke="#263238" stroke-width="1.6"'+(' stroke-dasharray="7 5"' if dashed else '')+(' marker-end="url(#arrow)"' if arrow else '')+'/>')
                continue
            _,label,x,y,w,h,style=item
            if 'ellipse' in style:
                s.append(f'<ellipse cx="{x+w/2}" cy="{y+h/2}" rx="{w/2}" ry="{h/2}" fill="'+('#263238' if 'fillColor=#263238' in style else 'white')+'" stroke="#263238" stroke-width="2"/>')
            elif not style.startswith('text;'):
                s.append(f'<rect x="{x}" y="{y}" width="{w}" height="{h}" rx="'+('0' if 'rounded=0' in style else '12')+'" fill="'+('none' if 'fillColor=none' in style else '#fff2cc' if '#fff2cc' in style else '#eef4f8' if '#eef4f8' in style else 'white')+'" stroke="#263238" stroke-width="1.6"/>')
            fs=27 if 'fontSize=27' in style else 16 if 'fontSize=16' in style else 18
            centered='align=center' in style or not style.startswith('text;')
            lines=label.split('\n')
            for j,t in enumerate(lines):
                ty=y+h/2-(len(lines)-1)*(fs+5)/2+j*(fs+5)+fs/3
                s.append(f'<text x="{x+w/2 if centered else x+8}" y="{ty}" font-family="Times New Roman,serif" font-size="{fs}" text-anchor="'+('middle' if centered else 'start')+'" fill="#17202a">'+html.escape(t)+'</text>')
        s.append('</svg>')
        (BASE/(stem+'.svg')).write_text(''.join(s),encoding='utf-8')

# 1. Lifecycle of the assessment: canonical saveScore + manager review.
p=Page('01 - Trạng thái đánh giá năng lực',1500,1050)
p.title('VÒNG ĐỜI ĐÁNH GIÁ NĂNG LỰC','Đối tượng: danh_gia_nang_luc.TrangThai • Áp dụng đánh giá đầu, định kỳ và đánh giá lại')
p.box('',740,155,20,20,'ellipse;fillColor=#263238;strokeColor=#263238;')
p.box('Chờ đánh giá\nChoDanhGia',550,240,400,100)
p.box('Chờ phê duyệt\nChoPheDuyet',550,480,400,100)
p.box('Trả lại để bổ sung\nTraLai',70,480,340,100)
p.box('Đã duyệt\nDaDuyet\nIsPassed = Đạt hoặc Chưa đạt',550,730,400,120)
p.line('Tạo lịch hợp lệ',[(750,175),(750,240)],labelpos=(780,185,250,40))
p.line('Trưởng ca chấm / gửi kết quả hợp lệ',[(750,340),(750,480)],labelpos=(785,375,490,55))
p.line('QL trả lại [có lý do]',[(550,510),(410,510)],labelpos=(365,430,310,45))
p.line('Trưởng ca bổ sung và gửi lại',[(410,555),(550,555)],labelpos=(335,590,350,50))
p.line('QL duyệt [đủ kết quả và đề xuất nếu chưa đạt]',[(750,580),(750,730)],labelpos=(780,630,625,55))
p.box('Đã duyệt là trạng thái ổn định của bản ghi, không xóa hồ sơ.\nĐánh giá chu kỳ mới tạo bản ghi mới. Chờ duyệt / Trả lại chưa phải nhãn chính thức.\nHoanThanh còn được đọc để tương thích dữ liệu cũ; review() hiện ghi DaDuyet.',70,890,1360,120,BOX+'fillColor=#fff2cc;')
p.save('BrewLearn-State-Danh-gia-nang-luc')

# 2. Retraining request carries two distinct persisted state fields.
p=Page('02 - Trạng thái yêu cầu đào tạo lại',1650,1770)
p.title('VÒNG ĐỜI YÊU CẦU ĐÀO TẠO LẠI QUA NHIỀU VÒNG','Cặp trong khối = yeu_cau_dao_tao_lai.trang_thai / tien_trinh\nThông tin vòng = retraining_rounds.status / result; các cột vòng là string, không phải enum')
p.box('Đầu vào: bản ghi chờ duyệt đã tồn tại',40,150,490,45,TEXT+'fontSize=16;')
p.line('',[(225,195),(225,210)])
p.box('Nhánh xử lý đề xuất có sẵn\ntrang_thai = ChoDuyet',60,210,330,95)
p.box('Từ chối: TuChoi\nNhánh kết thúc xử lý đề xuất',60,420,330,95)
p.box('',825,165,20,20,'ellipse;fillColor=#263238;strokeColor=#263238;')
p.box('Đã duyệt • Chờ giao vòng\nDaDuyet / ChoGiao',610,330,450,110)
p.box('Đang thực hiện vòng n\nYêu cầu: DaDuyet / DangThucHien\nretraining_rounds.status = DangThucHien\nretraining_rounds.result = ChuaCoKetQua',610,610,450,120,BOX+'fontSize=16;')
p.box('Chờ đánh giá lại / phê duyệt vòng n\nYêu cầu: DaDuyet / ChoDanhGiaLai\nretraining_rounds.status = ChoDanhGiaLai\nretraining_rounds.result = ChuaCoKetQua',610,935,450,120,BOX+'fontSize=16;')
p.box('Hoàn thành yêu cầu\nHoanThanh / HoanTat\nKết quả: Dat',1230,935,360,120)
p.line('Duyệt đánh giá chưa đạt có đề xuất\nTạo yêu cầu trực tiếp ở DaDuyet / ChoGiao',[(835,185),(835,330)],labelpos=(865,210,610,80))
p.line('QL duyệt đề xuất',[(390,257),(475,257),(475,385),(610,385)],labelpos=(365,285,250,45))
p.line('QL từ chối [có lý do]',[(225,305),(225,420)],labelpos=(55,345,340,45))
p.line('QL giao kế hoạch, người phụ trách và hạn\nTạo retraining_rounds với số vòng n',[(835,440),(835,610)],labelpos=(870,480,670,70))
p.line('QL tạo lịch đánh giá lại [đủ điều kiện]\nHọc/quiz mới theo kế hoạch + xác nhận đúng người',[(835,730),(835,935)],labelpos=(870,800,710,80))
p.line('QL duyệt đánh giá lại [kết quả Đạt]\ndanh_gia_nang_luc.IsPassed = true\nLưu vòng: status = DaDuyet; result = Dat',[(1060,995),(1230,995)],labelpos=(1065,1080,520,95))
p.line('QL duyệt đánh giá lại [kết quả Chưa đạt]\ndanh_gia_nang_luc.IsPassed = false\nLưu vòng: status = DaDuyet; result = ChuaDat\nGiữ lịch sử; xóa liên kết lịch cũ\nChờ QL chủ động giao vòng n+1',[(700,1055),(700,1190),(480,1190),(480,415),(610,415)],labelpos=(25,745,440,160))
p.box('Cập nhật kế hoạch đang thực hiện: vẫn vòng n.\nĐổi người phụ trách / nội dung: xóa xác nhận cũ.\nChưa đủ điều kiện: giữ DangThucHien, không tạo lịch.\nTrả lại đánh giá lại: chỉ đổi bản ghi đánh giá sang TraLai;\nyêu cầu vẫn ChoDanhGiaLai, không tạo vòng mới.\nChỉ kết quả Đạt đã duyệt mới đóng yêu cầu.',55,1230,1540,165,BOX+'fillColor=#fff2cc;')
p.box('ĐỐI CHIẾU GIÁ TRỊ VÒNG (retraining_rounds)\nstatus: DangThucHien → ChoDanhGiaLai → DaDuyet\nresult: ChuaCoKetQua → Dat hoặc ChuaDat sau khi QL duyệt\nTrả lại / Chờ phê duyệt thuộc bản ghi đánh giá; vòng vẫn ChoDanhGiaLai.\nHoanThanh / HoanTat thuộc yêu cầu; vòng đã duyệt giữ DaDuyet và kết quả.\nNhánh ChoDuyet chỉ xử lý bản ghi có sẵn: chưa có tuyến tạo đề xuất độc lập được xác minh.',55,1430,1540,190,BOX+'fillColor=#eef4f8;')
p.save('BrewLearn-State-Yeu-cau-dao-tao-lai')

def participants(p, names, xs, bottom):
    for name,x in zip(names,xs):
        p.box(name,x-120,155,240,85)
        p.line('',[(x,240),(x,bottom)],dashed=True,arrow=False)

def msg(p,label,x1,x2,y,reply=False):
    if x1==x2:
        p.line('',[(x1,y),(x1+75,y),(x1+75,y+38),(x1,y+38)])
        p.box(label,x1+85,y-5,350,55,TEXT+'fontSize=16;')
    else:
        p.line('',[(x1,y),(x2,y)],dashed=reply)
        p.box(label,min(x1,x2)+10,y-45,abs(x2-x1)-20,40,TEXT+'fontSize=16;align=center;')

# 3. Batch prediction: no scheduler or artisan wrapper exists in the source.
p=Page('03 - Tuần tự chạy dự đoán riêng',1800,1770)
p.title('TUẦN TỰ CHẠY DỰ ĐOÁN THEO LÔ','Hiện trạng: chạy script riêng; Scheduler chỉ nhắc hạn, chưa có Artisan gọi Python dự đoán')
participants(p,['Người vận hành\nTerminal','Python\nket_noi_db.py','MySQL\nCSDL BrewLearn','Mô hình + scaler\npredict.py'],[170,580,1030,1520],1580)
msg(p,'1. Chạy python app/ml/ket_noi_db.py',170,580,320)
msg(p,'2. Đọc .env; kết nối PyMySQL',580,1030,410)
msg(p,'3. SELECT nhân viên đang làm, có cửa hàng\nLoại trưởng ca / quản lý',580,1030,510)
msg(p,'4. Danh sách nhân viên',1030,580,610,True)
p.box('loop [từng nhân viên]',420,660,1280,45,TEXT+'fontSize=18;fontStyle=1;')
msg(p,'5. Đọc tiến độ, lịch sử quiz và lượt học',580,1030,770)
msg(p,'6. Dữ liệu để tổng hợp 5 biến',1030,580,870,True)
msg(p,'7. Tính 5 feature;\nkiểm tra điều kiện dữ liệu',580,580,940)
p.box('alt [quiz_attempts > 0 và learning_visits > 0]',420,1020,1280,45,TEXT+'fontSize=18;fontStyle=1;')
msg(p,'8. predict_risk(5 biến); chuẩn hóa nếu cần',580,1520,1120)
msg(p,'9. predict_proba → xác suất, mức nguy cơ, tên mô hình',1520,580,1220,True)
p.box('else [chưa đủ dữ liệu]: không gọi model;\nChuaDuDuLieu, diem_rui_ro = NULL',630,1260,990,65,BOX+'fillColor=#eef4f8;')
msg(p,'10. INSERT du_doan_nguy_co; COMMIT từng nhân viên',580,1030,1400)
msg(p,'11. In kết quả / hoàn tất',580,170,1530,True)
p.box('Nếu một nhân viên lỗi: ROLLBACK bản ghi của người đó và tiếp tục người kế tiếp.\nKết thúc: đóng kết nối. Laravel đọc kết quả đã lưu khi mở trang; không gọi model theo từng yêu cầu web.\nScheduler → Artisan → Python là phương án mở rộng, chưa phải luồng đã triển khai.',50,1620,1700,120,BOX+'fillColor=#fff2cc;')
p.frame(420,660,1280,820)
p.frame(450,1015,1230,325)
p.line('',[(450,1245),(1680,1245)],arrow=False)
p.save('BrewLearn-Sequence-Chay-du-doan')

# 4. HTTP schedule transaction and notification retrieval are sequential, not push.
p=Page('04 - Tuần tự tạo lịch và thông báo',1900,2050)
p.title('TUẦN TỰ TẠO LỊCH ĐÁNH GIÁ VÀ HIỂN THỊ THÔNG BÁO','Lịch lưu trong danh_gia_nang_luc.NgayHen • Thông báo được đọc khi nhân viên tải trang')
participants(p,['Trưởng ca\nTrình duyệt','Laravel\nLeaderEvaluationController','MySQL\nCSDL BrewLearn','Nhân viên\nTrình duyệt'],[170,650,1220,1740],1830)
msg(p,'1. Chọn nhân viên, ngày giờ, tiêu chuẩn và ghi chú',170,650,330)
msg(p,'2. POST /leader/create-schedule\n(auth; kiểm tra dữ liệu)',170,650,450)
msg(p,'3. BEGIN; khóa nhân viên trong phạm vi ca/cửa hàng\nKiểm tra đang làm và không có đánh giá đang xử lý',650,1220,580)
msg(p,'4. Kiểm tra bài học hoàn thành và từng quiz đạt',650,1220,700)
msg(p,'5. Trả dữ liệu kiểm tra điều kiện',1220,650,800,True)
p.box('alt [hợp lệ và đủ điều kiện]',430,845,960,45,TEXT+'fontSize=18;fontStyle=1;')
msg(p,'6. INSERT danh_gia_nang_luc\nNgayHen; TrangThai = ChoDanhGia',650,1220,960)
msg(p,'7. INSERT notifications\nuser_id; type = schedule; is_read = 0',650,1220,1090)
msg(p,'8. COMMIT',650,1220,1200)
msg(p,'9. Chuyển về trang + thông báo thành công',650,170,1310,True)
p.box('else [sai phạm vi / trùng / thiếu học hoặc quiz / lỗi ghi]\nROLLBACK; trả lỗi; không lưu lịch và thông báo từng phần.',430,1350,980,85,BOX+'fillColor=#eef4f8;')
msg(p,'10. Nhân viên mở hoặc tải lại trang đăng nhập hợp lệ',1740,650,1530)
msg(p,'11. View composer đọc notifications theo user_id',650,1220,1640)
msg(p,'12. Danh sách thông báo',1220,650,1740,True)
msg(p,'13. Render giao diện có chuông và thông báo lịch',650,1740,1830,True)
p.box('Không có bảng lich_danh_gia trong luồng này. Không khẳng định thông báo push / WebSocket.\nĐiều kiện đầu: nhân viên đã hoàn thành bài học và đạt từng quiz theo DiemChuan.\nNhánh đánh giá lại do quản lý tạo lịch dùng ManagerController::reassess, là luồng khác.',50,1900,1800,120,BOX+'fillColor=#fff2cc;')
p.frame(60,845,1380,600)
p.line('',[(60,1340),(1440,1340)],arrow=False)
p.save('BrewLearn-Sequence-Tao-lich-Thong-bao')

combined=E.Element('mxfile',host='app.diagrams.net',version='24.7.17',compressed='false')
stems=['BrewLearn-State-Danh-gia-nang-luc','BrewLearn-State-Yeu-cau-dao-tao-lai','BrewLearn-Sequence-Chay-du-doan','BrewLearn-Sequence-Tao-lich-Thong-bao']
for stem in stems:
    combined.append(E.parse(BASE/(stem+'.xml')).getroot().find('diagram'))
E.indent(combined)
out=BASE/'BrewLearn-State-Sequence-4-so-do.xml'
E.ElementTree(combined).write(out,encoding='utf-8',xml_declaration=True)
assert len(E.parse(out).getroot().findall('diagram'))==4
for d in combined:
    ids=[c.get('id') for c in d.iter('mxCell')]
    assert len(ids)==len(set(ids))
print(f'Created {out}; 4 pages, 4 individual XML files, 4 SVG previews.')
