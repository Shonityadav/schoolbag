<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EbookChapter extends Model
{
    use HasFactory;

    protected $fillable = [
        'ebook_id', 'chapter_number', 'chapter_name', 'start_page', 'end_page', 'index_page', 'total_stages'
    ];

    public function stages()
    {
        return $this->hasMany(EbookChapterStage::class)->orderBy('stage_number');
    }
}
