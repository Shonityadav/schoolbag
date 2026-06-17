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
        'role', 'user_type', 'institute_id', 'avatar',
        'banner',
        'total_xp', 'streak_count', 'last_streak_date', 'phone',
        'unlocked_items'
    ];

    protected $hidden = ['password', 'api_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_streak_date'  => 'date',
        'unlocked_items'    => 'array',
    ];

    // ── Relationships ──────────────────────────────────────────
    public function studentClass()    { return $this->belongsTo(ClassModel::class, 'class_id'); }
    public function institute()       { return $this->belongsTo(Institute::class, 'institute_id'); }

    public function xpTransactions()  { return $this->hasMany(XpTransaction::class); }
    public function attendances()     { return $this->hasMany(\App\Models\Attendance::class, 'created_for'); }
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
        $alreadyMarked = \App\Models\Attendance::where('created_for', $this->id)
            ->where('attendance_date', $today)
            ->exists();
        if ($alreadyMarked) return false;

        \App\Models\Attendance::create([
            'created_for' => $this->id,
            'attendance_date' => $today,
            'institute_id' => $this->institute_id,
            'status' => 'Present',
            'created_by' => $this->id,
        ]);

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

    public function getInitialsAttribute(): string
    {
        $name = trim($this->name);
        $words = explode(' ', $name);
        $initials = '';
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper($word[0]);
            }
        }
        return substr($initials, 0, 2);
    }

    public function getInitialsBgAttribute(): string
    {
        $gradients = [
            'linear-gradient(135deg, #71b8ff 0%, #4da6ff 100%)',
            'linear-gradient(135deg, #ffe099 0%, #ffc857 100%)',
            'linear-gradient(135deg, #ffa564 0%, #ff9f5a 100%)',
            'linear-gradient(135deg, #96e6b7 0%, #7ed9a3 100%)',
            'linear-gradient(135deg, #ff8b8b 0%, #ff6b6b 100%)',
        ];
        return $gradients[$this->id % 5];
    }

    public function getUnlockedFramesAttribute(): array
    {
        $level = $this->level;
        $frames = ['Bronze.png']; // Default unlocked for everyone
        
        if ($level >= 5) $frames[] = 'SIlver.png';
        if ($level >= 10) $frames[] = 'Gold.png';
        if ($level >= 15) $frames[] = 'Ace.png';
        if ($level >= 20) $frames[] = 'Ace_Master.png';
        if ($level >= 25) $frames[] = 'Diamond.png';
        
        return $frames;
    }

    public function awardChestDrop()
    {
        $avatarPath = public_path('uploads/images/banners/Avatar');
        if (!\Illuminate\Support\Facades\File::exists($avatarPath)) {
            return null;
        }

        $allAvatars = array_map(function($file) {
            return $file->getFilename();
        }, \Illuminate\Support\Facades\File::files($avatarPath));

        $unlocked = $this->unlocked_items ?? [];
        $unlockedAvatars = $unlocked['avatars'] ?? [];

        // Find avatars we haven't unlocked yet
        $lockedAvatars = array_diff($allAvatars, $unlockedAvatars);

        if (empty($lockedAvatars)) {
            return null; // everything is unlocked
        }

        // Pick one at random
        $newUnlock = $lockedAvatars[array_rand($lockedAvatars)];
        
        $unlockedAvatars[] = $newUnlock;
        $unlocked['avatars'] = $unlockedAvatars;
        
        $this->unlocked_items = $unlocked;
        $this->save();

        return $newUnlock;
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
        )->withTimestamps();
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
        )->withTimestamps();
    }

    public function student()
    {
        return $this->hasOne(StudentDetails::class, 'created_for');
    }

    public function staff()
    {
        return $this->hasOne(
            StaffDetails::class,
            'created_for'
        );
    }

    public function managedCategories()
    {
        return $this->belongsToMany(
            StaffCategory::class,
            'staff_category_user',
            'user_id',
            'staff_category_id'
        )->withTimestamps();
    }

    public function canManageCategory($categoryId)
    {
        if ($this->user_type == 1) {
            return true;
        }

        return $this->managedCategories()
            ->where('staff_categories.id', $categoryId)
            ->exists();
    }

    public function canAccessChatRoom($room)
    {
        if ($this->user_type == 1) {
            return true;
        }

        /*
        STUDENT
        */

        if (
            $this->user_type == 3 &&
            $room->type == 'class'
        ) {

            return optional(
                $this->student
            )->class_id == $room->class_id;
        }

        /*
        STAFF -> CLASS CHAT
        */

        if (
            $this->user_type == 2 &&
            $room->type == 'class'
        ) {

            return $this->classes()
                ->where(
                    'classes.id',
                    $room->class_id
                )
                ->exists();
        }

        /*
        STAFF CATEGORY CHAT
        */

        if (
            $room->type == 'staff_category'
        ) {

            $belongsToCategory = optional($this->staff)->staff_category_id == $room->staff_category_id;
            $managesCategory = $this->canManageCategory($room->staff_category_id);

            return $belongsToCategory || $managesCategory;
        }

        return false;
    }
}
