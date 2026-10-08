<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiPaperGenerator extends Model
{
    protected $table = 'ai_paper_generators';

    protected $fillable = [
        'set_id', 'ebook_id', 'chapter', 'chapter_num', 'section',
        'ques_type_id', 'question', 'diagrams', 'options',
        'answer', 'answer_text', 'company_id'
    ];

    public function questionType()
    {
        return $this->belongsTo('App\Models\AiQuesType', 'ques_type_id');
    }
}

