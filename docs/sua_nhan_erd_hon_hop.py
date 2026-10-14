from pathlib import Path
import json,re,unicodedata,xml.etree.ElementTree as E
B=Path(__file__).parent
data=json.loads((B/'erd-schema.json').read_text('utf-8'))
english=set('id user_id name name_tag email joined_at secondary_email phone email_verified_at password remember_token created_at updated_at dob id_card emergency_contact emergency_phone theme language notification_settings employment_status demo_progress_baseline lesson_id last_watched_seconds progress_percent is_completed visit_count attempt_token assessment_id request_id round_number assigned_by owner_id instructions proposal deadline assessment_date status result score approved_by approved_at required_lessons required_quizzes learning_baseline confirmed_by confirmed_at confirmation_note title message type is_read event_key'.split())
mapping={}
text=(B/'ERD-nhan-tieng-Viet-doi-chieu.md').read_text('utf-8')
for line in text.splitlines():
    m=re.match(r'\| ([^|]+) \| `([^`]+)` \|',line)
    if m:mapping.setdefault(m[2],m[1].strip())
def vn(s):
    s=re.sub(r'\s*\([^)]*\)','',s).replace('Đ','D').replace('đ','d')
    s=''.join(c for c in unicodedata.normalize('NFD',s) if unicodedata.category(c)!='Mn')
    return '_'.join(s.lower().split())
overrides={'id':'id','email':'email','user_id':'nguoi_dung_id','lesson_id':'bai_hoc_id','request_id':'yeu_cau_id','owner_id':'nguoi_phu_trach_id','assigned_by':'nguoi_giao_id','confirmed_by':'nguoi_xac_nhan_id','approved_by':'nguoi_duyet_id','assessment_id':'danh_gia_id','created_at':'thoi_diem_tao','updated_at':'thoi_diem_cap_nhat','progress_percent':'phan_tram_tien_do'}
tree=E.parse(B/'BrewLearn-ERD-nhan-tieng-Viet.drawio');cells={c.get('id'):c for c in tree.iter('mxCell')}
doc=['# Đối chiếu tên tiếng Anh được Việt hóa trên ERD','Tên thuộc tính tiếng Việt trong database được giữ nguyên. Chỉ đổi nhãn hiển thị của thuộc tính tiếng Anh; không đổi database.','','| Bảng | Tên thực tế | Nhãn trên ERD |','|---|---|---|']
for t,meta in data.items():
    # Keep original Vietnamese table identifiers; translate English identifiers only.
    cells[t+'-head'].set('value',{'users':'nguoi_dung','notifications':'thong_bao','retraining_rounds':'vong_dao_tao_lai','user_lesson_progresses':'tien_do_bai_hoc'}.get(t,t))
    for col in meta['columns']:
        n=col['name'];label=overrides.get(n,vn(mapping[n])) if n in english else n
        dtype=col['type'].upper();dtype='ENUM' if dtype.startswith('ENUM(') else dtype
        flags=[]
        if col['colkey']=='PRI':flags.append('PK')
        if any(f['col']==n for f in meta['foreign_keys']):flags.append('FK')
        null='NULL' if col['nullable']=='YES' else 'NOT NULL'
        cells[t+'-'+n].set('value',('/'.join(flags)+' ' if flags else '')+f'{label} : {dtype} | {null}')
        if n in english and label!=n:doc.append(f'| `{t}` | `{n}` | `{label}` |')
cells['note0'].set('value','Giữ nguyên tên thuộc tính tiếng Việt trong database; chỉ Việt hóa tên tiếng Anh trên sơ đồ.')
tree.getroot().find('diagram').set('name','ERD giữ nguyên thuộc tính tiếng Việt')
out=B/'BrewLearn-ERD-giu-ten-thuoc-tinh-Viet.xml';E.indent(tree);tree.write(out,encoding='utf-8',xml_declaration=True)
(B/'ERD-doi-chieu-ten-Anh-Viet.md').write_text('\n'.join(doc),encoding='utf-8')
assert cells['bai_kiem_tra-DiemChuan'].get('value').startswith('DiemChuan :')
assert 'TenBaiHoc :' in cells['bai_hoc-TenBaiHoc'].get('value')
print('Corrected all 18 entities; Vietnamese database identifiers preserved; English display identifiers translated.')
