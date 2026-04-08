<?php

use App\Models\Achievement;
use App\Services\XpService;

beforeEach(function () {
    $achievements = [
        ['key' => 'first_workout',  'icon' => '🏋️', 'xp_reward' => 100],
        ['key' => 'workouts_10',    'icon' => '💪',  'xp_reward' => 200],
        ['key' => 'workouts_50',    'icon' => '💪',  'xp_reward' => 500],
        ['key' => 'workouts_100',   'icon' => '💯',  'xp_reward' => 1000],
        ['key' => 'workouts_365',   'icon' => '🌟',  'xp_reward' => 5000],
        ['key' => 'streak_3',       'icon' => '🔥',  'xp_reward' => 50],
        ['key' => 'streak_7',       'icon' => '🔥',  'xp_reward' => 200],
        ['key' => 'streak_30',      'icon' => '🔥',  'xp_reward' => 1000],
        ['key' => 'early_bird',     'icon' => '🌅',  'xp_reward' => 75],
        ['key' => 'night_owl',      'icon' => '🦉',  'xp_reward' => 75],
        ['key' => 'speed_demon',    'icon' => '⚡',  'xp_reward' => 100],
        ['key' => 'marathon',       'icon' => '🏃',  'xp_reward' => 150],
        ['key' => 'first_routine',  'icon' => '📋',  'xp_reward' => 50],
        ['key' => 'variety',        'icon' => '🎯',  'xp_reward' => 100],
        ['key' => 'heavy_lifter',   'icon' => '🦾',  'xp_reward' => 200],
        ['key' => 'cardio_king',    'icon' => '❤️',  'xp_reward' => 200],
        ['key' => 'first_pr',       'icon' => '🏆',  'xp_reward' => 150],
        ['key' => 'pr_10',          'icon' => '🏆',  'xp_reward' => 300],
    ];
    foreach ($achievements as $data) {
        Achievement::firstOrCreate(['key' => $data['key']], $data);
    }
});

describe('Achievement model', function () {

    it('can be created with key, icon and xp_reward', function () {
        $ach = Achievement::create(['key' => 'test_ach', 'icon' => '⭐', 'xp_reward' => 50]);
        expect($ach->exists)->toBeTrue();
    });

    it('name accessor reads from lang file', function () {
        $ach = Achievement::where('key', 'first_workout')->first();
        expect($ach->name)->toBeString()->not->toBeEmpty();
    });

    it('description accessor reads from lang file', function () {
        $ach = Achievement::where('key', 'first_workout')->first();
        expect($ach->description)->toBeString()->not->toBeEmpty();
    });

    it('has a users relationship', function () {
        $ach = Achievement::where('key', 'first_workout')->first();
        expect($ach->users())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class);
    });
});

describe('Achievement unlocking flow', function () {

    it('is attached to user with an unlocked_at timestamp', function () {
        $user = createUser();
        $ach  = Achievement::where('key', 'first_workout')->first();

        $user->achievements()->attach($ach->id, ['unlocked_at' => now()]);

        $pivot = $user->achievements()->where('key', 'first_workout')->first()->pivot;
        expect($pivot->unlocked_at)->not->toBeNull();
    });

    it('hasAchievement returns false before unlock', function () {
        $user = createUser();
        expect($user->hasAchievement('first_workout'))->toBeFalse();
    });

    it('hasAchievement returns true after unlock', function () {
        $user = createUser();
        $ach  = Achievement::where('key', 'first_workout')->first();
        $user->achievements()->attach($ach->id, ['unlocked_at' => now()]);

        expect($user->hasAchievement('first_workout'))->toBeTrue();
    });

    it('cannot be unlocked twice via processWorkout', function () {
        $user    = createUser();
        $ach     = Achievement::where('key', 'first_workout')->first();
        $user->achievements()->attach($ach->id, ['unlocked_at' => now()]);

        $workout = createWorkout($user, 'completed');
        $workout->update(['started_at' => now()->subMinutes(30), 'finished_at' => now()]);

        $result = app(XpService::class)->processWorkout($user, $workout);

        $keys = collect($result['achievements'])->pluck('key');
        expect($keys)->not->toContain('first_workout');
        // pivot table should still have exactly 1 row
        expect($user->achievements()->where('key', 'first_workout')->count())->toBe(1);
    });

    it('workout completion gives XP equal to reported total', function () {
        $user    = createUser(['xp' => 0]);
        $workout = createWorkout($user, 'completed');
        $workout->update(['started_at' => now()->subMinutes(30), 'finished_at' => now()]);

        $result = app(XpService::class)->processWorkout($user, $workout);

        expect($user->fresh()->xp)->toBe($result['xp']);
    });

    it('workouts_10 is unlocked after 10 completed workouts', function () {
        $user = createUser();

        // Create 9 already-completed workouts
        for ($i = 0; $i < 9; $i++) {
            $w = createWorkout($user, 'completed');
            $w->update(['started_at' => now()->subMinutes(30), 'finished_at' => now()->subMinutes($i + 1)]);
        }

        // 10th workout triggers XpService
        $workout = createWorkout($user, 'completed');
        $workout->update(['started_at' => now()->subMinutes(30), 'finished_at' => now()]);

        $result = app(XpService::class)->processWorkout($user, $workout);

        $keys = collect($result['achievements'])->pluck('key');
        expect($keys)->toContain('workouts_10');
    });

    it('workouts_10 is not unlocked before 10 completed workouts', function () {
        $user    = createUser();
        $workout = createWorkout($user, 'completed');
        $workout->update(['started_at' => now()->subMinutes(30), 'finished_at' => now()]);

        $result = app(XpService::class)->processWorkout($user, $workout);

        $keys = collect($result['achievements'])->pluck('key');
        expect($keys)->not->toContain('workouts_10');
    });
});
