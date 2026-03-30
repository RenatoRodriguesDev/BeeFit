<?php

use App\Models\PersonalRecord;
use App\Models\Workout;
use App\Models\WorkoutExercise;
use App\Models\WorkoutSet;
use Livewire\Livewire;
use App\Livewire\Workout\WorkoutSession;

describe('WorkoutSession', function () {

    it('can pause an active workout', function () {
        $user    = createUser();
        $workout = createWorkout($user);
        $this->actingAs($user);

        Livewire::test(WorkoutSession::class, ['workout' => $workout])
            ->call('pauseWorkout');

        expect($workout->fresh()->status)->toBe('paused')
            ->and($workout->fresh()->paused_at)->not->toBeNull();
    });

    it('can resume a paused workout', function () {
        $user    = createUser();
        $workout = createWorkout($user, 'paused');
        $this->actingAs($user);

        Livewire::test(WorkoutSession::class, ['workout' => $workout])
            ->call('resumeWorkout');

        expect($workout->fresh()->status)->toBe('active')
            ->and($workout->fresh()->paused_at)->toBeNull();
    });

    it('can cancel a workout', function () {
        $user    = createUser();
        $workout = createWorkout($user);
        $this->actingAs($user);

        Livewire::test(WorkoutSession::class, ['workout' => $workout])
            ->call('cancelWorkout');

        expect(Workout::find($workout->id))->toBeNull();
    });

    it('finishing a workout marks it as completed', function () {
        $user    = createUser();
        $workout = createWorkout($user);
        $this->actingAs($user);

        Livewire::test(WorkoutSession::class, ['workout' => $workout])
            ->call('finishWorkout', false);

        expect($workout->fresh()->status)->toBe('completed')
            ->and($workout->fresh()->finished_at)->not->toBeNull();
    });

    it('finishing a workout computes personal records', function () {
        $user     = createUser();
        $exercise = createExercise();
        $workout  = createWorkout($user);
        addExerciseToWorkout($workout, $exercise); // 100kg x 10 reps
        $this->actingAs($user);

        Livewire::test(WorkoutSession::class, ['workout' => $workout])
            ->call('finishWorkout', false);

        expect(
            PersonalRecord::where('user_id', $user->id)
                ->where('exercise_id', $exercise->id)
                ->exists()
        )->toBeTrue();
    });

    it('can add a set to an exercise', function () {
        $user    = createUser();
        $workout = createWorkout($user);
        $we      = addExerciseToWorkout($workout);
        $this->actingAs($user);

        $countBefore = $we->sets()->count();

        Livewire::test(WorkoutSession::class, ['workout' => $workout])
            ->call('addSet', $we->id);

        expect($we->sets()->count())->toBe($countBefore + 1);
    });

    it('added set has an incremented set number', function () {
        $user    = createUser();
        $workout = createWorkout($user);
        $we      = addExerciseToWorkout($workout);
        $this->actingAs($user);

        Livewire::test(WorkoutSession::class, ['workout' => $workout])
            ->call('addSet', $we->id);

        $lastSet = $we->sets()->orderByDesc('set_number')->first();
        expect($lastSet->set_number)->toBe(2);
    });

    it('can remove a set and renumbers remaining sets', function () {
        $user    = createUser();
        $workout = createWorkout($user);
        $we      = addExerciseToWorkout($workout);

        // Add a second set
        WorkoutSet::create(['workout_exercise_id' => $we->id, 'set_number' => 2, 'weight' => 90, 'reps' => 8]);

        $firstSet = $we->sets()->orderBy('set_number')->first();
        $this->actingAs($user);

        Livewire::test(WorkoutSession::class, ['workout' => $workout])
            ->call('removeSet', $firstSet->id);

        $remaining = $we->sets()->orderBy('set_number')->get();
        expect($remaining->count())->toBe(1)
            ->and($remaining->first()->set_number)->toBe(1);
    });

    it('can remove an exercise from the workout', function () {
        $user    = createUser();
        $workout = createWorkout($user);
        $we      = addExerciseToWorkout($workout);
        $this->actingAs($user);

        Livewire::test(WorkoutSession::class, ['workout' => $workout])
            ->call('removeExercise', $we->id);

        expect(WorkoutExercise::find($we->id))->toBeNull();
    });

    it('can update the weight of a set', function () {
        $user    = createUser();
        $workout = createWorkout($user);
        $we      = addExerciseToWorkout($workout);
        $set     = $we->sets()->first();
        $this->actingAs($user);

        Livewire::test(WorkoutSession::class, ['workout' => $workout])
            ->call('updateWeight', $set->id, 120);

        expect((float) $set->fresh()->weight)->toBe(120.0);
    });

    it('can update the reps of a set', function () {
        $user    = createUser();
        $workout = createWorkout($user);
        $we      = addExerciseToWorkout($workout);
        $set     = $we->sets()->first();
        $this->actingAs($user);

        Livewire::test(WorkoutSession::class, ['workout' => $workout])
            ->call('updateReps', $set->id, 12);

        expect((int) $set->fresh()->reps)->toBe(12);
    });

    it('aborts 403 when accessing another users workout', function () {
        $owner = createUser();
        $other = createUser();
        $workout = createWorkout($owner);
        $this->actingAs($other);

        Livewire::test(WorkoutSession::class, ['workout' => $workout])
            ->assertStatus(403);
    });
});
