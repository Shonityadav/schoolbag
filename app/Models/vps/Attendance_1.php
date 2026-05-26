<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'member_id',
        'role',
        'school_id',
        'date',
        'status',
        'marked_by',
        'note'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function marker()
    {
        return $this->belongsTo(Member::class, 'marked_by');
    }
}

