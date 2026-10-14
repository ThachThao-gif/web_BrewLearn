# Thiết kế cơ sở dữ liệu

## 1. Xác định thực thể và thuộc tính

Danh sách dưới đây được đối chiếu trực tiếp với cấu trúc MySQL của database `brewlearn` và mã nguồn hiện tại ngày 04/10/2026. Tên bảng, tên cột giữ nguyên theo triển khai. Ký hiệu **PK** là khóa chính. Không tự thêm tiền tố `FK_` cho cột không có tiền tố này trong database.

### 1.1. Tài khoản, nhân sự và cửa hàng

**1. users — Tài khoản và thông tin nhân sự**

users (**id [PK]**, name, name_tag, email, joined_at, secondary_email, phone, email_verified_at, password, remember_token, ChucVu, ChiNhanh, cua_hang_id, CapDo, TienDoTong, dob, id_card, tram_lam_viec, emergency_contact, emergency_phone, theme, language, notification_settings, ca_lam_viec_id, employment_status, created_at, updated_at)

`ChucVu` lưu chức danh và được dùng để xác định vai trò; các chức danh nghiệp vụ trong phạm vi báo cáo là nhân viên, trưởng ca và quản lý cửa hàng. `joined_at` là ngày vào làm; `employment_status` là trạng thái làm việc. Thông tin tài khoản và nhân viên được lưu chung, không có bảng `nhan_vien` riêng trong database đang chạy.

**2. cua_hang — Cửa hàng**

cua_hang (**id [PK]**, vung_id, ma_cua_hang, ten_cua_hang, dia_chi, created_at, updated_at)

Database hiện tại không có cột `FK_MaQLCH`. Phạm vi cửa hàng của tài khoản quản lý được xác định bằng `users.cua_hang_id` và `users.ChucVu`.

**3. ca_lam_viec — Ca làm việc**

ca_lam_viec (**id [PK]**, cua_hang_id, ten_ca, bat_dau, ket_thuc, created_at, updated_at)

Nhân viên được phân ca bằng `users.ca_lam_viec_id`. Bảng này không phải lịch chấm công hoặc bảng ghi nhận từng lượt làm việc.

**4. vung — Danh mục vùng còn tồn tại trong triển khai**

vung (**id [PK]**, ma_vung, ten_vung, nguoi_quan_ly_id, created_at, updated_at)

Bảng này vẫn được `cua_hang.vung_id` tham chiếu. Trường `nguoi_quan_ly_id` là trường còn tồn tại trong schema; không có nghĩa báo cáo phải đưa tác nhân quản lý vùng trở lại. Có thể trình bày bảng này như danh mục địa lý còn tồn tại trong triển khai. Nếu chọn thiết kế mục tiêu loại bỏ hoàn toàn vùng, cần ghi rõ đây là thiết kế đề xuất và phải sửa database, không gọi đó là cấu trúc hiện tại.

### 1.2. Đào tạo, bài học và kiểm tra

**5. chuong_trinh_dao_tao — Chương trình đào tạo**

chuong_trinh_dao_tao (**MaCTDT [PK]**, TenCTDT, MoTa, ChucVuApDung, CapDo)

Không có các cột `ThoiLuong`, `NgayTao`, `FK_MaNguoiTao` trong bảng hiện tại. Khóa học được xác định theo chức danh áp dụng.

**6. bai_hoc — Bài học**

bai_hoc (**MaBaiHoc [PK]**, FK_MaCTDT, TenBaiHoc, NoiDung, VideoURL, ThuTu)

**7. nhan_vien_khoa_hoc — Tiến độ khóa học của nhân viên**

nhan_vien_khoa_hoc (**id [PK]**, user_id, MaCTDT, TienDo, TrangThai, han_hoan_thanh, demo_progress_baseline, created_at, updated_at)

Bảng này thay cho thực thể `ghi_danh_dao_tao` của bản cũ. `demo_progress_baseline` phục vụ dữ liệu demo, không phải thuộc tính nghiệp vụ cần bổ sung vào thiết kế khái niệm. Không có các cột riêng `NgayBatDau`, `NgayHoanThanh`, `NgayDenHanDanhGia` trong bảng này.

**8. user_lesson_progresses — Tiến độ học từng bài**

user_lesson_progresses (**id [PK]**, user_id, lesson_id, last_watched_seconds, progress_percent, is_completed, visit_count, created_at, updated_at)

Tiến độ liên kết trực tiếp tài khoản với bài học; không thông qua `FK_MaGhiDanh`. `last_watched_seconds` là vị trí xem gần nhất, `progress_percent` là phần trăm tiến độ, `is_completed` là cờ hoàn thành, `visit_count` là số lượt truy cập được lưu.

**9. bai_kiem_tra — Bài kiểm tra**

bai_kiem_tra (**MaBaiKiemTra [PK]**, TenBaiKiemTra, ThoiGianLamBai, DiemChuan, MaBaiHoc, ChucVu, created_at, updated_at)

Bài kiểm tra liên kết với bài học qua `MaBaiHoc`; từ bài học xác định chương trình đào tạo. Ngưỡng đạt là `DiemChuan`. Không có cột `FK_MaCTDT`, `SoCauHoi`, `DiemDat` trong bảng thực tế.

**10. cau_hoi_kiem_tra — Câu hỏi và phương án trả lời**

cau_hoi_kiem_tra (**id [PK]**, MaBaiKiemTra, CauHoi, PhuongAnA, PhuongAnB, PhuongAnC, PhuongAnD, DapAnDung, GiaiThich)

Câu hỏi và bốn phương án nằm chung một bảng; không có hai bảng riêng `cau_hoi`, `dap_an` trong database đang chạy.

**11. ket_qua_kiem_tra — Kết quả từng lượt làm bài**

ket_qua_kiem_tra (**id [PK]**, user_id, MaBaiKiemTra, Diem, TrangThai, SoCauDung, TongSoCau, attempt_token, created_at, updated_at)

Mỗi bản ghi là một lượt làm bài; hệ thống giữ lịch sử các lượt. `attempt_token` nhận diện lượt nộp, hỗ trợ tránh lưu trùng. Không có cột `LanThi`, `NgayThi`; thời gian lưu lượt được lấy từ `created_at` và số thứ tự lượt được xác định từ lịch sử.

### 1.3. Đánh giá năng lực và lỗi nghiệp vụ

**12. danh_gia_nang_luc — Lịch và kết quả đánh giá năng lực**

danh_gia_nang_luc (**id [PK]**, user_id, NguoiDanhGia_id, MaCTDT, TieuChuan, NgayDanhGia, NgayHen, Diem, NhanXet, TrangThai, Diem_NghiepVu, Diem_KyNang, Diem_QuyTrinh, Diem_PhucVu, Diem_TacPhong, DeXuatXuLy, ChiTietDeXuat, ChiTietDiem, IsPassed, nguoi_duyet_id, ngay_duyet, ly_do_tra_lai, created_at, updated_at)

`NgayHen` là thời gian hẹn, `NgayDanhGia` là thời điểm đánh giá. `NguoiDanhGia_id` nhận diện người chấm; `nguoi_duyet_id` nhận diện người xét duyệt. `Diem` là điểm tổng, `IsPassed` là kết quả đạt/chưa đạt, `TrangThai` là trạng thái xử lý. Nội dung đề xuất và chi tiết điểm có thể được lưu dưới dạng dữ liệu JSON theo code. Bảng không có `FK_MaTC`; tiêu chuẩn và năm điểm thành phần được lưu trực tiếp. Trạng thái chờ duyệt, trả lại, đã duyệt được phân biệt với kết quả đạt/chưa đạt.

**13. loi_nghiep_vu — Lỗi nghiệp vụ**

loi_nghiep_vu (**id [PK]**, user_id, nguoi_ghi_nhan_id, loai_loi, muc_do, diem_tru_risk, mo_ta, thoi_diem_xay_ra, chi_nhanh_id, created_at, updated_at)

`diem_tru_risk` là tên cột còn tồn tại; không được suy ra rằng model ML hiện tại sử dụng lỗi nghiệp vụ làm đặc trưng. Script dự đoán hiện dùng dữ liệu học tập và quiz.

### 1.4. Dự đoán nguy cơ và đào tạo lại

**14. du_doan_nguy_co — Kết quả dự đoán đã lưu**

du_doan_nguy_co (**id [PK]**, user_id, cua_hang_id, diem_rui_ro, muc_canh_bao, chi_tiet_yeu_to, phien_ban_mo_hinh, ngay_du_doan, created_at, updated_at)

`chi_tiet_yeu_to` lưu dữ liệu đầu vào; không nên mô tả là kết luận giải thích nguyên nhân của model. Khi chưa đủ dữ liệu, `muc_canh_bao = ChuaDuDuLieu`, `diem_rui_ro` và `phien_ban_mo_hinh` để trống. Dự đoán độc lập với đánh giá của trưởng ca.

**15. yeu_cau_dao_tao_lai — Yêu cầu và kế hoạch đào tạo lại**

yeu_cau_dao_tao_lai (**id [PK]**, user_id, du_doan_id, nguoi_de_xuat_id, nguoi_duyet_id, ly_do, trang_thai, ket_qua_sau_dao_tao_lai, ngay_de_xuat, ngay_duyet, assessment_id, nguon_de_xuat, ke_hoach, nguoi_phu_trach_id, han_hoan_thanh, ngay_danh_gia_lai, tien_trinh, ghi_chu_thuc_hien, danh_gia_lai_id, created_at, updated_at)

`assessment_id` liên kết đánh giá nguồn, `danh_gia_lai_id` liên kết lượt đánh giá lại được tham chiếu trong kế hoạch. `du_doan_id` tồn tại trong schema nhưng không đồng nghĩa model tự động tạo yêu cầu; luồng hiện tại tạo yêu cầu từ đánh giá chưa đạt đã được quản lý phê duyệt. `trang_thai` biểu diễn trạng thái xét duyệt; `tien_trinh` biểu diễn tiến trình thực hiện.

**16. retraining_rounds — Các vòng đào tạo lại**

retraining_rounds (**id [PK]**, request_id, round_number, assigned_by, owner_id, instructions, proposal, deadline, assessment_date, assessment_id, status, result, score, approved_by, approved_at, required_lessons, required_quizzes, learning_baseline, confirmed_by, confirmed_at, confirmation_note, created_at, updated_at)

Một yêu cầu có nhiều vòng; mỗi vòng lưu phân công, yêu cầu học/quiz, dữ liệu mốc ban đầu, xác nhận hoàn thành và kết quả đánh giá riêng. `owner_id` là người phụ trách vòng, không phải mặc định nhân viên được đào tạo. `assessment_id` là lượt đánh giá lại của vòng. Ràng buộc duy nhất `(request_id, round_number)` giúp giữ số thứ tự vòng trong từng yêu cầu; `assessment_id` cũng có ràng buộc duy nhất.

### 1.5. Thông báo và hỗ trợ

**17. notifications — Thông báo**

notifications (**id [PK]**, user_id, title, message, type, is_read, event_key, created_at, updated_at)

Lưu thông báo cho từng tài khoản. `event_key` hỗ trợ tránh phát thông báo trùng. Nhắc hạn đánh giá được tính từ dữ liệu nhân sự và đánh giá, rồi lưu thông báo; không có bảng `canh_bao` riêng.

**18. yeu_cau_ho_tro — Yêu cầu hỗ trợ**

yeu_cau_ho_tro (**id [PK]**, user_id, loai_yeu_cau, chu_de, noi_dung, muc_do_uu_tien, trang_thai, created_at, updated_at)

## 2. Quan hệ nghiệp vụ chính

Các quan hệ dưới đây là quan hệ được schema hoặc code sử dụng. Không phải mọi cột liên kết đều đã được khai báo ràng buộc FOREIGN KEY trong MySQL.

| Quan hệ | Cột liên kết |
|---|---|
| Vùng 1–N Cửa hàng | `cua_hang.vung_id → vung.id` |
| Cửa hàng 1–N Tài khoản | `users.cua_hang_id → cua_hang.id` |
| Cửa hàng 1–N Ca | `ca_lam_viec.cua_hang_id → cua_hang.id` |
| Ca 1–N Tài khoản | `users.ca_lam_viec_id → ca_lam_viec.id` |
| Chương trình 1–N Bài học | `bai_hoc.FK_MaCTDT → chuong_trinh_dao_tao.MaCTDT` |
| Tài khoản N–N Chương trình | qua `nhan_vien_khoa_hoc` |
| Tài khoản N–N Bài học | qua `user_lesson_progresses`, cột `lesson_id` tham chiếu `MaBaiHoc` theo code |
| Bài học 1–N Bài kiểm tra | `bai_kiem_tra.MaBaiHoc → bai_hoc.MaBaiHoc` |
| Bài kiểm tra 1–N Câu hỏi | `cau_hoi_kiem_tra.MaBaiKiemTra → bai_kiem_tra.MaBaiKiemTra` |
| Tài khoản/Bài kiểm tra 1–N Kết quả | `ket_qua_kiem_tra.user_id`, `MaBaiKiemTra` |
| Tài khoản 1–N Đánh giá | `danh_gia_nang_luc.user_id → users.id` |
| Tài khoản 1–N Lượt chấm | `danh_gia_nang_luc.NguoiDanhGia_id → users.id` |
| Tài khoản 1–N Lượt duyệt | `danh_gia_nang_luc.nguoi_duyet_id → users.id` theo code |
| Tài khoản 1–N Dự đoán | `du_doan_nguy_co.user_id → users.id` |
| Tài khoản 1–N Yêu cầu đào tạo lại | `yeu_cau_dao_tao_lai.user_id → users.id` |
| Đánh giá nguồn 1–0..1 Yêu cầu đào tạo lại | `yeu_cau_dao_tao_lai.assessment_id → danh_gia_nang_luc.id`; cột nguồn có UNIQUE |
| Yêu cầu đào tạo lại 1–N Vòng | `retraining_rounds.request_id → yeu_cau_dao_tao_lai.id` theo code |
| Vòng 0..1–0..1 Đánh giá lại | `retraining_rounds.assessment_id → danh_gia_nang_luc.id` theo code; cột có UNIQUE |
| Tài khoản 1–N Vòng được giao | `retraining_rounds.owner_id → users.id` theo code |
| Tài khoản 1–N Thông báo/Yêu cầu hỗ trợ | `notifications.user_id`, `yeu_cau_ho_tro.user_id → users.id` |

## 3. Các thực thể cũ cần thay hoặc bỏ

| Bản cũ | Thực tế hiện tại |
|---|---|
| `vai_tro` | Không có bảng riêng; chức danh được lưu trong `users.ChucVu` |
| `nhan_vien` | Thay bằng `users` |
| `ghi_danh_dao_tao` | Thay bằng `nhan_vien_khoa_hoc` |
| `tien_do_hoc` | Thay bằng `user_lesson_progresses` |
| `cau_hoi`, `dap_an` | Thay bằng `cau_hoi_kiem_tra` |
| `nang_luc`, `tieu_chuan_nang_luc` | Không có bảng riêng; tiêu chuẩn và điểm nằm trong `danh_gia_nang_luc` |
| `canh_bao` | Không có bảng riêng; mức nguy cơ trong `du_doan_nguy_co`, nhắc việc trong `notifications` |
| `bao_cao` | Không có bảng riêng; dashboard tổng hợp dữ liệu khi truy vấn |

Việc có file model mang tên một thực thể cũ không chứng minh bảng đó tồn tại hoặc được luồng hiện tại sử dụng. Ưu tiên schema database đang chạy và controller đang thực thi.

## 4. Dashboard và phạm vi trình bày trong đồ án

Biểu đồ đường không phát sinh thực thể mới. Dashboard tổng hợp `danh_gia_nang_luc` theo tháng, lấy kết quả đã duyệt cuối cùng của từng nhân viên trong tháng đánh giá, trong sáu tháng gần nhất và theo bộ lọc nhân viên đang làm. Phân bố nguy cơ lấy bản ghi dự đoán mới nhất từ `du_doan_nguy_co`. Không thêm bảng `dashboard`, `bieu_do` hoặc `bao_cao` chỉ vì có biểu đồ.

Các bảng hạ tầng Laravel tồn tại nhưng có thể tách khỏi phần mô hình thực thể nghiệp vụ: `migrations`, `sessions`, `password_reset_tokens`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`. Nếu báo cáo có mục cấu trúc triển khai đầy đủ, liệt kê riêng các bảng này.

Danh sách trên phản ánh triển khai hiện tại, không khẳng định schema đã được chuẩn hóa tối ưu. Dữ liệu JSON, tên cột không đồng nhất và một số quan hệ thiếu ràng buộc FOREIGN KEY cần được nêu rõ khi đánh giá thiết kế; không tự đổi tên hoặc thêm bảng mới rồi mô tả như chức năng đã triển khai.
