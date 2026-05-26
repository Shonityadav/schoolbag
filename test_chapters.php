<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::find(1);
$course = App\Models\Course::where('class_id', $user->class_id)->first();
$chapters = $course->chapters()->with('lessons')->get()->toArray();
print_r(array_map(function($c) {
    return [
        'id' => $c['id'],
        'title' => $c['title'],
        'order' => $c['order'],
        'lessons' => array_map(function($l) { return ['id' => $l['id'], 'title' => $l['title'], 'order' => $l['order']]; }, $c['lessons'])
    ];
}, $chapters));
