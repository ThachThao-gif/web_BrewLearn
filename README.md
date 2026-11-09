# BrewLearn

Nguyên mẫu web của đề tài **“Phân tích và thiết kế hệ thống thông tin quản lý đào tạo và cảnh báo sớm nguy cơ nhân viên chưa đạt chuẩn năng lực tại chuỗi cửa hàng cà phê BrewLearn”** – Đồ án chuyên ngành, Khoa Hệ thống thông tin quản lý, Trường Đại học Ngân hàng Thành phố Hồ Chí Minh.

- Sinh viên thực hiện: Bùi Thị Thạch Thảo – MSSV 030239230215 – Lớp DH39CDS02
- Giảng viên hướng dẫn: TS. Phạm Xuân Kiên
- Báo cáo đồ án: [`report/ĐỒ ÁN_BREWLEARN_ban-cuoi-v4-dong-bo.docx`](report/)

> Bối cảnh nghiệp vụ và dữ liệu trong nguyên mẫu là dữ liệu giả định, dùng để minh họa thiết kế.

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

Các tài khoản thuộc cửa hàng Flagship Quận 1 (Bảng PL2.1 của báo cáo):

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

Bảy kịch bản Selenium (KB01–KB07, 25 ca kiểm thử – mục 5.5 và Phụ lục 5 của báo cáo) nằm trong `tests/selenium/test_luong_nghiep_vu.py`. Chương trình tự sao chép cơ sở dữ liệu sang `brewlearn_selenium` và chạy máy chủ riêng ở cổng 8001, nên dữ liệu minh họa không bị thay đổi.

```bash
venv\Scripts\python.exe -m unittest tests.selenium.test_luong_nghiep_vu -v
```

Đặt biến môi trường `HEADLESS=1` để chạy Chrome ẩn. Nếu MySQL không cài ở `D:\wamp64\bin\mysql\mysql9.1.0\bin`, đặt biến `MYSQL_BIN` trỏ tới thư mục chứa `mysql` và `mysqldump`.

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
