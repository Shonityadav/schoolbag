<?php
$chapters = \App\Models\Chapter::where('course_id', 1)->whereIn('title', ['Action Words', 'Colors & Shapes', 'Basic Numbers'])->get();
foreach ($chapters as $i => $ch) {
    $newCh = $ch->replicate();
    $newCh->course_id = 19;
    $newCh->order = $i + 1;
    $newCh->save();
}
echo "Done\n";
