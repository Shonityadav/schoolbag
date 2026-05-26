<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassModel extends Model
{
    use SoftDeletes;

    protected $table = 'classes';

    protected $fillable = [
        'institute_id',
        'standard',
        'description',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function institute()
    {
        return $this->belongsTo(School::class, 'institute_id');
    }
    
     
    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }
}

