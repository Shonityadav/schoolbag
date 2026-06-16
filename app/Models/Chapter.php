<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    use HasFactory;

    protected $fillable = ['course_id', 'title', 'order', 'unlock_threshold', 'xp_reward', 'is_active'];

    public function course()
    {
        return $this->belongsTo(AssignedEbook::class);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }



    public function nextChapter()
    {
        return Chapter::where('course_id', $this->course_id)
            ->where('order', '>', $this->order)
            ->orderBy('order')
            ->first();
    }

    public function isUnlockedFor(User $user): bool
    {
        // First chapter in a course is always unlocked
        if ($this->order === 0) return true;

        $prevChapter = Chapter::where('course_id', $this->course_id)
            ->where('order', '<', $this->order)
            ->orderByDesc('order')
            ->first();

        if (!$prevChapter) return true;

        return $prevChapter->isCompletedBy($user);
    }

    public function isCompletedBy(User $user): bool
    {
        $lessonsCount = $this->lessons()->count();
        if ($lessonsCount === 0) return false;

        $completedCount = $this->lessons->filter(function($l) use ($user) {
            return $l->isCompletedBy($user);
        })->count();

        return $completedCount === $lessonsCount;
    }
}
