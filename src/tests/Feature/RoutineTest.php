<?php

use App\Models\User;
use App\Models\Routine;
use App\Models\Workout;
use App\Models\WorkoutExercise;
use App\Models\WorkoutSet;
use Livewire\Livewire;
use App\Livewire\Routine\RoutineManager;
use App\Livewire\Routine\RoutineList;

describe('Routine creation', function () {

    it('authenticated user can create a routine', function () {
        $user = createUser();
        $this->actingAs($user);

        Livewire::test(RoutineManager::class)
            ->set('name', 'Push Day')
            ->call('createRoutine');

        expect(Routine::where('user_id', $user->id)->where('name', 'Push Day')->exists())->toBeTrue();
    });

    it('routine name must be at least 3 characters', function () {
        $user = createUser();
        $this->actingAs($user);

        Livewire::test(RoutineManager::class)
            ->set('name', 'AB')
            ->call('createRoutine')
            ->assertHasErrors(['name']);
    });

    it('routine name is required', function () {
        $user = createUser();
        $this->actingAs($user);

        Livewire::test(RoutineManager::class)
            ->set('name', '')
            ->call('createRoutine')
            ->assertHasErrors(['name']);
    });

    it('free user cannot exceed 3 routines', function () {
        $user = createUser(['plan' => 'free']);
        $this->actingAs($user);

        for ($i = 0; $i < 3; $i++) {
            $user->routines()->create(['name' => "Routine {$i}"]);
        }

        Livewire::test(RoutineManager::class)
            ->set('name', 'Fourth Routine')
            ->call('createRoutine');

        expect(Routine::where('user_id', $user->id)->count())->toBe(3);
    });

    it('premium user can create more than 3 routines', function () {
        $user = User::factory()->premium()->create();
        $this->actingAs($user);

        for ($i = 0; $i < 3; $i++) {
            $user->routines()->create(['name' => "Routine {$i}"]);
        }

        Livewire::test(RoutineManager::class)
            ->set('name', 'Fourth Routine')
            ->call('createRoutine');

        expect(Routine::where('user_id', $user->id)->count())->toBe(4);
    });

    it('creating a routine deactivates all others', function () {
        $user = createUser();
        $this->actingAs($user);

        $existing = $user->routines()->create(['name' => 'Old Routine', 'is_active' => true]);

        Livewire::test(RoutineManager::class)
            ->set('name', 'New Routine')
            ->call('createRoutine');

        expect($existing->fresh()->is_active)->toBeFalsy();
    });
});

describe('Routine deletion', function () {

    it('user can delete their own routine', function () {
        $user    = createUser();
        $routine = $user->routines()->create(['name' => 'My Routine']);
        $this->actingAs($user);

        Livewire::test(RoutineList::class)
            ->call('confirmDelete', $routine->id)
            ->call('deleteRoutine');

        expect(Routine::find($routine->id))->toBeNull();
    });

    it('user cannot delete another users routine', function () {
        $user  = createUser();
        $other = createUser();
        $routine = $other->routines()->create(['name' => 'Their Routine']);
        $this->actingAs($user);

        Livewire::test(RoutineList::class)
            ->call('confirmDelete', $routine->id)
            ->call('deleteRoutine');

        expect(Routine::find($routine->id))->not->toBeNull();
    });
});

describe('Starting a workout', function () {

    it('starting a workout clones the routine structure', function () {
        $user     = createUser();
        $exercise = createExercise();
        $this->actingAs($user);

        $routine = $user->routines()->create(['name' => 'Push', 'is_active' => true]);
        $re = $routine->exercises()->create(['exercise_id' => $exercise->id, 'order' => 1]);
        $re->sets()->create(['set_number' => 1, 'weight' => 80, 'reps' => 8]);

        Livewire::test(RoutineList::class)
            ->call('startWorkout', $routine->id);

        $workout = Workout::where('user_id', $user->id)->first();
        expect($workout)->not->toBeNull();

        $we = WorkoutExercise::where('workout_id', $workout->id)->first();
        expect($we)->not->toBeNull();

        $set = WorkoutSet::where('workout_exercise_id', $we->id)->first();
        expect($set)->not->toBeNull()
            ->and((float) $set->weight)->toBe(80.0)
            ->and((int) $set->reps)->toBe(8);
    });
});
