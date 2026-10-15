<?php

return [
    'risk_rules_version' => 'rules-v2',
    'risk_python' => env('BREWLEARN_RISK_PYTHON', base_path('venv/Scripts/python.exe')),
    'risk_script' => base_path('app/ml/ket_noi_db.py'),
    'risk_timeout' => 600,
];
