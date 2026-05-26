<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutoRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'trigger_event', 'trigger_value',
        'action_type', 'action_payload', 'is_active'
    ];

    protected $casts = [
        'action_payload' => 'array',
        'is_active'      => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForEvent($query, string $event)
    {
        return $query->where('trigger_event', $event);
    }
}
