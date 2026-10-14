# Cảnh báo theo luật BrewLearn

Phương án vận hành mặc định là `rules-v2`, thống nhất với
`config/brewlearn.php`. Điểm 0–1 là điểm luật chuẩn hóa, không phải xác suất.

## Chạy thủ công

Từ thư mục gốc dự án:

```powershell
venv\Scripts\python.exe -X utf8 app/ml/ket_noi_db.py
```

Chương trình dùng cấu hình MySQL trong `.env`, chỉ xử lý Barista, Thu ngân, Phục vụ đang làm có
cửa hàng; loại trưởng ca, quản lý và HR. Mỗi nhân viên được đọc trong một
transaction có snapshot nhất quán. Thời điểm lấy từ UTC_TIMESTAMP() của MySQL, cùng quy ước UTC của Laravel, được dùng
cả để lọc quiz và lưu `ngay_du_doan`. Mỗi lần thành công thêm bản ghi mới;
không xóa cảnh báo ML cũ hoặc sửa dữ liệu học tập, đánh giá, đào tạo lại.

## Dữ liệu và luật

- Tiến độ: trung bình `TienDo` của khóa áp dụng theo chức danh và ALL STAFF;
  khóa áp dụng chưa bắt đầu tính 0. Tiến độ khóa được Laravel đồng bộ khi học.
- Quiz: chỉ bài liên kết với khóa áp dụng; lấy `created_at DESC, id DESC`
  đến thời điểm tính. Từng bài chỉ góp một kết quả, dùng `DiemChuan`, mặc định
  80 nếu NULL. Không tự dùng lượt cũ nếu lượt mới nhất bị lỗi dữ liệu.
- Đủ dữ liệu: có khóa áp dụng và ít nhất hai bài khác nhau đã làm. Không yêu
  cầu lượt mở bài; số lượt chỉ giữ để tương thích và truy vết.
- Điểm quiz trung bình <60: 3; từ 60 đến <80: 2; còn lại: 0.
- Tỷ lệ bài chưa đạt >=0,5: 3; >0 đến <0,5: 1; bằng 0: 0.
- Tiến độ <50: 2; từ 50 đến <80: 1; còn lại: 0.
- Tổng 0–2: Thấp; 3–4: Trung bình; 5–8: Cao. Điểm chuẩn hóa = tổng/8.
- So ngưỡng trước khi làm tròn. Dữ liệu thiếu lưu NULL với phiên bản rules-v2;
  dữ liệu không hợp lệ hoặc lỗi truy vấn được ghi lỗi, rollback riêng nhân viên.

JSON lưu đầu vào, số bài đã làm/tổng bài áp dụng, mã các kết quả quiz được
chọn, tổng và điểm thành phần, lý do, thời điểm tính. Giao diện mặc định chỉ
đọc rules-v2; chưa có kết quả và chưa đủ dữ liệu là hai nhóm khác nhau.

## Kiểm thử

```powershell
python -m unittest discover -s tests/python -v
php vendor/phpunit/phpunit/phpunit --do-not-cache-result --filter ManagerWorkflowTest
```

Các kiểm thử Python dùng SQLite bộ nhớ để kiểm tra các truy vấn tổng hợp,
ngoài kiểm thử biên của luật. Chạy thủ công trên MySQL để xác nhận tích hợp
trước khi dùng. Không có lịch chạy tự động; luôn đối chiếu thời điểm trên UI.

## Phần ML thực nghiệm

`train_model.py`, dữ liệu CSV và các tệp mô hình `.pkl` vẫn được giữ riêng.
Không cần scikit-learn hoặc mô hình `.pkl` để chạy luật. Script
`tinh_diem_rui_ro.py` là thử nghiệm cũ, không phải bộ luật vận hành này.
Kết quả thử nghiệm chưa xác nhận khả năng dự đoán năng lực thực tế.
