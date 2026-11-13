# BrewLearn

Nguyên mẫu web của đề tài **“Phân tích và thiết kế hệ thống thông tin quản lý đào tạo và cảnh báo sớm nguy cơ nhân viên chưa đạt chuẩn năng lực tại chuỗi cửa hàng cà phê BrewLearn”** – Đồ án chuyên ngành, Khoa Hệ thống thông tin quản lý, Trường Đại học Ngân hàng Thành phố Hồ Chí Minh.

- Sinh viên thực hiện: Bùi Thị Thạch Thảo – MSSV 030239230215 – Lớp DH39CDS02
- Giảng viên hướng dẫn: TS. Phạm Xuân Kiên
- Báo cáo đồ án: [`report/Đồ án HTTTQL.final.docx`](report/)

> Bối cảnh nghiệp vụ và dữ liệu trong nguyên mẫu là dữ liệu giả định, dùng để minh họa thiết kế.
>
> Dữ liệu minh họa được dựng tại ngày **29/10/2026**. Với cấu hình mẫu (`BREWLEARN_DEMO_TODAY=2026-10-29` trong `.env.example`), ứng dụng và chương trình cảnh báo coi ngày này là "hôm nay" (giờ phút vẫn theo đồng hồ thật), nên hạn đánh giá, số lỗi trong 30 ngày và mức cảnh báo khớp với báo cáo. Để trống biến này nếu muốn dùng ngày thật.

## Chức năng chính

| Vai trò | Chức năng |
|---|---|
| Nhân viên (Barista, Thu ngân, Phục vụ) | Học bài theo chức danh, làm và làm lại bài kiểm tra, xem lịch và kết quả đánh giá đã duyệt, thực hiện kế hoạch đào tạo lại |
| Trưởng ca | Theo dõi nhân viên cùng cửa hàng, cùng ca; tạo lịch và chấm điểm năng lực theo năm tiêu chí; đề xuất đào tạo lại; xác nhận hoàn thành vòng đào tạo lại; ghi nhận lỗi nghiệp vụ |
| Quản lý cửa hàng | Phê duyệt hoặc trả lại kết quả đánh giá, giao vòng đào tạo lại (tối đa ba vòng), tạo lịch đánh giá lại, xem thông tin cảnh báo nguy cơ |

Cảnh báo nguy cơ được tính theo bộ quy tắc `rules-v4` (mục 4.5 của báo cáo) bằng chương trình Python trong `app/risk_rules/` (không dùng học máy), kèm lý do cảnh báo.

## Công nghệ

- PHP 8.3, Laravel 13, MySQL (WAMP), Blade, Tailwind CSS 4, Vite
- Python 3 (`pymysql`, `python-dotenv`) cho chương trình cảnh báo
- Selenium WebDriver cho kiểm thử chức năng

## Cài đặt

Yêu cầu: PHP 8.3+, Composer, Node.js, MySQL 8+, Python 3, trình duyệt Chrome (để chạy kiểm thử).

1. Cài thư viện và tạo tệp cấu hình:

   ```bash
   composer install
   npm install
   copy .env.example .env
   php artisan key:generate
   ```

2. Tạo cơ sở dữ liệu `brewlearn` và nạp dữ liệu minh họa (cấu trúc bảng và dữ liệu trong Phụ lục 1 của báo cáo):

   ```bash
   mysql -uroot -e "CREATE DATABASE brewlearn CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
   mysql -uroot --default-character-set=utf8mb4 brewlearn < database/brewlearn.sql
   ```

3. Tạo môi trường Python cho chương trình cảnh báo:

   ```bash
   python -m venv venv
   venv\Scripts\pip install -r app/risk_rules/requirements.txt
   ```

4. Biên dịch giao diện và chạy ứng dụng tại http://localhost:8000:

   ```bash
   npm run build
   php artisan serve
   ```

## Tài khoản minh họa

Các tài khoản thuộc cửa hàng Flagship Quận 1 (dữ liệu minh họa ở Phụ lục 1 của báo cáo):

| Vai trò | Email |
|---|---|
| Quản lý cửa hàng | ngocbich@brewlearn.com |
| Trưởng ca | minhthu@brewlearn.com |
| Nhân viên (Thu ngân) | vannam@brewlearn.com |
| Nhân viên (Barista) – đang đào tạo lại | quocbao.demo@brewlearn.com |
| Nhân viên (Barista) – dừng sau ba vòng | hoanglong.demo@brewlearn.com |
| Nhân viên (Barista) – hoàn tất đào tạo lại sau khi đánh giá lại Đạt | thanhtung.demo@brewlearn.com |

## Lệnh nghiệp vụ

```bash
php artisan brewlearn:calculate-risk       # tính cảnh báo nguy cơ (lịch chạy 01:00 hằng ngày)
php artisan brewlearn:remind-assessments   # nhắc hạn đánh giá định kỳ và hạn đào tạo lại (08:00 hằng ngày)
php artisan schedule:work                  # chạy bộ lập lịch khi thử trên máy cá nhân
```

## Kiểm thử

Bảy kịch bản Selenium (KB01–KB07, 25 ca kiểm thử – mục 5.5 và Phụ lục 4 của báo cáo) nằm trong `tests/selenium/test_luong_nghiep_vu.py`. Chương trình tự sao chép cơ sở dữ liệu sang `brewlearn_selenium` và chạy máy chủ riêng ở cổng 8001, nên dữ liệu minh họa không bị thay đổi.

```bash
venv\Scripts\python.exe -m unittest tests.selenium.test_luong_nghiep_vu -v
```

Đặt biến môi trường `HEADLESS=1` để chạy Chrome ẩn. Nếu MySQL không cài ở `D:\wamp64\bin\mysql\mysql9.1.0\bin`, đặt biến `MYSQL_BIN` trỏ tới thư mục chứa `mysql` và `mysqldump`.

## Tên bảng trong báo cáo và trong cơ sở dữ liệu

Mục 4.2, ERD (Hình 4.2) và từ điển dữ liệu (Phụ lục 7) của báo cáo dùng **tên thiết kế mức logic**: tiếng Việt không dấu, chữ thường, nối bằng dấu gạch dưới. Cơ sở dữ liệu của nguyên mẫu (`database/brewlearn.sql`) giữ một số tên theo quy ước của Laravel hoặc theo cách đặt ban đầu, nên tên vật lý khác nhưng cấu trúc và quan hệ giữ nguyên như thiết kế.

Bốn bảng có tên khác:

| Tên trong báo cáo | Tên trong cơ sở dữ liệu |
|---|---|
| `nguoi_dung` | `users` |
| `thong_bao` | `notifications` |
| `tien_do_bai_hoc` | `user_lesson_progresses` |
| `vong_dao_tao_lai` | `retraining_rounds` |

14 bảng còn lại trùng tên. Khác biệt về tên cột:

| Trong báo cáo | Trong cơ sở dữ liệu |
|---|---|
| `nguoi_dung_id` (khóa ngoại tới người dùng) | `user_id` |
| Cột tiếng Việt viết thường, ví dụ `ma_bai_hoc`, `ma_ctdt`, `diem_chuan`, `diem_nghiep_vu`, `dat_yeu_cau` | Viết hoa đầu từ, ví dụ `MaBaiHoc`, `FK_MaCTDT` (trong `bai_hoc`), `DiemChuan`, `Diem_NghiepVu`, `IsPassed` |
| `danh_gia_nang_luc.nguoi_danh_gia_id` | `NguoiDanhGia_id` |
| `yeu_cau_dao_tao_lai.danh_gia_nguon_id` | `assessment_id` |
| `ket_qua_kiem_tra.ma_luot_nop` | `attempt_token` |
| `nguoi_dung`: `ho_ten`, `chuc_vu`, `ngay_vao_lam`, `trang_thai_lam_viec`, `cap_do` | `users`: `name`, `ChucVu`, `joined_at`, `employment_status`, `CapDo` |
| `thong_bao`: `tieu_de`, `noi_dung`, `loai`, `da_doc`, `ma_su_kien` | `notifications`: `title`, `message`, `type`, `is_read`, `event_key` |
| `tien_do_bai_hoc`: `ma_bai_hoc`, `so_giay_xem_gan_nhat`, `phan_tram_tien_do`, `da_hoan_thanh`, `so_luot_hoc` | `user_lesson_progresses`: `lesson_id`, `last_watched_seconds`, `progress_percent`, `is_completed`, `visit_count` |
| `vong_dao_tao_lai`: `yeu_cau_id`, `so_vong`, `nguoi_giao_id`, `nguoi_phu_trach_id`, `noi_dung_huong_dan`, `noi_dung_de_xuat`, `han_hoan_thanh`, `ngay_danh_gia_lai`, `danh_gia_lai_id`, `trang_thai`, `ket_qua`, `diem`, `nguoi_duyet_id`, `ngay_duyet`, `danh_sach_bai_hoc_yeu_cau`, `danh_sach_bai_kiem_tra_yeu_cau`, `moc_du_lieu_hoc_tap_ban_dau`, `nguoi_xac_nhan_id`, `ngay_xac_nhan`, `ghi_chu_xac_nhan` | `retraining_rounds`: `request_id`, `round_number`, `assigned_by`, `owner_id`, `instructions`, `proposal`, `deadline`, `assessment_date`, `assessment_id`, `status`, `result`, `score`, `approved_by`, `approved_at`, `required_lessons`, `required_quizzes`, `learning_baseline`, `confirmed_by`, `confirmed_at`, `confirmation_note` |

Ngoài 18 bảng nghiệp vụ, cơ sở dữ liệu còn các bảng hệ thống của Laravel (`cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `migrations`, `password_reset_tokens`, `sessions`); các bảng này không thuộc thiết kế nghiệp vụ nên không có trong báo cáo.

## Cấu trúc thư mục chính

| Thư mục | Nội dung |
|---|---|
| `app/Http/Controllers` | Xử lý yêu cầu theo từng vai trò (nhân viên, trưởng ca, quản lý cửa hàng) |
| `app/Models` | Người dùng và các quy tắc nghiệp vụ: điều kiện đánh giá, nhắc hạn, tiến độ học, hồ sơ nhân viên |
| `app/Console/Commands` | Lệnh tính cảnh báo, nhắc hạn và dời mốc ngày của dữ liệu minh họa |
| `app/risk_rules` | Chương trình Python tính cảnh báo theo bộ quy tắc |
| `resources/views` | Giao diện theo ba vai trò |
| `database/brewlearn.sql` | Cấu trúc và dữ liệu minh họa đầy đủ (dùng để cài đặt) |
| `database/migrations` | Lịch sử thay đổi cấu trúc trong quá trình phát triển |
| `public/videos` | Video bài học minh họa |
| `tests/selenium` | Kịch bản kiểm thử chức năng |
| `report` | Báo cáo đồ án bản cuối |
