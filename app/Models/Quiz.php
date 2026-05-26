<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = ['chapter_id', 'title', 'time_limit_minutes', 'xp_reward', 'is_active'];

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('order');
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function bestAttemptFor(int $userId): ?QuizAttempt
    {
        return $this->attempts()
            ->where('user_id', $userId)
            ->orderByDesc('percentage')
            ->first();
    }

    public function hasPassedBy(int $userId): bool
    {
        return $this->attempts()
            ->where('user_id', $userId)
            ->where('status', 'pass')
            ->exists();
    }
}
