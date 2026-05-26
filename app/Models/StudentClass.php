<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentClass extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = ['name', 'level', 'icon', 'color'];

    public function courses()
    {
        return $this->hasMany(Course::class, 'class_id');
    }

    public function students()
    {
        return $this->hasMany(User::class, 'class_id');
    }
}
