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
        'section',
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
        return $this->hasMany(StudentDetails::class, 'class_id');
    }

    public function classEbooks()
    {
        return $this->hasMany(ClassEbook::class, 'class_id');
    }

    public function ebooks()
    {
        return $this->belongsToMany(Ebook::class, 'class_ebooks', 'class_id', 'ebook_id');
    }

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'class_user',
            'class_id',
            'user_id'
        );
    }
}