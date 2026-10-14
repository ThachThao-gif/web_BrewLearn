"""Default warning rules; normalized scores are NOT probabilities."""
import math

MODEL_NAME = "rules-v2"
MAX_SCORE = 8


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


def predict_risk(progress_percent, avg_quiz_score, quiz_attempts, failed_rate, learning_visits):
    """Keep legacy keyword arguments; distinct-quiz eligibility is checked upstream."""
    progress = bounded_number(progress_percent, "progress_percent")
    average = bounded_number(avg_quiz_score, "avg_quiz_score")
    failed = bounded_number(failed_rate, "failed_rate", upper=1)
    reasons = []
    quiz_score = 3 if average < 60 else 2 if average < 80 else 0
    if quiz_score:
        reasons.append("Điểm quiz trung bình dưới 60" if average < 60 else "Điểm quiz trung bình từ 60 đến dưới 80")
    failed_score = 3 if failed >= 0.5 else 1 if failed > 0 else 0
    if failed_score:
        reasons.append("Có ít nhất 50% số bài đã làm chưa đạt" if failed >= 0.5 else "Có bài kiểm tra chưa đạt, chiếm dưới 50% số bài đã làm")
    progress_score = 2 if progress < 50 else 1 if progress < 80 else 0
    if progress_score:
        reasons.append("Tiến độ đào tạo dưới 50%" if progress < 50 else "Tiến độ đào tạo từ 50% đến dưới 80%")
    total = quiz_score + failed_score + progress_score
    score = total / MAX_SCORE
    return {
        "probability": score,
        "percentage": round(score * 100, 1),
        "risk_level": "Cao" if total >= 5 else "Trung bình" if total >= 3 else "Thấp",
        "model_name": MODEL_NAME,
        "details": {
            "tong_diem": total,
            "diem_thanh_phan": {"quiz_tb": quiz_score, "quiz_chua_dat": failed_score, "tien_do": progress_score},
            "ly_do_canh_bao": reasons,
        },
    }
