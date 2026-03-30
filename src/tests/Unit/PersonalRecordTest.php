<?php

use App\Models\PersonalRecord;

describe('PersonalRecord::epley()', function () {

    it('returns weight unchanged for 1 rep', function () {
        expect(PersonalRecord::epley(100, 1))->toBe(100.0);
    });

    it('applies the epley formula for multiple reps', function () {
        // weight * (1 + reps/30)
        $expected = round(100 * (1 + 10 / 30), 2);
        expect(PersonalRecord::epley(100, 10))->toBe($expected);
    });

    it('rounds to 2 decimal places', function () {
        $result = PersonalRecord::epley(75, 7);
        expect($result)->toBe(round(75 * (1 + 7 / 30), 2));
    });

    it('increases with more reps at the same weight', function () {
        expect(PersonalRecord::epley(100, 5))->toBeLessThan(PersonalRecord::epley(100, 10));
    });
});

describe('PersonalRecord::updateFromWorkout()', function () {

    it('creates a new record when none exists', function () {
        $user     = createUser();
        $exercise = createExercise();
        $workout  = createWorkout($user);
        $we       = addExerciseToWorkout($workout, $exercise);

        $sets = $we->sets;

        PersonalRecord::updateFromWorkout($user->id, $exercise->id, $workout->id, $sets);

        $record = PersonalRecord::where('user_id', $user->id)
            ->where('exercise_id', $exercise->id)
            ->first();

        expect($record)->not->toBeNull()
            ->and($record->max_weight)->toBe(100.0)
            ->and($record->max_reps)->toBe(10);
    });

    it('skips sets with zero weight', function () {
        $user     = createUser();
        $exercise = createExercise();
        $workout  = createWorkout($user);
        $we       = addExerciseToWorkout($workout, $exercise);

        $we->sets()->update(['weight' => 0]);

        PersonalRecord::updateFromWorkout($user->id, $exercise->id, $workout->id, $we->sets()->get());

        expect(PersonalRecord::where('user_id', $user->id)->count())->toBe(0);
    });

    it('skips sets with zero reps', function () {
        $user     = createUser();
        $exercise = createExercise();
        $workout  = createWorkout($user);
        $we       = addExerciseToWorkout($workout, $exercise);

        $we->sets()->update(['reps' => 0]);

        PersonalRecord::updateFromWorkout($user->id, $exercise->id, $workout->id, $we->sets()->get());

        expect(PersonalRecord::where('user_id', $user->id)->count())->toBe(0);
    });

    it('updates the record when max weight improves', function () {
        $user     = createUser();
        $exercise = createExercise();
        $workout  = createWorkout($user);
        $we       = addExerciseToWorkout($workout, $exercise);

        PersonalRecord::create([
            'user_id'            => $user->id,
            'exercise_id'        => $exercise->id,
            'workout_id'         => $workout->id,
            'max_weight'         => 80,
            'reps_at_max_weight' => 10,
            'max_volume_set'     => 800,
            'max_reps'           => 10,
            'weight_at_max_reps' => 80,
            'estimated_1rm'      => PersonalRecord::epley(80, 10),
        ]);

        // sets have weight=100, reps=10 — improvement
        PersonalRecord::updateFromWorkout($user->id, $exercise->id, $workout->id, $we->sets()->get());

        $record = PersonalRecord::where('user_id', $user->id)->first();
        expect($record->max_weight)->toBe(100.0);
    });

    it('does not update when no metrics improve', function () {
        $user     = createUser();
        $exercise = createExercise();
        $workout  = createWorkout($user);
        $we       = addExerciseToWorkout($workout, $exercise);

        PersonalRecord::create([
            'user_id'            => $user->id,
            'exercise_id'        => $exercise->id,
            'workout_id'         => $workout->id,
            'max_weight'         => 200,
            'reps_at_max_weight' => 10,
            'max_volume_set'     => 2000,
            'max_reps'           => 20,
            'weight_at_max_reps' => 200,
            'estimated_1rm'      => 300,
        ]);

        // sets have weight=100, reps=10 — no improvement
        PersonalRecord::updateFromWorkout($user->id, $exercise->id, $workout->id, $we->sets()->get());

        $record = PersonalRecord::where('user_id', $user->id)->first();
        expect($record->max_weight)->toBe(200.0); // unchanged
    });

    it('stores estimated 1rm using epley formula', function () {
        $user     = createUser();
        $exercise = createExercise();
        $workout  = createWorkout($user);
        $we       = addExerciseToWorkout($workout, $exercise); // 100kg x 10 reps

        PersonalRecord::updateFromWorkout($user->id, $exercise->id, $workout->id, $we->sets()->get());

        $expected = PersonalRecord::epley(100, 10);
        $record   = PersonalRecord::where('user_id', $user->id)->first();

        expect($record->estimated_1rm)->toBe($expected);
    });
});
