<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Institute extends Model
{
    protected $fillable = [
        'name',
        'address',
        'number',
        
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'institute_id');
    }

    public function staff()
    {
        return $this->hasMany(Staff::class, 'institute_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'institute_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'institute_id');
    }
}
