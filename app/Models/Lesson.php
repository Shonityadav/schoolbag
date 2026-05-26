<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = ['chapter_id', 'title', 'content', 'type', 'order', 'xp_reward', 'duration_minutes', 'is_active'];

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }

    public function progress()
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function isCompletedBy(User $user): bool
    {
        return LessonProgress::where('user_id', $user->id)
            ->where('lesson_id', $this->id)
            ->where('completed', true)
            ->exists();
    }

    public function getTypeIconAttribute(): string
    {
        return match ($this->type) {
            'reading'   => '📖',
            'flashcard' => '🃏',
            'dictation' => '🎙️',
            default     => '📚',
        };
    }
    public static function getHardWordsMcqs(): array
    {
        return [
            ['question' => 'What colour is a pumpkin?', 'options' => ['(a) Black', '(b) White', '(c) Orange', '(d) Blue'], 'correct' => 2],
            ['question' => 'What is the opposite of cold?', 'options' => ['(a) Freezing', '(b) Hot', '(c) Cool', '(d) Warm'], 'correct' => 1],
            ['question' => 'Which animal is known as the king of the jungle?', 'options' => ['(a) Elephant', '(b) Tiger', '(c) Lion', '(d) Bear'], 'correct' => 2],
            ['question' => 'How many legs does a spider have?', 'options' => ['(a) 6', '(b) 8', '(c) 10', '(d) 12'], 'correct' => 1],
            ['question' => 'What comes after Monday?', 'options' => ['(a) Sunday', '(b) Wednesday', '(c) Tuesday', '(d) Friday'], 'correct' => 2],
            ['question' => 'What do bees make?', 'options' => ['(a) Honey', '(b) Milk', '(c) Juice', '(d) Water'], 'correct' => 0],
            ['question' => 'Which is the largest planet in our solar system?', 'options' => ['(a) Earth', '(b) Mars', '(c) Jupiter', '(d) Saturn'], 'correct' => 2],
            ['question' => 'How many colors are in a rainbow?', 'options' => ['(a) 5', '(b) 6', '(c) 7', '(d) 8'], 'correct' => 2],
            ['question' => 'What do you use to write on a blackboard?', 'options' => ['(a) Pen', '(b) Pencil', '(c) Chalk', '(d) Marker'], 'correct' => 2],
            ['question' => 'What is the color of the sky on a clear day?', 'options' => ['(a) Red', '(b) Green', '(c) Yellow', '(d) Blue'], 'correct' => 3],
        ];
    }

    public static function getActivityMatchPairs(): array
    {
        return [
            ['left' => 'roar',  'right' => 'lion'],
            ['left' => 'Meow',  'right' => 'cat'],
            ['left' => 'bark',  'right' => 'dog'],
            ['left' => 'paw',   'right' => 'elephant'],
        ];
    }
}
