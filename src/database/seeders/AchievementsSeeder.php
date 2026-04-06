<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;

class AchievementsSeeder extends Seeder
{
    public function run(): void
    {
        $achievements = [
            ['key' => 'first_workout',    'icon' => '🏋️', 'xp_reward' => 100],
            ['key' => 'streak_3',         'icon' => '🔥', 'xp_reward' => 50],
            ['key' => 'streak_7',         'icon' => '🔥', 'xp_reward' => 200],
            ['key' => 'streak_30',        'icon' => '🔥', 'xp_reward' => 1000],
            ['key' => 'first_pr',         'icon' => '🏆', 'xp_reward' => 150],
            ['key' => 'pr_10',            'icon' => '🏆', 'xp_reward' => 300],
            ['key' => 'workouts_10',      'icon' => '💪', 'xp_reward' => 200],
            ['key' => 'workouts_50',      'icon' => '💪', 'xp_reward' => 500],
            ['key' => 'workouts_100',     'icon' => '💯', 'xp_reward' => 1000],
            ['key' => 'workouts_365',     'icon' => '🌟', 'xp_reward' => 5000],
            ['key' => 'early_bird',       'icon' => '🌅', 'xp_reward' => 75],
            ['key' => 'night_owl',        'icon' => '🦉', 'xp_reward' => 75],
            ['key' => 'speed_demon',      'icon' => '⚡', 'xp_reward' => 100],
            ['key' => 'marathon',         'icon' => '🏃', 'xp_reward' => 150],
            ['key' => 'variety',          'icon' => '🎯', 'xp_reward' => 100],
            ['key' => 'first_routine',    'icon' => '📋', 'xp_reward' => 50],
            ['key' => 'heavy_lifter',     'icon' => '🦾', 'xp_reward' => 200],
            ['key' => 'cardio_king',      'icon' => '❤️', 'xp_reward' => 200],
        ];

        foreach ($achievements as $data) {
            Achievement::firstOrCreate(['key' => $data['key']], $data);
        }
    }
}
