"""
Kết nối CSDL MySQL của Laravel, lấy dữ liệu đào tạo thực tế
của từng nhân viên, tạo 5 biến đầu vào cho mô hình ML,
gọi predict_risk() và lưu kết quả vào bảng du_doan_nguy_co.

5 biến đầu vào:
1. progress_percent
2. avg_quiz_score
3. quiz_attempts
4. failed_rate
5. learning_visits

Điều kiện chạy mô hình:
quiz_attempts > 0 AND learning_visits > 0

Nếu chưa đủ dữ liệu:
- Không gọi model
- muc_canh_bao = 'ChuaDuDuLieu'
- diem_rui_ro = NULL

Chạy:
python app/ml/ket_noi_db.py
"""

import json
import os

import pymysql
from dotenv import load_dotenv

from predict import predict_risk


# ===============================================================
# 1. ĐỌC CẤU HÌNH DATABASE TỪ FILE .env CỦA LARAVEL
# ===============================================================

BASE_DIR = os.path.dirname(os.path.abspath(__file__))

ENV_PATH = os.path.abspath(
    os.path.join(os.path.dirname(__file__), "..", "..", ".env")
)

load_dotenv(ENV_PATH, override=True)

DB_CONFIG = {
    "host": os.getenv("DB_HOST", "127.0.0.1"),
    "port": int(os.getenv("DB_PORT", 3306)),
    "user": os.getenv("DB_USERNAME", "root"),
    "password": os.getenv("DB_PASSWORD", ""),
    "database": os.getenv("DB_DATABASE", "brewlearn"),
    "charset": "utf8mb4",
    "cursorclass": pymysql.cursors.DictCursor,
}


# ===============================================================
# 2. LẤY DANH SÁCH NHÂN VIÊN
# ===============================================================

def lay_danh_sach_nhan_vien(conn) -> list[dict]:
    """
    Lấy các nhân viên cần dự đoán.

    Quản lý cửa hàng không phải đối tượng được mô hình
    dự đoán nguy cơ nên được loại khỏi danh sách.
    """

    with conn.cursor() as cur:

        cur.execute(
            """
            SELECT
                id,
                cua_hang_id,
                ChucVu
            FROM users
            WHERE ChucVu NOT IN ('Quản lý cửa hàng')
            """
        )

        return cur.fetchall()


# ===============================================================
# 3. LẤY TIẾN ĐỘ ĐÀO TẠO
# ===============================================================

def lay_progress_percent(conn, user_id: int) -> float:
    """
    Tính tiến độ đào tạo trung bình của nhân viên.

    Nguồn:
        nhan_vien_khoa_hoc.TienDo

    Nếu chưa có khóa học:
        trả về 0.0
    """

    with conn.cursor() as cur:

        cur.execute(
            """
            SELECT
                COALESCE(AVG(TienDo), 0) AS progress_percent
            FROM nhan_vien_khoa_hoc
            WHERE user_id = %s
            """,
            (user_id,),
        )

        row = cur.fetchone()

    return float(row["progress_percent"] or 0)


# ===============================================================
# 4. LẤY THỐNG KÊ QUIZ
# ===============================================================

def lay_thong_ke_quiz(conn, user_id: int) -> dict:
    """
    Lấy các biến liên quan đến Quiz:

    - avg_quiz_score
    - quiz_attempts
    - failed_attempts
    - failed_rate

    Quy ước:
        Điểm < 80 = không đạt.

    Nếu chưa làm Quiz:
        avg_quiz_score = None
        failed_rate = None
        quiz_attempts = 0
        co_du_lieu_quiz = False
    """

    with conn.cursor() as cur:

        cur.execute(
            """
            SELECT
                AVG(Diem) AS avg_quiz_score,
                COUNT(*) AS quiz_attempts,
                SUM(
                    CASE
                        WHEN Diem < 80 THEN 1
                        ELSE 0
                    END
                ) AS failed_attempts
            FROM ket_qua_kiem_tra
            WHERE user_id = %s
            """,
            (user_id,),
        )

        row = cur.fetchone()

    quiz_attempts = int(row["quiz_attempts"] or 0)
    failed_attempts = int(row["failed_attempts"] or 0)

    co_du_lieu_quiz = quiz_attempts > 0

    if co_du_lieu_quiz:

        avg_quiz_score = float(row["avg_quiz_score"])

        failed_rate = (
            failed_attempts / quiz_attempts
        )

    else:

        avg_quiz_score = None
        failed_rate = None

    return {
        "avg_quiz_score": avg_quiz_score,
        "quiz_attempts": quiz_attempts,
        "failed_attempts": failed_attempts,
        "failed_rate": failed_rate,
        "co_du_lieu_quiz": co_du_lieu_quiz,
    }


# ===============================================================
# 5. LẤY SỐ LƯỢT HỌC
# ===============================================================

def lay_learning_visits(conn, user_id: int) -> int:
    """
    Tổng số lượt nhân viên mở các bài học.

    Nguồn:
        user_lesson_progresses.visit_count
    """

    with conn.cursor() as cur:

        cur.execute(
            """
            SELECT
                COALESCE(SUM(visit_count), 0) AS learning_visits
            FROM user_lesson_progresses
            WHERE user_id = %s
            """,
            (user_id,),
        )

        row = cur.fetchone()

    return int(row["learning_visits"] or 0)


# ===============================================================
# 6. TẠO BỘ DỮ LIỆU ĐẦU VÀO CHO 1 NHÂN VIÊN
# ===============================================================

def lay_du_lieu_du_doan(conn, user_id: int) -> dict:
    """
    Tổng hợp dữ liệu từ các bảng để tạo dữ liệu đầu vào.

    Model chỉ được gọi khi:
        quiz_attempts > 0
        AND
        learning_visits > 0
    """

    progress_percent = lay_progress_percent(
        conn,
        user_id
    )

    quiz = lay_thong_ke_quiz(
        conn,
        user_id
    )

    learning_visits = lay_learning_visits(
        conn,
        user_id
    )

    du_dieu_kien_du_doan = (
        quiz["quiz_attempts"] > 0
        and learning_visits > 0
    )

    return {
        "progress_percent": progress_percent,
        "avg_quiz_score": quiz["avg_quiz_score"],
        "quiz_attempts": quiz["quiz_attempts"],
        "failed_rate": quiz["failed_rate"],
        "learning_visits": learning_visits,

        # Hai biến này KHÔNG phải feature của model.
        # Chỉ dùng để quản lý trạng thái dữ liệu.
        "co_du_lieu_quiz": quiz["co_du_lieu_quiz"],
        "du_dieu_kien_du_doan": du_dieu_kien_du_doan,
    }


# ===============================================================
# 7. THỰC HIỆN DỰ ĐOÁN
# ===============================================================

def du_doan_nhan_vien(du_lieu: dict) -> dict:
    """
    Nếu chưa đủ dữ liệu:
        không gọi model.

    Nếu đủ:
        gọi predict_risk() với đúng 5 feature.
    """

    if not du_lieu["du_dieu_kien_du_doan"]:

        return {
            "diem_rui_ro": None,
            "muc_canh_bao": "ChuaDuDuLieu",
            "chi_tiet_yeu_to": du_lieu,
            "phien_ban_mo_hinh": None,
        }

    result = predict_risk(
        progress_percent=du_lieu["progress_percent"],
        avg_quiz_score=du_lieu["avg_quiz_score"],
        quiz_attempts=du_lieu["quiz_attempts"],
        failed_rate=du_lieu["failed_rate"],
        learning_visits=du_lieu["learning_visits"],
    )

    # predict.py trả risk_level:
    # Thấp / Trung bình / Cao
    #
    # Database dùng:
    # Thap / TrungBinh / Cao

    risk_mapping = {
        "Thấp": "Thap",
        "Trung bình": "TrungBinh",
        "Cao": "Cao",
    }

    muc_canh_bao = risk_mapping.get(
        result["risk_level"]
    )

    if muc_canh_bao is None:
        raise ValueError(
            f"Mức nguy cơ không hợp lệ từ model: "
            f"{result['risk_level']}"
        )

    return {
        # Lưu probability 0 -> 1.
        # Ví dụ: 0.7001 = 70.01%
        "diem_rui_ro": float(result["probability"]),

        "muc_canh_bao": muc_canh_bao,

        # Chỉ lưu dữ liệu đầu vào thô.
        # Không gọi đây là nguyên nhân/yếu tố ảnh hưởng.
        "chi_tiet_yeu_to": du_lieu,

        "phien_ban_mo_hinh": result["model_name"],
    }


# ===============================================================
# 8. GHI KẾT QUẢ VÀO DATABASE
# ===============================================================

def ghi_ket_qua_du_doan(
    conn,
    user_id: int,
    cua_hang_id: int | None,
    ket_qua: dict
) -> None:

    with conn.cursor() as cur:

        cur.execute(
            """
            INSERT INTO du_doan_nguy_co
            (
                user_id,
                cua_hang_id,
                diem_rui_ro,
                muc_canh_bao,
                chi_tiet_yeu_to,
                phien_ban_mo_hinh,
                ngay_du_doan
            )
            VALUES
            (
                %s,
                %s,
                %s,
                %s,
                %s,
                %s,
                NOW()
            )
            """,
            (
                user_id,
                cua_hang_id,
                ket_qua["diem_rui_ro"],
                ket_qua["muc_canh_bao"],
                json.dumps(
                    ket_qua["chi_tiet_yeu_to"],
                    ensure_ascii=False
                ),
                ket_qua["phien_ban_mo_hinh"],
            ),
        )


# ===============================================================
# 9. CHẠY DỰ ĐOÁN CHO TẤT CẢ NHÂN VIÊN
# ===============================================================

def chay_du_doan_cho_tat_ca_nhan_vien() -> None:

    conn = pymysql.connect(**DB_CONFIG)

    try:

        danh_sach_nv = lay_danh_sach_nhan_vien(
            conn
        )

        print(
            f"Tìm thấy {len(danh_sach_nv)} "
            f"nhân viên cần kiểm tra."
        )

        for nv in danh_sach_nv:

            user_id = nv["id"]

            try:

                # -------------------------------------------
                # Lấy dữ liệu thực tế từ MySQL
                # -------------------------------------------

                du_lieu = lay_du_lieu_du_doan(
                    conn,
                    user_id
                )

                # -------------------------------------------
                # Chạy model hoặc trả ChuaDuDuLieu
                # -------------------------------------------

                ket_qua = du_doan_nhan_vien(
                    du_lieu
                )

                # -------------------------------------------
                # Ghi kết quả vào DB
                # -------------------------------------------

                ghi_ket_qua_du_doan(
                    conn,
                    user_id,
                    nv["cua_hang_id"],
                    ket_qua
                )

                conn.commit()

                # -------------------------------------------
                # Hiển thị kết quả terminal
                # -------------------------------------------

                if ket_qua["diem_rui_ro"] is None:

                    print(
                        f"User {user_id}: "
                        f"CHƯA ĐỦ DỮ LIỆU "
                        f"(Quiz={du_lieu['quiz_attempts']}, "
                        f"Visits={du_lieu['learning_visits']})"
                    )

                else:

                    percentage = (
                        ket_qua["diem_rui_ro"] * 100
                    )

                    print(
                        f"User {user_id}: "
                        f"{percentage:.2f}% - "
                        f"{ket_qua['muc_canh_bao']} - "
                        f"{ket_qua['phien_ban_mo_hinh']}"
                    )

            except Exception as e:

                # Nếu một nhân viên lỗi thì rollback
                # bản ghi của nhân viên đó nhưng tiếp tục
                # xử lý các nhân viên khác.

                conn.rollback()

                print(
                    f"User {user_id}: LỖI - {e}"
                )

        print("Hoàn tất.")

    finally:

        conn.close()


# ===============================================================
# 10. MAIN
# ===============================================================

if __name__ == "__main__":
    chay_du_doan_cho_tat_ca_nhan_vien()