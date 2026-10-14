<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$manager = App\Models\User::where('ChucVu', 'Quản lý cửa hàng')->whereNotNull('cua_hang_id')->firstOrFail();
Illuminate\Support\Facades\Auth::setUser($manager);
foreach (['/manager/staff', '/manager/assessments'] as $path) {
    $response = $kernel->handle(Illuminate\Http\Request::create($path));
    echo $path.' status='.$response->getStatusCode().' rules_label='.(str_contains($response->getContent(), 'Cảnh báo') ? 'yes' : 'no').PHP_EOL;
}
echo 'version='.config('brewlearn.risk_rules_version').PHP_EOL;
foreach (Illuminate\Support\Facades\DB::table('du_doan_nguy_co')->where('phien_ban_mo_hinh', 'rules-v1')->select('muc_canh_bao')->selectRaw('COUNT(*) as total')->groupBy('muc_canh_bao')->get() as $row) {
    echo json_encode($row, JSON_UNESCAPED_UNICODE).PHP_EOL;
}
