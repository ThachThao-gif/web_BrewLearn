<?php

return [
    'risk_rules_version' => 'rules-v3',
    'risk_python' => env('BREWLEARN_RISK_PYTHON', base_path('venv/Scripts/python.exe')),
    'risk_script' => base_path('app/ml/ket_noi_db.py'),
    'risk_timeout' => 600,
    // Tự dời dữ liệu demo sang ngày hiện tại ở lần truy cập đầu tiên mỗi ngày (chỉ dùng cho bản demo).
    'demo_auto_shift' => (bool) env('BREWLEARN_DEMO_AUTO_SHIFT', false),
];
