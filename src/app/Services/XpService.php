<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\User;
use App\Models\Workout;

class XpService
{
    /**
     * Award XP for a completed workout and check achievements.
     * Returns ['xp' => int, 'achievements' => Achievement[]]
     */
    public function processWorkout(User $user, Workout $workout): array
    {
        $xpEarned      = 0;
        $newAchievements = [];

        $workout->load('exercises.sets', 'exercises.exercise');

        // ── XP per workout ────────────────────────────────────────────
        $xpEarned += 50;

        // ── XP per completed set ──────────────────────────────────────
        $totalSets = $workout->exercises->sum(fn($e) => $e->sets->count());
        $xpEarned += $totalSets * 5;

        // ── XP for duration bonus ──────────────────────────────────────
        $minutes = $workout->started_at->diffInMinutes($workout->finished_at ?? now());
        if ($minutes >= 60) $xpEarned += 30;
        if ($minutes >= 90) $xpEarned += 20;

        // ── XP for early bird / night owl ─────────────────────────────
        $hour = now()->hour;
        if ($hour < 7)  $xpEarned += 20;
        if ($hour >= 22) $xpEarned += 20;

        // ── XP for new exercise (never done before this workout) ───────
        $previousExerciseIds = Workout::where('user_id', $user->id)
            ->where('id', '!=', $workout->id)
            ->where('status', 'completed')
            ->with('exercises:id,workout_id,exercise_id')
            ->get()
            ->pluck('exercises')
            ->flatten()
            ->pluck('exercise_id')
            ->unique()
            ->toArray();

        foreach ($workout->exercises as $we) {
            if (! in_array($we->exercise_id, $previousExerciseIds)) {
                $xpEarned += 30;
            }
        }

        // ── Award XP ──────────────────────────────────────────────────
        $user->addXp($xpEarned);

        // ── Check achievements ────────────────────────────────────────
        $newAchievements = $this->checkAchievements($user, $workout, $minutes);

        // Award achievement XP
        foreach ($newAchievements as $achievement) {
            $user->addXp($achievement->xp_reward);
            $xpEarned += $achievement->xp_reward;
        }

        return [
            'xp'           => $xpEarned,
            'achievements' => $newAchievements,
            'level_before' => $user->fresh()->level() - ($xpEarned > 0 ? 0 : 0), // checked after adding
        ];
    }

    private function checkAchievements(User $user, Workout $workout, int $minutes): array
    {
        $unlocked  = [];
        $completed = Workout::where('user_id', $user->id)->where('status', 'completed')->count();

        $this->tryUnlock($user, $unlocked, 'first_workout', $completed >= 1);
        $this->tryUnlock($user, $unlocked, 'workouts_10',   $completed >= 10);
        $this->tryUnlock($user, $unlocked, 'workouts_50',   $completed >= 50);
        $this->tryUnlock($user, $unlocked, 'workouts_100',  $completed >= 100);
        $this->tryUnlock($user, $unlocked, 'workouts_365',  $completed >= 365);

        // Streak
        $streak = $this->currentStreak($user);
        $this->tryUnlock($user, $unlocked, 'streak_3',  $streak >= 3);
        $this->tryUnlock($user, $unlocked, 'streak_7',  $streak >= 7);
        $this->tryUnlock($user, $unlocked, 'streak_30', $streak >= 30);

        // Time of day
        $hour = now()->hour;
        $this->tryUnlock($user, $unlocked, 'early_bird', $hour < 7);
        $this->tryUnlock($user, $unlocked, 'night_owl',  $hour >= 22);

        // Duration
        $this->tryUnlock($user, $unlocked, 'speed_demon', $minutes > 0 && $minutes < 20);
        $this->tryUnlock($user, $unlocked, 'marathon',    $minutes >= 90);

        // First routine
        $this->tryUnlock($user, $unlocked, 'first_routine', $user->routines()->count() >= 1);

        // Variety: 10+ distinct exercises ever
        $distinctExercises = Workout::where('user_id', $user->id)
            ->where('status', 'completed')
            ->with('exercises:id,workout_id,exercise_id')
            ->get()->pluck('exercises')->flatten()
            ->pluck('exercise_id')->unique()->count();
        $this->tryUnlock($user, $unlocked, 'variety', $distinctExercises >= 10);

        // Heavy lifter: any set with weight >= 100kg
        $heavySet = $workout->exercises->flatMap->sets->first(fn($s) => ($s->weight ?? 0) >= 100);
        $this->tryUnlock($user, $unlocked, 'heavy_lifter', $heavySet !== null);

        // Cardio king: 10+ cardio exercises done ever
        $cardioCount = Workout::where('user_id', $user->id)
            ->where('status', 'completed')
            ->with('exercises.exercise')
            ->get()
            ->pluck('exercises')->flatten()
            ->filter(fn($e) => $e->exercise?->isCardio())
            ->count();
        $this->tryUnlock($user, $unlocked, 'cardio_king', $cardioCount >= 10);

        // PRs
        $prCount = \App\Models\PersonalRecord::where('user_id', $user->id)->count();
        $this->tryUnlock($user, $unlocked, 'first_pr', $prCount >= 1);
        $this->tryUnlock($user, $unlocked, 'pr_10',    $prCount >= 10);

        return $unlocked;
    }

    private function tryUnlock(User $user, array &$unlocked, string $key, bool $condition): void
    {
        if (! $condition) return;
        if ($user->hasAchievement($key)) return;

        $achievement = Achievement::where('key', $key)->first();
        if (! $achievement) return;

        $user->achievements()->attach($achievement->id, ['unlocked_at' => now()]);
        $unlocked[] = $achievement;
    }

    private function currentStreak(User $user): int
    {
        $dates = Workout::where('user_id', $user->id)
            ->where('status', 'completed')
            ->orderByDesc('finished_at')
            ->pluck('finished_at')
            ->map(fn($d) => $d->toDateString())
            ->unique()
            ->values();

        if ($dates->isEmpty()) return 0;

        $streak = 1;
        $today  = now()->toDateString();
        $first  = $dates->first();

        // Must have trained today or yesterday to have active streak
        if ($first !== $today && $first !== now()->subDay()->toDateString()) {
            return 0;
        }

        for ($i = 0; $i < $dates->count() - 1; $i++) {
            $current  = \Carbon\Carbon::parse($dates[$i]);
            $previous = \Carbon\Carbon::parse($dates[$i + 1]);
            if ((int) abs($current->diffInDays($previous)) === 1) {
                $streak++;
            } else {
                break;
            }
        }

        return $streak;
    }
}
