<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
echo 'app_timezone='.config('app.timezone').' php_now='.now()->format('Y-m-d H:i:s P').PHP_EOL;
echo 'app_url='.config('app.url').PHP_EOL;
DB::beginTransaction();
try {
    $latest = DB::table('ket_qua_kiem_tra')->first();
    $submitted = now()->format('Y-m-d H:i:s');
    $probeId = DB::table('ket_qua_kiem_tra')->insertGetId([
        'user_id' => $latest->user_id, 'MaBaiKiemTra' => $latest->MaBaiKiemTra,
        'Diem' => 79, 'TrangThai' => 'KhongDat', 'created_at' => $submitted, 'updated_at' => $submitted,
    ]);
    echo 'rollback_timestamp_probe='.json_encode(DB::selectOne('SELECT created_at, UTC_TIMESTAMP() AS calculated_at FROM ket_qua_kiem_tra WHERE id = ?', [$probeId])).PHP_EOL;
} finally {
    DB::rollBack();
}
foreach ([
    'roles' => 'SELECT ChucVu, employment_status, COUNT(*) AS total FROM users GROUP BY ChucVu, employment_status',
    'duplicate_progress' => 'SELECT user_id, MaCTDT, COUNT(*) AS total FROM nhan_vien_khoa_hoc GROUP BY user_id, MaCTDT HAVING COUNT(*) > 1',
    'clock' => 'SELECT NOW() AS mysql_now, UTC_TIMESTAMP() AS utc_now, @@session.time_zone AS session_zone, @@global.time_zone AS global_zone, @@system_time_zone AS system_zone',
    'result_status' => 'SELECT TrangThai, COUNT(*) AS total, SUM(Diem IS NULL) AS null_scores, SUM(created_at IS NULL) AS null_times FROM ket_qua_kiem_tra GROUP BY TrangThai',
    'latest_result' => 'SELECT id, Diem, TrangThai, created_at FROM ket_qua_kiem_tra ORDER BY created_at DESC, id DESC LIMIT 1',
    'progress_keys' => 'SHOW INDEX FROM nhan_vien_khoa_hoc',
] as $name => $sql) {
    echo $name.'='.json_encode(DB::select($sql), JSON_UNESCAPED_UNICODE).PHP_EOL;
}
