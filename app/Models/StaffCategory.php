<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffCategory extends Model
{
    protected $fillable = [
        'institute_id',
        'name',
        'description'
    ];

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'staff_category_user',
            'staff_category_id',
            'user_id'
        );
    }

    public function institute()
    {
        return $this->belongsTo(
            Institute::class,
            'institute_id'
        );
    }

    public function staffs()
    {
        return $this->hasMany(
            StaffDetails::class,
            'staff_category_id'
        );
    }
}