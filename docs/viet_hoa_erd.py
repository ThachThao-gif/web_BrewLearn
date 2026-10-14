from pathlib import Path
import json, re, html, shutil
import xml.etree.ElementTree as E
B=Path(__file__).parent
data=json.loads((B/'erd-schema.json').read_text('utf-8'))
tables=dict(zip(data, ['Tài khoản và nhân sự','Vùng','Cửa hàng','Ca làm việc','Chương trình đào tạo','Bài học','Tiến độ khóa học','Tiến độ bài học','Bài kiểm tra','Câu hỏi kiểm tra','Kết quả kiểm tra','Đánh giá năng lực','Lỗi nghiệp vụ','Dự đoán nguy cơ','Yêu cầu đào tạo lại','Vòng đào tạo lại','Thông báo','Yêu cầu hỗ trợ']))
# Explicit table mapping is independent of JSON ordering.
tables.update(users='Tài khoản và nhân sự',vung='Vùng',cua_hang='Cửa hàng',ca_lam_viec='Ca làm việc',chuong_trinh_dao_tao='Chương trình đào tạo',bai_hoc='Bài học',nhan_vien_khoa_hoc='Tiến độ khóa học',user_lesson_progresses='Tiến độ bài học',bai_kiem_tra='Bài kiểm tra',cau_hoi_kiem_tra='Câu hỏi kiểm tra',ket_qua_kiem_tra='Kết quả kiểm tra',danh_gia_nang_luc='Đánh giá năng lực',loi_nghiep_vu='Lỗi nghiệp vụ',du_doan_nguy_co='Dự đoán nguy cơ',yeu_cau_dao_tao_lai='Yêu cầu đào tạo lại',retraining_rounds='Vòng đào tạo lại',notifications='Thông báo',yeu_cau_ho_tro='Yêu cầu hỗ trợ')
names={}
for line in '''id|Mã bản ghi
user_id|Mã tài khoản
name|Họ tên
name_tag|Tên hiển thị
email|Email
joined_at|Ngày vào làm
secondary_email|Email phụ
phone|Số điện thoại
email_verified_at|Thời điểm xác minh email
password|Mật khẩu mã hóa
remember_token|Mã ghi nhớ đăng nhập
created_at|Thời điểm tạo
updated_at|Thời điểm cập nhật
ChucVu|Chức danh
ChiNhanh|Chi nhánh (cột cũ)
cua_hang_id|Mã cửa hàng
CapDo|Cấp độ
TienDoTong|Tiến độ tổng
dob|Ngày sinh
id_card|Số giấy tờ định danh
tram_lam_viec|Trạm làm việc
emergency_contact|Người liên hệ khẩn cấp
emergency_phone|Điện thoại khẩn cấp
theme|Giao diện
language|Ngôn ngữ
notification_settings|Cài đặt thông báo
ca_lam_viec_id|Mã ca làm việc
employment_status|Trạng thái làm việc
vung_id|Mã vùng
ma_cua_hang|Mã định danh cửa hàng
ten_cua_hang|Tên cửa hàng
dia_chi|Địa chỉ
ma_vung|Mã định danh vùng
ten_vung|Tên vùng
nguoi_quan_ly_id|Mã người quản lý
ten_ca|Tên ca
bat_dau|Giờ bắt đầu
ket_thuc|Giờ kết thúc
MaCTDT|Mã chương trình đào tạo
TenCTDT|Tên chương trình đào tạo
MoTa|Mô tả
ChucVuApDung|Chức danh áp dụng
MaBaiHoc|Mã bài học
FK_MaCTDT|Mã chương trình đào tạo
TenBaiHoc|Tên bài học
NoiDung|Nội dung
VideoURL|Đường dẫn video
ThuTu|Thứ tự
TienDo|Tiến độ
TrangThai|Trạng thái
han_hoan_thanh|Hạn hoàn thành
demo_progress_baseline|Mốc tiến độ mô phỏng
lesson_id|Mã bài học
last_watched_seconds|Vị trí xem gần nhất (giây)
progress_percent|Tiến độ (%)
is_completed|Đã hoàn thành
visit_count|Số lượt truy cập
MaBaiKiemTra|Mã bài kiểm tra
TenBaiKiemTra|Tên bài kiểm tra
ThoiGianLamBai|Thời gian làm bài
DiemChuan|Điểm chuẩn
CauHoi|Câu hỏi
PhuongAnA|Phương án A
PhuongAnB|Phương án B
PhuongAnC|Phương án C
PhuongAnD|Phương án D
DapAnDung|Đáp án đúng
GiaiThich|Giải thích
Diem|Điểm
SoCauDung|Số câu đúng
TongSoCau|Tổng số câu
attempt_token|Mã nhận diện lượt nộp
NguoiDanhGia_id|Mã người đánh giá
TieuChuan|Tiêu chuẩn
NgayDanhGia|Thời điểm đánh giá
NgayHen|Thời điểm hẹn
NhanXet|Nhận xét
Diem_NghiepVu|Điểm nghiệp vụ
Diem_KyNang|Điểm kỹ năng
Diem_QuyTrinh|Điểm quy trình
Diem_PhucVu|Điểm phục vụ
Diem_TacPhong|Điểm tác phong
DeXuatXuLy|Đề xuất xử lý
ChiTietDeXuat|Chi tiết đề xuất
ChiTietDiem|Chi tiết điểm
IsPassed|Kết quả đạt
nguoi_duyet_id|Mã người duyệt
ngay_duyet|Thời điểm duyệt
ly_do_tra_lai|Lý do trả lại
nguoi_ghi_nhan_id|Mã người ghi nhận
loai_loi|Loại lỗi
muc_do|Mức độ
diem_tru_risk|Điểm trừ nguy cơ (cột cũ)
mo_ta|Mô tả
thoi_diem_xay_ra|Thời điểm xảy ra
chi_nhanh_id|Mã chi nhánh (cột cũ)
diem_rui_ro|Điểm nguy cơ
muc_canh_bao|Mức cảnh báo
chi_tiet_yeu_to|Chi tiết dữ liệu đầu vào
phien_ban_mo_hinh|Thông tin mô hình
ngay_du_doan|Thời điểm dự đoán
du_doan_id|Mã dự đoán
nguoi_de_xuat_id|Mã người đề xuất
ly_do|Lý do
trang_thai|Trạng thái
ket_qua_sau_dao_tao_lai|Kết quả sau đào tạo lại
ngay_de_xuat|Thời điểm đề xuất
assessment_id|Mã đánh giá liên kết
nguon_de_xuat|Nguồn đề xuất
ke_hoach|Kế hoạch
nguoi_phu_trach_id|Mã người phụ trách
ngay_danh_gia_lai|Ngày đánh giá lại
tien_trinh|Tiến trình
ghi_chu_thuc_hien|Ghi chú thực hiện
danh_gia_lai_id|Mã đánh giá lại
request_id|Mã yêu cầu đào tạo lại
round_number|Số thứ tự vòng
assigned_by|Mã người giao vòng
owner_id|Mã người phụ trách vòng
instructions|Nội dung hướng dẫn
proposal|Nội dung đề xuất
deadline|Hạn hoàn thành
assessment_date|Ngày đánh giá lại
status|Trạng thái vòng
result|Kết quả vòng
score|Điểm đánh giá
approved_by|Mã người duyệt
approved_at|Thời điểm duyệt
required_lessons|Danh sách bài học yêu cầu
required_quizzes|Danh sách bài kiểm tra yêu cầu
learning_baseline|Mốc dữ liệu học tập ban đầu
confirmed_by|Mã người xác nhận
confirmed_at|Thời điểm xác nhận
confirmation_note|Nội dung xác nhận
title|Tiêu đề
message|Nội dung thông báo
type|Loại thông báo
is_read|Đã đọc
event_key|Mã sự kiện thông báo
loai_yeu_cau|Loại yêu cầu
chu_de|Chủ đề
noi_dung|Nội dung
muc_do_uu_tien|Mức độ ưu tiên'''.splitlines():
    k,v=line.split('|');names[k]=v
tree=E.parse(B/'BrewLearn-ERD-lien-ket-tong-the.drawio'); root=tree.getroot()
cells={c.get('id'):c for c in root.iter('mxCell')}
S=1.65
for g in root.iter('mxGeometry'):
    for a in ('x','width'):
        if g.get(a):g.set(a,str(float(g.get(a))*S))
for p in root.iter('mxPoint'):
    if p.get('x'):p.set('x',str(float(p.get('x'))*S))
for m in root.iter('mxGraphModel'):m.set('pageWidth',str(4460*S))
md=['# Đối chiếu nhãn ERD với cơ sở dữ liệu','Chỉ đổi nhãn hiển thị, không đổi cấu trúc database. FK trên hình chỉ đánh dấu ràng buộc khóa ngoại đã khai báo. Các đường còn lại thể hiện liên kết được ứng dụng sử dụng.','']
for t,meta in data.items():
    cells[t+'-head'].set('value',tables[t])
    md+=['## '+tables[t]+' — `'+t+'`','| Nhãn | Tên cột | Kiểu dữ liệu | Rỗng |','|---|---|---|---|']
    for col in meta['columns']:
        n=col['name'];assert n in names,(t,n)
        label=names[n]
        if n=='assessment_id':label='Mã đánh giá nguồn' if t=='yeu_cau_dao_tao_lai' else 'Mã đánh giá lại của vòng'
        dtype=col['type'].upper();null='NULL' if col['nullable']=='YES' else 'NOT NULL'
        flags=[]
        if col['colkey']=='PRI':flags.append('PK')
        if any(f['col']==n for f in meta['foreign_keys']):flags.append('FK')
        # Full ENUM values remain in the dictionary; the ERD states the actual SQL type.
        shown='ENUM' if dtype.startswith('ENUM(') else dtype
        display=(' / '.join(flags)+'  ' if flags else '')+f'{label} : {shown} | {null}'
        cells[t+'-'+n].set('value',display)
        cells[t+'-'+n].set('tooltip',f'{t}.{n} : {dtype} | {null}')
        md.append(f'| {label} | `{n}` | `{dtype}` | {null} |')
cells['legend'].set('value','PK: khóa chính · FK: khóa ngoại đã khai báo · NULL: cho phép rỗng · NOT NULL: bắt buộc có giá trị')
notes=['Nhãn tiếng Việt được đối chiếu với tên bảng và cột thực tế trong tài liệu kèm theo.',
       'Đường nối thể hiện quan hệ dữ liệu; chỉ cột có ràng buộc khóa ngoại thực tế mới được đánh dấu FK.',
       'Giữ nguyên JSON, cột cũ và mốc mô phỏng để phản ánh cấu trúc hiện tại. ENUM: xem các giá trị trong từ điển dữ liệu.',
       'Vòng đào tạo lại có ràng buộc duy nhất theo cặp (Mã yêu cầu đào tạo lại, Số thứ tự vòng).']
for i,s in enumerate(notes):cells['note'+str(i)].set('value',s)
root.find('diagram').set('name','ERD nhãn tiếng Việt')
out=B/'BrewLearn-ERD-nhan-tieng-Viet.drawio';E.indent(root);tree.write(out,encoding='utf-8',xml_declaration=True)
shutil.copyfile(out,out.with_suffix('.xml'))
(B/'ERD-nhan-tieng-Viet-doi-chieu.md').write_text('\n'.join(md),encoding='utf-8')
# Render the exact edited positions to SVG, keeping all connection points.
W=4460*S;H=3950
svg=[f'<svg xmlns="http://www.w3.org/2000/svg" width="{W}" height="{H}" viewBox="0 0 {W} {H}"><rect width="100%" height="100%" fill="white"/><defs><marker id="many" markerWidth="16" markerHeight="16" refX="15" refY="8" orient="auto-start-reverse"><path d="M0 8 L15 1 M0 8 L15 8 M0 8 L15 15" fill="none" stroke="#555"/></marker><marker id="one" markerWidth="16" markerHeight="16" refX="15" refY="8" orient="auto-start-reverse"><path d="M6 1 V15 M11 1 V15" stroke="#555"/></marker></defs>']
def box(c):
    g=c.find('mxGeometry');return [float(g.get(k,'0')) for k in ('x','y','width','height')]
for c in cells.values():
    if c.get('edge')!='1':continue
    sty=dict(re.findall(r'([^;=]+)=([^;]+)',c.get('style','')))
    a=box(cells[c.get('source')]);b=box(cells[c.get('target')])
    points=[(a[0]+a[2]*float(sty['exitX']),a[1]+a[3]*float(sty['exitY']))]
    points +=[(float(p.get('x')),float(p.get('y'))) for p in c.findall('.//mxPoint')]
    points +=[(b[0]+b[2]*float(sty['entryX']),b[1]+b[3]*float(sty['entryY']))]
    end='many' if sty.get('endArrow')=='ERzeroToMany' else 'one'
    svg.append('<polyline points="'+' '.join(f'{x},{y}' for x,y in points)+f'" fill="none" stroke="#777" stroke-width="1.5" marker-start="url(#one)" marker-end="url(#{end})"/>')
for t in data:
    x,y,w,h=box(cells[t]);svg.append(f'<rect x="{x}" y="{y}" width="{w}" height="{h}" fill="#f8fbfd" stroke="#829eb0"/>')
    svg.append(f'<rect x="{x}" y="{y}" width="{w}" height="44" fill="#176387"/>')
    svg.append(f'<text x="{x+w/2}" y="{y+30}" text-anchor="middle" font-family="Arial" font-size="23" font-weight="bold" fill="white">{html.escape(tables[t])}</text>')
    for col in data[t]['columns']:
        c=cells[t+'-'+col['name']];rx,ry,_,_=box(c)
        svg.append(f'<text x="{x+12}" y="{y+ry+20}" font-family="Arial" font-size="18" fill="#182c39">{html.escape(c.get("value"))}</text>')
for id in ['title','legend']+['note'+str(i) for i in range(4)]:
    c=cells[id];x,y,_,_=box(c);size=38 if id=='title' else 22
    svg.append(f'<text x="{x}" y="{y+size}" font-family="Arial" font-size="{size}">{html.escape(c.get("value"))}</text>')
svg.append('</svg>');(B/'BrewLearn-ERD-nhan-tieng-Viet.svg').write_text(''.join(svg),encoding='utf-8')
assert all('?' not in c.get('value','') for c in cells.values())
print('Generated Vietnamese ERD: 18 entities, explicit types and NULL, original columns preserved.')
