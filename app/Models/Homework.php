<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Homework extends Model
{
    protected $table = 'homeworks';
    
    protected $fillable = [
        'institute_id',
        'class_id',
        'user_id',
        'for_date',
        'content',
    ];

    public function institute()
    {
        return $this->belongsTo(Institute::class);
    }

    public function studentClass()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
