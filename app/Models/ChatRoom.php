<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
    protected $fillable = [
        'institute_id',
        'name',
        'type',
        'class_id',
        'staff_category_id'
    ];

    public function messages()
    {
        return $this->hasMany(
            ChatMessage::class
        );
    }

    public function schoolClass()
    {
        return $this->belongsTo(
            ClassModel::class,
            'class_id'
        );
    }

    public function category()
    {
        return $this->belongsTo(
            StaffCategory::class,
            'staff_category_id'
        );
    }
}