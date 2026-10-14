# Sơ đồ trạng thái và tuần tự BrewLearn

Mở BrewLearn-State-Sequence-4-so-do.xml bằng File → Open From → Device trong draw.io để có bốn trang. Có thể mở từng tệp XML riêng nếu muốn đưa một sơ đồ vào tài liệu đang vẽ. SVG đi kèm dùng xem trước hoặc chèn hình vào Word; XML chứa hình, chữ và đường nối có thể chỉnh sửa.

## Vị trí trong báo cáo

- Hai trang trạng thái: mục 3.4, thiết kế xử lý nghiệp vụ.
- Hai trang tuần tự: mục 3.1, tương tác giữa các thành phần kiến trúc.

## Các điểm đối chiếu mã nguồn

1. Danh_gia_nang_luc: tạo lịch ghi ChoDanhGia; saveScore ghi ChoPheDuyet từ ChoDanhGia hoặc TraLai; ManagerController::review ghi DaDuyet hoặc TraLai. Sơ đồ mô tả luồng chính này. HoanThanh là giá trị còn được đọc tương thích, không phải trạng thái mới do review ghi. Hàm submitEvaluation cũ chỉ đặt ChoPheDuyet, thiếu kiểm soát trạng thái tương đương saveScore; không lấy hàm cũ làm căn cứ khẳng định mọi tuyến đều cưỡng chế toàn bộ vòng đời.
2. Yêu cầu đào tạo lại có hai trường trang_thai và tien_trinh. Nhánh đề xuất ChoDuyet được reviewTraining xử lý, nhưng duyệt đánh giá chưa đạt trong review tạo yêu cầu trực tiếp ở DaDuyet/ChoGiao. Không mô tả mọi yêu cầu bắt buộc đi qua ChoDuyet.
3. Khi tạo lịch lại, yêu cầu chuyển ChoDanhGiaLai. Trong quá trình trưởng ca chấm và quản lý trả lại, yêu cầu vẫn ở tiến trình này; bản ghi đánh giá mới thay đổi trạng thái. Kết quả chưa đạt đã duyệt giữ lịch sử vòng rồi chuyển ChoGiao; quản lý phải giao vòng mới. Kết quả đạt đã duyệt chuyển HoanThanh/HoanTat.
4. Mã routes/console.php chỉ lập lịch brewlearn:remind-assessments. Chưa có command hoặc scheduler gọi script dự đoán. Sơ đồ dự đoán mô tả người vận hành chạy ket_noi_db.py theo lô, đọc dữ liệu, gọi predict_risk khi đủ dữ liệu, ghi du_doan_nguy_co và commit từng nhân viên. Không chạy script ghi dữ liệu khi tạo các hình này.
5. Tạo lịch đầu qua LeaderEvaluationController::createSchedule lưu NgayHen trong danh_gia_nang_luc và bản ghi notifications trong cùng transaction. Không có bảng lich_danh_gia trong luồng. Nhân viên đọc thông báo khi tải trang qua view composer; sơ đồ không khẳng định push thời gian thực.

Mỗi trang có chiều rộng/chiều cao riêng để giữ khoảng cách. Các đường chuyển trạng thái đi theo hành lang riêng và các thông điệp tuần tự đặt ở các hàng khác nhau; đường thông điệp đi ngang đường sống là ký pháp bình thường của sơ đồ tuần tự, không phải giao cắt giữa hai thông điệp.

Căn cứ: ManagerController::review, reviewTraining, assignTraining, reassess; LeaderEvaluationController::createSchedule, saveScore, submitEvaluation; routes/web.php; routes/console.php; app/ml/ket_noi_db.py; app/ml/predict.py. Đối chiếu ngày 05/10/2026.
