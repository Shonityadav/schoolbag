<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id', 'question', 'option_a', 'option_b',
        'option_c', 'option_d', 'correct_option', 'explanation', 'order'
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function getOptionsAttribute(): array
    {
        return array_filter([
            'a' => $this->option_a,
            'b' => $this->option_b,
            'c' => $this->option_c,
            'd' => $this->option_d,
        ]);
    }
}
