<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserIdentityCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'template_id',
        'token',
        'status',
        'issued_on',
        'expires_on',
        'printed_by',
        'rfid_uid',
        'nfc_identifier',
    ];

    protected $casts = [
        'issued_on' => 'date',
        'expires_on' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->token)) {
                $model->token = Str::random(32);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function template()
    {
        return $this->belongsTo(IdCardTemplate::class, 'template_id');
    }

    public function printedBy()
    {
        return $this->belongsTo(User::class, 'printed_by');
    }
}
