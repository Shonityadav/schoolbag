<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Worksheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'description', 'content',
        'status', 'auto_assigned', 'xp_reward', 'due_date', 'completed_at'
    ];

    protected $casts = [
        'auto_assigned' => 'boolean',
        'due_date'      => 'date',
        'completed_at'  => 'datetime',
    ];

    public function user() { return $this->belongsTo(User::class); }

    public function scopePending($query) { return $query->where('status', 'pending'); }
    public function scopeDone($query)    { return $query->where('status', 'done'); }

    public function isOverdue(): bool
    {
        return $this->due_date && $this->status === 'pending' && $this->due_date->isPast();
    }
}
