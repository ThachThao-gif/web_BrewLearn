import numpy as np
import pandas as pd
import joblib

from sklearn.model_selection import train_test_split
from sklearn.preprocessing import StandardScaler
from sklearn.linear_model import LogisticRegression
from sklearn.tree import DecisionTreeClassifier
from sklearn.ensemble import RandomForestClassifier

from sklearn.metrics import (
    accuracy_score,
    precision_score,
    recall_score,
    f1_score,
    confusion_matrix
)


# =====================================================
# 1. CẤU HÌNH
# =====================================================

np.random.seed(42)

N = 1000

FEATURES = [
    "progress_percent",
    "avg_quiz_score",
    "quiz_attempts",
    "failed_rate",
    "learning_visits"
]


# =====================================================
# 2. TẠO DỮ LIỆU GIẢ LẬP
# =====================================================

# Tiến độ đào tạo: 0 - 100%
progress_percent = np.random.uniform(
    0,
    100,
    N
)

# Điểm Quiz trung bình: 0 - 100
avg_quiz_score = np.random.uniform(
    0,
    100,
    N
)

# Tổng số lần làm Quiz
quiz_attempts = np.random.randint(
    1,
    9,
    N
)

# Số lần Quiz không đạt (< 80)
failed_attempts = np.array([
    np.random.randint(
        0,
        attempts + 1
    )
    for attempts in quiz_attempts
])

# Tỷ lệ Quiz không đạt
failed_rate = (
    failed_attempts
    / quiz_attempts
)

# Số lượt học
learning_visits = np.random.randint(
    1,
    41,
    N
)


# =====================================================
# 3. TẠO NHÃN GIẢ LẬP COMPETENCY_STATUS
# =====================================================
#
# Đây chỉ là quy tắc sinh dữ liệu giả lập.
# KHÔNG phải mô hình dự đoán cuối cùng.
#
# Nguy cơ tăng khi:
# - Tiến độ đào tạo thấp
# - Điểm Quiz thấp
# - Tỷ lệ Quiz không đạt cao
# - Số lượt học thấp
#
# Thêm nhiễu để dữ liệu không hoàn hảo.

risk_score = (
    (100 - progress_percent) * 0.025
    + (100 - avg_quiz_score) * 0.030
    + failed_rate * 2.0
    - learning_visits * 0.025
)

noise = np.random.normal(
    0,
    0.5,
    N
)

risk_score = (
    risk_score
    + noise
)

# Chuyển risk_score thành xác suất
probability = (
    1
    / (
        1
        + np.exp(
            -(risk_score - 3.0)
        )
    )
)

# 0 = Đạt chuẩn
# 1 = Chưa đạt chuẩn
competency_status = np.random.binomial(
    1,
    probability
)


# =====================================================
# 4. TẠO DATAFRAME
# =====================================================

df = pd.DataFrame({
    "progress_percent":
        progress_percent.round(2),

    "avg_quiz_score":
        avg_quiz_score.round(2),

    "quiz_attempts":
        quiz_attempts,

    "failed_rate":
        failed_rate.round(4),

    "learning_visits":
        learning_visits,

    "competency_status":
        competency_status
})


print(
    "\n===== 5 DÒNG DỮ LIỆU ĐẦU ====="
)

print(
    df.head()
)


# =====================================================
# 5. KIỂM TRA PHÂN BỐ NHÃN
# =====================================================

print(
    "\n===== PHÂN BỐ NHÃN ====="
)

print(
    df[
        "competency_status"
    ].value_counts()
)


print(
    "\n===== TỶ LỆ NHÃN ====="
)

print(
    df[
        "competency_status"
    ]
    .value_counts(
        normalize=True
    )
    .mul(100)
    .round(2)
)


# Lưu dữ liệu giả lập
df.to_csv(
    "app/ml/brewlearn_dataset.csv",
    index=False
)


# =====================================================
# 6. TÁCH FEATURES X VÀ TARGET y
# =====================================================

X = df[
    FEATURES
]

y = df[
    "competency_status"
]


# =====================================================
# 7. CHIA TRAIN / TEST
# =====================================================

X_train, X_test, y_train, y_test = (
    train_test_split(
        X,
        y,
        test_size=0.20,
        random_state=42,
        stratify=y
    )
)


print(
    "\n===== KÍCH THƯỚC DỮ LIỆU ====="
)

print(
    "Training:",
    len(X_train)
)

print(
    "Testing :",
    len(X_test)
)


# =====================================================
# 8. CHUẨN HÓA CHO LOGISTIC REGRESSION
# =====================================================

scaler = StandardScaler()

X_train_scaled = scaler.fit_transform(
    X_train
)

X_test_scaled = scaler.transform(
    X_test
)


# =====================================================
# 9. KHỞI TẠO 3 MÔ HÌNH
# =====================================================

models = {

    "Logistic Regression":
        LogisticRegression(
            random_state=42,
            max_iter=1000
        ),

    "Decision Tree":
        DecisionTreeClassifier(
            random_state=42,
            max_depth=5
        ),

    "Random Forest":
        RandomForestClassifier(
            n_estimators=100,
            random_state=42,
            max_depth=6
        )
}


# =====================================================
# 10. HUẤN LUYỆN + KIỂM THỬ
# =====================================================

results = {}

for name, model in models.items():

    # ---------------------------------------------
    # Logistic Regression cần dữ liệu đã scale
    # ---------------------------------------------

    if name == "Logistic Regression":

        model.fit(
            X_train_scaled,
            y_train
        )

        y_pred = model.predict(
            X_test_scaled
        )

    # ---------------------------------------------
    # Decision Tree / Random Forest không cần scale
    # ---------------------------------------------

    else:

        model.fit(
            X_train,
            y_train
        )

        y_pred = model.predict(
            X_test
        )


    # ---------------------------------------------
    # Tính metrics
    # ---------------------------------------------

    accuracy = accuracy_score(
        y_test,
        y_pred
    )

    precision = precision_score(
        y_test,
        y_pred,
        zero_division=0
    )

    recall = recall_score(
        y_test,
        y_pred,
        zero_division=0
    )

    f1 = f1_score(
        y_test,
        y_pred,
        zero_division=0
    )

    cm = confusion_matrix(
        y_test,
        y_pred
    )


    results[name] = {

        "Accuracy":
            accuracy,

        "Precision":
            precision,

        "Recall":
            recall,

        "F1-score":
            f1
    }


    # ---------------------------------------------
    # In kết quả từng model
    # ---------------------------------------------

    print(
        "\n================================"
    )

    print(
        name
    )

    print(
        "================================"
    )

    print(
        f"Accuracy : {accuracy:.4f}"
    )

    print(
        f"Precision: {precision:.4f}"
    )

    print(
        f"Recall   : {recall:.4f}"
    )

    print(
        f"F1-score : {f1:.4f}"
    )

    print(
        "\nConfusion Matrix:"
    )

    print(
        cm
    )


# =====================================================
# 11. TẠO BẢNG SO SÁNH MODEL
# =====================================================

result_df = pd.DataFrame(
    results
).T


print(
    "\n================================"
)

print(
    "SO SÁNH CÁC MÔ HÌNH"
)

print(
    "================================"
)

print(
    result_df
)


# Lưu bảng kết quả
result_df.to_csv(
    "app/ml/model_results.csv"
)


# =====================================================
# 12. TỰ ĐỘNG CHỌN MODEL TỐT NHẤT
# =====================================================
#
# Prototype BrewLearn sử dụng F1-score
# làm tiêu chí lựa chọn model.
#
# Không hard-code Logistic Regression.
# Nếu dữ liệu thay đổi và model khác có F1 cao hơn,
# model đó sẽ tự động được lựa chọn.

best_model_name = (
    result_df[
        "F1-score"
    ].idxmax()
)

best_model = models[
    best_model_name
]

best_f1 = result_df.loc[
    best_model_name,
    "F1-score"
]


print(
    "\n================================"
)

print(
    "MÔ HÌNH ĐƯỢC LỰA CHỌN"
)

print(
    "================================"
)

print(
    f"Model    : {best_model_name}"
)

print(
    f"F1-score : {best_f1:.4f}"
)


# =====================================================
# 13. XÁC ĐỊNH MODEL CÓ CẦN SCALER
# =====================================================

use_scaler = (
    best_model_name
    == "Logistic Regression"
)


# =====================================================
# 14. LƯU MODEL TỐT NHẤT
# =====================================================

joblib.dump(
    best_model,
    "app/ml/best_model.pkl"
)


# =====================================================
# 15. LƯU THÔNG TIN MODEL
# =====================================================

model_info = {

    "model_name":
        best_model_name,

    "selection_metric":
        "F1-score",

    "f1_score":
        float(best_f1),

    "use_scaler":
        use_scaler,

    "features":
        FEATURES
}


joblib.dump(
    model_info,
    "app/ml/model_info.pkl"
)


# =====================================================
# 16. LƯU SCALER NẾU MODEL CẦN
# =====================================================

if use_scaler:

    joblib.dump(
        scaler,
        "app/ml/scaler.pkl"
    )


# =====================================================
# 17. HIỂN THỊ KẾT QUẢ CUỐI
# =====================================================

print(
    "\n===== ĐÃ LƯU MODEL ====="
)

print(
    "Model:",
    "app/ml/best_model.pkl"
)

print(
    "Thông tin model:",
    "app/ml/model_info.pkl"
)

if use_scaler:

    print(
        "Scaler:",
        "app/ml/scaler.pkl"
    )

else:

    print(
        "Scaler: Không cần sử dụng"
    )


print(
    "\n===== MODEL INFO ====="
)

print(
    model_info
)