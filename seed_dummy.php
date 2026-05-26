<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$course = App\Models\Course::find(19);
if($course) {
    $chapter = $course->chapters()->create(['title' => 'The Three Little Birds', 'order' => 0, 'unlock_threshold' => 0, 'xp_reward' => 50, 'is_active' => true]);
    $chapter->lessons()->createMany([
        ['title' => 'Reading Mission', 'description' => 'Read the story and answer the questions.', 'content' => '', 'order' => 0, 'xp_reward' => 20, 'is_active' => true],
        ['title' => 'Hard Words', 'description' => 'Learn new words and their meanings.', 'content' => '', 'order' => 1, 'xp_reward' => 15, 'is_active' => true],
        ['title' => 'Activity Mission', 'description' => 'Play fun activities to understand better.', 'content' => '', 'order' => 2, 'xp_reward' => 25, 'is_active' => true]
    ]);
    $chapter->quiz()->create(['title' => 'Chapter Boss', 'description' => 'Test your knowledge!', 'xp_reward' => 50, 'is_active' => true]);
    echo "Created dummy chapter and lessons\n";
} else {
    echo "Course not found\n";
}
