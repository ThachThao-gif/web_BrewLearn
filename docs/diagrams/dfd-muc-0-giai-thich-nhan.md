# Giải thích nhãn luồng DFD mức 0

Sơ đồ: dfd-muc-0-brewlearn-nhan-ngan.xml. Giữ nguyên 49 luồng của bản trước; rút gọn chữ hiển thị.

| Luồng | Nhãn trên sơ đồ |
|---|---|
| f1 | Thông tin tài khoản, hỗ trợ |
| f2 | Thông tin cá nhân, thông báo |
| f3 | Tài khoản, hỗ trợ, nhắc việc |
| f4 | Thông tin cá nhân, thông báo |
| f5 | Tài khoản, nhân sự, hỗ trợ |
| f6 | Kết quả quản lý, thông báo |
| f7 | Dữ liệu tài khoản, nhân sự |
| f8 | Cập nhật tài khoản, nhân sự |
| f9 | Thông báo, yêu cầu hỗ trợ |
| f10 | Thông báo, yêu cầu cá nhân |
| f11 | Yêu cầu học, tiến độ, đáp án |
| f12 | Nội dung, tiến độ, điểm thi |
| f13 | Yêu cầu học, thi, theo dõi |
| f14 | Kết quả học và theo dõi |
| f15 | Chức danh, phạm vi nhân sự |
| f16 | Nội dung và đáp án chuẩn |
| f17 | Tiến độ, kết quả kiểm tra |
| f18 | Lịch sử học tập, kiểm tra |
| f19 | Lịch, điểm, nhận xét, đề xuất |
| f20 | Hồ sơ, điều kiện đánh giá |
| f21 | Quyết định phê duyệt |
| f22 | Hồ sơ, kết quả phê duyệt |
| f23 | Nhân sự thuộc phạm vi |
| f24 | Dữ liệu điều kiện đánh giá |
| f25 | Lịch, kết quả, phê duyệt |
| f26 | Lịch sử đánh giá |
| f27 | Thông báo đánh giá |
| f28 | Quyết định, phân công, lịch |
| f29 | Kế hoạch, lịch sử vòng |
| f30 | Yêu cầu nhiệm vụ, xác nhận |
| f31 | Nhiệm vụ, điều kiện vòng |
| f32 | Dữ liệu vòng đào tạo lại |
| f33 | Lịch sử đào tạo lại |
| f34 | Kết quả học, quiz mới |
| f35 | Tiến độ học lại khởi tạo |
| f36 | Kết quả đánh giá đã duyệt |
| f37 | Lịch đánh giá lại |
| f38 | Thông báo đào tạo lại |
| f39 | Yêu cầu tra cứu, thống kê |
| f40 | Hồ sơ, thống kê, nguy cơ |
| f41 | Dữ liệu nhân sự |
| f42 | Dữ liệu học tập, kiểm tra |
| f43 | Dữ liệu đánh giá |
| f44 | Dữ liệu đào tạo lại |
| f45 | Kết quả nguy cơ đã lưu |
| f46 | Nhân viên, chức danh |
| f47 | Nội dung theo chức danh |
| f48 | Dữ liệu học tập, quiz |
| f49 | Kết quả dự đoán |

D1: users, dữ liệu cửa hàng và ca_lam_viec.
D2: notifications, yeu_cau_ho_tro; thông tin sự cố được ghi bằng thông báo trong xử lý trưởng ca.
D3: chuong_trinh_dao_tao, bai_hoc, bai_kiem_tra, cau_hoi_kiem_tra; trang tiêu chuẩn năng lực là nội dung giao diện tĩnh, không phải bảng CSDL riêng trong sơ đồ này.
D4: user_lesson_progresses, nhan_vien_khoa_hoc, ket_qua_kiem_tra.
D5: danh_gia_nang_luc.
D6: yeu_cau_dao_tao_lai, retraining_rounds.
D7: du_doan_nguy_co.

Kho trùng mã là cùng kho logic. Python chạy riêng; web không kích hoạt mô hình khi người dùng mở trang.
