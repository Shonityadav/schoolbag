<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Staff extends Model
{
    use HasFactory;
    protected $table = 'staffs'; 
    protected $fillable = [
        'created_for',
        'institute_id',
        'staff_category_id',
        'designation',
        'department',
        'joining_date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_for');
    }

    public function school()
    {
        return $this->belongsTo(School::class, 'institute_id');
    }
    
    public function subjects()
    {
        return $this->hasMany(StaffSubject::class, 'staff_id');
    }

    public function category()
    {
        return $this->belongsTo(
            StaffCategory::class,
            'staff_category_id'
        );
    }

}
 
