<?php
$chapters = \App\Models\Chapter::where('course_id', 19)->where('id', '>', 4)->get();
foreach ($chapters as $ch) {
    $ch->order = $ch->order + 1;
    $ch->save();
}
echo "Orders shifted\n";
