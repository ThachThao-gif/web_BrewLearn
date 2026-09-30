-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1:3306
-- Thời gian đã tạo: Th9 24, 2026 lúc 08:05 AM
-- Phiên bản máy phục vụ: 9.1.0
-- Phiên bản PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `brewlearn`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bai_hoc`
--

CREATE TABLE `bai_hoc` (
  `MaBaiHoc` int NOT NULL,
  `FK_MaCTDT` varchar(50) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `TenBaiHoc` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `NoiDung` text COLLATE utf8mb3_unicode_ci,
  `VideoURL` varchar(191) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `ThuTu` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `bai_hoc`
--

INSERT INTO `bai_hoc` (`MaBaiHoc`, `FK_MaCTDT`, `TenBaiHoc`, `NoiDung`, `VideoURL`, `ThuTu`) VALUES
(1, '1', 'GIỚI THIỆU KỸ NĂNG AN TOÀN', '1. Tổng quan về an toàn lao động trong ngành F&B.\n2. Các nguy cơ tai nạn phổ biến nhất tại quầy Bar barista.\n3. Trách nhiệm của nhân viên đối với bản thân và đồng nghiệp.', 'steaming-sua.mp4', 1),
(2, '1', 'NHẬN DIỆN CÁC MỐI NGUY & ĐÁNH GIÁ RỦI RO', '1. Cách phát hiện sự cố điện, rò rỉ nước hoặc khu vực trơn trượt.\n2. Quy trình báo cáo sự cố khẩn cấp cho Quản lý ca.\n3. Phương pháp gắn biển cảnh báo và khắc phục tạm thời.', 'steaming-sua.mp4', 2),
(3, '1', 'QUY TẮC PHÒNG TRÁNH TRƯỢT - VẤP - NGÃ', '1. Quy định về trang phục và giày bảo hộ chống trượt.\n2. Kỹ năng di chuyển an toàn khi bê đồ nặng hoặc mang chất lỏng nóng.\n3. Hướng dẫn sử dụng thang và kê xếp hàng hóa đúng độ cao.', 'steaming-sua.mp4', 3),
(4, '2', 'TỐI ƯU HÓA CHIẾT XUẤT ESPRESSO', '1. Tìm hiểu các yếu tố ảnh hưởng: Nhiệt độ nước, áp suất máy, độ mịn cà phê.\n2. Hướng dẫn Cân - Nén (Tamping) chuẩn lực 15kg.\n3. Đọc thông số đo Brew Ratio (Tỷ lệ chiết xuất).', 'steaming-sua.mp4', 1),
(5, '2', 'KỸ THUẬT TẠO BỌT SỮA MICROFOAM', '1. Cách đặt vòi hơi (Steam wand) đúng góc độ và độ sâu trong ca sữa.\n2. Hai giai đoạn đánh sữa: Cấp khí (Streching) và Xoáy sữa (Rolling).\n3. Nhận biết nhiệt độ sữa chuẩn qua cảm nhận tay và nhiệt kế.', 'steaming-sua.mp4', 2),
(6, '2', 'THỰC HÀNH LATTE ART NÂNG CAO', '1. Kỹ thuật ép sữa, gõ ca và lắc ca sữa tạo dòng chảy đều.\n2. Chi tiết từng bước vẽ hình Heart (Trái tim) cơ bản.\n3. Chi tiết từng bước ngắt dòng vẽ hình Rosetta (Lá mầm).', 'steaming-sua.mp4', 3),
(7, '3', 'CHECKLIST QUY TRÌNH MỞ CA (OPENING)', '1. Kiểm tra vệ sinh tổng thể quầy Bar và máy móc thiết bị.\n2. Bật máy pha cà phê, máy xay và chạy xả nước vệ sinh đầu ca.\n3. Kiểm tra danh mục nguyên liệu chuẩn bị (Prep list) trước khi đón khách.', 'steaming-sua.mp4', 1),
(8, '3', 'XỬ LÝ PHÀN NÀN & CHĂM SÓC KHÁCH HÀNG', '1. Nguyên tắc LAST trong dịch vụ (Listen - Apologize - Solve - Thank).\n2. Hướng dẫn đổi trả đồ uống hoặc pha lại khi khách chưa hài lòng.\n3. Xử lý các tình huống khách hàng đông hoặc chờ lâu.', 'steaming-sua.mp4', 2),
(9, '3', 'QUY TRÌNH ĐÓNG CA VÀ VỆ SINH TỔNG THỂ (CLOSING)', '1. Quy trình vệ sinh Backflush máy pha cà phê bằng thuốc rửa chuyên dụng.\n2. Vệ sinh lưỡi dao máy xay và bảo quản hạt cà phê dư.\n3. Bàn giao tiền mặt, tồn kho nguyên liệu và bàn giao sổ ca.', 'steaming-sua.mp4', 3);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bai_kiem_tra`
--

CREATE TABLE `bai_kiem_tra` (
  `MaBaiKiemTra` int NOT NULL,
  `TenBaiKiemTra` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `ThoiGianLamBai` int DEFAULT NULL COMMENT 'Thời gian tính bằng phút',
  `DiemChuan` int NOT NULL DEFAULT '80' COMMENT 'Điểm đạt (%)',
  `MaBaiHoc` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `bai_kiem_tra`
--

INSERT INTO `bai_kiem_tra` (`MaBaiKiemTra`, `TenBaiKiemTra`, `ThoiGianLamBai`, `DiemChuan`, `MaBaiHoc`, `created_at`, `updated_at`) VALUES
(1, 'Bài Kiểm Tra Sát Hạch: Giao Tiếp & Nghiệp Vụ Xử Lý Tình Huống Giờ Cao Điểm', 20, 80, 1, '2026-09-23 07:39:40', '2026-09-23 07:39:40'),
(2, 'Sát Hạch Thực Hành: Chiết Xuất Espresso & Steaming Sữa Microfoam', 15, 85, 2, '2026-09-23 07:39:40', '2026-09-23 07:39:40'),
(3, 'Sát Hạch Vệ Sinh An Toàn Thực Phẩm & Đóng Ca Barista', 15, 90, 3, '2026-09-23 07:39:40', '2026-09-23 07:39:40'),
(4, 'Sát Hạch Quy Trình Mở Ca (Opening Checklist) & An Toàn Lao Động', 15, 80, 7, '2026-09-23 08:20:12', '2026-09-23 08:20:12'),
(5, 'Sát Hạch Kỹ Năng Quản Lý Nguyên Vật Liệu & Giảm Thất Thoát (Waste)', 20, 85, 8, '2026-09-23 08:20:12', '2026-09-23 08:20:12');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cache`
--

CREATE TABLE `cache` (
  `key` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb3_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `owner` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cau_hoi_kiem_tra`
--

CREATE TABLE `cau_hoi_kiem_tra` (
  `id` int NOT NULL,
  `MaBaiKiemTra` int NOT NULL,
  `CauHoi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `PhuongAnA` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `PhuongAnB` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `PhuongAnC` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `PhuongAnD` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `DapAnDung` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'A, B, C hoặc D',
  `GiaiThich` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `cau_hoi_kiem_tra`
--

INSERT INTO `cau_hoi_kiem_tra` (`id`, `MaBaiKiemTra`, `CauHoi`, `PhuongAnA`, `PhuongAnB`, `PhuongAnC`, `PhuongAnD`, `DapAnDung`, `GiaiThich`) VALUES
(1, 1, 'Theo quy tắc LAST trong xử lý khiếu nại, chữ \"A\" đại diện cho hành động nào?', 'Ask questions (Hỏi thông tin)', 'Apologize (Thành thật xin lỗi)', 'Action (Hành động ngay)', 'Accept (Chấp nhận lỗi)', 'B', 'LAST nghĩa là Listen - Apologize - Solve - Thank.'),
(2, 1, 'Thời gian tiêu chuẩn để phục vụ 01 đơn hàng gồm 2 ly cà phê vào giờ cao điểm là bao lâu?', 'Dưới 2 phút', 'Dưới 3 phút', 'Dưới 5 phút', 'Dưới 8 phút', 'B', 'Tiêu chuẩn quầy Bar BrewLearn là tối đa 3 phút/đơn 2 ly.'),
(3, 1, 'Khi khách hàng phản ánh Espresso quá chua và loãng, nguyên nhân kỹ thuật chính là gì?', 'Cà phê bị Over-extraction (chiết xuất quá mức)', 'Nhiệt độ nước quá cao (>96°C)', 'Cà phê bị Under-extraction (xay quá thô/chiết xuất nhanh)', 'Lực nén Tamping quá mạnh', 'C', 'Vị chua loãng xuất hiện khi chảy quá nhanh (Under-extraction).'),
(4, 1, 'Thao tác đầu tiên Barista cần làm ngay khi nhận phản hồi chưa hài lòng từ khách là gì?', 'Giải thích lý do quầy bar đang đông', 'Lắng nghe chăm chú và không ngắt lời khách', 'Pha lại ly mới ngay lập tức', 'Báo Quản lý ca xử lý', 'B', 'Bước Listen (Lắng nghe) luôn là ưu tiên hàng đầu.'),
(5, 1, 'Trình tự ưu tiên chuẩn khi xử lý đơn hàng tại quầy trong ca làm việc là gì?', 'Khách đến trước - Đơn Grab/Shopee - Đơn mang đi', 'Theo thứ tự thời gian in trên bill order', 'Làm món lạnh trước món nóng', 'Làm đơn nhiều ly trước', 'B', 'Đảm bảo công bằng và tốc độ theo thời gian in bill.'),
(6, 2, 'Tỷ lệ chiết xuất Brew Ratio chuẩn cho 1 shot Double Espresso tiêu chuẩn tại quầy là bao nhiêu?', '18g bột lấy 18ml dịch', '18g bột lấy 36ml - 40ml dịch', '18g bột lấy 60ml dịch', '20g bột lấy 80ml dịch', 'B', 'Tỷ lệ chiết xuất chuẩn Barista là 1:2 (18g bột thu 36-40ml).'),
(7, 2, 'Nhiệt độ đánh sữa Microfoam chuẩn dành cho Cappuccino/Latte là bao nhiêu?', '40°C - 50°C', '60°C - 65°C', '75°C - 80°C', 'Trên 85°C', 'B', 'Trên 70°C sữa sẽ bị cháy đường Lactose và mất độ ngọt tự nhiên.'),
(8, 2, 'Thời gian chiết xuất tiêu chuẩn cho một shot Double Espresso rơi vào khoảng nào?', '10 - 15 giây', '18 - 22 giây', '25 - 30 giây', '35 - 40 giây', 'C', 'Khung thời gian lý tưởng từ 25 - 30 giây.'),
(9, 2, 'Kỹ thuật \"Stretching\" trong quá trình đánh sữa có tác dụng chính là gì?', 'Tạo sóng xoáy làm mịn bọt sữa', 'Đưa không khí vào sữa để tạo bọt', 'Làm nóng sữa lên 65°C', 'Trộn đều sữa tươi và cà phê', 'B', 'Stretching là giai đoạn hạ ca lấy hơi tạo lớp bọt ban đầu.'),
(10, 2, 'Lực nén Tamping tiêu chuẩn khi chuẩn bị tay pha (Portafilter) là bao nhiêu?', '5kg', '10kg', '13kg - 15kg', '25kg', 'C', 'Lực nén tiêu chuẩn phẳng đều từ 13 - 15kg.'),
(11, 3, 'Tần suất xả nước nóng làm sạch Headgroup (Grouphead) của máy pha cà phê là khi nào?', 'Mỗi tiếng 1 lần', 'Sau mỗi lần chiết xuất Espresso', 'Cuối ca làm việc', 'Mỗi đầu ca sáng', 'B', 'Xả tay pha ngay sau mỗi lần chiết xuất để tránh tồn bã cà phê cũ.'),
(12, 3, 'Quy trình Backflush vệ sinh máy pha cà phê bằng thuốc rửa chuyên dụng được thực hiện khi nào?', 'Cuối mỗi ca làm việc (Closing)', '1 tuần 1 lần', 'Mỗi khi thay hạt cà phê mới', 'Khi máy báo lỗi áp suất', 'A', 'Backflush là quy trình bắt buộc cuối mỗi ca vận hành.'),
(13, 3, 'Sau khi sử dụng vòi Steam đánh sữa, Barista cần thực hiện ngay thao tác nào?', 'Tắt máy pha cà phê', 'Lau vòi bằng khăn ẩm sạch và xả hơi (Purge) 1 giây', 'Ngâm vòi vào ca nước lạnh', 'Tháo đầu vòi ra rửa', 'B', 'Lau và xả hơi ngay lập tức để tránh sữa khô đọng cặn trong vòi.'),
(14, 3, 'Hạt cà phê dư trong phễu máy xay (Hopper) vào cuối ngày cần bảo quản như thế nào?', 'Để nguyên trong phễu qua đêm', 'Cho vào túi xả van kín khí và bảo quản nơi khô ráo', 'Bỏ vào tủ đông lạnh', 'Đổ chung vào thùng cà phê cũ', 'B', 'Tránh ánh sáng và không khí làm mất hương vị hạt.'),
(15, 3, 'Khăn lau vòi đánh sữa (Steam wand) có thể dùng chung để lau mặt bàn quầy bar không?', 'Có, để tiết kiệm khăn', 'Chỉ dùng chung khi quầy bar sạch', 'Tuyệt đối không, phải dùng khăn riêng tiệt trùng', 'Tùy thuộc vào chỉ đạo Quản lý', 'C', 'Khăn vòi sữa bắt buộc dùng riêng để đảm bảo an toàn vệ sinh thực phẩm.');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chuong_trinh_dao_tao`
--

CREATE TABLE `chuong_trinh_dao_tao` (
  `MaCTDT` int NOT NULL,
  `TenCTDT` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `MoTa` text COLLATE utf8mb3_unicode_ci,
  `ChucVuApDung` varchar(191) COLLATE utf8mb3_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `chuong_trinh_dao_tao`
--

INSERT INTO `chuong_trinh_dao_tao` (`MaCTDT`, `TenCTDT`, `MoTa`, `ChucVuApDung`) VALUES
(1, 'Làm Việc An Toàn - Kỹ Năng Tại Nơi Làm Việc', 'Trong khoá học KỸ NĂNG AN TOÀN TẠI NƠI LÀM VIỆC, bạn sẽ học:\n\n• Mối nguy (hazards) là gì, vì sao xuất hiện và cách nhận biết nhanh trong mọi môi trường.\n• Cách đánh giá rủi ro & giảm thiểu rủi ro trước khi sự cố xảy ra.\n• Cách xếp dỡ hàng hoá, kê kệ, sắp xếp vật dụng đúng chuẩn an toàn.\n• Quy tắc di chuyển xe đẩy, xe hàng, xe trolley để tránh tai nạn nghiêm trọng.\n• Cách phòng tránh trượt – vấp – ngã, một trong những nguyên nhân gây tai nạn lao động phổ biến nhất.\n• Hiểu đúng về mệt mỏi – suy giảm thể trạng (fatigue) và vì sao nó làm tăng rủi ro tai nạn.\n• Cách sử dụng dụng cụ, thiết bị, công cụ sắc nhọn và thiết bị chuyên dụng an toàn.\n• Nguyên tắc an toàn khi xử lý tiền mặt, tránh rủi ro về sức khỏe và rủi ro an ninh.', 'THỰC HÀNH CA BARISTA'),
(2, 'Kỹ Thuật Espresso & Đánh Sữa Nâng Cao', 'Trong khoá học KỸ THUẬT ESPRESSO & ĐÁNH SỮA NÂNG CAO, bạn sẽ học:\n\n• Nguyên lý chiết xuất Espresso chuẩn: Độ mịn grind size, định lượng dosage và thời gian chảy.\n• Nhận biết chiết xuất Under-extraction (Chua/Loãng) và Over-extraction (Đắng/Khét).\n• Kỹ thuật đánh sữa Microfoam mịn màng, căn chỉnh nhiệt độ lý tưởng từ 55°C - 65°C.\n• Phương pháp đổ Latte Art cơ bản: Hình Trái Tim (Heart), Tulips và Rosetta.\n• Quy trình vệ sinh và bảo dưỡng Headgroup máy pha cà phê hàng ngày.', 'BARISTA CHÍNH'),
(3, 'Quy Trình Phục Vụ & Vận Hành Ca Barista (SOP)', 'Trong khoá học QUY TRÌNH PHỤC VỤ & VẬN HÀNH CA BARISTA, bạn sẽ học:\n\n• Quy trình mở ca (Opening Check) và đóng ca (Closing Check) chuẩn SOP chuỗi cà phê.\n• Tiêu chuẩn vệ sinh an toàn thực phẩm (FSS) tại quầy pha chế.\n• Kỹ năng giao tiếp, xử lý phàn nàn của khách hàng (Service Recovery) chuyên nghiệp.\n• Quản lý nguyên vật liệu, kiểm kê tồn kho và giảm thiểu thất thoát (Waste Management).\n• Điều phối nhân sự và phân công công việc hiệu quả trong ca cao điểm.', 'QUẢN LÝ CA / LEADER');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `connection` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `queue` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb3_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb3_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb3_unicode_ci NOT NULL,
  `attempts` smallint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb3_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb3_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ket_qua_kiem_tra`
--

CREATE TABLE `ket_qua_kiem_tra` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `MaBaiKiemTra` int NOT NULL,
  `Diem` int NOT NULL,
  `TrangThai` varchar(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `ket_qua_kiem_tra`
--

INSERT INTO `ket_qua_kiem_tra` (`id`, `user_id`, `MaBaiKiemTra`, `Diem`, `TrangThai`, `created_at`) VALUES
(1, 1, 1, 0, 'ChuaDat', '2026-09-23 01:36:06'),
(2, 1, 1, 40, 'ChuaDat', '2026-09-23 01:36:20'),
(3, 1, 1, 20, 'ChuaDat', '2026-09-23 02:07:38'),
(4, 1, 1, 20, 'ChuaDat', '2026-09-23 02:09:12'),
(5, 1, 1, 0, 'ChuaDat', '2026-09-23 02:09:32'),
(6, 1, 1, 0, 'ChuaDat', '2026-09-23 02:10:24'),
(7, 1, 1, 20, 'ChuaDat', '2026-09-23 02:10:47'),
(8, 1, 1, 60, 'ChuaDat', '2026-09-23 02:11:33'),
(9, 2, 1, 0, 'ChuaDat', '2026-09-23 09:06:47');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_09_22_142623_create_bai_kiem_tra_table', 2),
(5, '2026_09_22_162338_create_bai_hoc_table', 3),
(6, '2026_09_22_163241_create_chuong_trinh_dao_tao_table', 4),
(7, '2026_09_23_150425_add_employee_fields_to_users_table', 5);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhan_vien_khoa_hoc`
--

CREATE TABLE `nhan_vien_khoa_hoc` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `MaCTDT` int NOT NULL,
  `TienDo` int DEFAULT '0',
  `TrangThai` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'DangHoc',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nhan_vien_khoa_hoc`
--

INSERT INTO `nhan_vien_khoa_hoc` (`id`, `user_id`, `MaCTDT`, `TienDo`, `TrangThai`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 100, 'HoanThanh', '2026-09-23 08:20:12', '2026-09-23 08:20:12'),
(2, 1, 2, 85, 'DangHoc', '2026-09-23 08:20:12', '2026-09-23 08:20:12'),
(3, 1, 3, 40, 'DangHoc', '2026-09-23 08:20:12', '2026-09-23 08:20:12'),
(4, 2, 1, 35, 'DangHoc', '2026-09-23 08:20:12', '2026-09-23 08:20:12'),
(5, 2, 2, 0, 'ChuaHoc', '2026-09-23 08:20:12', '2026-09-23 08:20:12');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb3_unicode_ci,
  `payload` longtext COLLATE utf8mb3_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('1S7j14oQAj5YxYgj9McAcSrYhQG7uwQpjOLwhr7x', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJBajAydlVpOXNRMDVxUUdzOUdtTWZFbmg1SHI1YlFaN2N2Vlh5WGtwIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9hc3Nlc3NtZW50c1wvMy1tb250aHMiLCJyb3V0ZSI6ImFzc2Vzc21lbnRzLnNob3cifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1790236765),
('chDKIDckrjwPT44rVpsQ7WkiOVysPprWrTDsmYJZ', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJhN1NJeFBoSkRMWEwxazExNW1wVzJSRVdablVSbkJzUnVPTkwzY0t2IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC8/dXNlcl9pZD0xIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1790237043);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `name_tag` varchar(191) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `phone` varchar(191) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `ChucVu` varchar(100) COLLATE utf8mb3_unicode_ci DEFAULT 'Barista',
  `ChiNhanh` varchar(100) COLLATE utf8mb3_unicode_ci DEFAULT 'CN Quận 1',
  `CapDo` int DEFAULT '1',
  `TienDoTong` int DEFAULT '0',
  `dob` date DEFAULT NULL,
  `id_card` varchar(191) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `tram_lam_viec` varchar(191) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `emergency_contact` varchar(191) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `emergency_phone` varchar(191) COLLATE utf8mb3_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `name`, `name_tag`, `email`, `phone`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `ChucVu`, `ChiNhanh`, `CapDo`, `TienDoTong`, `dob`, `id_card`, `tram_lam_viec`, `emergency_contact`, `emergency_phone`) VALUES
(1, 'Trần Minh Thư', 'Minh Thư', 'minhthu@brewlearn.com', '0982 456 789 555', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.X2A29iM6i', NULL, '2026-09-23 07:53:25', '2026-09-23 08:35:02', 'Barista Chính', 'Flagship Quận 1 - Hồ Tùng Mậu, TP.HCM', 2, 75, '2001-08-14', '07920108****', 'Quầy Bar Chính (Espresso & Milk Steam)', 'Trần Văn Nam (Bố)', '0903 123 456'),
(2, 'Nguyễn Văn Nam', NULL, 'vannam@brewlearn.com', NULL, NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.X2A29iM6i', NULL, '2026-09-23 07:53:25', '2026-09-23 07:53:25', 'Thực Tập Sinh Ca Tối', 'CN Quận 3', 1, 35, NULL, NULL, NULL, NULL, NULL);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `bai_hoc`
--
ALTER TABLE `bai_hoc`
  ADD PRIMARY KEY (`MaBaiHoc`);

--
-- Chỉ mục cho bảng `bai_kiem_tra`
--
ALTER TABLE `bai_kiem_tra`
  ADD PRIMARY KEY (`MaBaiKiemTra`);

--
-- Chỉ mục cho bảng `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Chỉ mục cho bảng `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Chỉ mục cho bảng `cau_hoi_kiem_tra`
--
ALTER TABLE `cau_hoi_kiem_tra`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `chuong_trinh_dao_tao`
--
ALTER TABLE `chuong_trinh_dao_tao`
  ADD PRIMARY KEY (`MaCTDT`);

--
-- Chỉ mục cho bảng `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  ADD KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`);

--
-- Chỉ mục cho bảng `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Chỉ mục cho bảng `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `ket_qua_kiem_tra`
--
ALTER TABLE `ket_qua_kiem_tra`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `nhan_vien_khoa_hoc`
--
ALTER TABLE `nhan_vien_khoa_hoc`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Chỉ mục cho bảng `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `bai_hoc`
--
ALTER TABLE `bai_hoc`
  MODIFY `MaBaiHoc` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `bai_kiem_tra`
--
ALTER TABLE `bai_kiem_tra`
  MODIFY `MaBaiKiemTra` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `cau_hoi_kiem_tra`
--
ALTER TABLE `cau_hoi_kiem_tra`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT cho bảng `chuong_trinh_dao_tao`
--
ALTER TABLE `chuong_trinh_dao_tao`
  MODIFY `MaCTDT` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `ket_qua_kiem_tra`
--
ALTER TABLE `ket_qua_kiem_tra`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `nhan_vien_khoa_hoc`
--
ALTER TABLE `nhan_vien_khoa_hoc`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
