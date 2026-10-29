"""Default warning rules; normalized scores are NOT probabilities."""
import math

RULES_VERSION = "rules-v4"
MAX_SCORE = 10


def bounded_number(value, name, lower=0, upper=100):
    if isinstance(value, bool):
        raise ValueError(f"{name}: invalid number")
    try:
        number = float(value)
    except (TypeError, ValueError, OverflowError) as exc:
        raise ValueError(f"{name}: invalid number") from exc
    if not math.isfinite(number) or not lower <= number <= upper:
        raise ValueError(f"{name}: outside [{lower}, {upper}]")
    return number


def predict_risk(progress_percent, avg_quiz_score, quiz_attempts, failed_rate, learning_visits,
                 latest_assessment_failed=False, recent_error_count=0, recent_serious_error_count=0):
    """Keep legacy keyword arguments; distinct-quiz eligibility is checked upstream."""
    progress = bounded_number(progress_percent, "progress_percent")
    average = bounded_number(avg_quiz_score, "avg_quiz_score")
    failed = bounded_number(failed_rate, "failed_rate", upper=1)
    reasons = []
    # S1 và S2 cùng phản ánh mức nắm kiến thức: chỉ lấy dấu hiệu mạnh hơn để không tính trùng.
    quiz_score = 3 if average < 60 else 2 if average < 80 else 0
    failed_score = 3 if failed >= 0.5 else 1 if failed > 0 else 0
    knowledge_score = max(quiz_score, failed_score)
    if knowledge_score and quiz_score == knowledge_score:
        reasons.append("Điểm quiz trung bình dưới 60" if average < 60 else "Điểm quiz trung bình từ 60 đến dưới 80")
    if knowledge_score and failed_score == knowledge_score:
        reasons.append("Có ít nhất 50% số bài đã làm chưa đạt" if failed >= 0.5 else "Có bài kiểm tra chưa đạt, chiếm dưới 50% số bài đã làm")
    progress_score = 2 if progress < 50 else 1 if progress < 80 else 0
    if progress_score:
        reasons.append("Tiến độ đào tạo dưới 50%" if progress < 50 else "Tiến độ đào tạo từ 50% đến dưới 80%")
    assessment_score = 3 if latest_assessment_failed else 0
    if assessment_score:
        reasons.append("Kết quả đánh giá năng lực gần nhất được duyệt là Chưa đạt")
    errors = int(bounded_number(recent_error_count, "recent_error_count", upper=1000))
    serious = int(bounded_number(recent_serious_error_count, "recent_serious_error_count", upper=errors))
    error_score = 2 if errors >= 2 or serious else 1 if errors == 1 else 0
    if error_score:
        reasons.append(f"Có {errors} lỗi nghiệp vụ trong 30 ngày gần nhất"
                       + (f", trong đó {serious} lỗi nghiêm trọng" if serious else ""))
    total = knowledge_score + progress_score + assessment_score + error_score
    score = total / MAX_SCORE
    return {
        "normalized_score": score,
        "percentage": round(score * 100, 1),
        "risk_level": "Cao" if total >= 5 else "Trung bình" if total >= 3 else "Thấp",
        "rules_version": RULES_VERSION,
        "details": {
            "tong_diem": total,
            "diem_thanh_phan": {"kiem_tra": knowledge_score, "quiz_tb": quiz_score, "quiz_chua_dat": failed_score,
                                "tien_do": progress_score,
                                "danh_gia_gan_nhat": assessment_score, "loi_nghiep_vu": error_score},
            "ly_do_canh_bao": reasons,
        },
    }
