<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IdCardAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'institute_id',
        'name',
        'type',
        'file_path',
    ];

    public function institute()
    {
        return $this->belongsTo(Institute::class);
    }
}
