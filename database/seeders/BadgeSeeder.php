<?php

namespace Database\Seeders;

use App\Models\Badge;
use App\Models\AutoRule;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            ['name' => 'First Step',    'description' => 'Passed your first quiz!',    'icon' => '🌟', 'color' => '#FFD700', 'condition_type' => 'quiz_pass_count',  'condition_value' => 1],
            ['name' => 'Quiz Master',   'description' => 'Passed 10 quizzes!',         'icon' => '🏆', 'color' => '#F59E0B', 'condition_type' => 'quiz_pass_count',  'condition_value' => 10],
            ['name' => 'On Fire 🔥',    'description' => '7-day login streak!',        'icon' => '🔥', 'color' => '#EF4444', 'condition_type' => 'streak_days',      'condition_value' => 7],
            ['name' => 'Consistent',    'description' => '30-day login streak!',       'icon' => '💎', 'color' => '#3B82F6', 'condition_type' => 'streak_days',      'condition_value' => 30],
            ['name' => 'XP Rookie',     'description' => 'Earned 500 XP!',            'icon' => '⚡', 'color' => '#8B5CF6', 'condition_type' => 'xp_total',         'condition_value' => 500],
            ['name' => 'XP Champion',   'description' => 'Earned 5,000 XP!',          'icon' => '👑', 'color' => '#EC4899', 'condition_type' => 'xp_total',         'condition_value' => 5000],
            ['name' => 'Bookworm',      'description' => 'Completed 20 lessons!',     'icon' => '📚', 'color' => '#10B981', 'condition_type' => 'lessons_complete',  'condition_value' => 20],
            ['name' => 'Worksheet Pro', 'description' => 'Completed 10 worksheets!',  'icon' => '📝', 'color' => '#06B6D4', 'condition_type' => 'worksheets_done',   'condition_value' => 10],
        ];

        foreach ($badges as $badge) {
            Badge::firstOrCreate(['name' => $badge['name']], $badge);
        }

        // Auto rules
        $firstStepBadge = Badge::where('name', 'First Step')->first();

        $rules = [
            [
                'name'           => 'Award First Step Badge on First Quiz Pass',
                'trigger_event'  => 'quiz_pass',
                'trigger_value'  => 1,
                'action_type'    => 'award_badge',
                'action_payload' => ['badge_id' => $firstStepBadge?->id],
                'is_active'      => true,
            ],
            [
                'name'           => 'Bonus XP for Daily Login',
                'trigger_event'  => 'login',
                'trigger_value'  => 0,
                'action_type'    => 'add_xp',
                'action_payload' => ['amount' => 10, 'description' => 'Daily login bonus'],
                'is_active'      => true,
            ],
            [
                'name'           => 'Assign Review Worksheet on Quiz Fail',
                'trigger_event'  => 'quiz_fail',
                'trigger_value'  => 0,
                'action_type'    => 'assign_worksheet',
                'action_payload' => [
                    'title'       => 'Review & Practice',
                    'description' => 'Practice the chapter material and try the quiz again!',
                    'xp_reward'   => 15,
                    'due_days'    => 2,
                ],
                'is_active'      => true,
            ],
        ];

        foreach ($rules as $rule) {
            AutoRule::firstOrCreate(['name' => $rule['name']], $rule);
        }
    }
}
