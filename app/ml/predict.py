import os
import joblib
import pandas as pd


# =====================================================
# 1. ĐƯỜNG DẪN FILE
# =====================================================

BASE_DIR = os.path.dirname(
    os.path.abspath(__file__)
)

MODEL_PATH = os.path.join(
    BASE_DIR,
    "best_model.pkl"
)

INFO_PATH = os.path.join(
    BASE_DIR,
    "model_info.pkl"
)

SCALER_PATH = os.path.join(
    BASE_DIR,
    "scaler.pkl"
)


# =====================================================
# 2. LOAD MODEL VÀ THÔNG TIN MODEL
# =====================================================

model = joblib.load(
    MODEL_PATH
)

model_info = joblib.load(
    INFO_PATH
)

FEATURES = model_info[
    "features"
]


# =====================================================
# 3. LOAD SCALER NẾU MODEL CẦN
# =====================================================

scaler = None

if model_info["use_scaler"]:

    scaler = joblib.load(
        SCALER_PATH
    )


# =====================================================
# 4. HÀM DỰ ĐOÁN NGUY CƠ
# =====================================================

def predict_risk(
    progress_percent,
    avg_quiz_score,
    quiz_attempts,
    failed_rate,
    learning_visits
):

    # Tạo dữ liệu đúng thứ tự feature
    data = pd.DataFrame(
        [[
            progress_percent,
            avg_quiz_score,
            quiz_attempts,
            failed_rate,
            learning_visits
        ]],
        columns=FEATURES
    )


    # Nếu model cần chuẩn hóa
    if model_info["use_scaler"]:

        model_input = scaler.transform(
            data
        )

    else:

        model_input = data


    # Xác suất thuộc lớp 1:
    # 1 = Chưa đạt chuẩn năng lực
    probability = model.predict_proba(
        model_input
    )[0][1]


    # Chuyển sang %
    percentage = (
        probability * 100
    )


    # =================================================
    # PHÂN LOẠI MỨC NGUY CƠ
    # =================================================

    if probability < 0.30:

        risk_level = "Thấp"

    elif probability < 0.60:

        risk_level = "Trung bình"

    else:

        risk_level = "Cao"


    # =================================================
    # TRẢ KẾT QUẢ
    # =================================================

    return {

        "probability":
            round(
                float(probability),
                4
            ),

        "percentage":
            round(
                float(percentage),
                2
            ),

        "risk_level":
            risk_level,

        "model_name":
            model_info["model_name"]
    }


# =====================================================
# 5. TEST MODEL
# =====================================================

if __name__ == "__main__":

    test_cases = [

        {
            "name": "Học rất tốt",
            "progress_percent": 100,
            "avg_quiz_score": 92,
            "quiz_attempts": 2,
            "failed_rate": 0.00,
            "learning_visits": 30
        },

        {
            "name": "Học khá",
            "progress_percent": 75,
            "avg_quiz_score": 78,
            "quiz_attempts": 3,
            "failed_rate": 0.33,
            "learning_visits": 15
        },

        {
            "name": "Cần theo dõi",
            "progress_percent": 65,
            "avg_quiz_score": 68,
            "quiz_attempts": 4,
            "failed_rate": 0.50,
            "learning_visits": 12
        },

        {
            "name": "Nguy cơ cao",
            "progress_percent": 45,
            "avg_quiz_score": 55,
            "quiz_attempts": 6,
            "failed_rate": 0.67,
            "learning_visits": 8
        }
    ]


    print(
        "\n================================"
    )

    print(
        "MODEL ĐANG SỬ DỤNG:",
        model_info["model_name"]
    )

    print(
        "================================"
    )


    for case in test_cases:

        result = predict_risk(
            case["progress_percent"],
            case["avg_quiz_score"],
            case["quiz_attempts"],
            case["failed_rate"],
            case["learning_visits"]
        )

        print(
            f"\n--- {case['name']} ---"
        )

        print(
            f"Xác suất nguy cơ : "
            f"{result['percentage']}%"
        )

        print(
            f"Mức nguy cơ      : "
            f"{result['risk_level']}"
        )