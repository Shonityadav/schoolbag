<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IdCardDownload extends Model
{
    use HasFactory;

    protected $fillable = [
        'institute_id',
        'requested_by',
        'file_path',
        'status',
        'progress',
    ];

    public function institute()
    {
        return $this->belongsTo(Institute::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }
}
