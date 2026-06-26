<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminDesignerPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'grid_size',
        'snap_enabled',
        'zoom_level',
        'theme',
        'show_rulers',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
