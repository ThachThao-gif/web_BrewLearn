"""
Tính điểm rủi ro (diem_rui_ro) cho từng nhân viên - BrewLearn
Công thức rule-based (chưa cần machine learning thật).

diem_rui_ro thang điểm 0 - 100, CÀNG CAO CÀNG RỦI RO.

=====================================================================
CÔNG THỨC
=====================================================================
diem_an_toan = (
      0.30 * diem_danh_gia_trung_binh      # từ danh_gia_nang_luc.Diem
    + 0.25 * ty_le_dat_bai_kiem_tra * 100  # từ ket_qua_kiem_tra
    + 0.20 * tien_do_dao_tao_trung_binh    # từ nhan_vien_khoa_hoc.TienDo
    + 0.25 * (100 - diem_tru_tu_loi)       # từ loi_nghiep_vu.diem_tru_risk
)

diem_rui_ro = 100 - diem_an_toan + phat_tre_han

Trọng số (0.30 / 0.25 / 0.20 / 0.25) chỉ là điểm khởi đầu hợp lý —
bạn nên điều chỉnh lại sau khi có nhiều dữ liệu thực tế hơn, và nên
nêu rõ trong báo cáo đây là trọng số do nhóm tự đề xuất (không phải
suy ra từ machine learning) để hội đồng không hiểu nhầm là kết quả
huấn luyện.

=====================================================================
LƯU Ý QUAN TRỌNG VỀ DỮ LIỆU
=====================================================================
Trong bảng ket_qua_kiem_tra hiện tại, cột TrangThai đang bị LẶP
KHÔNG ĐỒNG NHẤT giữa các bản ghi cũ và mới:
    'Dat' / 'DaDat' / 'Đạt'      -> đều có nghĩa là ĐẠT
    'ChuaDat' / 'Không đạt'      -> đều có nghĩa là CHƯA ĐẠT
Cần chuẩn hóa (ví dụ ép hết về 2 giá trị 'Dat' / 'ChuaDat', hoặc
chuyển hẳn sang kiểu boolean/enum) TRƯỚC khi tính điểm, nếu không
tỷ lệ đạt sẽ bị tính sai.
"""

from datetime import date, timedelta

# Ánh xạ chuẩn hóa các biến thể trạng thái đang tồn tại trong dữ liệu
CAC_GIA_TRI_DAT = {"Dat", "DaDat", "Đạt"}
CAC_GIA_TRI_CHUA_DAT = {"ChuaDat", "Không đạt"}


def chuan_hoa_trang_thai(trang_thai: str) -> bool:
    """Trả về True nếu là 'đạt', False nếu 'chưa đạt'."""
    if trang_thai in CAC_GIA_TRI_DAT:
        return True
    if trang_thai in CAC_GIA_TRI_CHUA_DAT:
        return False
    raise ValueError(f"Giá trị TrangThai lạ, cần bổ sung vào ánh xạ: {trang_thai!r}")


def tinh_diem_danh_gia_trung_binh(danh_sach_danh_gia: list[dict]) -> float:
    """
    danh_sach_danh_gia: các bản ghi danh_gia_nang_luc của 1 nhân viên,
    chỉ lấy những bản ghi đã có Diem (TrangThai không phải 'ChoDanhGia').
    Trả về điểm trung bình 0-100. Không có dữ liệu -> mặc định trung tính 70.
    """
    diem_hop_le = [dg["Diem"] for dg in danh_sach_danh_gia if dg.get("Diem")]
    if not diem_hop_le:
        return 70.0
    return sum(diem_hop_le) / len(diem_hop_le)


def tinh_ty_le_dat_bai_kiem_tra(danh_sach_ket_qua: list[dict]) -> float:
    """
    danh_sach_ket_qua: các bản ghi ket_qua_kiem_tra của 1 nhân viên.
    Trả về tỷ lệ 0.0 - 1.0. Chưa làm bài nào -> mặc định trung tính 0.7.
    """
    if not danh_sach_ket_qua:
        return 0.7
    so_dat = sum(1 for kq in danh_sach_ket_qua if chuan_hoa_trang_thai(kq["TrangThai"]))
    return so_dat / len(danh_sach_ket_qua)


def tinh_tien_do_dao_tao_trung_binh(danh_sach_khoa_hoc: list[dict]) -> float:
    """
    danh_sach_khoa_hoc: các bản ghi nhan_vien_khoa_hoc của 1 nhân viên.
    Trả về % tiến độ trung bình 0-100.
    """
    if not danh_sach_khoa_hoc:
        return 70.0
    return sum(kh["TienDo"] for kh in danh_sach_khoa_hoc) / len(danh_sach_khoa_hoc)


def tinh_diem_tru_tu_loi(danh_sach_loi: list[dict], ngay_hien_tai: date, so_ngay_xem_xet: int = 30) -> float:
    """
    danh_sach_loi: các bản ghi loi_nghiep_vu của 1 nhân viên.
    Chỉ tính lỗi trong vòng `so_ngay_xem_xet` ngày gần nhất (mặc định 30 ngày)
    để lỗi cũ không ám ảnh nhân viên mãi mãi. Trả về tổng điểm trừ, giới hạn tối đa 100.
    """
    nguong_thoi_gian = ngay_hien_tai - timedelta(days=so_ngay_xem_xet)
    tong = sum(
        loi["diem_tru_risk"]
        for loi in danh_sach_loi
        if loi["thoi_diem_xay_ra"].date() >= nguong_thoi_gian
    )
    return min(tong, 100.0)


def tinh_phat_tre_han(danh_sach_khoa_hoc: list[dict], ngay_hien_tai: date) -> float:
    """
    Cộng thêm điểm rủi ro nếu có khóa học quá han_hoan_thanh mà chưa HoanThanh.
    Mỗi khóa trễ hạn: +5 điểm, tối đa +20 điểm.
    """
    so_khoa_tre = 0
    for kh in danh_sach_khoa_hoc:
        han = kh.get("han_hoan_thanh")
        if han and kh["TrangThai"] != "HoanThanh" and han < ngay_hien_tai:
            so_khoa_tre += 1
    return min(so_khoa_tre * 5, 20.0)


def tinh_diem_rui_ro(
    danh_sach_danh_gia: list[dict],
    danh_sach_ket_qua: list[dict],
    danh_sach_khoa_hoc: list[dict],
    danh_sach_loi: list[dict],
    ngay_hien_tai: date | None = None,
) -> dict:
    """Hàm chính: trả về dict gồm điểm rủi ro, mức cảnh báo và breakdown để giải thích."""
    ngay_hien_tai = ngay_hien_tai or date.today()

    diem_danh_gia = tinh_diem_danh_gia_trung_binh(danh_sach_danh_gia)
    ty_le_dat = tinh_ty_le_dat_bai_kiem_tra(danh_sach_ket_qua)
    tien_do = tinh_tien_do_dao_tao_trung_binh(danh_sach_khoa_hoc)
    diem_tru_loi = tinh_diem_tru_tu_loi(danh_sach_loi, ngay_hien_tai)
    phat_tre_han = tinh_phat_tre_han(danh_sach_khoa_hoc, ngay_hien_tai)

    diem_an_toan = (
        0.30 * diem_danh_gia
        + 0.25 * ty_le_dat * 100
        + 0.20 * tien_do
        + 0.25 * (100 - diem_tru_loi)
    )

    diem_rui_ro = max(0.0, min(100.0, 100 - diem_an_toan + phat_tre_han))

    if diem_rui_ro >= 60:
        muc_canh_bao = "Cao"
    elif diem_rui_ro >= 30:
        muc_canh_bao = "TrungBinh"
    else:
        muc_canh_bao = "Thap"

    return {
        "diem_rui_ro": round(diem_rui_ro, 1),
        "muc_canh_bao": muc_canh_bao,
        "chi_tiet_yeu_to": {
            "diem_danh_gia_trung_binh": round(diem_danh_gia, 1),
            "ty_le_dat_bai_kiem_tra": round(ty_le_dat * 100, 1),
            "tien_do_dao_tao_trung_binh": round(tien_do, 1),
            "diem_tru_tu_loi_30_ngay": round(diem_tru_loi, 1),
            "phat_tre_han": round(phat_tre_han, 1),
        },
    }


# ---------------------------------------------------------------
# VÍ DỤ CHẠY THỬ (dữ liệu giả lập theo đúng cấu trúc cột thật)
# ---------------------------------------------------------------
if __name__ == "__main__":
    ket_qua = tinh_diem_rui_ro(
        danh_sach_danh_gia=[{"Diem": 83}, {"Diem": 71}],
        danh_sach_ket_qua=[
            {"TrangThai": "ChuaDat"},
            {"TrangThai": "Không đạt"},
            {"TrangThai": "Đạt"},
        ],
        danh_sach_khoa_hoc=[
            {"TienDo": 100, "TrangThai": "HoanThanh", "han_hoan_thanh": None},
            {"TienDo": 14, "TrangThai": "DangHoc", "han_hoan_thanh": date(2026, 9, 20)},
        ],
        danh_sach_loi=[
            {"diem_tru_risk": 5, "thoi_diem_xay_ra": __import__("datetime").datetime(2026, 9, 26, 12, 27)},
            {"diem_tru_risk": 5, "thoi_diem_xay_ra": __import__("datetime").datetime(2026, 9, 29, 9, 28)},
        ],
    )
    print(ket_qua)