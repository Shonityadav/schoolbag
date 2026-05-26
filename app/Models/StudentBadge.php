<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentBadge extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['user_id', 'badge_id', 'awarded_at'];

    protected $casts = ['awarded_at' => 'datetime'];

    public function user()  { return $this->belongsTo(User::class); }
    public function badge() { return $this->belongsTo(Badge::class); }
}
