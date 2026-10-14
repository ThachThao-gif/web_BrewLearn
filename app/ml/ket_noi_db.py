"""Append rule warning history: python app/ml/ket_noi_db.py.

Training scripts and ML artifacts remain separate from the default rules.
"""
import json
import logging
import os
from pathlib import Path

from predict import MODEL_NAME, bounded_number, predict_risk

logger = logging.getLogger(__name__)
ELIGIBLE_POSITIONS = ("barista", "thu ngân", "phục vụ")


def summarize_quizzes(tests, results):
    """Consume cutoff-filtered rows sorted by created_at DESC, id DESC.

    Validate newest rows before grouping; never replace an invalid latest row
    with an earlier pass. Missing timestamps cannot be ordered safely.
    """
    standards = {row["MaBaiKiemTra"]: bounded_number(
        80 if row["DiemChuan"] is None else row["DiemChuan"], "DiemChuan"
    ) for row in tests}
    latest = {}
    for row in results:
        test_id = row["MaBaiKiemTra"]
        if test_id not in standards:
            continue
        if row["created_at"] is None:
            raise ValueError("Quiz result has no created_at")
        if test_id not in latest:
            latest[test_id] = {
                "result_id": row["id"], "test_id": test_id,
                "score": bounded_number(row["Diem"], "Diem"),
                "pass_score": standards[test_id],
                "created_at": str(row["created_at"]),
            }
    count = len(latest)
    failed = sum(row["score"] < row["pass_score"] for row in latest.values())
    return {
        "avg_quiz_score": sum(row["score"] for row in latest.values()) / count if count else None,
        "failed_rate": failed / count if count else None,
        "failed_quiz_count": failed,
        "completed_quiz_count": count,
        "applicable_quiz_count": len(standards),
        "quiz_attempts": sum(row["MaBaiKiemTra"] in standards for row in results),
        "selected_results": list(latest.values()),
    }


def lay_danh_sach_nhan_vien(conn):
    with conn.cursor() as cur:
        slots = ",".join(["%s"] * len(ELIGIBLE_POSITIONS))
        cur.execute(f"""
            SELECT id, cua_hang_id, ChucVu FROM users
            WHERE LOWER(TRIM(COALESCE(ChucVu, ''))) IN ({slots})
            AND employment_status = 'DangLam' AND cua_hang_id IS NOT NULL
        """, ELIGIBLE_POSITIONS)
        return cur.fetchall()


def lay_du_lieu_du_doan(conn, user_id, calculated_at):
    with conn.cursor() as cur:
        cur.execute("""
            SELECT c.MaCTDT, COALESCE(n.TienDo, 0) AS progress
            FROM users u JOIN chuong_trinh_dao_tao c ON (
                UPPER(TRIM(c.ChucVuApDung)) = UPPER(TRIM(COALESCE(u.ChucVu, '')))
                OR UPPER(TRIM(c.ChucVuApDung)) IN ('ALL STAFF', 'ALL', 'TẤT CẢ'))
            LEFT JOIN nhan_vien_khoa_hoc n ON n.user_id = u.id AND n.MaCTDT = c.MaCTDT
            WHERE u.id = %s
        """, (user_id,))
        courses = cur.fetchall()
        progresses = [bounded_number(row["progress"], "TienDo") for row in courses]
        course_ids = list(dict.fromkeys(row["MaCTDT"] for row in courses))
        if len(course_ids) != len(courses):
            raise ValueError("Duplicate course progress")
        tests, results = [], []
        visits = 0
        if course_ids:
            slots = ",".join(["%s"] * len(course_ids))
            cur.execute(f"""
                SELECT q.MaBaiKiemTra, q.DiemChuan
                FROM bai_kiem_tra q JOIN bai_hoc l ON l.MaBaiHoc = q.MaBaiHoc
                WHERE l.FK_MaCTDT IN ({slots})
            """, tuple(course_ids))
            tests = cur.fetchall()
            if tests:
                test_ids = [row["MaBaiKiemTra"] for row in tests]
                test_slots = ",".join(["%s"] * len(test_ids))
                cur.execute(f"""
                    SELECT id, MaBaiKiemTra, Diem, created_at
                    FROM ket_qua_kiem_tra
                    WHERE user_id = %s AND MaBaiKiemTra IN ({test_slots})
                    AND (created_at <= %s OR created_at IS NULL)
                    ORDER BY created_at DESC, id DESC
                """, (user_id, *test_ids, calculated_at))
                results = cur.fetchall()
            cur.execute(f"""
                SELECT COALESCE(SUM(p.visit_count), 0) AS visits
                FROM user_lesson_progresses p JOIN bai_hoc l ON l.MaBaiHoc = p.lesson_id
                WHERE p.user_id = %s AND l.FK_MaCTDT IN ({slots})
            """, (user_id, *course_ids))
            visits = int(cur.fetchone()["visits"] or 0)
    quiz = summarize_quizzes(tests, results)
    missing = []
    if not courses:
        missing.append("Không có chương trình đào tạo áp dụng")
    if quiz["completed_quiz_count"] < 2:
        missing.append("Cần kết quả của ít nhất hai bài kiểm tra khác nhau")
    return {
        "progress_percent": sum(progresses) / len(progresses) if progresses else 0,
        **quiz,
        "learning_visits": visits,
        "applicable_course_count": len(courses),
        "co_du_lieu_quiz": quiz["completed_quiz_count"] > 0,
        "du_dieu_kien_du_doan": not missing,
        "missing_reasons": missing,
        "calculated_at": str(calculated_at),
    }


def du_doan_nhan_vien(du_lieu):
    if not du_lieu["du_dieu_kien_du_doan"]:
        return {"diem_rui_ro": None, "muc_canh_bao": "ChuaDuDuLieu",
                "chi_tiet_yeu_to": du_lieu, "phien_ban_mo_hinh": MODEL_NAME}
    result = predict_risk(**{key: du_lieu[key] for key in (
        "progress_percent", "avg_quiz_score", "quiz_attempts", "failed_rate", "learning_visits")})
    return {
        "diem_rui_ro": result["probability"],
        "muc_canh_bao": {"Thấp": "Thap", "Trung bình": "TrungBinh", "Cao": "Cao"}[result["risk_level"]],
        "chi_tiet_yeu_to": {**du_lieu, "details": result["details"]},
        "phien_ban_mo_hinh": MODEL_NAME,
    }


def ghi_ket_qua_du_doan(conn, user_id, cua_hang_id, ket_qua, calculated_at):
    with conn.cursor() as cur:
        cur.execute("""
            INSERT INTO du_doan_nguy_co
            (user_id, cua_hang_id, diem_rui_ro, muc_canh_bao, chi_tiet_yeu_to, phien_ban_mo_hinh, ngay_du_doan)
            VALUES (%s, %s, %s, %s, %s, %s, %s)
        """, (user_id, cua_hang_id, ket_qua["diem_rui_ro"], ket_qua["muc_canh_bao"],
              json.dumps(ket_qua["chi_tiet_yeu_to"], ensure_ascii=False, allow_nan=False),
              ket_qua["phien_ban_mo_hinh"], calculated_at))


def chay_du_doan_cho_tat_ca_nhan_vien():
    # Database packages are not required to import/test pure rules.
    import pymysql
    from dotenv import load_dotenv
    load_dotenv(Path(__file__).resolve().parents[2] / ".env", override=True)
    conn = pymysql.connect(host=os.getenv("DB_HOST", "127.0.0.1"),
        port=int(os.getenv("DB_PORT", 3306)), user=os.getenv("DB_USERNAME", "root"),
        password=os.getenv("DB_PASSWORD", ""), database=os.getenv("DB_DATABASE", "brewlearn"),
        charset="utf8mb4", cursorclass=pymysql.cursors.DictCursor)
    counts = {"calculated": 0, "insufficient": 0, "errors": 0}
    try:
        employees = lay_danh_sach_nhan_vien(conn)
        conn.rollback()
        for employee in employees:
            try:
                with conn.cursor() as cur:
                    cur.execute("SET TRANSACTION ISOLATION LEVEL REPEATABLE READ")
                    cur.execute("START TRANSACTION WITH CONSISTENT SNAPSHOT")
                    # Laravel config/app.php uses UTC and writes naive UTC
                    # values through the default MySQL session. Use the same
                    # wall-clock convention without reinterpreting old rows.
                    cur.execute("SELECT UTC_TIMESTAMP() AS calculated_at")
                    calculated_at = cur.fetchone()["calculated_at"]
                    slots = ",".join(["%s"] * len(ELIGIBLE_POSITIONS))
                    cur.execute(f"SELECT id FROM users WHERE id = %s AND employment_status = 'DangLam' AND cua_hang_id = %s AND LOWER(TRIM(COALESCE(ChucVu, ''))) IN ({slots})",
                                (employee["id"], employee["cua_hang_id"], *ELIGIBLE_POSITIONS))
                    if not cur.fetchone():
                        raise ValueError("Employee no longer eligible")
                data = lay_du_lieu_du_doan(conn, employee["id"], calculated_at)
                result = du_doan_nhan_vien(data)
                ghi_ket_qua_du_doan(conn, employee["id"], employee["cua_hang_id"], result, calculated_at)
                conn.commit()
                key = "insufficient" if result["diem_rui_ro"] is None else "calculated"
                counts[key] += 1
                score = "—" if result["diem_rui_ro"] is None else f"{result['diem_rui_ro'] * 100:.1f}/100"
                print(f"User {employee['id']}: {result['muc_canh_bao']} · Điểm {score} · {MODEL_NAME}")
            except Exception:
                conn.rollback()
                counts["errors"] += 1
                logger.exception("Không tạo cảnh báo cho user_id=%s", employee["id"])
        print(json.dumps(counts, ensure_ascii=False))
        return counts
    finally:
        conn.close()


if __name__ == "__main__":
    logging.basicConfig(level=logging.INFO)
    summary = chay_du_doan_cho_tat_ca_nhan_vien()
    raise SystemExit(1 if summary["errors"] else 0)
