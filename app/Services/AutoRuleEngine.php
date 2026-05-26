<?php

namespace App\Services;

use App\Models\AutoRule;
use App\Models\Badge;
use App\Models\StudentBadge;
use App\Models\User;
use App\Models\Worksheet;
use Carbon\Carbon;

class AutoRuleEngine
{
    protected User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Fire all active rules matching the given event.
     */
    public function fire(string $event, array $context = []): void
    {
        $rules = AutoRule::active()->forEvent($event)->get();

        foreach ($rules as $rule) {
            if ($this->conditionMet($rule, $context)) {
                $this->executeAction($rule, $context);
            }
        }

        // Always check badge eligibility after any event
        $this->checkBadges();
    }

    protected function conditionMet(AutoRule $rule, array $context): bool
    {
        return match ($rule->trigger_event) {
            'quiz_pass', 'quiz_fail'      => true,
            'chapter_complete'            => true,
            'worksheet_complete'          => true,
            'streak_days'                 => $this->user->streak_count >= $rule->trigger_value,
            'login'                       => true,
            default                       => false,
        };
    }

    protected function executeAction(AutoRule $rule, array $context): void
    {
        $payload = $rule->action_payload;

        match ($rule->action_type) {
            'add_xp' => $this->user->addXp(
                $payload['amount'] ?? 10,
                'quiz',
                $context['source_id'] ?? null,
                $rule->name
            ),
            'award_badge' => $this->awardBadge($payload['badge_id'] ?? null),
            'assign_worksheet' => $this->assignWorksheet($payload),
            default => null,
        };
    }

    protected function awardBadge(?int $badgeId): void
    {
        if (!$badgeId) return;
        $badge = Badge::find($badgeId);
        if (!$badge || $badge->isEarnedBy($this->user->id)) return;

        StudentBadge::create([
            'user_id'    => $this->user->id,
            'badge_id'   => $badgeId,
            'awarded_at' => now(),
        ]);

        $this->user->addXp(50, 'badge', $badgeId, "Badge earned: {$badge->name}");
    }

    protected function assignWorksheet(array $payload): void
    {
        Worksheet::create([
            'user_id'       => $this->user->id,
            'title'         => $payload['title'] ?? 'Auto-assigned Worksheet',
            'description'   => $payload['description'] ?? null,
            'content'       => $payload['content'] ?? null,
            'status'        => 'pending',
            'auto_assigned' => true,
            'xp_reward'     => $payload['xp_reward'] ?? 25,
            'due_date'      => now()->addDays($payload['due_days'] ?? 3),
        ]);
    }

    protected function checkBadges(): void
    {
        $badges = Badge::all();

        foreach ($badges as $badge) {
            if ($badge->isEarnedBy($this->user->id)) continue;

            $earned = match ($badge->condition_type) {
                'xp_total'         => $this->user->total_xp >= $badge->condition_value,
                'streak_days'      => $this->user->streak_count >= $badge->condition_value,
                'quiz_pass_count'  => $this->user->quizAttempts()->where('status', 'pass')->count() >= $badge->condition_value,
                'worksheets_done'  => $this->user->worksheets()->where('status', 'done')->count() >= $badge->condition_value,
                'lessons_complete' => $this->user->lessonProgress()->where('completed', true)->count() >= $badge->condition_value,
                default            => false,
            };

            if ($earned) {
                $this->awardBadge($badge->id);
            }
        }
    }
}
