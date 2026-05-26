<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$chapter = App\Models\Chapter::find(11);
if ($chapter) {
    $lesson = App\Models\Lesson::firstOrCreate([
        'chapter_id' => 11,
        'order' => 3
    ], [
        'title' => 'Exercise Mission',
        'type' => 'reading',
        'content' => 'Complete the exercises to build your skills.',
        'duration_minutes' => 10,
        'xp_reward' => 20,
        'is_active' => true,
    ]);
    echo "Added lesson ID: " . $lesson->id;
} else {
    echo "Chapter not found";
}
