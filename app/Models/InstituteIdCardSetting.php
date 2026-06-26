<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstituteIdCardSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'institute_id',
        'primary_color',
        'secondary_color',
        'text_color',
        'show_qr',
        'show_barcode',
        'show_signature',
    ];

    public function institute()
    {
        return $this->belongsTo(Institute::class);
    }
}
