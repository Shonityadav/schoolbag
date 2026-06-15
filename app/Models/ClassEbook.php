<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassEbook extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'class_ebooks';

    protected $fillable = [
        'ebook_id',
        'class_id',
        'institute_id',
        'created_by',
        'updated_by',
    ];

    public function ebook()
    {
        return $this->belongsTo(Ebook::class);
    }

    public function classModel()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }
}
