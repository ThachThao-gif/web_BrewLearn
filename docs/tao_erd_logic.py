from pathlib import Path
import json,xml.etree.ElementTree as E
B=Path(__file__).parent
data=json.loads((B/'erd-schema.json').read_text('utf-8'))
source=(B/'render_erd.py').read_text('utf-8')
logical=eval(source.split('logical=',1)[1].split('\nfor key,target',1)[0],{'__builtins__':{}})
links=dict(logical)
for t,m in data.items():
    for f in m['foreign_keys']:links[t,f['col']]=(f['target'],f['target_col'])
main={
'users':'name email ChucVu employment_status',
'vung':'ma_vung ten_vung',
'cua_hang':'ma_cua_hang ten_cua_hang',
'ca_lam_viec':'ten_ca bat_dau ket_thuc',
'chuong_trinh_dao_tao':'TenCTDT ChucVuApDung',
'bai_hoc':'TenBaiHoc ThuTu',
'nhan_vien_khoa_hoc':'TienDo TrangThai',
'user_lesson_progresses':'progress_percent is_completed visit_count',
'bai_kiem_tra':'TenBaiKiemTra DiemChuan ThoiGianLamBai',
'cau_hoi_kiem_tra':'CauHoi DapAnDung',
'ket_qua_kiem_tra':'Diem TrangThai created_at',
'danh_gia_nang_luc':'NgayHen Diem TrangThai IsPassed',
'loi_nghiep_vu':'loai_loi muc_do thoi_diem_xay_ra',
'du_doan_nguy_co':'diem_rui_ro muc_canh_bao ngay_du_doan',
'yeu_cau_dao_tao_lai':'trang_thai tien_trinh',
'retraining_rounds':'round_number status result confirmed_at',
'notifications':'title is_read',
'yeu_cau_ho_tro':'chu_de trang_thai'}
tree=E.parse(B/'BrewLearn-ERD-giu-ten-thuoc-tinh-Viet.xml')
graph=tree.find('.//root');cells={c.get('id'):c for c in graph.findall('mxCell')}
old={}
for t,m in data.items():
    parent=cells[t];g=parent.find('mxGeometry');old[t]=float(g.get('height'))
    chosen=[]
    for col in m['columns']:
        n=col['name'];c=cells[t+'-'+n]
        if col['colkey']!='PRI' and (t,n) not in links and n not in main[t].split():
            graph.remove(c);continue
        chosen.append(c)
        flags=[]
        if col['colkey']=='PRI':flags.append('PK')
        if (t,n) in links:flags.append('FK')
        # Preserve the previous translated English identifier and original Vietnamese identifier.
        name=c.get('value').split(' : ')[0]
        for prefix in ['PK/FK ','PK ','FK ']:
            if name.startswith(prefix):name=name[len(prefix):];break
        c.set('value',('/'.join(flags)+' ' if flags else '')+name)
        c.find('mxGeometry').set('y',str(44+27*(len(chosen)-1)))
    g.set('height',str(44+27*len(chosen)))
for c in graph.findall('mxCell'):
    if c.get('edge')!='1':continue
    style=c.get('style')
    import re
    for nodekey,axis in [('source','exitY'),('target','entryY')]:
        t=c.get(nodekey);new=float(cells[t].find('mxGeometry').get('height'))
        match=re.search(axis+r'=([^;]+)',style)
        if match:
            original=float(match[1])*old[t]
            value=min(max(original,12),new-12)/new
            style=re.sub(axis+r'=[^;]+',axis+'='+str(value),style)
    c.set('style',style)
cells['title'].set('value','MÔ HÌNH DỮ LIỆU LOGIC — BREWLEARN')
cells['legend'].set('value','PK: khóa chính · FK: cột tham chiếu ở mức logic · Chỉ thể hiện thuộc tính chính')
notes=[
'Các thực thể, thuộc tính và cột liên kết lấy từ cấu trúc dữ liệu cùng mã xử lý hiện có của dự án.',
'FK thể hiện tham chiếu ở mức logic; không khẳng định mọi tham chiếu đã có ràng buộc FOREIGN KEY trong MySQL.',
'Tên tiếng Việt trong database được giữ nguyên; tên tiếng Anh được Việt hóa trên hình, có tooltip tên thực tế.',
'Một yêu cầu có nhiều vòng; cặp (request_id, round_number) là duy nhất. assessment_id ở yêu cầu và vòng cũng là duy nhất.']
for i,n in enumerate(notes):cells['note'+str(i)].set('value',n)
tree.getroot().find('diagram').set('name','ERD logic — thuộc tính chính')
E.indent(tree);tree.write(B/'BrewLearn-ERD-logic-thuoc-tinh-chinh.xml',encoding='utf-8',xml_declaration=True)
print('18 actual entities; retained primary keys, existing reference columns and selected business attributes; no schema mutations.')
