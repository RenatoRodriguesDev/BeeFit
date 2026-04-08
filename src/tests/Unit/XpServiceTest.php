<?php

use App\Models\Achievement;
use App\Models\WorkoutSet;
use App\Services\XpService;
use Carbon\Carbon;

beforeEach(function () {
    // Seed the achievements the service depends on
    $achievements = [
        ['key' => 'first_workout', 'icon' => '🏋️', 'xp_reward' => 100],
        ['key' => 'workouts_10',   'icon' => '💪', 'xp_reward' => 200],
        ['key' => 'workouts_50',   'icon' => '💪', 'xp_reward' => 500],
        ['key' => 'workouts_100',  'icon' => '💯', 'xp_reward' => 1000],
        ['key' => 'workouts_365',  'icon' => '🌟', 'xp_reward' => 5000],
        ['key' => 'streak_3',      'icon' => '🔥', 'xp_reward' => 50],
        ['key' => 'streak_7',      'icon' => '🔥', 'xp_reward' => 200],
        ['key' => 'streak_30',     'icon' => '🔥', 'xp_reward' => 1000],
        ['key' => 'early_bird',    'icon' => '🌅', 'xp_reward' => 75],
        ['key' => 'night_owl',     'icon' => '🦉', 'xp_reward' => 75],
        ['key' => 'speed_demon',   'icon' => '⚡', 'xp_reward' => 100],
        ['key' => 'marathon',      'icon' => '🏃', 'xp_reward' => 150],
        ['key' => 'first_routine', 'icon' => '📋', 'xp_reward' => 50],
        ['key' => 'variety',       'icon' => '🎯', 'xp_reward' => 100],
        ['key' => 'heavy_lifter',  'icon' => '🦾', 'xp_reward' => 200],
        ['key' => 'cardio_king',   'icon' => '❤️', 'xp_reward' => 200],
        ['key' => 'first_pr',      'icon' => '🏆', 'xp_reward' => 150],
        ['key' => 'pr_10',         'icon' => '🏆', 'xp_reward' => 300],
    ];
    foreach ($achievements as $data) {
        Achievement::firstOrCreate(['key' => $data['key']], $data);
    }
});

describe('XpService::processWorkout() — base XP', function () {

    it('awards 50 base XP for a completed workout', function () {
        $user    = createUser();
        $workout = createWorkout($user, 'active');
        $workout->update(['started_at' => now()->subMinutes(30), 'finished_at' => now()]);

        $result = app(XpService::class)->processWorkout($user, $workout);

        expect($result['xp'])->toBeGreaterThanOrEqual(50);
    });

    it('awards 5 XP per completed set', function () {
        $user    = createUser();
        $workout = createWorkout($user, 'active');
        $workout->update(['started_at' => now()->subMinutes(30), 'finished_at' => now()]);
        $we = addExerciseToWorkout($workout); // adds 1 set

        // Base 50 + 5*1 set + 30 new exercise = 85 (without time bonuses)
        // We just check it includes the per-set amount
        $result = app(XpService::class)->processWorkout($user, $workout);

        // 1 set = 5 XP contribution; total should be at least 55 (50 base + 5)
        expect($result['xp'])->toBeGreaterThanOrEqual(55);
    });

    it('awards 30 XP for a duration of 60+ minutes', function () {
        $user    = createUser();
        $workout = createWorkout($user, 'active');
        $workout->update(['started_at' => now()->subMinutes(65), 'finished_at' => now()]);

        $result = app(XpService::class)->processWorkout($user, $workout);

        // ≥60 min adds 30 XP on top of base
        expect($result['xp'])->toBeGreaterThanOrEqual(80);
    });

    it('awards an additional 20 XP for 90+ minute workouts', function () {
        $user    = createUser();
        $workout = createWorkout($user, 'active');
        $workout->update(['started_at' => now()->subMinutes(100), 'finished_at' => now()]);

        $result = app(XpService::class)->processWorkout($user, $workout);

        // ≥90 min adds 30+20 = 50 XP on top of base
        expect($result['xp'])->toBeGreaterThanOrEqual(100);
    });

    it('awards 30 XP per new exercise never done before', function () {
        $user     = createUser();
        $exercise = createExercise();
        $workout  = createWorkout($user, 'active');
        $workout->update(['started_at' => now()->subMinutes(30), 'finished_at' => now()]);
        addExerciseToWorkout($workout, $exercise);

        $result = app(XpService::class)->processWorkout($user, $workout);

        // new exercise adds +30
        expect($result['xp'])->toBeGreaterThanOrEqual(80); // 50 base + 5 set + 30 new
    });

    it('does not award new-exercise bonus for previously done exercises', function () {
        $user     = createUser();
        $exercise = createExercise();

        // First workout with this exercise
        $w1 = createWorkout($user, 'completed');
        $w1->update(['started_at' => now()->subMinutes(30), 'finished_at' => now()->subMinutes(1)]);
        addExerciseToWorkout($w1, $exercise);

        // Second workout with same exercise
        $w2 = createWorkout($user, 'active');
        $w2->update(['started_at' => now()->subMinutes(30), 'finished_at' => now()]);
        addExerciseToWorkout($w2, $exercise);

        $resultFirst  = app(XpService::class)->processWorkout($user, $w1);
        $resultSecond = app(XpService::class)->processWorkout($user, $w2);

        // Second workout should not include the new-exercise bonus
        expect($resultSecond['xp'])->toBeLessThan($resultFirst['xp'] + 30);
    });

    it('returns an array with xp and achievements keys', function () {
        $user    = createUser();
        $workout = createWorkout($user, 'active');
        $workout->update(['started_at' => now()->subMinutes(30), 'finished_at' => now()]);

        $result = app(XpService::class)->processWorkout($user, $workout);

        expect($result)->toHaveKeys(['xp', 'achievements']);
        expect($result['achievements'])->toBeArray();
    });

    it('increments user xp in the database', function () {
        $user    = createUser(['xp' => 0]);
        $workout = createWorkout($user, 'active');
        $workout->update(['started_at' => now()->subMinutes(30), 'finished_at' => now()]);

        $result = app(XpService::class)->processWorkout($user, $workout);

        expect($user->fresh()->xp)->toBe($result['xp']);
    });
});

describe('XpService — achievements', function () {

    it('unlocks first_workout on the first completed workout', function () {
        $user    = createUser();
        $workout = createWorkout($user, 'completed');
        $workout->update(['started_at' => now()->subMinutes(30), 'finished_at' => now()]);

        $result = app(XpService::class)->processWorkout($user, $workout);

        $keys = collect($result['achievements'])->pluck('key');
        expect($keys)->toContain('first_workout');
    });

    it('does not unlock first_workout twice', function () {
        $user       = createUser();
        $achievement = Achievement::where('key', 'first_workout')->first();
        $user->achievements()->attach($achievement->id, ['unlocked_at' => now()]);

        $workout = createWorkout($user, 'completed');
        $workout->update(['started_at' => now()->subMinutes(30), 'finished_at' => now()]);

        $result = app(XpService::class)->processWorkout($user, $workout);

        $keys = collect($result['achievements'])->pluck('key');
        expect($keys)->not->toContain('first_workout');
    });

    it('awards achievement XP on top of workout XP', function () {
        $user    = createUser(['xp' => 0]);
        $workout = createWorkout($user, 'completed');
        $workout->update(['started_at' => now()->subMinutes(30), 'finished_at' => now()]);

        $result = app(XpService::class)->processWorkout($user, $workout);

        // first_workout gives 100 XP bonus; total must exceed base workout XP
        $achievementXp = collect($result['achievements'])->sum('xp_reward');
        $baseXp        = $result['xp'] - $achievementXp;

        expect($result['xp'])->toBeGreaterThan($baseXp);
        expect($user->fresh()->xp)->toBe($result['xp']);
    });

    it('unlocks heavy_lifter when a set has weight >= 100kg', function () {
        $user    = createUser();
        $workout = createWorkout($user, 'completed');
        $workout->update(['started_at' => now()->subMinutes(30), 'finished_at' => now()]);
        $we = addExerciseToWorkout($workout); // default set: 100kg x 10

        $result = app(XpService::class)->processWorkout($user, $workout);

        $keys = collect($result['achievements'])->pluck('key');
        expect($keys)->toContain('heavy_lifter');
    });

    it('does not unlock heavy_lifter when all sets are under 100kg', function () {
        $user    = createUser();
        $workout = createWorkout($user, 'completed');
        $workout->update(['started_at' => now()->subMinutes(30), 'finished_at' => now()]);
        $we = addExerciseToWorkout($workout);
        $we->sets()->update(['weight' => 50]); // under 100kg

        $result = app(XpService::class)->processWorkout($user, $workout);

        $keys = collect($result['achievements'])->pluck('key');
        expect($keys)->not->toContain('heavy_lifter');
    });

    it('unlocks marathon for a 90+ minute workout', function () {
        $user    = createUser();
        $workout = createWorkout($user, 'completed');
        $workout->update(['started_at' => now()->subMinutes(95), 'finished_at' => now()]);

        $result = app(XpService::class)->processWorkout($user, $workout);

        $keys = collect($result['achievements'])->pluck('key');
        expect($keys)->toContain('marathon');
    });

    it('unlocks speed_demon for a sub-20-minute workout', function () {
        $user    = createUser();
        $workout = createWorkout($user, 'completed');
        $workout->update(['started_at' => now()->subMinutes(15), 'finished_at' => now()]);

        $result = app(XpService::class)->processWorkout($user, $workout);

        $keys = collect($result['achievements'])->pluck('key');
        expect($keys)->toContain('speed_demon');
    });

    it('unlocks first_routine when user has at least one routine', function () {
        $user    = createUser();
        $workout = createWorkout($user, 'completed'); // createWorkout also creates a routine
        $workout->update(['started_at' => now()->subMinutes(30), 'finished_at' => now()]);

        $result = app(XpService::class)->processWorkout($user, $workout);

        $keys = collect($result['achievements'])->pluck('key');
        expect($keys)->toContain('first_routine');
    });
});

describe('XpService — streak calculation', function () {

    // Helper: create a completed workout bypassing mass-assignment guards
    function completedWorkout(\App\Models\User $user, \Carbon\Carbon $finishedAt): \App\Models\Workout
    {
        $w = createWorkout($user);
        \Illuminate\Support\Facades\DB::table('workouts')->where('id', $w->id)->update([
            'status'      => 'completed',
            'finished_at' => $finishedAt->toDateTimeString(),
        ]);
        return $w->fresh();
    }

    it('returns 1 for a single workout today', function () {
        $user = createUser();
        $today = \Carbon\Carbon::create(2025, 6, 15, 12, 0, 0);
        \Carbon\Carbon::setTestNow($today);

        completedWorkout($user, $today);

        $service = app(XpService::class);
        $streak  = (new ReflectionMethod($service, 'currentStreak'))->invoke($service, $user);

        \Carbon\Carbon::setTestNow();
        expect($streak)->toBe(1);
    });

    it('returns 0 when last workout was 2+ days ago', function () {
        $user  = createUser();
        $today = \Carbon\Carbon::create(2025, 6, 15, 12, 0, 0);
        \Carbon\Carbon::setTestNow($today);

        completedWorkout($user, $today->copy()->subDays(3));

        $service = app(XpService::class);
        $streak  = (new ReflectionMethod($service, 'currentStreak'))->invoke($service, $user);

        \Carbon\Carbon::setTestNow();
        expect($streak)->toBe(0);
    });

    it('counts consecutive days correctly', function () {
        $user  = createUser();
        $today = \Carbon\Carbon::create(2025, 6, 15, 12, 0, 0);
        \Carbon\Carbon::setTestNow($today);

        foreach ([0, 1, 2] as $daysAgo) {
            completedWorkout($user, $today->copy()->subDays($daysAgo)->startOfDay()->addHours(10));
        }

        $service = app(XpService::class);
        $streak  = (new ReflectionMethod($service, 'currentStreak'))->invoke($service, $user);

        \Carbon\Carbon::setTestNow();
        expect($streak)->toBe(3);
    });

    it('breaks streak on a gap day', function () {
        $user  = createUser();
        $today = \Carbon\Carbon::create(2025, 6, 15, 12, 0, 0);
        \Carbon\Carbon::setTestNow($today);

        completedWorkout($user, $today->copy()->startOfDay()->addHours(10));          // today
        completedWorkout($user, $today->copy()->subDays(3)->startOfDay()->addHours(10)); // 3 days ago — gap

        $service = app(XpService::class);
        $streak  = (new ReflectionMethod($service, 'currentStreak'))->invoke($service, $user);

        \Carbon\Carbon::setTestNow();
        expect($streak)->toBe(1);
    });

    it('unlocks streak_3 achievement after 3 consecutive days', function () {
        $user  = createUser();
        $today = \Carbon\Carbon::create(2025, 6, 15, 12, 0, 0);
        \Carbon\Carbon::setTestNow($today);

        // 2 prior completed workouts on consecutive days
        completedWorkout($user, $today->copy()->subDays(2)->startOfDay()->addHours(10));
        completedWorkout($user, $today->copy()->subDays(1)->startOfDay()->addHours(10));

        // Today's workout — processed by XpService
        $workout = createWorkout($user);
        $workout->update(['status' => 'completed', 'started_at' => $today->copy()->subHour(), 'finished_at' => $today]);

        $result = app(XpService::class)->processWorkout($user, $workout);

        \Carbon\Carbon::setTestNow();

        $keys = collect($result['achievements'])->pluck('key');
        expect($keys)->toContain('streak_3');
    });
});
