<?php
require __DIR__.'/../vendor/autoload.php';
$app=require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$db=Illuminate\Support\Facades\DB::connection();$name=$db->getDatabaseName();
$tables=['users','vung','cua_hang','ca_lam_viec','chuong_trinh_dao_tao','bai_hoc','nhan_vien_khoa_hoc','user_lesson_progresses','bai_kiem_tra','cau_hoi_kiem_tra','ket_qua_kiem_tra','danh_gia_nang_luc','loi_nghiep_vu','du_doan_nguy_co','yeu_cau_dao_tao_lai','retraining_rounds','notifications','yeu_cau_ho_tro'];
$result=[];
foreach($tables as $t){$result[$t]=['columns'=>$db->select('SELECT COLUMN_NAME AS name, COLUMN_TYPE AS type, IS_NULLABLE AS nullable, COLUMN_KEY AS colkey FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=? AND TABLE_NAME=? ORDER BY ORDINAL_POSITION',[$name,$t]),'foreign_keys'=>$db->select('SELECT COLUMN_NAME AS col, REFERENCED_TABLE_NAME AS target, REFERENCED_COLUMN_NAME AS target_col FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA=? AND TABLE_NAME=? AND REFERENCED_TABLE_NAME IS NOT NULL',[$name,$t]),'indexes'=>$db->select('SELECT INDEX_NAME AS name, NON_UNIQUE AS non_unique, COLUMN_NAME AS col, SEQ_IN_INDEX AS seq FROM information_schema.STATISTICS WHERE TABLE_SCHEMA=? AND TABLE_NAME=? ORDER BY INDEX_NAME,SEQ_IN_INDEX',[$name,$t])];}
file_put_contents(__DIR__.'/erd-schema.json',json_encode($result,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
echo 'Exported metadata for '.count($result).' tables. No employee records exported.';
