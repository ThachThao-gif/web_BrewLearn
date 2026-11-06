<?php

return [
    'risk_rules_version' => 'rules-v4',
    'risk_python' => env('BREWLEARN_RISK_PYTHON', base_path('venv/Scripts/python.exe')),
    'risk_script' => base_path('app/risk_rules/ket_noi_db.py'),
    'risk_timeout' => 600,
    // Tự dời dữ liệu demo sang ngày hiện tại ở lần truy cập đầu tiên mỗi ngày (chỉ dùng cho bản demo).
    'demo_auto_shift' => (bool) env('BREWLEARN_DEMO_AUTO_SHIFT', false),
    // Cố định "hôm nay" theo mốc dữ liệu giả lập (Y-m-d, giờ Việt Nam); giờ phút vẫn chạy theo đồng hồ thật. Để trống để dùng ngày thật.
    'demo_today' => env('BREWLEARN_DEMO_TODAY') ?: null,
];
