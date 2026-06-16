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
        return $this->hasMany(StaffDetails::class, 'institute_id');
    }

    public function students()
    {
        return $this->hasMany(StudentDetails::class, 'institute_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'institute_id');
    }
}
