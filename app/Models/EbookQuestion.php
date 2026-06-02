<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EbookQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'ebook_id',
        'chapter_id',
        'stage_id',
        'ques_type_id',
        'question',
        'answer',
        'subject'
    ];
}
