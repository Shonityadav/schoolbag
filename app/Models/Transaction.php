<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'institute_id',
        'created_for',
        'created_by',
        'transaction_type',
        'transaction_amount',
        'transaction_method',
        'transaction_date',
        'transaction_duration',
        'remarks',
        'status',
    ];

    public function institute()
    {
        return $this->belongsTo(School::class, 'institute_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_for');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}