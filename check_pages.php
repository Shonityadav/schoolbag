<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$ebook = \App\Models\Ebook::with('pages')->find(1118);
if (!$ebook) { echo "Ebook not found\n"; exit; }

echo json_encode($ebook->pages->take(3)->toArray(), JSON_PRETTY_PRINT);
