-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1:3306
-- Thời gian đã tạo: Th9 22, 2026 lúc 02:18 PM
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
  `MaBaiHoc` varchar(20) NOT NULL,
  `FK_MaCTDT` varchar(20) NOT NULL,
  `TenBaiHoc` varchar(150) NOT NULL,
  `NoiDung` text,
  `ThuTu` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `bai_hoc`
--

INSERT INTO `bai_hoc` (`MaBaiHoc`, `FK_MaCTDT`, `TenBaiHoc`, `NoiDung`, `ThuTu`) VALUES
('BH_01', 'CTDT_01', 'Chào hỏi khách hàng', 'Nội dung quy tắc 5S và chào hỏi', 1),
('BH_02', 'CTDT_01', 'Xử lý từ chối', 'Kỹ thuật thuyết phục khi khách từ chối', 2);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bai_kiem_tra`
--

CREATE TABLE `bai_kiem_tra` (
  `MaBaiKT` varchar(20) NOT NULL,
  `FK_MaCTDT` varchar(20) NOT NULL,
  `TenBaiKT` varchar(150) NOT NULL,
  `SoCauHoi` int NOT NULL,
  `DiemDat` decimal(4,2) NOT NULL,
  `ThoiGianLamBai` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `bai_kiem_tra`
--

INSERT INTO `bai_kiem_tra` (`MaBaiKT`, `FK_MaCTDT`, `TenBaiKT`, `SoCauHoi`, `DiemDat`, `ThoiGianLamBai`) VALUES
('BKT_01', 'CTDT_01', 'Kiểm tra Kỹ năng Bán hàng', 10, 8.00, 30);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bao_cao`
--

CREATE TABLE `bao_cao` (
  `MaBaoCao` varchar(20) NOT NULL,
  `LoaiBaoCao` varchar(100) NOT NULL,
  `CapDo` varchar(50) DEFAULT NULL,
  `FK_MaCH` varchar(20) DEFAULT NULL,
  `FK_MaVung` varchar(20) DEFAULT NULL,
  `NgayTao` datetime DEFAULT CURRENT_TIMESTAMP,
  `FK_MaNguoiTao` varchar(20) NOT NULL,
  `NoiDungTomTat` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `bao_cao`
--

INSERT INTO `bao_cao` (`MaBaoCao`, `LoaiBaoCao`, `CapDo`, `FK_MaCH`, `FK_MaVung`, `NgayTao`, `FK_MaNguoiTao`, `NoiDungTomTat`) VALUES
('BC_001', 'Báo cáo nguy cơ nhân sự tháng 2', 'VUNG', NULL, 'VUNG_01', '2024-02-22 09:00:00', 'NV001', 'Tổng hợp 1 nhân viên có nguy cơ rủi ro cao tại CH_001');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `canh_bao`
--

CREATE TABLE `canh_bao` (
  `MaCanhBao` varchar(20) NOT NULL,
  `CapDoCanhBao` varchar(50) DEFAULT NULL,
  `FK_MaNV` varchar(20) DEFAULT NULL,
  `FK_MaCH` varchar(20) DEFAULT NULL,
  `FK_MaVung` varchar(20) DEFAULT NULL,
  `NoiDung` varchar(500) NOT NULL,
  `MucDo` varchar(20) DEFAULT NULL,
  `NgayTao` datetime DEFAULT CURRENT_TIMESTAMP,
  `TrangThai` varchar(50) DEFAULT 'CHUA_XU_LY',
  `FK_MaNguoiXuLy` varchar(20) DEFAULT NULL,
  `NgayXuLy` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `canh_bao`
--

INSERT INTO `canh_bao` (`MaCanhBao`, `CapDoCanhBao`, `FK_MaNV`, `FK_MaCH`, `FK_MaVung`, `NoiDung`, `MucDo`, `NgayTao`, `TrangThai`, `FK_MaNguoiXuLy`, `NgayXuLy`) VALUES
('CB_001', 'NHAN_VIEN', 'NV003', 'CH_001', 'VUNG_01', 'Nhân viên Lê Hoàng Cường có nguy cơ hiệu suất thấp (Dự đoán AI: CAO)', 'CAO', '2024-02-21 08:30:00', 'CHUA_XU_LY', NULL, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cau_hoi`
--

CREATE TABLE `cau_hoi` (
  `MaCauHoi` varchar(20) NOT NULL,
  `FK_MaBaiKT` varchar(20) NOT NULL,
  `NoiDungCauHoi` text NOT NULL,
  `LoaiCauHoi` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chuong_trinh_dao_tao`
--

CREATE TABLE `chuong_trinh_dao_tao` (
  `MaCTDT` varchar(20) NOT NULL,
  `TenCTDT` varchar(150) NOT NULL,
  `MoTa` varchar(500) DEFAULT NULL,
  `ThoiLuong` int DEFAULT NULL,
  `NgayTao` date DEFAULT (curdate()),
  `FK_MaNguoiTao` varchar(20) DEFAULT NULL,
  `ChucVuApDung` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `chuong_trinh_dao_tao`
--

INSERT INTO `chuong_trinh_dao_tao` (`MaCTDT`, `TenCTDT`, `MoTa`, `ThoiLuong`, `NgayTao`, `FK_MaNguoiTao`, `ChucVuApDung`) VALUES
('CTDT_01', 'Quy trình Bán hàng Chuẩn', 'Khóa học bắt buộc cho NVBH mới', 12, '2024-01-01', 'NV001', 'VT_NVBH');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cua_hang`
--

CREATE TABLE `cua_hang` (
  `MaCH` varchar(20) NOT NULL,
  `TenCH` varchar(100) NOT NULL,
  `DiaChi` varchar(255) DEFAULT NULL,
  `FK_MaVung` varchar(20) DEFAULT NULL,
  `FK_MaQLCH` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `cua_hang`
--

INSERT INTO `cua_hang` (`MaCH`, `TenCH`, `DiaChi`, `FK_MaVung`, `FK_MaQLCH`) VALUES
('CH_001', 'Cửa hàng Hoàn Kiếm', '12 Phố Cổ, Hà Nội', 'VUNG_01', 'NV002'),
('CH_002', 'Cửa hàng Quận 1', '100 Nguyễn Huệ, TP.HCM', 'VUNG_02', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danh_gia_nang_luc`
--

CREATE TABLE `danh_gia_nang_luc` (
  `MaDGNL` varchar(20) NOT NULL,
  `FK_MaNV` varchar(20) NOT NULL,
  `FK_MaTC` varchar(20) NOT NULL,
  `NgayDanhGia` date NOT NULL,
  `DiemDanhGia` decimal(4,2) NOT NULL,
  `FK_MaNguoiDanhGia` varchar(20) NOT NULL,
  `KetQua` varchar(50) DEFAULT NULL,
  `NhanXet` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `danh_gia_nang_luc`
--

INSERT INTO `danh_gia_nang_luc` (`MaDGNL`, `FK_MaNV`, `FK_MaTC`, `NgayDanhGia`, `DiemDanhGia`, `FK_MaNguoiDanhGia`, `KetQua`, `NhanXet`) VALUES
('DGNL_01', 'NV003', 'TC001', '2024-02-20', 2.50, 'NV002', 'KHONG_DAT', 'Cần cải thiện thái độ giao tiếp với khách hàng');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `dap_an`
--

CREATE TABLE `dap_an` (
  `MaDapAn` varchar(20) NOT NULL,
  `FK_MaCauHoi` varchar(20) NOT NULL,
  `NoiDungDapAn` text NOT NULL,
  `LaDapAnDung` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `du_doan_nguy_co`
--

CREATE TABLE `du_doan_nguy_co` (
  `MaDuDoan` varchar(20) NOT NULL,
  `FK_MaNV` varchar(20) NOT NULL,
  `FK_BatchID` varchar(20) DEFAULT NULL,
  `NgayDuDoan` datetime DEFAULT CURRENT_TIMESTAMP,
  `MucDoNguyCo` varchar(20) DEFAULT NULL,
  `CacChiSoDauVao` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `du_doan_nguy_co`
--

INSERT INTO `du_doan_nguy_co` (`MaDuDoan`, `FK_MaNV`, `FK_BatchID`, `NgayDuDoan`, `MucDoNguyCo`, `CacChiSoDauVao`) VALUES
('DD_001', 'NV003', 'BATCH_2024_01', '2024-02-21 08:00:00', 'CAO', '{\"avg_exam_score\": 5.5, \"exam_pass_rate\": 0.0, \"learning_progress_rate\": 50.0, \"avg_completion_time_days\": 15, \"competency_gap_score\": -1.5, \"overdue_evaluation_count\": 1, \"tenure_months\": 8, \"role_change_count\": 0, \"incident_warning_count\": 1}');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ghi_danh_dao_tao`
--

CREATE TABLE `ghi_danh_dao_tao` (
  `MaGhiDanh` varchar(20) NOT NULL,
  `FK_MaNV` varchar(20) NOT NULL,
  `FK_MaCTDT` varchar(20) NOT NULL,
  `NgayBatDau` date NOT NULL,
  `NgayHoanThanh` date DEFAULT NULL,
  `TrangThai` varchar(50) DEFAULT 'DANG_HOC',
  `NgayDenHanDanhGia` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `ghi_danh_dao_tao`
--

INSERT INTO `ghi_danh_dao_tao` (`MaGhiDanh`, `FK_MaNV`, `FK_MaCTDT`, `NgayBatDau`, `NgayHoanThanh`, `TrangThai`, `NgayDenHanDanhGia`) VALUES
('GD_001', 'NV003', 'CTDT_01', '2024-02-01', NULL, 'CANH_BAO', '2024-02-28');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ket_qua_kiem_tra`
--

CREATE TABLE `ket_qua_kiem_tra` (
  `MaKQ` varchar(20) NOT NULL,
  `FK_MaNV` varchar(20) NOT NULL,
  `FK_MaBaiKT` varchar(20) NOT NULL,
  `Diem` decimal(4,2) NOT NULL,
  `NgayThi` datetime DEFAULT CURRENT_TIMESTAMP,
  `LanThi` int DEFAULT '1',
  `KetQua` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `ket_qua_kiem_tra`
--

INSERT INTO `ket_qua_kiem_tra` (`MaKQ`, `FK_MaNV`, `FK_MaBaiKT`, `Diem`, `NgayThi`, `LanThi`, `KetQua`) VALUES
('KQ_001', 'NV003', 'BKT_01', 5.50, '2024-02-15 14:00:00', 1, 'KHONG_DAT');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nang_luc`
--

CREATE TABLE `nang_luc` (
  `MaNangLuc` varchar(20) NOT NULL,
  `TenNangLuc` varchar(100) NOT NULL,
  `MoTa` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `nang_luc`
--

INSERT INTO `nang_luc` (`MaNangLuc`, `TenNangLuc`, `MoTa`) VALUES
('NL_COMM', 'Kỹ năng giao tiếp', 'Khả năng trình bày và thuyết phục khách hàng'),
('NL_LEAD', 'Kỹ năng quản lý', 'Lập kế hoạch và điều phối nhân sự');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhan_vien`
--

CREATE TABLE `nhan_vien` (
  `MaNV` varchar(20) NOT NULL,
  `FK_MaVaiTro` varchar(20) NOT NULL,
  `FK_MaCuaHang` varchar(20) DEFAULT NULL,
  `HoTen` varchar(100) NOT NULL,
  `NgaySinh` date DEFAULT NULL,
  `GioiTinh` varchar(10) DEFAULT NULL,
  `SDT` varchar(15) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `NgayVaoLam` date NOT NULL,
  `TrangThai` varchar(50) DEFAULT 'HOAT_DONG'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `nhan_vien`
--

INSERT INTO `nhan_vien` (`MaNV`, `FK_MaVaiTro`, `FK_MaCuaHang`, `HoTen`, `NgaySinh`, `GioiTinh`, `SDT`, `Email`, `NgayVaoLam`, `TrangThai`) VALUES
('NV001', 'VT_QLV', NULL, 'Nguyễn Văn An', '1985-05-12', 'Nam', '0901234567', 'an.nguyen@company.com', '2018-01-15', 'HOAT_DONG'),
('NV002', 'VT_QLCH', 'CH_001', 'Trần Thị Bình', '1990-08-20', 'Nữ', '0902345678', 'binh.tran@company.com', '2020-03-01', 'HOAT_DONG'),
('NV003', 'VT_NVBH', 'CH_001', 'Lê Hoàng Cường', '1998-11-02', 'Nam', '0903456789', 'cuong.le@company.com', '2023-06-10', 'HOAT_DONG');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tien_do_hoc`
--

CREATE TABLE `tien_do_hoc` (
  `MaTienDo` varchar(20) NOT NULL,
  `FK_MaGhiDanh` varchar(20) NOT NULL,
  `FK_MaBaiHoc` varchar(20) NOT NULL,
  `TrangThaiBaiHoc` varchar(50) DEFAULT 'CHUA_HOAN_THANH',
  `NgayHoanThanh` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `tien_do_hoc`
--

INSERT INTO `tien_do_hoc` (`MaTienDo`, `FK_MaGhiDanh`, `FK_MaBaiHoc`, `TrangThaiBaiHoc`, `NgayHoanThanh`) VALUES
('TD_001', 'GD_001', 'BH_01', 'HOAN_THANH', '2024-02-05 10:30:00'),
('TD_002', 'GD_001', 'BH_02', 'CHUA_HOAN_THANH', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tieu_chuan_nang_luc`
--

CREATE TABLE `tieu_chuan_nang_luc` (
  `MaTC` varchar(20) NOT NULL,
  `FK_MaVaiTro` varchar(20) NOT NULL,
  `FK_MaNangLuc` varchar(20) NOT NULL,
  `MucDoYeuCau` int DEFAULT NULL
) ;

--
-- Đang đổ dữ liệu cho bảng `tieu_chuan_nang_luc`
--

INSERT INTO `tieu_chuan_nang_luc` (`MaTC`, `FK_MaVaiTro`, `FK_MaNangLuc`, `MucDoYeuCau`) VALUES
('TC001', 'VT_NVBH', 'NL_COMM', 4),
('TC002', 'VT_QLCH', 'NL_LEAD', 5);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `training_batch`
--

CREATE TABLE `training_batch` (
  `BatchID` varchar(20) NOT NULL,
  `ModelVersion` varchar(50) NOT NULL,
  `TrainedAt` datetime DEFAULT CURRENT_TIMESTAMP,
  `AccuracyScore` decimal(5,4) DEFAULT NULL,
  `MoTaAlgorithm` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `training_batch`
--

INSERT INTO `training_batch` (`BatchID`, `ModelVersion`, `TrainedAt`, `AccuracyScore`, `MoTaAlgorithm`) VALUES
('BATCH_2024_01', 'v1.2.0-XGBoost', '2024-01-01 00:00:00', 0.9125, 'Mô hình XGBoost phân loại rủi ro nghỉ việc/kém hiệu suất');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `vai_tro`
--

CREATE TABLE `vai_tro` (
  `MaVaiTro` varchar(20) NOT NULL,
  `TenVaiTro` varchar(100) NOT NULL,
  `MoTa` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `vai_tro`
--

INSERT INTO `vai_tro` (`MaVaiTro`, `TenVaiTro`, `MoTa`) VALUES
('VT_NVBH', 'Nhân viên bán hàng', 'Tư vấn bán hàng và vận hành sản phẩm'),
('VT_QLCH', 'Quản lý cửa hàng', 'Điều hành hoạt động 1 cửa hàng'),
('VT_QLV', 'Quản lý vùng', 'Quản lý toàn bộ cửa hàng thuộc 1 vùng');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `vung`
--

CREATE TABLE `vung` (
  `MaVung` varchar(20) NOT NULL,
  `TenVung` varchar(100) NOT NULL,
  `FK_MaQLVung` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `vung`
--

INSERT INTO `vung` (`MaVung`, `TenVung`, `FK_MaQLVung`) VALUES
('VUNG_01', 'Vùng Miền Bắc', 'NV001'),
('VUNG_02', 'Vùng Miền Nam', NULL);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `bai_hoc`
--
ALTER TABLE `bai_hoc`
  ADD PRIMARY KEY (`MaBaiHoc`),
  ADD KEY `FK_BaiHoc_CTDT` (`FK_MaCTDT`);

--
-- Chỉ mục cho bảng `bai_kiem_tra`
--
ALTER TABLE `bai_kiem_tra`
  ADD PRIMARY KEY (`MaBaiKT`),
  ADD KEY `FK_BKT_CTDT` (`FK_MaCTDT`);

--
-- Chỉ mục cho bảng `bao_cao`
--
ALTER TABLE `bao_cao`
  ADD PRIMARY KEY (`MaBaoCao`),
  ADD KEY `FK_BC_CH` (`FK_MaCH`),
  ADD KEY `FK_BC_Vung` (`FK_MaVung`),
  ADD KEY `FK_BC_NguoiTao` (`FK_MaNguoiTao`);

--
-- Chỉ mục cho bảng `canh_bao`
--
ALTER TABLE `canh_bao`
  ADD PRIMARY KEY (`MaCanhBao`),
  ADD KEY `FK_CB_NV` (`FK_MaNV`),
  ADD KEY `FK_CB_CH` (`FK_MaCH`),
  ADD KEY `FK_CB_Vung` (`FK_MaVung`),
  ADD KEY `FK_CB_NguoiXL` (`FK_MaNguoiXuLy`);

--
-- Chỉ mục cho bảng `cau_hoi`
--
ALTER TABLE `cau_hoi`
  ADD PRIMARY KEY (`MaCauHoi`),
  ADD KEY `FK_CH_BKT` (`FK_MaBaiKT`);

--
-- Chỉ mục cho bảng `chuong_trinh_dao_tao`
--
ALTER TABLE `chuong_trinh_dao_tao`
  ADD PRIMARY KEY (`MaCTDT`),
  ADD KEY `FK_CTDT_NguoiTao` (`FK_MaNguoiTao`),
  ADD KEY `FK_CTDT_ChucVu` (`ChucVuApDung`);

--
-- Chỉ mục cho bảng `cua_hang`
--
ALTER TABLE `cua_hang`
  ADD PRIMARY KEY (`MaCH`),
  ADD KEY `FK_CH_Vung` (`FK_MaVung`),
  ADD KEY `FK_CH_QL` (`FK_MaQLCH`);

--
-- Chỉ mục cho bảng `danh_gia_nang_luc`
--
ALTER TABLE `danh_gia_nang_luc`
  ADD PRIMARY KEY (`MaDGNL`),
  ADD KEY `FK_DGNL_NV` (`FK_MaNV`),
  ADD KEY `FK_DGNL_TC` (`FK_MaTC`),
  ADD KEY `FK_DGNL_NguoiDG` (`FK_MaNguoiDanhGia`);

--
-- Chỉ mục cho bảng `dap_an`
--
ALTER TABLE `dap_an`
  ADD PRIMARY KEY (`MaDapAn`),
  ADD KEY `FK_DA_CauHoi` (`FK_MaCauHoi`);

--
-- Chỉ mục cho bảng `du_doan_nguy_co`
--
ALTER TABLE `du_doan_nguy_co`
  ADD PRIMARY KEY (`MaDuDoan`),
  ADD KEY `FK_DD_NV` (`FK_MaNV`),
  ADD KEY `FK_DD_Batch` (`FK_BatchID`);

--
-- Chỉ mục cho bảng `ghi_danh_dao_tao`
--
ALTER TABLE `ghi_danh_dao_tao`
  ADD PRIMARY KEY (`MaGhiDanh`),
  ADD KEY `FK_GD_NV` (`FK_MaNV`),
  ADD KEY `FK_GD_CTDT` (`FK_MaCTDT`);

--
-- Chỉ mục cho bảng `ket_qua_kiem_tra`
--
ALTER TABLE `ket_qua_kiem_tra`
  ADD PRIMARY KEY (`MaKQ`),
  ADD KEY `FK_KQ_NV` (`FK_MaNV`),
  ADD KEY `FK_KQ_BKT` (`FK_MaBaiKT`);

--
-- Chỉ mục cho bảng `nang_luc`
--
ALTER TABLE `nang_luc`
  ADD PRIMARY KEY (`MaNangLuc`);

--
-- Chỉ mục cho bảng `nhan_vien`
--
ALTER TABLE `nhan_vien`
  ADD PRIMARY KEY (`MaNV`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD KEY `FK_NV_VaiTro` (`FK_MaVaiTro`),
  ADD KEY `FK_NV_CuaHang` (`FK_MaCuaHang`);

--
-- Chỉ mục cho bảng `tien_do_hoc`
--
ALTER TABLE `tien_do_hoc`
  ADD PRIMARY KEY (`MaTienDo`),
  ADD KEY `FK_TD_GhiDanh` (`FK_MaGhiDanh`),
  ADD KEY `FK_TD_BaiHoc` (`FK_MaBaiHoc`);

--
-- Chỉ mục cho bảng `tieu_chuan_nang_luc`
--
ALTER TABLE `tieu_chuan_nang_luc`
  ADD PRIMARY KEY (`MaTC`),
  ADD KEY `FK_TCNL_VaiTro` (`FK_MaVaiTro`),
  ADD KEY `FK_TCNL_NangLuc` (`FK_MaNangLuc`);

--
-- Chỉ mục cho bảng `training_batch`
--
ALTER TABLE `training_batch`
  ADD PRIMARY KEY (`BatchID`);

--
-- Chỉ mục cho bảng `vai_tro`
--
ALTER TABLE `vai_tro`
  ADD PRIMARY KEY (`MaVaiTro`);

--
-- Chỉ mục cho bảng `vung`
--
ALTER TABLE `vung`
  ADD PRIMARY KEY (`MaVung`),
  ADD KEY `FK_Vung_QL` (`FK_MaQLVung`);

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `bai_hoc`
--
ALTER TABLE `bai_hoc`
  ADD CONSTRAINT `FK_BaiHoc_CTDT` FOREIGN KEY (`FK_MaCTDT`) REFERENCES `chuong_trinh_dao_tao` (`MaCTDT`);

--
-- Các ràng buộc cho bảng `bai_kiem_tra`
--
ALTER TABLE `bai_kiem_tra`
  ADD CONSTRAINT `FK_BKT_CTDT` FOREIGN KEY (`FK_MaCTDT`) REFERENCES `chuong_trinh_dao_tao` (`MaCTDT`);

--
-- Các ràng buộc cho bảng `bao_cao`
--
ALTER TABLE `bao_cao`
  ADD CONSTRAINT `FK_BC_CH` FOREIGN KEY (`FK_MaCH`) REFERENCES `cua_hang` (`MaCH`),
  ADD CONSTRAINT `FK_BC_NguoiTao` FOREIGN KEY (`FK_MaNguoiTao`) REFERENCES `nhan_vien` (`MaNV`),
  ADD CONSTRAINT `FK_BC_Vung` FOREIGN KEY (`FK_MaVung`) REFERENCES `vung` (`MaVung`);

--
-- Các ràng buộc cho bảng `canh_bao`
--
ALTER TABLE `canh_bao`
  ADD CONSTRAINT `FK_CB_CH` FOREIGN KEY (`FK_MaCH`) REFERENCES `cua_hang` (`MaCH`),
  ADD CONSTRAINT `FK_CB_NguoiXL` FOREIGN KEY (`FK_MaNguoiXuLy`) REFERENCES `nhan_vien` (`MaNV`),
  ADD CONSTRAINT `FK_CB_NV` FOREIGN KEY (`FK_MaNV`) REFERENCES `nhan_vien` (`MaNV`),
  ADD CONSTRAINT `FK_CB_Vung` FOREIGN KEY (`FK_MaVung`) REFERENCES `vung` (`MaVung`);

--
-- Các ràng buộc cho bảng `cau_hoi`
--
ALTER TABLE `cau_hoi`
  ADD CONSTRAINT `FK_CH_BKT` FOREIGN KEY (`FK_MaBaiKT`) REFERENCES `bai_kiem_tra` (`MaBaiKT`);

--
-- Các ràng buộc cho bảng `chuong_trinh_dao_tao`
--
ALTER TABLE `chuong_trinh_dao_tao`
  ADD CONSTRAINT `FK_CTDT_ChucVu` FOREIGN KEY (`ChucVuApDung`) REFERENCES `vai_tro` (`MaVaiTro`),
  ADD CONSTRAINT `FK_CTDT_NguoiTao` FOREIGN KEY (`FK_MaNguoiTao`) REFERENCES `nhan_vien` (`MaNV`);

--
-- Các ràng buộc cho bảng `cua_hang`
--
ALTER TABLE `cua_hang`
  ADD CONSTRAINT `FK_CH_QL` FOREIGN KEY (`FK_MaQLCH`) REFERENCES `nhan_vien` (`MaNV`),
  ADD CONSTRAINT `FK_CH_Vung` FOREIGN KEY (`FK_MaVung`) REFERENCES `vung` (`MaVung`);

--
-- Các ràng buộc cho bảng `danh_gia_nang_luc`
--
ALTER TABLE `danh_gia_nang_luc`
  ADD CONSTRAINT `FK_DGNL_NguoiDG` FOREIGN KEY (`FK_MaNguoiDanhGia`) REFERENCES `nhan_vien` (`MaNV`),
  ADD CONSTRAINT `FK_DGNL_NV` FOREIGN KEY (`FK_MaNV`) REFERENCES `nhan_vien` (`MaNV`),
  ADD CONSTRAINT `FK_DGNL_TC` FOREIGN KEY (`FK_MaTC`) REFERENCES `tieu_chuan_nang_luc` (`MaTC`);

--
-- Các ràng buộc cho bảng `dap_an`
--
ALTER TABLE `dap_an`
  ADD CONSTRAINT `FK_DA_CauHoi` FOREIGN KEY (`FK_MaCauHoi`) REFERENCES `cau_hoi` (`MaCauHoi`);

--
-- Các ràng buộc cho bảng `du_doan_nguy_co`
--
ALTER TABLE `du_doan_nguy_co`
  ADD CONSTRAINT `FK_DD_Batch` FOREIGN KEY (`FK_BatchID`) REFERENCES `training_batch` (`BatchID`),
  ADD CONSTRAINT `FK_DD_NV` FOREIGN KEY (`FK_MaNV`) REFERENCES `nhan_vien` (`MaNV`);

--
-- Các ràng buộc cho bảng `ghi_danh_dao_tao`
--
ALTER TABLE `ghi_danh_dao_tao`
  ADD CONSTRAINT `FK_GD_CTDT` FOREIGN KEY (`FK_MaCTDT`) REFERENCES `chuong_trinh_dao_tao` (`MaCTDT`),
  ADD CONSTRAINT `FK_GD_NV` FOREIGN KEY (`FK_MaNV`) REFERENCES `nhan_vien` (`MaNV`);

--
-- Các ràng buộc cho bảng `ket_qua_kiem_tra`
--
ALTER TABLE `ket_qua_kiem_tra`
  ADD CONSTRAINT `FK_KQ_BKT` FOREIGN KEY (`FK_MaBaiKT`) REFERENCES `bai_kiem_tra` (`MaBaiKT`),
  ADD CONSTRAINT `FK_KQ_NV` FOREIGN KEY (`FK_MaNV`) REFERENCES `nhan_vien` (`MaNV`);

--
-- Các ràng buộc cho bảng `nhan_vien`
--
ALTER TABLE `nhan_vien`
  ADD CONSTRAINT `FK_NV_CuaHang` FOREIGN KEY (`FK_MaCuaHang`) REFERENCES `cua_hang` (`MaCH`),
  ADD CONSTRAINT `FK_NV_VaiTro` FOREIGN KEY (`FK_MaVaiTro`) REFERENCES `vai_tro` (`MaVaiTro`);

--
-- Các ràng buộc cho bảng `tien_do_hoc`
--
ALTER TABLE `tien_do_hoc`
  ADD CONSTRAINT `FK_TD_BaiHoc` FOREIGN KEY (`FK_MaBaiHoc`) REFERENCES `bai_hoc` (`MaBaiHoc`),
  ADD CONSTRAINT `FK_TD_GhiDanh` FOREIGN KEY (`FK_MaGhiDanh`) REFERENCES `ghi_danh_dao_tao` (`MaGhiDanh`);

--
-- Các ràng buộc cho bảng `tieu_chuan_nang_luc`
--
ALTER TABLE `tieu_chuan_nang_luc`
  ADD CONSTRAINT `FK_TCNL_NangLuc` FOREIGN KEY (`FK_MaNangLuc`) REFERENCES `nang_luc` (`MaNangLuc`),
  ADD CONSTRAINT `FK_TCNL_VaiTro` FOREIGN KEY (`FK_MaVaiTro`) REFERENCES `vai_tro` (`MaVaiTro`);

--
-- Các ràng buộc cho bảng `vung`
--
ALTER TABLE `vung`
  ADD CONSTRAINT `FK_Vung_QL` FOREIGN KEY (`FK_MaQLVung`) REFERENCES `nhan_vien` (`MaNV`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
