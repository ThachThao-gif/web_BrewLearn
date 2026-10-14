# Đối chiếu nhãn ERD với cơ sở dữ liệu
Chỉ đổi nhãn hiển thị, không đổi cấu trúc database. FK trên hình chỉ đánh dấu ràng buộc khóa ngoại đã khai báo. Các đường còn lại thể hiện liên kết được ứng dụng sử dụng.

## Tài khoản và nhân sự — `users`
| Nhãn | Tên cột | Kiểu dữ liệu | Rỗng |
|---|---|---|---|
| Mã bản ghi | `id` | `BIGINT UNSIGNED` | NOT NULL |
| Họ tên | `name` | `VARCHAR(191)` | NOT NULL |
| Tên hiển thị | `name_tag` | `VARCHAR(191)` | NULL |
| Email | `email` | `VARCHAR(191)` | NOT NULL |
| Ngày vào làm | `joined_at` | `DATE` | NULL |
| Email phụ | `secondary_email` | `VARCHAR(191)` | NULL |
| Số điện thoại | `phone` | `VARCHAR(191)` | NULL |
| Thời điểm xác minh email | `email_verified_at` | `TIMESTAMP` | NULL |
| Mật khẩu mã hóa | `password` | `VARCHAR(191)` | NOT NULL |
| Mã ghi nhớ đăng nhập | `remember_token` | `VARCHAR(100)` | NULL |
| Thời điểm tạo | `created_at` | `TIMESTAMP` | NULL |
| Thời điểm cập nhật | `updated_at` | `TIMESTAMP` | NULL |
| Chức danh | `ChucVu` | `VARCHAR(100)` | NULL |
| Chi nhánh (cột cũ) | `ChiNhanh` | `VARCHAR(100)` | NULL |
| Mã cửa hàng | `cua_hang_id` | `BIGINT UNSIGNED` | NULL |
| Cấp độ | `CapDo` | `INT` | NULL |
| Tiến độ tổng | `TienDoTong` | `INT` | NULL |
| Ngày sinh | `dob` | `DATE` | NULL |
| Số giấy tờ định danh | `id_card` | `VARCHAR(191)` | NULL |
| Trạm làm việc | `tram_lam_viec` | `VARCHAR(191)` | NULL |
| Người liên hệ khẩn cấp | `emergency_contact` | `VARCHAR(191)` | NULL |
| Điện thoại khẩn cấp | `emergency_phone` | `VARCHAR(191)` | NULL |
| Giao diện | `theme` | `VARCHAR(20)` | NULL |
| Ngôn ngữ | `language` | `VARCHAR(10)` | NULL |
| Cài đặt thông báo | `notification_settings` | `JSON` | NULL |
| Mã ca làm việc | `ca_lam_viec_id` | `BIGINT UNSIGNED` | NULL |
| Trạng thái làm việc | `employment_status` | `VARCHAR(20)` | NOT NULL |
## Vùng — `vung`
| Nhãn | Tên cột | Kiểu dữ liệu | Rỗng |
|---|---|---|---|
| Mã bản ghi | `id` | `BIGINT UNSIGNED` | NOT NULL |
| Mã định danh vùng | `ma_vung` | `VARCHAR(20)` | NOT NULL |
| Tên vùng | `ten_vung` | `VARCHAR(191)` | NOT NULL |
| Mã người quản lý | `nguoi_quan_ly_id` | `BIGINT UNSIGNED` | NULL |
| Thời điểm tạo | `created_at` | `TIMESTAMP` | NULL |
| Thời điểm cập nhật | `updated_at` | `TIMESTAMP` | NULL |
## Cửa hàng — `cua_hang`
| Nhãn | Tên cột | Kiểu dữ liệu | Rỗng |
|---|---|---|---|
| Mã bản ghi | `id` | `BIGINT UNSIGNED` | NOT NULL |
| Mã vùng | `vung_id` | `BIGINT UNSIGNED` | NOT NULL |
| Mã định danh cửa hàng | `ma_cua_hang` | `VARCHAR(20)` | NOT NULL |
| Tên cửa hàng | `ten_cua_hang` | `VARCHAR(191)` | NOT NULL |
| Địa chỉ | `dia_chi` | `TEXT` | NULL |
| Thời điểm tạo | `created_at` | `TIMESTAMP` | NULL |
| Thời điểm cập nhật | `updated_at` | `TIMESTAMP` | NULL |
## Ca làm việc — `ca_lam_viec`
| Nhãn | Tên cột | Kiểu dữ liệu | Rỗng |
|---|---|---|---|
| Mã bản ghi | `id` | `BIGINT UNSIGNED` | NOT NULL |
| Mã cửa hàng | `cua_hang_id` | `BIGINT UNSIGNED` | NOT NULL |
| Tên ca | `ten_ca` | `VARCHAR(191)` | NOT NULL |
| Giờ bắt đầu | `bat_dau` | `TIME` | NULL |
| Giờ kết thúc | `ket_thuc` | `TIME` | NULL |
| Thời điểm tạo | `created_at` | `TIMESTAMP` | NULL |
| Thời điểm cập nhật | `updated_at` | `TIMESTAMP` | NULL |
## Chương trình đào tạo — `chuong_trinh_dao_tao`
| Nhãn | Tên cột | Kiểu dữ liệu | Rỗng |
|---|---|---|---|
| Mã chương trình đào tạo | `MaCTDT` | `INT` | NOT NULL |
| Tên chương trình đào tạo | `TenCTDT` | `VARCHAR(191)` | NOT NULL |
| Mô tả | `MoTa` | `TEXT` | NULL |
| Chức danh áp dụng | `ChucVuApDung` | `VARCHAR(191)` | NULL |
| Cấp độ | `CapDo` | `INT` | NULL |
## Bài học — `bai_hoc`
| Nhãn | Tên cột | Kiểu dữ liệu | Rỗng |
|---|---|---|---|
| Mã bài học | `MaBaiHoc` | `INT` | NOT NULL |
| Mã chương trình đào tạo | `FK_MaCTDT` | `INT` | NULL |
| Tên bài học | `TenBaiHoc` | `VARCHAR(191)` | NOT NULL |
| Nội dung | `NoiDung` | `TEXT` | NULL |
| Đường dẫn video | `VideoURL` | `VARCHAR(191)` | NULL |
| Thứ tự | `ThuTu` | `INT` | NOT NULL |
## Tiến độ khóa học — `nhan_vien_khoa_hoc`
| Nhãn | Tên cột | Kiểu dữ liệu | Rỗng |
|---|---|---|---|
| Mã bản ghi | `id` | `INT` | NOT NULL |
| Mã tài khoản | `user_id` | `BIGINT UNSIGNED` | NOT NULL |
| Mã chương trình đào tạo | `MaCTDT` | `INT` | NOT NULL |
| Tiến độ | `TienDo` | `DECIMAL(5,1)` | NULL |
| Trạng thái | `TrangThai` | `VARCHAR(50)` | NULL |
| Hạn hoàn thành | `han_hoan_thanh` | `DATE` | NULL |
| Thời điểm tạo | `created_at` | `TIMESTAMP` | NULL |
| Thời điểm cập nhật | `updated_at` | `TIMESTAMP` | NULL |
| Mốc tiến độ mô phỏng | `demo_progress_baseline` | `DOUBLE` | NULL |
## Tiến độ bài học — `user_lesson_progresses`
| Nhãn | Tên cột | Kiểu dữ liệu | Rỗng |
|---|---|---|---|
| Mã bản ghi | `id` | `BIGINT UNSIGNED` | NOT NULL |
| Mã tài khoản | `user_id` | `BIGINT UNSIGNED` | NOT NULL |
| Mã bài học | `lesson_id` | `BIGINT UNSIGNED` | NOT NULL |
| Vị trí xem gần nhất (giây) | `last_watched_seconds` | `DOUBLE` | NOT NULL |
| Tiến độ (%) | `progress_percent` | `INT` | NOT NULL |
| Đã hoàn thành | `is_completed` | `TINYINT(1)` | NOT NULL |
| Số lượt truy cập | `visit_count` | `INT` | NOT NULL |
| Thời điểm tạo | `created_at` | `TIMESTAMP` | NULL |
| Thời điểm cập nhật | `updated_at` | `TIMESTAMP` | NULL |
## Bài kiểm tra — `bai_kiem_tra`
| Nhãn | Tên cột | Kiểu dữ liệu | Rỗng |
|---|---|---|---|
| Mã bài kiểm tra | `MaBaiKiemTra` | `INT` | NOT NULL |
| Tên bài kiểm tra | `TenBaiKiemTra` | `VARCHAR(191)` | NOT NULL |
| Thời gian làm bài | `ThoiGianLamBai` | `INT` | NULL |
| Điểm chuẩn | `DiemChuan` | `INT` | NOT NULL |
| Mã bài học | `MaBaiHoc` | `INT` | NULL |
| Chức danh | `ChucVu` | `VARCHAR(50)` | NULL |
| Thời điểm tạo | `created_at` | `TIMESTAMP` | NULL |
| Thời điểm cập nhật | `updated_at` | `TIMESTAMP` | NULL |
## Câu hỏi kiểm tra — `cau_hoi_kiem_tra`
| Nhãn | Tên cột | Kiểu dữ liệu | Rỗng |
|---|---|---|---|
| Mã bản ghi | `id` | `INT` | NOT NULL |
| Mã bài kiểm tra | `MaBaiKiemTra` | `INT` | NOT NULL |
| Câu hỏi | `CauHoi` | `TEXT` | NOT NULL |
| Phương án A | `PhuongAnA` | `TEXT` | NOT NULL |
| Phương án B | `PhuongAnB` | `TEXT` | NOT NULL |
| Phương án C | `PhuongAnC` | `TEXT` | NOT NULL |
| Phương án D | `PhuongAnD` | `TEXT` | NOT NULL |
| Đáp án đúng | `DapAnDung` | `VARCHAR(1)` | NOT NULL |
| Giải thích | `GiaiThich` | `TEXT` | NULL |
## Kết quả kiểm tra — `ket_qua_kiem_tra`
| Nhãn | Tên cột | Kiểu dữ liệu | Rỗng |
|---|---|---|---|
| Mã bản ghi | `id` | `INT` | NOT NULL |
| Mã tài khoản | `user_id` | `BIGINT UNSIGNED` | NOT NULL |
| Mã bài kiểm tra | `MaBaiKiemTra` | `INT` | NOT NULL |
| Điểm | `Diem` | `INT` | NOT NULL |
| Trạng thái | `TrangThai` | `VARCHAR(20)` | NOT NULL |
| Số câu đúng | `SoCauDung` | `INT` | NULL |
| Tổng số câu | `TongSoCau` | `INT` | NULL |
| Thời điểm tạo | `created_at` | `TIMESTAMP` | NULL |
| Thời điểm cập nhật | `updated_at` | `TIMESTAMP` | NULL |
| Mã nhận diện lượt nộp | `attempt_token` | `CHAR(36)` | NULL |
## Đánh giá năng lực — `danh_gia_nang_luc`
| Nhãn | Tên cột | Kiểu dữ liệu | Rỗng |
|---|---|---|---|
| Mã bản ghi | `id` | `BIGINT UNSIGNED` | NOT NULL |
| Mã tài khoản | `user_id` | `BIGINT UNSIGNED` | NOT NULL |
| Mã người đánh giá | `NguoiDanhGia_id` | `BIGINT UNSIGNED` | NULL |
| Mã chương trình đào tạo | `MaCTDT` | `INT` | NULL |
| Tiêu chuẩn | `TieuChuan` | `VARCHAR(191)` | NULL |
| Thời điểm đánh giá | `NgayDanhGia` | `TIMESTAMP` | NULL |
| Thời điểm hẹn | `NgayHen` | `DATETIME` | NULL |
| Điểm | `Diem` | `DECIMAL(5,1)` | NULL |
| Nhận xét | `NhanXet` | `TEXT` | NULL |
| Trạng thái | `TrangThai` | `VARCHAR(50)` | NULL |
| Thời điểm tạo | `created_at` | `TIMESTAMP` | NULL |
| Thời điểm cập nhật | `updated_at` | `TIMESTAMP` | NULL |
| Điểm nghiệp vụ | `Diem_NghiepVu` | `FLOAT` | NULL |
| Điểm kỹ năng | `Diem_KyNang` | `FLOAT` | NULL |
| Điểm quy trình | `Diem_QuyTrinh` | `FLOAT` | NULL |
| Điểm phục vụ | `Diem_PhucVu` | `FLOAT` | NULL |
| Điểm tác phong | `Diem_TacPhong` | `FLOAT` | NULL |
| Đề xuất xử lý | `DeXuatXuLy` | `JSON` | NULL |
| Chi tiết đề xuất | `ChiTietDeXuat` | `JSON` | NULL |
| Chi tiết điểm | `ChiTietDiem` | `JSON` | NULL |
| Kết quả đạt | `IsPassed` | `TINYINT(1)` | NULL |
| Mã người duyệt | `nguoi_duyet_id` | `BIGINT UNSIGNED` | NULL |
| Thời điểm duyệt | `ngay_duyet` | `DATETIME` | NULL |
| Lý do trả lại | `ly_do_tra_lai` | `TEXT` | NULL |
## Lỗi nghiệp vụ — `loi_nghiep_vu`
| Nhãn | Tên cột | Kiểu dữ liệu | Rỗng |
|---|---|---|---|
| Mã bản ghi | `id` | `BIGINT UNSIGNED` | NOT NULL |
| Mã tài khoản | `user_id` | `BIGINT UNSIGNED` | NOT NULL |
| Mã người ghi nhận | `nguoi_ghi_nhan_id` | `BIGINT UNSIGNED` | NOT NULL |
| Loại lỗi | `loai_loi` | `VARCHAR(100)` | NOT NULL |
| Mức độ | `muc_do` | `ENUM('NHANH','TRUNGBINH','NGHIEMTRONG')` | NOT NULL |
| Điểm trừ nguy cơ (cột cũ) | `diem_tru_risk` | `INT` | NOT NULL |
| Mô tả | `mo_ta` | `TEXT` | NOT NULL |
| Thời điểm xảy ra | `thoi_diem_xay_ra` | `DATETIME` | NOT NULL |
| Mã chi nhánh (cột cũ) | `chi_nhanh_id` | `INT` | NULL |
| Thời điểm tạo | `created_at` | `TIMESTAMP` | NULL |
| Thời điểm cập nhật | `updated_at` | `TIMESTAMP` | NULL |
## Dự đoán nguy cơ — `du_doan_nguy_co`
| Nhãn | Tên cột | Kiểu dữ liệu | Rỗng |
|---|---|---|---|
| Mã bản ghi | `id` | `BIGINT UNSIGNED` | NOT NULL |
| Mã tài khoản | `user_id` | `BIGINT UNSIGNED` | NOT NULL |
| Mã cửa hàng | `cua_hang_id` | `BIGINT UNSIGNED` | NULL |
| Điểm nguy cơ | `diem_rui_ro` | `FLOAT` | NULL |
| Mức cảnh báo | `muc_canh_bao` | `ENUM('CHUADUDULIEU','THAP','TRUNGBINH','CAO')` | NOT NULL |
| Chi tiết dữ liệu đầu vào | `chi_tiet_yeu_to` | `JSON` | NULL |
| Thông tin mô hình | `phien_ban_mo_hinh` | `VARCHAR(50)` | NULL |
| Thời điểm dự đoán | `ngay_du_doan` | `TIMESTAMP` | NULL |
| Thời điểm tạo | `created_at` | `TIMESTAMP` | NULL |
| Thời điểm cập nhật | `updated_at` | `TIMESTAMP` | NULL |
## Yêu cầu đào tạo lại — `yeu_cau_dao_tao_lai`
| Nhãn | Tên cột | Kiểu dữ liệu | Rỗng |
|---|---|---|---|
| Mã bản ghi | `id` | `BIGINT UNSIGNED` | NOT NULL |
| Mã tài khoản | `user_id` | `BIGINT UNSIGNED` | NOT NULL |
| Mã dự đoán | `du_doan_id` | `BIGINT UNSIGNED` | NULL |
| Mã người đề xuất | `nguoi_de_xuat_id` | `BIGINT UNSIGNED` | NULL |
| Mã người duyệt | `nguoi_duyet_id` | `BIGINT UNSIGNED` | NULL |
| Lý do | `ly_do` | `TEXT` | NULL |
| Trạng thái | `trang_thai` | `ENUM('CHODUYET','DADUYET','TUCHOI','HOANTHANH')` | NOT NULL |
| Kết quả sau đào tạo lại | `ket_qua_sau_dao_tao_lai` | `ENUM('CHUACOKETQUA','DAT','CHUADAT')` | NOT NULL |
| Thời điểm đề xuất | `ngay_de_xuat` | `TIMESTAMP` | NULL |
| Thời điểm duyệt | `ngay_duyet` | `TIMESTAMP` | NULL |
| Thời điểm tạo | `created_at` | `TIMESTAMP` | NULL |
| Thời điểm cập nhật | `updated_at` | `TIMESTAMP` | NULL |
| Mã đánh giá nguồn | `assessment_id` | `BIGINT UNSIGNED` | NULL |
| Nguồn đề xuất | `nguon_de_xuat` | `VARCHAR(191)` | NULL |
| Kế hoạch | `ke_hoach` | `JSON` | NULL |
| Mã người phụ trách | `nguoi_phu_trach_id` | `BIGINT UNSIGNED` | NULL |
| Hạn hoàn thành | `han_hoan_thanh` | `DATE` | NULL |
| Ngày đánh giá lại | `ngay_danh_gia_lai` | `DATE` | NULL |
| Tiến trình | `tien_trinh` | `VARCHAR(191)` | NOT NULL |
| Ghi chú thực hiện | `ghi_chu_thuc_hien` | `TEXT` | NULL |
| Mã đánh giá lại | `danh_gia_lai_id` | `BIGINT UNSIGNED` | NULL |
## Vòng đào tạo lại — `retraining_rounds`
| Nhãn | Tên cột | Kiểu dữ liệu | Rỗng |
|---|---|---|---|
| Mã bản ghi | `id` | `BIGINT UNSIGNED` | NOT NULL |
| Mã yêu cầu đào tạo lại | `request_id` | `BIGINT UNSIGNED` | NOT NULL |
| Số thứ tự vòng | `round_number` | `INT UNSIGNED` | NOT NULL |
| Mã người giao vòng | `assigned_by` | `BIGINT UNSIGNED` | NOT NULL |
| Mã người phụ trách vòng | `owner_id` | `BIGINT UNSIGNED` | NULL |
| Nội dung hướng dẫn | `instructions` | `TEXT` | NULL |
| Nội dung đề xuất | `proposal` | `JSON` | NULL |
| Hạn hoàn thành | `deadline` | `DATE` | NULL |
| Ngày đánh giá lại | `assessment_date` | `DATE` | NULL |
| Mã đánh giá lại của vòng | `assessment_id` | `BIGINT UNSIGNED` | NULL |
| Trạng thái vòng | `status` | `VARCHAR(191)` | NOT NULL |
| Kết quả vòng | `result` | `VARCHAR(191)` | NOT NULL |
| Điểm đánh giá | `score` | `DECIMAL(5,1)` | NULL |
| Mã người duyệt | `approved_by` | `BIGINT UNSIGNED` | NULL |
| Thời điểm duyệt | `approved_at` | `TIMESTAMP` | NULL |
| Thời điểm tạo | `created_at` | `TIMESTAMP` | NULL |
| Thời điểm cập nhật | `updated_at` | `TIMESTAMP` | NULL |
| Danh sách bài học yêu cầu | `required_lessons` | `JSON` | NULL |
| Danh sách bài kiểm tra yêu cầu | `required_quizzes` | `JSON` | NULL |
| Mốc dữ liệu học tập ban đầu | `learning_baseline` | `JSON` | NULL |
| Mã người xác nhận | `confirmed_by` | `BIGINT UNSIGNED` | NULL |
| Thời điểm xác nhận | `confirmed_at` | `TIMESTAMP` | NULL |
| Nội dung xác nhận | `confirmation_note` | `TEXT` | NULL |
## Thông báo — `notifications`
| Nhãn | Tên cột | Kiểu dữ liệu | Rỗng |
|---|---|---|---|
| Mã bản ghi | `id` | `BIGINT UNSIGNED` | NOT NULL |
| Mã tài khoản | `user_id` | `BIGINT UNSIGNED` | NOT NULL |
| Tiêu đề | `title` | `VARCHAR(191)` | NOT NULL |
| Nội dung thông báo | `message` | `TEXT` | NULL |
| Loại thông báo | `type` | `VARCHAR(191)` | NOT NULL |
| Đã đọc | `is_read` | `TINYINT(1)` | NOT NULL |
| Thời điểm tạo | `created_at` | `TIMESTAMP` | NULL |
| Thời điểm cập nhật | `updated_at` | `TIMESTAMP` | NULL |
| Mã sự kiện thông báo | `event_key` | `VARCHAR(191)` | NULL |
## Yêu cầu hỗ trợ — `yeu_cau_ho_tro`
| Nhãn | Tên cột | Kiểu dữ liệu | Rỗng |
|---|---|---|---|
| Mã bản ghi | `id` | `BIGINT UNSIGNED` | NOT NULL |
| Mã tài khoản | `user_id` | `BIGINT UNSIGNED` | NOT NULL |
| Loại yêu cầu | `loai_yeu_cau` | `VARCHAR(191)` | NOT NULL |
| Chủ đề | `chu_de` | `VARCHAR(191)` | NOT NULL |
| Nội dung | `noi_dung` | `TEXT` | NOT NULL |
| Mức độ ưu tiên | `muc_do_uu_tien` | `VARCHAR(191)` | NOT NULL |
| Trạng thái | `trang_thai` | `VARCHAR(191)` | NOT NULL |
| Thời điểm tạo | `created_at` | `TIMESTAMP` | NULL |
| Thời điểm cập nhật | `updated_at` | `TIMESTAMP` | NULL |