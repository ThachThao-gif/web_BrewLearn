from pathlib import Path
import json,xml.etree.ElementTree as E
B=Path(__file__).parent
data=json.loads((B/'erd-schema.json').read_text('utf-8'))
tree=E.parse(B/'BrewLearn-ERD-giu-ten-thuoc-tinh-Viet.xml')
graph=tree.find('.//root');cells={c.get('id'):c for c in graph.findall('mxCell')}
source=(B/'render_erd.py').read_text('utf-8')
links=eval(source.split('logical=',1)[1].split('\nfor key,target',1)[0],{'__builtins__':{}})
for t,m in data.items():
    for f in m['foreign_keys']:links[t,f['col']]=(f['target'],f['target_col'])
# Remove only technical account settings, demo fields, redundant legacy fields,
# and secondary timestamps. Keep the important personnel, scoring and round data.
omit={
'users':set('name_tag secondary_email email_verified_at remember_token ChiNhanh CapDo TienDoTong id_card tram_lam_viec emergency_contact emergency_phone theme language notification_settings created_at updated_at'.split()),
'nhan_vien_khoa_hoc':{'demo_progress_baseline'},
'loi_nghiep_vu':{'chi_nhanh_id','diem_tru_risk'},
}
titles={'users':'Nhân viên / tài khoản (users)','vung':'Vùng','cua_hang':'Cửa hàng','ca_lam_viec':'Ca làm việc','chuong_trinh_dao_tao':'Chương trình đào tạo','bai_hoc':'Bài học','nhan_vien_khoa_hoc':'Tiến độ khóa học','user_lesson_progresses':'Tiến độ bài học','bai_kiem_tra':'Bài kiểm tra','cau_hoi_kiem_tra':'Câu hỏi kiểm tra','ket_qua_kiem_tra':'Kết quả kiểm tra','danh_gia_nang_luc':'Đánh giá năng lực','loi_nghiep_vu':'Lỗi nghiệp vụ','du_doan_nguy_co':'Dự đoán nguy cơ','yeu_cau_dao_tao_lai':'Yêu cầu đào tạo lại','retraining_rounds':'Vòng đào tạo lại','notifications':'Thông báo','yeu_cau_ho_tro':'Yêu cầu hỗ trợ'}
count=0
for t,m in data.items():
    cells[t+'-head'].set('value',titles[t])
    row=0
    for col in m['columns']:
        n=col['name'];c=cells[t+'-'+n]
        drop=n in omit.get(t,set()) or (n=='updated_at')
        if drop:graph.remove(c);continue
        value=c.get('value');label=value.split(' : ')[0]
        for pre in ['PK/FK ','PK ','FK ']:
            if label.startswith(pre):label=label[len(pre):];break
        if t=='users' and n=='joined_at':label='ngay_vao_lam'
        flags=[]
        if col['colkey']=='PRI':flags.append('PK')
        if (t,n) in links:flags.append('FK')
        dtype=col['type'].upper();dtype='ENUM' if dtype.startswith('ENUM(') else dtype
        null='NULL' if col['nullable']=='YES' else 'NOT NULL'
        c.set('value',('/'.join(flags)+' ' if flags else '')+f'{label} : {dtype} | {null}')
        c.find('mxGeometry').set('y',str(44+27*row));row+=1;count+=1
    # Keep outer geometry to preserve the existing routed connectors.
cells['title'].set('value','ERD LOGIC BREWLEARN — THUỘC TÍNH NGHIỆP VỤ')
cells['legend'].set('value','PK: khóa chính · FK: tham chiếu ở mức logic · NULL / NOT NULL: cho phép rỗng / bắt buộc')
notes=[
'Nhân viên, trưởng ca và quản lý cửa hàng cùng lưu trong users; phân biệt bằng ChucVu, không tạo thêm bảng nhân viên.',
'ngay_vao_lam là nhãn tiếng Việt của joined_at. Tên thuộc tính đã là tiếng Việt trong database được giữ nguyên.',
'Thuộc tính và kiểu dữ liệu lấy từ cấu trúc dự án; FK ở mức logic gồm cả tham chiếu hiện được mã nguồn sử dụng.',
'Giữ đủ dữ liệu chấm năm tiêu chí, phê duyệt, từng vòng đào tạo lại và xác nhận; lược bỏ trường mô phỏng và một số trường kỹ thuật.']
for i,n in enumerate(notes):cells['note'+str(i)].set('value',n)
tree.getroot().find('diagram').set('name','ERD logic — dữ liệu nghiệp vụ đầy đủ')
E.indent(tree);tree.write(B/'BrewLearn-ERD-logic-nghiep-vu-day-du.xml',encoding='utf-8',xml_declaration=True)
assert 'ngay_vao_lam : DATE' in cells['users-joined_at'].get('value')
assert all('danh_gia_nang_luc-'+n in cells for n in ['Diem_NghiepVu','Diem_KyNang','Diem_QuyTrinh','Diem_PhucVu','Diem_TacPhong'])
print(f'Generated 18 entities and {count} actual attributes, including joined_at and all five criterion scores.')
