<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$db = Illuminate\Support\Facades\DB::connection();
$name = $db->getDatabaseName();
$cols = $db->select('SELECT TABLE_NAME, COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_KEY FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? ORDER BY TABLE_NAME, ORDINAL_POSITION', [$name]);
$group=[];
foreach ($cols as $c) { $group[$c->TABLE_NAME][] = $c->COLUMN_NAME.($c->COLUMN_KEY === 'PRI' ? '[PK]' : ''); }
foreach($group as $table=>$fields) { echo $table.' ('.implode(', ', $fields).")\n"; }
echo "\nFOREIGN KEYS\n";
foreach($db->select('SELECT TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = ? AND REFERENCED_TABLE_NAME IS NOT NULL', [$name]) as $f) { echo "$f->TABLE_NAME.$f->COLUMN_NAME -> $f->REFERENCED_TABLE_NAME.$f->REFERENCED_COLUMN_NAME\n"; }
