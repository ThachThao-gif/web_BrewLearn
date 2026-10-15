import re, sys, html
src, dst = sys.argv[1], sys.argv[2]
s = open(src, encoding='utf-8').read()
BR = '&lt;br&gt;'
new = {
    'ql': 'QUẢN LÝ CỬA HÀNG',
    'p5-body': 'Tính cảnh báo nguy cơ' + BR + '(Python chạy thủ công)',
    'd5a-body': 'Kết quả' + BR + 'cảnh báo',
    'f2': '2. Hồ sơ, thông báo,' + BR + 'kết quả đã duyệt',
    'f5': '5. Tài khoản, nhắc việc,' + BR + 'sự cố, hỗ trợ',
    'f8': '8. Nội dung, tiêu chuẩn,' + BR + 'tiến độ, điểm',
    'f12': '12. Hồ sơ đánh giá, lý do' + BR + 'trả lại, nhiệm vụ đào tạo',
    'f13': '13. Quyết định duyệt/trả lại,' + BR + 'giao vòng, lịch đánh giá lại',
    'f14': '14. Hồ sơ, lịch sử vòng,' + BR + 'thông báo dừng đào tạo lại',
    'f16': '16. Hồ sơ, thống kê,' + BR + 'kết quả cảnh báo',
    'f27': '27. Thông báo lịch, kết quả,' + BR + 'phân công, dừng đào tạo lại',
    'f33': '33. Kết quả cảnh báo' + BR + 'đã lưu',
    'f35': '35. Tiến độ học,' + BR + 'kết quả kiểm tra',
    'f36': '36. Kết quả' + BR + 'cảnh báo',
    'legend': 'Kho lặp cùng mã là cùng một kho logic. Tiến trình 5.0 do người vận hành chạy thủ công bằng Python, chỉ ghi kết quả vào D5 và không ghi vào D4.',
}
for key, value in new.items():
    ids = [key] if not key.startswith('f') else [key, key + '-label']
    for cid in ids:
        pat = re.compile(r'(<mxCell id="' + re.escape(cid) + r'" value=")[^"]*(")')
        s, n = pat.subn(lambda m: m.group(1) + value + m.group(2), s)
        assert n == 1, (cid, n)
assert 'Dự đoán' not in s and 'dự đoán' not in s and 'nguy cơ"' not in s.replace('Tính cảnh báo nguy cơ', '')
open(dst, 'w', encoding='utf-8').write(s)
print('ok', len(new))
