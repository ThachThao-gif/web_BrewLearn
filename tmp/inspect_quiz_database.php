<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
try {
    $columns = DB::select('SHOW COLUMNS FROM ket_qua_kiem_tra');
    echo "RESULT_COLUMNS\n";
    foreach ($columns as $column) {
        echo json_encode(['name' => $column->Field, 'type' => $column->Type, 'nullable' => $column->Null, 'extra' => $column->Extra], JSON_UNESCAPED_UNICODE)."\n";
    }
    echo "RESULT_TIMESTAMP_COVERAGE\n";
    echo json_encode(DB::table('ket_qua_kiem_tra')->selectRaw('COUNT(*) as total, SUM(created_at IS NULL) as missing_created_at, MIN(created_at) as first_created_at, MAX(created_at) as last_created_at')->first(), JSON_UNESCAPED_UNICODE)."\n";
    echo "PROGRAM_COLUMNS\n";
    foreach (['chuong_trinh_dao_tao','bai_hoc','bai_kiem_tra'] as $table) {
        echo $table.': '.implode(', ', array_map(fn($c) => $c->Field, DB::select('SHOW COLUMNS FROM '.$table)))."\n";
    }
    echo "QUIZZES_PER_PROGRAM\n";
    $rows = DB::table('chuong_trinh_dao_tao as c')->leftJoin('bai_hoc as l','l.FK_MaCTDT','=','c.MaCTDT')->leftJoin('bai_kiem_tra as q','q.MaBaiHoc','=','l.MaBaiHoc')
        ->select('c.MaCTDT','c.TenCTDT','c.ChucVuApDung')->selectRaw('COUNT(DISTINCT l.MaBaiHoc) as lessons, COUNT(DISTINCT q.MaBaiKiemTra) as quizzes')->groupBy('c.MaCTDT','c.TenCTDT','c.ChucVuApDung')->orderBy('c.MaCTDT')->get();
    foreach ($rows as $row) echo json_encode($row, JSON_UNESCAPED_UNICODE)."\n";
    echo "QUIZZES_WITHOUT_PROGRAM\n";
    echo json_encode(DB::table('bai_kiem_tra as q')->leftJoin('bai_hoc as l','l.MaBaiHoc','=','q.MaBaiHoc')->leftJoin('chuong_trinh_dao_tao as c','c.MaCTDT','=','l.FK_MaCTDT')->whereNull('c.MaCTDT')->select('q.MaBaiKiemTra','q.TenBaiKiemTra','q.ChucVu')->get(),JSON_UNESCAPED_UNICODE)."\n";
    echo "PASS_THRESHOLDS\n";
    echo json_encode(DB::table('bai_kiem_tra')->select('DiemChuan')->selectRaw('COUNT(*) as quizzes')->groupBy('DiemChuan')->get(),JSON_UNESCAPED_UNICODE)."\n";
} catch (Throwable $e) {
    fwrite(STDERR, 'Read-only database inspection failed: '.get_class($e)."\n");
    exit(1);
}
