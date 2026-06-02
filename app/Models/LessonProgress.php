<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'chapter_id', 'completed', 'completed_at', 'answers', 'score', 'time_taken',
        'ebook_id', 'publication_name', 'subject', 'standard', 'stage_number', 'stage_attempt_number'
    ];

    protected $casts = ['completed' => 'boolean', 'completed_at' => 'datetime', 'answers' => 'array'];

    public function user()    { return $this->belongsTo(User::class); }
    public function chapter() { return $this->belongsTo(EbookChapter::class, 'chapter_id'); }
}
