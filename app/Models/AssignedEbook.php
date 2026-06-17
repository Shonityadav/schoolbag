<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignedEbook extends Model
{
    use HasFactory;

    protected $table = 'assigned_ebooks';

    protected $fillable = [
        'class_id', 'user_id', 'ebook_id', 'title', 'description', 'icon', 'color', 'order', 'is_active'
    ];

    public function studentClass()
    {
        return $this->belongsTo(StudentClass::class, 'class_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ebook()
    {
        return $this->belongsTo(Ebook::class);
    }

    public function chapters()
    {
        return $this->hasMany(Chapter::class, 'course_id')->orderBy('order');
    }

    public function firstChapter()
    {
        return $this->hasOne(Chapter::class, 'course_id')->orderBy('order');
    }

    public function getResolvedEbookId()
    {
        if ($this->ebook_id) {
            return $this->ebook_id;
        }

        // Try to find an ebook with chapters that matches the course title
        $ebook = \Illuminate\Support\Facades\DB::table('ebooks')
            ->whereExists(function ($query) {
                $query->select(\Illuminate\Support\Facades\DB::raw(1))
                      ->from('ebook_chapters')
                      ->whereColumn('ebook_chapters.ebook_id', 'ebooks.id');
            })
            ->where(function($q) {
                $q->where('name', 'like', '%' . $this->title . '%')
                  ->orWhere('subject', 'like', '%' . $this->title . '%');
            })
            ->first();

        return $ebook ? $ebook->id : null;
    }
}
