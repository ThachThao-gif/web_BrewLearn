from copy import deepcopy
from pathlib import Path
import json, zipfile, hashlib
from docx import Document
from docx.oxml import OxmlElement
from docx.oxml.ns import qn
from docx.text.paragraph import Paragraph
from docx.shared import Pt, RGBColor

source = Path(r'D:\download\ĐỒ ÁN_MIS.docx')
output = Path(r'D:\wamp64\Brewlearn\output\Do_an_MIS_da_sua_phan_du_doan.docx')
output.parent.mkdir(parents=True, exist_ok=True)
doc = Document(source)
paras = list(doc.paragraphs)
tables = list(doc.tables)
assert paras[421].text.startswith('3.5 ')
changes = []

def replace(p, text):
    if p.text == text:
        return
    props = deepcopy(p.runs[0]._r.rPr) if p.runs and p.runs[0]._r.rPr is not None else None
    p.clear()
    run = p.add_run(text)
    if props is not None:
        run._r.insert(0, props)
    changes.append(text[:110])

def cell(table, label, text):
    for row in table.rows:
        if row.cells[0].text.strip() == label:
            p = row.cells[1].paragraphs[0]
            for other in list(row.cells[1].paragraphs)[1:]:
                other._p.getparent().remove(other._p)
            replace(p, text)
            return
    raise ValueError(label)

replace(paras[66], 'Về thiết kế, chức năng dự đoán hướng đến hỗ trợ theo dõi nguy cơ Chưa đạt ở lần đánh giá năng lực lần đầu hoặc định kỳ sắp tới; chưa mở rộng thành mô hình riêng cho đánh giá lại sau đào tạo. Nguyên mẫu hiện tính nguy cơ từ dữ liệu có tại thời điểm chạy Python, chưa liên kết một kết quả dự đoán với một lịch đánh giá hoặc khoảng dự báo cố định. Vì vậy, đề tài không khẳng định khả năng dự báo trước một hoặc ba tháng. Khi phát triển theo mục tiêu trên, dữ liệu đầu vào phải được chốt trước khi trưởng ca chấm lần đánh giá dùng làm kết quả đối chiếu.')
replace(paras[93], 'Chương trình dự đoán chạy riêng và lưu kết quả để ứng dụng web đọc, chưa được cấu hình lịch chạy tự động. Quản lý tham khảo nguy cơ khi xem hồ sơ và xét duyệt điểm, nhận xét, đề xuất đào tạo lại. Kết quả không tự phê duyệt, không tự tạo hoặc phân công đào tạo lại, không cộng vào điểm năng lực và không quyết định biện pháp xử lý sau ba vòng. Việc tính nguy cơ sau khi đã chấm không được coi là dự báo trước cho chính lần đánh giá đó.')
replace(paras[95], 'Trong dữ liệu huấn luyện giả lập hiện tại, điểm quiz trung bình và số lượt chưa đạt được sinh riêng; số lượt chưa đạt được lấy ngẫu nhiên từ 0 đến tổng lượt rồi tính tỷ lệ. Cách sinh này chưa bảo đảm sự nhất quán giữa điểm trung bình và tỷ lệ chưa đạt. Khi tổng hợp từ MySQL, chương trình lại đếm điểm dưới 80, trong khi nghiệp vụ sử dụng DiemChuan từng bài. Hai giới hạn này được phân tích tại mục 3.5 và chưa được coi là đã khắc phục trong kết quả thực nghiệm hiện có.')
replace(paras[181], 'Xem xét, phê duyệt hoặc trả lại. Quản lý xem điểm, nhận xét và đề xuất đào tạo lại, đồng thời có thể mở thông tin nguy cơ đã lưu để tham khảo. Quản lý đối chiếu thời điểm dự đoán với thời điểm đánh giá; kết quả tính sau khi chấm chỉ phản ánh dữ liệu cập nhật, không chứng minh dự báo trước. Nếu cần chỉnh sửa hồ sơ, quản lý trả lại kèm lý do; trưởng ca bổ sung và gửi lại trên cùng bản ghi. Khi đủ căn cứ, quản lý phê duyệt và hệ thống thông báo kết quả. Chưa có dự đoán hoặc chưa đủ dữ liệu không ngăn việc xét duyệt.')
replace(paras[227], 'Sử dụng kết quả dự đoán. Python tổng hợp dữ liệu học tập và kiểm tra, không dùng điểm đánh giá thực hành làm đầu vào. Quản lý tham khảo mức nguy cơ và thời điểm tính khi theo dõi hoặc xem xét hồ sơ đánh giá, đề xuất đào tạo lại. Không sử dụng nguy cơ làm điều kiện duyệt, thay đổi kết luận Đạt/Chưa đạt hoặc tự quyết định đào tạo lại, biện pháp xử lý. Thiếu lượt kiểm tra hoặc lượt truy cập bài học được ghi nhận là Chưa đủ dữ liệu. Về thiết kế dự báo trước, dữ liệu phải được chốt trước lần đánh giá mục tiêu; nguyên mẫu hiện chưa ràng buộc mốc này. Khi xét duyệt đánh giá lại, nguy cơ chung đã lưu chỉ có tính tham khảo, chưa được kiểm chứng là dự báo của vòng đào tạo lại.')

cell(tables[18], 'Basic Flow', '1. Quản lý mở hồ sơ Chờ phê duyệt.\n2. Hệ thống hiển thị điểm, kết luận, nhận xét và đề xuất. Quản lý có thể xem thông tin nguy cơ gần nhất đã lưu trên tổng quan hoặc hồ sơ theo UC-QL09; thiết kế đề xuất hiển thị thông tin này cùng hồ sơ xét duyệt.\n3. Quản lý kiểm tra hồ sơ, đối chiếu thời điểm và phạm vi dữ liệu nguy cơ nếu có, rồi chọn Phê duyệt.\n4. Hệ thống kiểm tra quyền, dữ liệu và trạng thái.\n5. Hệ thống lưu quyết định, người duyệt, thời điểm và chuyển sang Đã duyệt.\n6. Hệ thống cập nhật nghiệp vụ đào tạo lại theo các nhánh 6a–6e.\n7. Hệ thống gửi kết quả đã duyệt cho nhân viên và thông báo trưởng ca theo quy định công bố kết quả.')
cell(tables[23], 'Description', 'Là Quản lý cửa hàng, tôi muốn xem mức nguy cơ và dữ liệu học tập, kiểm tra tại thời điểm tính để tham khảo khi theo dõi nhân viên và xem xét hồ sơ đánh giá, đề xuất đào tạo lại.')
cell(tables[23], 'Basic Flow', '1. Quản lý mở tổng quan, danh sách hoặc hồ sơ nhân viên.\n2. Hệ thống xác định phạm vi dữ liệu được phép xem.\n3. Hệ thống đọc kết quả dự đoán gần nhất đã lưu; không chạy mô hình khi mở trang.\n4. Hệ thống hiển thị mức nguy cơ và thời điểm tính khi có dữ liệu.\n5. Quản lý đối chiếu với tiến độ, kết quả kiểm tra, thời điểm đánh giá và hồ sơ cần xét duyệt.\n6. Quản lý sử dụng thông tin để tham khảo; việc phê duyệt thực hiện theo UC-QL05.')
cell(tables[23], 'Business Rule', 'BR-QL09-1: Chỉ xem dữ liệu thuộc cửa hàng được phép.\nBR-QL09-2: Python chạy riêng; chức năng xem đọc kết quả đã lưu.\nBR-QL09-3: Nguy cơ không cộng vào điểm, không là điều kiện phê duyệt và không tự tạo đào tạo lại hoặc biện pháp xử lý.\nBR-QL09-4: Kết quả trên dữ liệu giả lập không chứng minh khả năng dự báo năng lực thực tế.\nBR-QL09-5: Dự đoán tính sau khi chấm không được diễn giải là dự báo trước cho lần đánh giá đó. Nguyên mẫu chưa liên kết kết quả dự đoán với một lần đánh giá cụ thể.\nBR-QL09-6: chi_tiet_yeu_to lưu dữ liệu đầu vào và trạng thái đủ dữ liệu, chưa thể hiện đóng góp hoặc nguyên nhân cảnh báo của từng biến.')

# Rebuild only section 3.5; retain the three original tables and their formatting.
start = paras[421]._p
end = next(p._p for p in paras[449:] if p.text.strip().startswith('3.7.'))
base = paras[425]
heading = paras[424]
saved_tables = [deepcopy(tables[i]._tbl) for i in [30,31,32]]
node = start
while node is not end:
    nxt = node.getnext()
    node.getparent().remove(node)
    node = nxt

def add(text, kind='body'):
    el = OxmlElement('w:p')
    template = heading if kind == 'heading' else base
    if template._p.pPr is not None:
        props = deepcopy(template._p.pPr)
        for name in ['numPr', 'sectPr', 'pageBreakBefore']:
            child = props.find(qn('w:'+name))
            if child is not None:
                props.remove(child)
        el.append(props)
    end.addprevious(el)
    p = Paragraph(el, doc._body)
    run = p.add_run(text)
    if template.runs and template.runs[0]._r.rPr is not None:
        run._r.insert(0, deepcopy(template.runs[0]._r.rPr))
    if kind in ['heading','title']:
        run.bold = True
        run.font.color.rgb = RGBColor(0,0,0)
        p.paragraph_format.keep_with_next = True
    p.paragraph_format.widow_control = True
    if kind == 'caption':
        p.paragraph_format.keep_with_next = True
    return p

add('3.5 Thiết kế chức năng dự đoán nguy cơ và thực nghiệm trên dữ liệu giả lập', 'title')
add('Chức năng dự đoán bổ sung thông tin tham khảo cho quản lý khi theo dõi đào tạo và xem xét hồ sơ đánh giá. Nội dung này gồm mục tiêu thiết kế, cách chương trình hiện xử lý dữ liệu, kết quả thử nghiệm đã lưu và giới hạn cần hoàn thiện. Các chỉ số trong phần thực nghiệm phản ánh nhãn mô phỏng, chưa chứng minh khả năng dự đoán kết quả đánh giá thực tế.')
add('3.5.1 Mục tiêu dự đoán và vị trí trong quy trình', 'heading')
add('Mục tiêu thiết kế là ước lượng nguy cơ Chưa đạt ở lần đánh giá năng lực lần đầu hoặc định kỳ sắp tới. Mốc lấy dữ liệu phải trước khi trưởng ca chấm lần đánh giá mục tiêu. Chưa thiết kế mô hình riêng cho đánh giá lại sau đào tạo; nguyên mẫu cũng chưa liên kết từng dự đoán với lịch đánh giá, khoảng dự báo hoặc mốc chốt dữ liệu bắt buộc. Đầu ra hiện tại vì vậy được hiểu là phân loại tham khảo tại thời điểm chạy, không phải dự báo đã xác nhận trước một hoặc ba tháng.')
add('Trưởng ca gửi điểm, nhận xét và đề xuất đào tạo lại; quản lý có thể tham khảo nguy cơ đã lưu khi xem xét và duyệt hồ sơ. Điểm thực hành vẫn được xác định theo năm tiêu chí, không cộng hoặc trừ theo dự đoán. Kết quả chưa có, chưa đủ dữ liệu hoặc khác với đánh giá không tự ngăn phê duyệt. Nguy cơ tính sau khi đã chấm không được dùng để tuyên bố dự báo đúng cho lần đánh giá đó. Giới hạn ba vòng và biện pháp xử lý do quản lý quyết định, độc lập với mô hình.')
add('3.5.2 Dữ liệu thử nghiệm và biến đầu vào', 'heading')
add('Bộ dữ liệu brewlearn_dataset.csv gồm 1.000 mẫu giả lập, năm biến đầu vào và nhãn competency_status. Chương trình dùng seed 42. Bộ dữ liệu lưu hiện có 457 mẫu lớp 0 (45,7%) và 543 mẫu lớp 1 (54,3%); đây không phải tỷ lệ đạt/chưa đạt của nhân viên BrewLearn thực tế.')
add('Bảng 3.6 Biến đầu vào và cách tổng hợp trong nguyên mẫu', 'caption')
end.addprevious(saved_tables[0])
add('Tiến độ được tính trung bình trên các chương trình áp dụng theo chức danh; khóa chưa có tiến độ được tính bằng 0. Thống kê quiz hiện lấy toàn bộ lượt của tài khoản, chưa giới hạn theo chu kỳ đánh giá. Số lượt học là số lần mở bài, không đo trực tiếp thời gian tập trung hoặc mức độ hiểu bài. Phạm vi giả lập không phải giới hạn giá trị trong vận hành.')
add('3.5.3 Nhãn mô phỏng và nhãn mục tiêu trong vận hành', 'heading')
add('Trong thực nghiệm, lớp 0 biểu thị Đạt theo mô phỏng và lớp 1 biểu thị Chưa đạt theo mô phỏng. Chương trình tạo điểm rủi ro từ tiến độ, điểm quiz trung bình, tỷ lệ chưa đạt và số lượt học; cộng nhiễu chuẩn có độ lệch chuẩn 0,5, chuyển qua hàm logistic rồi lấy mẫu Bernoulli để sinh nhãn. Nhãn không được lấy từ đánh giá của trưởng ca. Mô hình học mối liên hệ do quy tắc mô phỏng tạo ra; kết quả tốt trên bộ dữ liệu này không xác nhận khả năng đánh giá năng lực con người.')
add('Khi có dữ liệu vận hành phù hợp, nhãn mục tiêu là kết quả Đạt/Chưa đạt của lần đánh giá lần đầu hoặc định kỳ đã được quản lý phê duyệt. Mỗi mẫu cần gắn với nhân viên, lần đánh giá và dữ liệu học tập trước khi chấm. Bản đánh giá đang chờ duyệt hoặc bị trả lại chưa được dùng làm nhãn chính thức. Điểm thực hành dùng để xác định nhãn đầu ra, không đưa vào năm biến đầu vào của cùng lần dự đoán.')
add('3.5.4 Huấn luyện và kết quả thực nghiệm hiện có', 'heading')
add('Chương trình chia dữ liệu một lần thành 800 mẫu huấn luyện và 200 mẫu kiểm thử, test_size = 0,20, random_state = 42 và stratify theo nhãn. StandardScaler chỉ được học từ tập huấn luyện để dùng với Logistic Regression. Cấu hình gồm Logistic Regression với max_iter = 1000; Decision Tree với max_depth = 5; Random Forest với 100 cây và max_depth = 6. Các bộ phân loại đều dùng random_state = 42.')
add('Bảng 3.7 Kết quả lưu của lần thử nghiệm trên dữ liệu giả lập', 'caption')
end.addprevious(saved_tables[1])
add('Precision, Recall và F1 được tính cho lớp 1 từ nhãn phân loại mặc định; đối với Logistic Regression, ranh giới xác suất nhị phân tương ứng 0,5. Logistic Regression có F1 cao nhất trong lần chia dữ liệu này nên được chọn và lưu vào best_model.pkl cùng model_info.pkl, scaler.pkl. Đây là lựa chọn trong lần thử nghiệm hiện có, chưa đủ để kết luận mô hình tốt hơn ổn định trên các tập dữ liệu khác hoặc trên nhân viên thực tế.')
add('Tập dùng để so sánh, chọn mô hình cũng là tập dùng để báo cáo, chưa có tập kiểm thử độc lập sau lựa chọn hoặc kiểm định chéo. Chương trình có tính và in confusion matrix nhưng tệp model_results.csv chỉ lưu bốn chỉ số trong bảng. Thực nghiệm chưa báo cáo mô hình cơ sở, ROC-AUC, khoảng bất định hoặc độ hiệu chuẩn xác suất; không diễn giải Accuracy 73,5% thành độ chính xác dự báo đã xác nhận trong vận hành.')
add('3.5.5 Phân mức cảnh báo và giới hạn xác suất', 'heading')
add('Chương trình predict.py lấy xác suất lớp 1 bằng predict_proba và chuyển thành ba mức cảnh báo theo quy ước sau. Các mức này phục vụ minh họa giao diện, khác với ngưỡng nhị phân dùng tính chỉ số trong Bảng 3.7.')
add('Bảng 3.8 Quy ước phân mức cảnh báo trong nguyên mẫu', 'caption')
end.addprevious(saved_tables[2])
add('Các mốc 0,30 và 0,60 chưa được tối ưu theo chi phí bỏ sót hoặc cảnh báo sai, chưa được xác nhận bằng dữ liệu doanh nghiệp. Xác suất chưa được kiểm tra hiệu chuẩn. Vì vậy, giá trị 0,70 không được diễn giải thành bằng chứng rằng 70% nhân viên tương tự sẽ Chưa đạt. Tên mức Thấp, Trung bình, Cao biểu thị đầu ra tham khảo của mô hình mô phỏng, không phải kết luận năng lực.')
add('3.5.6 Tổng hợp dữ liệu và tích hợp với BrewLearn', 'heading')
add('Khi được chạy riêng, ket_noi_db.py đọc cấu hình MySQL, chọn nhân viên đang làm việc có cửa hàng, loại trưởng ca và các vai trò quản lý/HR, tổng hợp năm biến và gọi predict_risk. Chỉ gọi mô hình khi số lượt quiz và số lượt mở bài đều lớn hơn 0. Thiếu một trong hai điều kiện thì lưu ChuaDuDuLieu, diem_rui_ro = NULL, không gán nguy cơ thấp.')
add('Kết quả được ghi vào du_doan_nguy_co gồm nhân viên, cửa hàng, diem_rui_ro trong khoảng 0–1, mức cảnh báo, dữ liệu đầu vào, thông tin mô hình và ngay_du_doan. chi_tiet_yeu_to hiện là JSON chứa năm giá trị đầu vào cùng cờ đủ dữ liệu; chưa phải hệ số, đóng góp từng biến hoặc nguyên nhân cảnh báo. phien_ban_mo_hinh hiện lưu tên mô hình được chọn, chưa phải mã phiên bản đầy đủ của bộ dữ liệu và cấu hình huấn luyện.')
add('Laravel đọc kết quả gần nhất đã lưu trên tổng quan, danh sách và hồ sơ; mở trang không tự huấn luyện hoặc tính lại nguy cơ. Dự đoán chưa có lịch chạy tự động và chưa lưu liên kết đến lần đánh giá mục tiêu. Trong thiết kế xét duyệt, quản lý đối chiếu mức nguy cơ với thời điểm tính và hồ sơ; nguyên mẫu cho phép tra cứu trên tổng quan/hồ sơ, còn việc đưa cùng thông tin trực tiếp lên màn hình phê duyệt là nội dung cần đồng bộ giao diện. Không có luồng từ dự đoán tự tạo yêu cầu đào tạo lại.')
add('3.5.7 Giới hạn và các nội dung cần hoàn thiện', 'heading')
add('Thứ nhất, dữ liệu quiz giả lập chưa nhất quán: điểm trung bình và số lượt chưa đạt được sinh riêng nên có thể tạo mẫu không phù hợp với lịch sử điểm khả dĩ. Hướng sửa là sinh từng lượt gồm điểm và điểm chuẩn, rồi tính trung bình, tổng lượt và tỷ lệ chưa đạt từ cùng lịch sử. Khi áp dụng, cần huấn luyện lại và thay bảng kết quả; không dùng số liệu cũ để đại diện cho dữ liệu đã sửa.')
add('Thứ hai, chương trình đọc MySQL đếm lượt chưa đạt bằng điểm dưới 80, trong khi chức năng kiểm tra dùng DiemChuan từng bài. Cần đồng bộ cách tính giữa nguồn huấn luyện và vận hành. Dữ liệu giả lập rải rộng, còn nhóm đủ điều kiện đánh giá thường đã hoàn thành học tập và có quiz đạt; cần đánh giá riêng nhóm mục tiêu. Không suy ra nguy cơ cao chỉ từ số lần mở bài ít.')
add('Thứ ba, cần chốt dữ liệu trước lần đánh giá mục tiêu và lưu liên kết dự đoán–đánh giá để đối chiếu. Khi có nhiều chu kỳ của cùng nhân viên, phải xem xét tách dữ liệu theo nhân viên và thời gian, tránh dùng thông tin sau đánh giá hoặc để lịch sử gần trùng xuất hiện ở cả tập học và tập kiểm thử. Khi quản lý duyệt đánh giá lại, đầu ra chung hiện có không được coi là mô hình đã được kiểm chứng cho vòng đào tạo đó.')
add('Thứ tư, cần bổ sung mô hình cơ sở, chọn mô hình bằng kiểm định chéo trên tập huấn luyện và giữ tập kiểm thử cuối độc lập; đánh giá confusion matrix, ROC-AUC, biến động chỉ số và hiệu chuẩn. Ngưỡng cảnh báo cần được xác định từ mục tiêu sử dụng rồi kiểm tra trên dữ liệu phù hợp. Những bước này là hướng hoàn thiện, chưa thuộc kết quả đã báo cáo trong Bảng 3.7.')
add('Mã đối chiếu thực nghiệm gồm app/ml/train_model.py (sinh dữ liệu, huấn luyện và chọn mô hình), app/ml/predict.py (nạp mô hình, suy luận và phân mức), app/ml/ket_noi_db.py (tổng hợp dữ liệu và ghi MySQL). Các tệp brewlearn_dataset.csv, model_results.csv, best_model.pkl, model_info.pkl và scaler.pkl cần được lưu cùng cấu hình môi trường khi tái lập. Phiên bản môi trường hiện có được nêu tại Bảng 4.1; chưa xác nhận đó là toàn bộ phiên bản dùng trong lần huấn luyện ban đầu.')

from docx.table import Table
feature_table = Table(saved_tables[0], doc._body)
feature_rows = [
    ('Biến đầu vào', 'Cách tính trong nguyên mẫu', 'Phạm vi giả lập'),
    ('Tiến độ đào tạo', 'Trung bình TienDo các khóa áp dụng; khóa chưa có tiến độ tính 0.', '0–100%'),
    ('Điểm quiz trung bình', 'Trung bình Diem của toàn bộ lượt quiz đã lưu của tài khoản.', '0–100'),
    ('Số lượt quiz', 'Tổng bản ghi kết quả quiz của tài khoản.', '1–8 lượt'),
    ('Tỷ lệ quiz chưa đạt', 'Hiện đếm lượt Diem < 80 rồi chia tổng lượt; chưa đồng bộ DiemChuan.', '0–1'),
    ('Số lượt mở bài', 'Tổng visit_count trong user_lesson_progresses.', '1–40 lượt'),
]
for row, values in zip(feature_table.rows, feature_rows):
    for c, value in zip(row.cells, values):
        replace(c.paragraphs[0], value)
        for p in list(c.paragraphs)[1:]:
            p._p.getparent().remove(p._p)

replace(paras[567], 'Thông tin dự đoán được hiển thị theo mức Thấp, Trung bình, Cao hoặc Chưa đủ dữ liệu. Quản lý xem thời điểm dự đoán để nhận biết độ mới, đối chiếu với tiến độ, lịch sử quiz và hồ sơ đang xét duyệt. Mức nguy cơ là đầu ra tham khảo từ mô hình huấn luyện trên nhãn giả lập; chưa phải xác suất đã được kiểm chứng cho kết quả đánh giá thực tế.')
replace(paras[568], 'Giao diện đọc kết quả Python đã lưu vào cơ sở dữ liệu. Quản lý có thể mở tổng quan hoặc hồ sơ để tham khảo khi xét duyệt điểm và đề xuất đào tạo lại; thiết kế đề xuất hiển thị cùng thông tin trên màn hình phê duyệt. chi_tiet_yeu_to lưu dữ liệu đầu vào tại thời điểm tính, chưa cung cấp đóng góp hay nguyên nhân của từng biến. Kết quả không tự tạo yêu cầu đào tạo lại, không chặn việc phê duyệt và không quyết định biện pháp xử lý.')
replace(paras[473], 'Chương cũng trình bày mục tiêu thiết kế chức năng dự đoán, cách tích hợp Python–MySQL–Laravel và kết quả phân loại trên nhãn giả lập. Nguy cơ hỗ trợ quản lý tham khảo khi theo dõi và xét duyệt, độc lập với kết quả đánh giá chính thức. Các giới hạn về sinh dữ liệu, thời điểm dự đoán, chọn mô hình, ngưỡng và hiệu chuẩn được phân biệt với những nội dung cần hoàn thiện.')
for row in tables[33].rows:
    if row.cells[0].text.strip() == 'Thử nghiệm mô hình':
        replace(row.cells[1].paragraphs[0], 'Môi trường hiện có: Python 3.14.6; NumPy 2.5.3; pandas 3.0.6; scikit-learn 1.9.1; joblib 1.6.0. Phiên bản kiểm tra từ môi trường dự án, chưa xác nhận môi trường của lần huấn luyện ban đầu.')

# Request field refresh on opening in Word without claiming layout verification.
settings = doc.settings.element
update = settings.find(qn('w:updateFields'))
if update is None:
    update = OxmlElement('w:updateFields')
    settings.append(update)
update.set(qn('w:val'), 'true')
doc.save(output)
check = Document(output)
assert len(check.tables) == len(tables)
assert any(p.text.startswith('3.5.7 ') for p in check.paragraphs)
assert check.tables[31].cell(1,1).text == tables[31].cell(1,1).text
with zipfile.ZipFile(source) as a, zipfile.ZipFile(output) as b:
    media = [n for n in a.namelist() if n.startswith('word/media/')]
    assert all(a.read(n) == b.read(n) for n in media)
Path('tmp/prediction-edit-check.json').write_text(json.dumps({'output':str(output),'tables':len(check.tables),'images_preserved':len(media),'changes':changes}, ensure_ascii=False, indent=2), encoding='utf-8')
print(str(output))
print('Verified document reopening, original metrics, table count and all embedded media.')
