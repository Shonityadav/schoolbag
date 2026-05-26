<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$lessons = App\Models\Lesson::where('chapter_id', 2)->orderBy('order')->get(['id', 'title', 'order', 'chapter_id'])->toArray();
print_r($lessons);
