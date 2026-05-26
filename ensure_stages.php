<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$chapters = App\Models\Chapter::all();

foreach ($chapters as $chapter) {
    // We want 4 lessons per chapter (orders 0, 1, 2, 3)
    for ($i = 0; $i < 4; $i++) {
        App\Models\Lesson::firstOrCreate([
            'chapter_id' => $chapter->id,
            'order' => $i
        ], [
            'title' => "Stage " . ($i + 1) . " Mission",
            'type' => 'reading',
            'content' => 'Complete this mission to build your skills.',
            'duration_minutes' => 10,
            'xp_reward' => 20,
            'is_active' => true,
        ]);
    }
}
echo "Ensured all chapters have 4 stages.";
