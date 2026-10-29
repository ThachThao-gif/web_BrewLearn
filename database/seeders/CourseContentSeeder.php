<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseContentSeeder extends Seeder
{
    /**
     * Mục tiêu học tập của từng khóa (mỗi phần tử là một dòng hiển thị).
     * Số liệu khớp với đáp án trong ngân hàng câu hỏi kiểm tra.
     *
     * @var array<int, list<string>>
     */
    private const COURSE_OUTCOMES = [
        1 => [
            'Nhận diện nguy cơ điện, nước và nhiệt tại quầy bar; kiểm tra an toàn trước khi bật thiết bị lúc mở ca.',
            'Áp dụng quy định vệ sinh an toàn thực phẩm: rửa tay, đeo găng, bảo quản sữa ở 0–5°C và loại bỏ nguyên liệu hết hạn.',
            'Giữ vệ sinh máy pha trong ca: xả đầu group sau mỗi shot, lau và xả hơi vòi đánh sữa, dùng khăn riêng cho từng khu vực.',
            'Sơ cứu bỏng hơi nước, đứt tay, trượt ngã và báo cáo sự cố lao động cho trưởng ca.',
            'Sử dụng bình chữa cháy CO2/bột và hướng dẫn khách, nhân viên di tản khi có báo động cháy.',
        ],
        2 => [
            'Lắng nghe chủ động: không ngắt lời, xác nhận lại yêu cầu của khách và đồng nghiệp.',
            'Xử lý phản hồi chưa hài lòng theo quy tắc LAST: Lắng nghe – Xin lỗi – Giải quyết – Cảm ơn.',
            'Bàn giao ca đầy đủ thông tin, không để sót việc giữa ca sáng và ca chiều.',
            'Sắp xếp thứ tự ưu tiên và quản lý thời gian để phục vụ đúng tốc độ giờ cao điểm.',
            'Thấu cảm và xây dựng quan hệ tin cậy với khách hàng, đồng nghiệp; giải quyết xung đột bình tĩnh, tôn trọng.',
            'Ứng xử chuyên nghiệp khi tham gia họp trực tuyến.',
        ],
        3 => [
            'Nắm thông số chuẩn của shot double: 18g bột cho 36–40ml (tỷ lệ 1:2), thời gian 25–30 giây, áp suất 9 bar, lực nén 13–15kg.',
            'Dial-in máy xay: chỉnh mịn hơn khi shot chảy nhanh, nhận biết chiết xuất thiếu (chua, thân mỏng) và chiết xuất quá.',
            'Đánh sữa microfoam: xả hơi trước khi đánh, cấp khí đến 30–37°C, hoàn tất ở 60–65°C.',
            'Rót latte art Heart và Rosetta rõ nét; giữ đúng tỷ lệ sữa và bọt cả với ly mang đi.',
            'Vệ sinh vòi hơi và đầu group ngay sau khi sử dụng để giữ chất lượng đồ uống.',
        ],
        4 => [
            'Chào khách, nhập order và ghi chú tùy chỉnh (đường, đá, sữa) chính xác trên máy POS.',
            'Xử lý nhanh sự cố máy in hóa đơn khi quán đông khách.',
            'Quản lý tiền thối đầu ca, phân loại mệnh giá và xử lý lịch sự khi nhận tiền nghi giả.',
            'Chốt két cuối ca: hai người cùng đếm, đối chiếu báo cáo POS, ghi biên bản chênh lệch và báo trưởng ca.',
        ],
        5 => [
            'Chào đón khách và phục vụ đồ uống theo thứ tự ưu tiên: người lớn tuổi, trẻ em, phụ nữ, nam giới.',
            'Bưng khay an toàn bằng hai tay, tối đa 4–5 ly, ly nặng đặt giữa khay; đặt ly từ phía bên phải khách.',
            'Dọn bàn trong 60 giây sau khi khách rời đi: thu ly đĩa rác, lau bằng khăn ẩm có dung dịch, xếp lại ghế.',
            'Quản lý khăn lau riêng theo khu vực, giữ sảnh và nhà vệ sinh sạch trong suốt ca.',
            'Xử lý khi khách báo sai món: xin lỗi, ghi nhận và đổi đúng món ngay.',
        ],
        6 => [
            'Thực hiện checklist mở ca: an toàn điện nước, tình trạng máy móc, hạn dùng và tồn kho nguyên liệu.',
            'Thực hiện checklist đóng ca: backflush máy pha, vệ sinh máy xay, chốt két, niêm phong, khóa cửa và kiểm tra an toàn cháy nổ.',
            'Xử lý hạng mục checklist chưa đạt trước khi mở bán; ký xác nhận và lưu checklist để đối chiếu.',
            'Điều phối nhân sự giờ cao điểm theo năng lực, bổ sung người cho khâu ùn tắc và thông báo thời gian chờ cho khách.',
            'Tổng kết điểm nghẽn sau giờ cao điểm để cải thiện ca sau.',
        ],
        7 => [
            'Tính tỷ lệ hao hụt và kiểm soát chi phí giá vốn (COGS) của cửa hàng.',
            'Áp dụng nguyên tắc FIFO/FEFO và sổ hủy nguyên liệu (waste log) để tìm nguyên nhân thất thoát.',
            'Đánh giá nhân viên dựa trên tiêu chí đã công bố, kết quả quan sát và dữ liệu đào tạo.',
            'Tổ chức buổi phản hồi 1-1: nêu hành vi cụ thể, tác động và thống nhất mục tiêu cải thiện có thời hạn.',
        ],
    ];

    /**
     * Nội dung chi tiết từng bài học: câu tóm tắt, các nội dung chính và lưu ý tại cửa hàng.
     *
     * @var array<int, array{summary: string, points: list<string>, note: string}>
     */
    private const LESSONS = [
        1 => [
            'summary' => 'Các nguyên tắc an toàn điện khi vận hành máy pha cà phê, máy xay và quy trình phòng cháy cơ bản tại quầy bar.',
            'points' => [
                'Việc đầu tiên khi mở ca: kiểm tra vệ sinh tổng thể và an toàn điện, nước quanh quầy bar trước khi bật thiết bị.',
                'Ổ cắm hoặc dây điện bị ướt: ngắt nguồn, báo trưởng ca và không sử dụng ổ cắm đó cho đến khi được kiểm tra.',
                'Không cắm nhiều thiết bị công suất lớn (máy pha, máy xay, máy xay sinh tố) trên cùng một ổ nối.',
                'Mang giày có đế cao su chống trượt vì sàn quầy bar thường đọng nước và sữa.',
                'Giữ lối thoát hiểm và vị trí bình chữa cháy luôn thông thoáng, không chất thùng hàng.',
            ],
            'note' => 'Mọi bất thường về điện phải ghi vào checklist mở ca để trưởng ca xử lý trước khi mở bán.',
        ],
        2 => [
            'summary' => 'Quy định vệ sinh cá nhân, bảo quản nguyên liệu và vệ sinh dụng cụ pha chế theo tiêu chuẩn an toàn thực phẩm.',
            'points' => [
                'Rửa tay đúng cách trước ca, sau khi đi vệ sinh, cầm tiền hoặc dọn rác; đeo găng khi chạm trực tiếp vào thực phẩm.',
                'Bảo quản sữa tươi trong tủ mát ở 0–5°C; kiểm tra hạn sử dụng (EXP) và loại bỏ nguyên liệu hết hạn.',
                'Xả nước nóng làm sạch đầu group sau mỗi lần chiết xuất espresso.',
                'Sau khi đánh sữa: lau vòi hơi bằng khăn ẩm sạch và xả hơi khoảng 1 giây; tuyệt đối không dùng khăn này lau mặt quầy.',
                'Cuối ca: backflush máy pha bằng thuốc rửa chuyên dụng; hạt cà phê dư cho vào túi van kín khí, để nơi khô ráo.',
            ],
            'note' => 'Khăn lau được phân biệt theo màu cho từng khu vực để tránh nhiễm chéo.',
        ],
        15 => [
            'summary' => 'Hướng dẫn xử lý các tai nạn thường gặp tại quầy bar và quy trình báo cáo sự cố lao động.',
            'points' => [
                'Bỏng hơi nước hoặc nước nóng: xả vết bỏng dưới vòi nước mát sạch 10–20 phút, không bôi kem đánh răng hay đá lạnh trực tiếp.',
                'Đứt tay: rửa sạch, ép cầm máu bằng gạc, băng kín và đeo găng trước khi quay lại làm việc.',
                'Trượt ngã: không vội nâng người bị ngã; hỏi tình trạng, đánh dấu khu vực trơn trượt và lau khô ngay.',
                'Hộp sơ cứu đặt cố định tại quầy, kiểm tra và bổ sung vật tư hằng tuần.',
                'Mọi sự cố đều báo trưởng ca và ghi nhận vào mục Lỗi & sự cố để theo dõi.',
            ],
            'note' => 'Trường hợp nặng (bỏng diện rộng, chảy máu không cầm được) gọi cấp cứu 115 trước, báo cáo sau.',
        ],
        16 => [
            'summary' => 'Kỹ năng sử dụng bình chữa cháy CO2/bột và quy trình di tản khách hàng, nhân viên khi có cháy.',
            'points' => [
                'Thao tác bình chữa cháy theo 4 bước: rút chốt, hướng vòi vào gốc lửa, bóp cò, quét đều qua lại.',
                'Bình CO2 phù hợp với đám cháy thiết bị điện; không cầm vào loa phun vì rất lạnh có thể gây bỏng.',
                'Khi có báo động: ngắt điện khu vực, hướng dẫn khách ra lối thoát gần nhất, không dùng thang máy.',
                'Trưởng ca kiểm đếm nhân viên tại điểm tập kết và báo cáo cho lực lượng chữa cháy.',
            ],
            'note' => 'Kiểm tra áp suất bình chữa cháy định kỳ hằng tháng và ghi vào sổ theo dõi PCCC.',
        ],
        3 => [
            'summary' => 'Kỹ thuật lắng nghe yêu cầu của đồng nghiệp và khách hàng mà không ngắt lời.',
            'points' => [
                'Dừng thao tác, nhìn vào người nói và để họ nói hết ý.',
                'Ghi nhanh các yêu cầu tùy chỉnh (ít đường, thêm đá, đổi sữa) ngay khi nghe.',
                'Nhắc lại yêu cầu để xác nhận trước khi thực hiện, tránh làm sai món.',
                'Đặt câu hỏi mở khi chưa rõ, không đoán ý khách.',
            ],
            'note' => 'Khi khách chưa hài lòng, việc đầu tiên là lắng nghe chăm chú và không ngắt lời khách.',
        ],
        4 => [
            'summary' => 'Phương pháp truyền đạt thông tin giữa các ca và giao tiếp trong ca để không sót việc.',
            'points' => [
                'Bàn giao ca theo mẫu: đơn đang chờ, nguyên liệu sắp hết, thiết bị lỗi, phản hồi của khách và việc còn dang dở.',
                'Xử lý đơn tại quầy theo thứ tự thời gian in trên bill; đơn 2 ly giờ cao điểm cần ra món dưới 3 phút.',
                'Phản hồi chưa hài lòng xử lý theo quy tắc LAST: Listen (lắng nghe), Apologize (xin lỗi), Solve (giải quyết), Thank (cảm ơn).',
                'Espresso bị chua, loãng thường do chiết xuất thiếu (xay quá thô, chảy quá nhanh); báo barista điều chỉnh ngay.',
            ],
            'note' => 'Thông tin bàn giao quan trọng phải ghi lại, không chỉ nói miệng.',
        ],
        17 => [
            'summary' => 'Phương pháp lập kế hoạch, sắp xếp thứ tự ưu tiên và sử dụng thời gian hiệu quả trong ca.',
            'points' => [
                'Chuẩn bị trước giờ cao điểm: định lượng sẵn nguyên liệu, bổ sung ly, nắp, ống hút.',
                'Ưu tiên việc ảnh hưởng trực tiếp đến khách, việc hậu cần làm vào lúc vắng khách.',
                'Gom các việc giống nhau để làm một lượt, giảm di chuyển qua lại.',
                'Cuối ca tự rà lại danh sách việc để bàn giao đầy đủ.',
            ],
            'note' => 'Một ca hiệu quả là ca không để việc dồn sang ca sau.',
        ],
        18 => [
            'summary' => 'Rèn luyện khả năng thấu hiểu cảm xúc, đặt mình vào vị trí người khác để giao tiếp tốt hơn.',
            'points' => [
                'Nhận ra cảm xúc của khách qua giọng nói, nét mặt khi chờ lâu hoặc nhận sai món.',
                'Thừa nhận cảm xúc trước khi giải thích: "Em hiểu anh/chị đã phải chờ lâu".',
                'Hỗ trợ đồng nghiệp đang quá tải thay vì phàn nàn.',
                'Giữ thái độ bình tĩnh, giọng nói nhẹ nhàng trong tình huống căng thẳng.',
            ],
            'note' => 'Thấu cảm không có nghĩa là đồng ý với mọi yêu cầu; vẫn cần tuân thủ quy định cửa hàng.',
        ],
        19 => [
            'summary' => 'Các nguyên tắc thiết lập và duy trì quan hệ làm việc tin cậy với khách hàng và đồng nghiệp.',
            'points' => [
                'Nhớ tên và món quen của khách thường xuyên để tạo trải nghiệm cá nhân hóa.',
                'Giữ lời hứa nhỏ: báo đúng thời gian chờ, mang đúng món đã hẹn.',
                'Ghi nhận đóng góp của đồng nghiệp và chia sẻ thông tin hữu ích trong ca.',
                'Phản hồi thẳng thắn, tôn trọng; không nói xấu sau lưng.',
            ],
            'note' => 'Sự tin cậy được xây dựng qua hành động lặp lại mỗi ca.',
        ],
        20 => [
            'summary' => 'Quy trình xử lý bất đồng và tình huống căng thẳng tại nơi làm việc.',
            'points' => [
                'Tách vấn đề khỏi cá nhân; tập trung vào sự việc và ảnh hưởng đến khách.',
                'Trao đổi riêng, không tranh luận trước mặt khách hàng.',
                'Mỗi bên trình bày, cùng tìm phương án và thống nhất việc cần làm.',
                'Nếu không tự giải quyết được, báo trưởng ca làm trung gian.',
            ],
            'note' => 'Mục tiêu là ca làm việc trơn tru, không phải phân thắng thua.',
        ],
        21 => [
            'summary' => 'Chuẩn mực tác phong và giao tiếp chuyên nghiệp khi tham gia họp trực tuyến.',
            'points' => [
                'Vào phòng họp đúng giờ, kiểm tra trước micro, camera và đường truyền.',
                'Chọn nơi yên tĩnh, ánh sáng đủ, trang phục gọn gàng.',
                'Tắt micro khi không phát biểu, giơ tay hoặc nhắn tin khi muốn nói.',
                'Ghi chú nội dung và việc được giao để thực hiện sau buổi họp.',
            ],
            'note' => 'Không vừa họp vừa phục vụ khách; xin phép rời họp nếu quầy cần hỗ trợ.',
        ],
        5 => [
            'summary' => 'Đo lường gram vào/ra, căn chỉnh thời gian chiết xuất và độ mịn để có shot espresso đạt chuẩn.',
            'points' => [
                'Công thức shot double chuẩn: 18g bột cho 36–40ml espresso (tỷ lệ 1:2), thời gian 25–30 giây.',
                'Máy pha hoạt động ở áp suất bơm 9 bar; lực nén (tamping) đều tay khoảng 13–15kg.',
                'Shot chảy nhanh dưới 20 giây (ví dụ 36ml trong 12 giây): cà phê xay quá thô hoặc nén chưa đủ lực, chỉnh máy xay mịn hơn.',
                'Vị chua gắt, thân mỏng là chiết xuất thiếu (under-extraction); vị đắng khét, khô là chiết xuất quá.',
                'Mỗi lần chỉnh chỉ thay đổi một thông số, pha thử và ghi lại kết quả.',
            ],
            'note' => 'Dial-in lại vào đầu ca và khi đổi mẻ hạt mới.',
        ],
        6 => [
            'summary' => 'Nhiệt độ đánh sữa tiêu chuẩn, tạo bọt microfoam mịn và tạo hình Heart, Rosetta.',
            'points' => [
                'Xả hơi vòi đánh sữa trước khi dùng để loại nước đọng trong vòi.',
                'Giai đoạn cấp khí (stretching): đầu vòi sát mặt sữa, tiếng xè xè nhỏ đều, dừng cấp khí khi sữa đạt 30–37°C (ấm tay).',
                'Giai đoạn cuộn xoáy: hạ vòi sâu hơn để làm mịn bọt, hoàn tất ở 60–65°C; không để sữa quá nóng.',
                'Bọt to, rời rạc là do đưa quá nhiều không khí khi vòi hơi ở quá nông.',
                'Rót latte art: rót từ trên cao với dòng nhỏ để sữa chìm dưới lớp crema, sau đó hạ ca sữa sát mặt ly và rót vào tâm cốc để tạo hình.',
            ],
            'note' => 'Ly mang đi có nắp vẫn giữ đúng tỷ lệ sữa và bọt theo công thức; latte art là tùy chọn.',
        ],
        7 => [
            'summary' => 'Thao tác nhập order, ghi chú tùy chỉnh và in hóa đơn trên máy POS.',
            'points' => [
                'Việc đầu tiên khi khách đến quầy là chào hỏi, sau đó mới nhận order.',
                'Chọn đúng món, size, rồi chọn tùy chọn trên POS, ví dụ Cà phê sữa với [Ít đường 50%] và [Nhiều đá].',
                'Mọi ghi chú phải nhập vào hệ thống; nếu không, barista pha theo công thức chuẩn và món phải làm lại.',
                'Đọc lại order và tổng tiền cho khách trước khi thanh toán.',
                'Máy in kẹt hoặc hết giấy giờ đông khách: thay giấy nhanh hoặc nhờ đồng nghiệp hỗ trợ, không để khách chờ lâu.',
            ],
            'note' => 'Luôn để sẵn cuộn giấy in dự phòng tại quầy thu ngân.',
        ],
        8 => [
            'summary' => 'Quy trình thu tiền, phân loại mệnh giá, kiểm soát tiền thối và chốt két cuối ca.',
            'points' => [
                'Tiền đầu ca (tiền thối cố định) chỉ dùng làm tiền lẻ thối cho khách trong ca.',
                'Xếp tiền theo mệnh giá, đếm tiền khách đưa trước mặt khách và đếm lại tiền thối.',
                'Tờ tiền nghi giả: lịch sự đề nghị khách đổi tờ khác và báo trưởng ca.',
                'Chốt két: hai người cùng đếm, đối chiếu với báo cáo POS và ký biên bản bàn giao.',
                'Chênh lệch so với POS: ghi rõ số tiền vào biên bản, báo trưởng ca kiểm tra giao dịch; không tự bù hoặc giấu.',
            ],
            'note' => 'Két tiền luôn khóa khi thu ngân rời quầy.',
        ],
        9 => [
            'summary' => 'Kỹ năng bưng khay an toàn và quy tắc phục vụ đồ uống tại bàn theo thứ tự ưu tiên.',
            'points' => [
                'Thứ tự phục vụ nhóm khách: người lớn tuổi, trẻ em, phụ nữ, nam giới.',
                'Bưng khay bằng hai tay ngang tầm ngực hoặc bụng, đi nhẹ nhàng, quan sát lối đi.',
                'Mỗi khay tối đa 4–5 ly, ly nặng đặt ở giữa khay để giữ cân bằng.',
                'Đặt ly từ phía bên phải khách, quai ly hướng về tay phải khách; giới thiệu tên món khi đặt.',
                'Khách báo sai món: xin lỗi, ghi nhận và đổi đúng món ngay.',
            ],
            'note' => 'Không bưng đồ uống nóng qua đầu khách hoặc trẻ em.',
        ],
        10 => [
            'summary' => 'Các bước dọn bàn trống trong 60 giây và giữ vệ sinh khu vực khách ngồi, nhà vệ sinh.',
            'points' => [
                'Tiêu chuẩn bus table: dọn xong bàn trong 60 giây sau khi khách rời đi.',
                'Thứ tự dọn: thu ly, đĩa, rác; lau bàn bằng khăn ẩm đã qua dung dịch khử khuẩn; xếp lại ghế.',
                'Khăn lau bàn khách và khăn lau quầy pha chế phân biệt theo màu hoặc khu vực để tránh nhiễm chéo.',
                'Kiểm tra nhà vệ sinh theo lịch trong ca và ghi vào bảng theo dõi.',
            ],
            'note' => 'Bàn sạch nhanh giúp khách mới có chỗ ngồi ngay trong giờ cao điểm.',
        ],
        11 => [
            'summary' => 'Kiểm tra máy móc, nguyên liệu đầu ca và checklist vệ sinh, niêm phong cuối ca.',
            'points' => [
                'Checklist mở ca: an toàn điện nước, tình trạng máy pha và máy xay, hạn dùng nguyên liệu, tồn kho trước giờ cao điểm.',
                'Hạng mục chưa đạt: ghi nhận, phân công xử lý và chỉ mở bán khi đã bảo đảm an toàn.',
                'Đóng ca: backflush mỗi đầu group với khoảng 3–5g bột vệ sinh chuyên dụng; vệ sinh gioăng, lưới lọc và thay khi bị chai cứng.',
                'Xả hết hạt trong phễu máy xay, vệ sinh phễu và bảo quản hạt kín.',
                'Trưởng ca kiểm tra thực tế từng hạng mục, két tiền, niêm phong, khóa cửa và an toàn cháy nổ trước khi ký xác nhận.',
            ],
            'note' => 'Checklist đã ký được lưu lại để đối chiếu khi có sự cố.',
        ],
        12 => [
            'summary' => 'Sắp xếp vị trí nhân sự linh hoạt khi quán đông khách để giảm thời gian chờ.',
            'points' => [
                'Phân công vị trí dựa trên năng lực và kết quả đào tạo của từng nhân viên.',
                'Khi quầy pha chế ùn tắc, điều động phục vụ hoặc thu ngân có kỹ năng sang hỗ trợ dán tem, đóng nắp, lấy nguyên liệu.',
                'Theo dõi thời gian ra món và thông báo thời gian chờ dự kiến cho khách.',
                'Sau giờ cao điểm, tổng kết nhanh các điểm nghẽn và nhắc cải thiện ngay trong ca.',
            ],
            'note' => 'Không cắt bớt bước an toàn vệ sinh để tăng tốc độ.',
        ],
        13 => [
            'summary' => 'Cách tính tỷ lệ hao hụt nguyên liệu và tối ưu chi phí giá vốn bán hàng (COGS).',
            'points' => [
                'Tỷ lệ hao hụt = lượng nguyên liệu hủy ÷ lượng nguyên liệu sử dụng trong kỳ.',
                'Xuất kho theo FIFO/FEFO: nhập trước hoặc hết hạn trước thì dùng trước.',
                'Ghi sổ hủy nguyên liệu (waste log): loại nguyên liệu, số lượng và lý do hủy.',
                'Đong sữa theo vạch chia trên ca theo size ly để tránh đánh dư; hạt đã mở bao bảo quản trong hũ hoặc túi van kín khí.',
                'Hao hụt tăng đột biến: đối chiếu waste log, tồn kho và thao tác của từng ca trước khi kết luận.',
            ],
            'note' => 'Theo dõi tỷ lệ hao hụt hằng tuần để phát hiện sớm bất thường.',
        ],
        14 => [
            'summary' => 'Phương pháp đánh giá hiệu suất làm việc định kỳ và phản hồi 1-1 với nhân viên.',
            'points' => [
                'Đánh giá dựa trên tiêu chí đã công bố, kết quả quan sát thực tế và dữ liệu đào tạo.',
                'Buổi 1-1 nêu hành vi cụ thể, tác động của hành vi và cùng thống nhất hành động cải thiện.',
                'Góp ý theo nguyên tắc Sandwich: ghi nhận điểm tốt, chỉ ra điểm cần cải thiện, động viên và nêu kỳ vọng.',
                'Mục tiêu cải thiện phải cụ thể, đo được và có thời hạn.',
                'Nhân viên mới cần phản hồi định kỳ ngắn, hằng tuần hoặc hai tuần một lần trong giai đoạn đầu.',
            ],
            'note' => 'Ghi lại nội dung đã thống nhất để theo dõi ở buổi 1-1 tiếp theo.',
        ],
    ];

    public function run(): void
    {
        foreach (self::COURSE_OUTCOMES as $courseId => $outcomes) {
            DB::table('chuong_trinh_dao_tao')->where('MaCTDT', $courseId)->update(['MucTieuHoc' => implode("\n", $outcomes)]);
        }

        foreach (self::LESSONS as $lessonId => $lesson) {
            $content = $lesson['summary']."\n\nNội dung chính:\n"
                .implode("\n", array_map(fn (string $point) => '• '.$point, $lesson['points']))
                ."\n\nLưu ý tại cửa hàng: ".$lesson['note'];
            DB::table('bai_hoc')->where('MaBaiHoc', $lessonId)->update(['NoiDung' => $content]);
        }
    }
}
