<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'icon', 'color', 'condition_type', 'condition_value'];

    public function studentBadges()
    {
        return $this->hasMany(StudentBadge::class);
    }

    public function isEarnedBy(int $userId): bool
    {
        return StudentBadge::where('user_id', $userId)->where('badge_id', $this->id)->exists();
    }
}
