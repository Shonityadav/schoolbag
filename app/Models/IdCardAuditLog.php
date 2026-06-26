<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IdCardAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'institute_id',
        'user_id',
        'ip_address',
        'action',
        'affected_record',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
