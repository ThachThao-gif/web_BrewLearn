# Cảnh báo theo bộ quy tắc BrewLearn

Phương án vận hành mặc định là `rules-v4`, thống nhất với
`config/brewlearn.php`. Điểm 0–1 là điểm quy tắc chuẩn hóa, không phải xác suất; chương trình không dùng học máy.

## Chạy thủ công

Từ thư mục gốc dự án:

```powershell
venv\Scripts\python.exe -X utf8 app/risk_rules/ket_noi_db.py
```

Chương trình dùng cấu hình MySQL trong `.env`, chỉ xử lý Barista, Thu ngân, Phục vụ đang làm có
cửa hàng; loại trưởng ca, quản lý và HR. Mỗi nhân viên được đọc trong một
transaction có snapshot nhất quán. Thời điểm lấy từ UTC_TIMESTAMP() của MySQL, cùng quy ước UTC của Laravel, được dùng
cả để lọc quiz và lưu cột `thoi_diem_tinh`. Mỗi lần thành công thêm bản ghi mới;
không ghi đè cảnh báo trước đó và không sửa dữ liệu học tập, đánh giá, đào tạo lại.

## Dữ liệu và luật

- Tiến độ: trung bình `TienDo` của khóa áp dụng theo chức danh và ALL STAFF;
  khóa áp dụng chưa bắt đầu tính 0. Tiến độ khóa được Laravel đồng bộ khi học.
- Quiz: chỉ bài liên kết với khóa áp dụng; lấy `created_at DESC, id DESC`
  đến thời điểm tính. Từng bài chỉ góp một kết quả, dùng `DiemChuan`, mặc định
  80 nếu NULL. Không tự dùng lượt cũ nếu lượt mới nhất bị lỗi dữ liệu.
- Đủ dữ liệu: có khóa áp dụng và ít nhất hai bài khác nhau đã làm. Không yêu
  cầu lượt mở bài; số lượt chỉ giữ để tương thích và truy vết.
- Kết quả kiểm tra = max(S1, S2), tối đa 3, để không tính trùng một dấu hiệu:
  S1 điểm quiz trung bình <60: 3; từ 60 đến <80: 2; còn lại: 0.
  S2 tỷ lệ bài chưa đạt >=0,5: 3; >0 đến <0,5: 1; bằng 0: 0.
- Tiến độ <50: 2; từ 50 đến <80: 1; còn lại: 0.
- Kết quả đánh giá năng lực gần nhất đã duyệt là Chưa đạt: 3; Đạt hoặc chưa có: 0.
- Lỗi nghiệp vụ trong 30 ngày: >=2 lỗi hoặc có lỗi nghiêm trọng: 2; 1 lỗi: 1; không có: 0.
- Nhóm học tập (kiểm tra + tiến độ) và nhóm thực hiện công việc (đánh giá + lỗi)
  cùng tối đa 5 điểm.
- Tổng 0–2: Thấp; 3–4: Trung bình; 5–10: Cao. Điểm chuẩn hóa = tổng/10.
- So ngưỡng trước khi làm tròn. Dữ liệu thiếu lưu NULL với phiên bản rules-v4;
  dữ liệu không hợp lệ hoặc lỗi truy vấn được ghi lỗi, rollback riêng nhân viên.

JSON lưu đầu vào, số bài đã làm/tổng bài áp dụng, mã các kết quả quiz được
chọn, tổng và điểm thành phần, lý do, thời điểm tính. Giao diện mặc định chỉ
đọc rules-v4; chưa có kết quả và chưa đủ dữ liệu là hai nhóm khác nhau.

## Kiểm thử

Kịch bản KB07 trong `tests/selenium/test_luong_nghiep_vu.py` ghi nhận lỗi,
chạy tính cảnh báo và đối chiếu kết quả với phép tính tay. Lệnh `brewlearn:calculate-risk` được lập lịch chạy lúc 01:00
hằng ngày (`routes/console.php`); luôn đối chiếu thời điểm tính trên UI.
