<?php

use App\Models\Equipment;
use App\Models\Exercise;
use App\Models\ExerciseTranslation;
use App\Models\Follow;
use App\Models\Muscle;
use App\Models\Routine;
use App\Models\User;
use App\Models\Workout;
use App\Models\WorkoutExercise;
use App\Models\WorkoutSet;
use Illuminate\Foundation\Testing\RefreshDatabase;

pest()->extend(Tests\TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature', 'Unit');

// ── Helpers ────────────────────────────────────────────────────────────────

function createUser(array $attrs = []): User
{
    return User::factory()->create($attrs);
}

function createExercise(): Exercise
{
    $equipment = Equipment::create([]);
    $muscle    = Muscle::create([]);

    $exercise = Exercise::create([
        'equipment_id'      => $equipment->id,
        'primary_muscle_id' => $muscle->id,
    ]);

    ExerciseTranslation::create([
        'exercise_id' => $exercise->id,
        'locale'      => 'en',
        'name'        => 'Test Exercise',
    ]);

    return $exercise;
}

function createWorkout(User $user, string $status = 'active'): Workout
{
    $routine = $user->routines()->create(['name' => 'Test Routine', 'is_active' => true]);

    return Workout::create([
        'user_id'    => $user->id,
        'routine_id' => $routine->id,
        'started_at' => now(),
        'status'     => $status,
    ]);
}

function addExerciseToWorkout(Workout $workout, ?Exercise $exercise = null): WorkoutExercise
{
    $exercise ??= createExercise();

    $we = WorkoutExercise::create([
        'workout_id'  => $workout->id,
        'exercise_id' => $exercise->id,
        'order'       => $workout->exercises()->count() + 1,
    ]);

    WorkoutSet::create([
        'workout_exercise_id' => $we->id,
        'set_number'          => 1,
        'weight'              => 100,
        'reps'                => 10,
    ]);

    return $we;
}

function follow(User $follower, User $following, string $status = 'accepted'): Follow
{
    return Follow::create([
        'follower_id'  => $follower->id,
        'following_id' => $following->id,
        'status'       => $status,
    ]);
}
