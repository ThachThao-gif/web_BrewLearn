# DFD mức 0 của BrewLearn

Quy ước theo bộ sơ đồ của dự án: sơ đồ ngữ cảnh có tiến trình 0; DFD mức 0 phân rã thành các tiến trình 1.0–8.0. Ba trang XML là các vùng trình bày của cùng mức, không phải ba mức phân rã khác nhau.

## Kho dữ liệu logic

| Mã | Phạm vi dữ liệu | Bảng hiện có |
|---|---|---|
| D1 | Tài khoản, chức danh, cửa hàng, ca, ngày vào làm, trạng thái | users; ca_lam_viec |
| D2 | Khóa học, bài học, quiz và tiêu chuẩn liên quan | chuong_trinh_dao_tao; bai_hoc; bai_kiem_tra và nội dung đáp án hiện có |
| D3 | Tiến độ, lượt truy cập, mọi lượt thi và điểm | user_lesson_progresses; nhan_vien_khoa_hoc; ket_qua_kiem_tra |
| D4 | Lịch đánh giá, 5 tiêu chí, nhận xét, đề xuất và quyết định duyệt | danh_gia_nang_luc |
| D5 | Yêu cầu đào tạo lại, kế hoạch, từng vòng, xác nhận và kết quả | yeu_cau_dao_tao_lai; retraining_rounds |
| D6 | Kết quả dự đoán, thời điểm và phiên bản | du_doan_nguy_co |
| D7 | Thông báo, người nhận, trạng thái đọc | notifications |
| D8 | Sự cố nghiệp vụ được ghi nhận | loi_nghiep_vu |
| D9 | Yêu cầu hỗ trợ của tài khoản | yeu_cau_ho_tro |

Kho logic không nhất thiết tương ứng một bảng SQL. Bản lặp cùng mã thể hiện cùng kho/tác nhân để tránh giao cắt, không tạo database hoặc tác nhân mới.

## Giới hạn đúng theo code

- D2 đã có nội dung ban đầu trong database. Không vẽ HR hay chức năng biên soạn, xuất bản chưa có trên web.
- 6.0 đọc D1, D2, D3 và ghi D6; không có đầu vào D4 hoặc D5. Model không tự quyết định nhãn, giao đào tạo hoặc đóng yêu cầu.
- Script dự đoán chạy riêng. Lịch tự động hiện có trong routes/console.php là nhắc hạn đánh giá.
- 4.0 mới công bố kết quả sau quyết định duyệt; trả lại chỉ gửi trưởng ca để bổ sung.
- 4.0 lưu kết quả vòng đã duyệt vào D5; 5.0 dùng lịch sử đó để giao vòng tiếp theo. Chỉ Đạt đã duyệt mới đóng yêu cầu.
- Xác nhận của người phụ trách không phải nhân viên tự đóng yêu cầu. Người phụ trách có thể là trưởng ca, quản lý hoặc tài khoản khác được giao trong phạm vi cửa hàng.
- D8 thể hiện chức năng ghi nhận hiện có, không bổ sung báo cáo sự cố hay đưa sự cố vào model. D9 không bổ sung actor HR hoặc màn hình giải quyết hỗ trợ chưa có.
- Tác nhân chỉ kết nối tiến trình; không kết nối trực tiếp kho dữ liệu. DFD không thể hiện thứ tự, gateway hoặc nhánh điều khiển BPMN.

Tham khảo kiểm tra và ký hiệu bản lặp: https://www.cs.uct.ac.za/mit_notes/software/htmls/ch06s13.html
