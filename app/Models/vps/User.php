<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'institute_id',
        'name',
        'email',
        'password',
        'mobile',
        'dob',
        'state',
        'city',
        'user_type',   // 1=admin, 2=staff, 3=student
        'created_by',
        'api_token'
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];

    /* =====================
       RELATIONSHIPS
    ====================== */

    public function school()
    {
        return $this->belongsTo(School::class, 'institute_id');
    }

    public function staff()
    {
        return $this->hasOne(Staff::class, 'user_id');
    }

    public function student()
    {
        return $this->hasOne(Student::class, 'user_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'user_id');
    }
}

