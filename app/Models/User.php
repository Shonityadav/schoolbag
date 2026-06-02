<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password',
        'class_id', 'role', 'user_type', 'institute_id', 'avatar',
        'total_xp', 'streak_count', 'last_streak_date', 'phone',
    ];

    protected $hidden = ['password', 'api_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_streak_date'  => 'date',
    ];

    // ── Relationships ──────────────────────────────────────────
    public function studentClass()    { return $this->belongsTo(StudentClass::class, 'class_id'); }
    public function quizAttempts()    { return $this->hasMany(QuizAttempt::class); }
    public function xpTransactions()  { return $this->hasMany(XpTransaction::class); }
    public function attendances()     { return $this->hasMany(Attendance::class); }
    public function worksheets()      { return $this->hasMany(Worksheet::class); }
    public function studentBadges()   { return $this->hasMany(StudentBadge::class); }
    public function badges()          { return $this->hasManyThrough(Badge::class, StudentBadge::class, 'user_id', 'id', 'id', 'badge_id'); }
    public function lessonProgress()  { return $this->hasMany(LessonProgress::class); }

    // ── Helpers ────────────────────────────────────────────────
    public function isAdmin(): bool { return $this->role === 'admin'; }

    public function addXp(int $amount, string $sourceType, ?int $sourceId = null, ?string $description = null): void
    {
        $this->increment('total_xp', $amount);
        XpTransaction::create([
            'user_id'     => $this->id,
            'amount'      => $amount,
            'source_type' => $sourceType,
            'source_id'   => $sourceId,
            'description' => $description,
        ]);
    }

    public function markAttendanceToday(): bool
    {
        $today = now()->toDateString();
        $alreadyMarked = Attendance::where('user_id', $this->id)->where('date', $today)->exists();
        if ($alreadyMarked) return false;

        Attendance::create(['user_id' => $this->id, 'date' => $today]);

        // Update streak
        $yesterday = now()->subDay()->toDateString();
        if ($this->last_streak_date && $this->last_streak_date->toDateString() === $yesterday) {
            $this->increment('streak_count');
        } else {
            $this->streak_count = 1;
        }
        $this->last_streak_date = $today;
        $this->save();

        return true;
    }

    public function getLevelAttribute(): int
    {
        // Each level = 500 XP
        return (int) floor($this->total_xp / 500) + 1;
    }

    public function getXpToNextLevelAttribute(): int
    {
        return 500 - ($this->total_xp % 500);
    }

    public function getXpProgressPercentAttribute(): int
    {
        return (int) (($this->total_xp % 500) / 500 * 100);
    }

    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class,
            'user_permissions',
            'user_id',
            'permission_id'
        );
    }

    public function hasPermission($permission)
    {
        // Institute admin has all permissions
        if ($this->user_type == 1) {
            return true;
        }

        return $this->permissions()
            ->where('name', $permission)
            ->exists();
    }

    public function classes()
    {
        return $this->belongsToMany(
            ClassModel::class,
            'class_user',
            'user_id',
            'class_id'
        );
    }
}
