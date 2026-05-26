<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EbookChapterStage extends Model
{
    use HasFactory;
    protected $fillable = [
        'ebook_id', 'ebook_chapter_id', 'stage_number', 'stage_name', 'description'
    ];

    public function chapter()
    {
        return $this->belongsTo(EbookChapter::class, 'ebook_chapter_id');
    }
}
