<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$chapters = App\Models\Chapter::where('course_id', 19)->get();
foreach($chapters as $c) {
    if ($c->lessons()->count() == 0) {
        $c->delete();
        echo "Deleted empty chapter ID: " . $c->id . "\n";
    }
}
