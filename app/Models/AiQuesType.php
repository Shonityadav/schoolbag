<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiQuesType extends Model
{
    protected $table = 'ai_ques_types';

    protected $fillable = ['name', 'marks', 'description', 'company_id', 'status'];
}

