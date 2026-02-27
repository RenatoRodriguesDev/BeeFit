<?php

namespace App\Livewire\Workout;

use Livewire\Component;
use App\Models\Workout;
use App\Models\WorkoutExercise;
use App\Models\WorkoutSet;


class WorkoutSession extends Component
{
    public Workout $workout;

    public function mount(Workout $workout)
    {
        abort_unless($workout->user_id === auth()->id(), 403);

        $this->workout = $workout->load(
            'exercises.exercise',
            'exercises.sets'
        );
    }

    public function updateWeight($setId, $value)
    {
        WorkoutSet::findOrFail($setId)
            ->update(['weight' => $value ?: null]);
    }

    public function updateReps($setId, $value)
    {
        WorkoutSet::findOrFail($setId)
            ->update(['reps' => $value ?: null]);
    }

    public function pauseWorkout()
    {
        $this->workout->update([
            'status' => 'paused',
            'paused_at' => now(),
        ]);

        $this->workout->refresh();
    }

    public function resumeWorkout()
    {
        $this->workout->update([
            'status' => 'active',
            'paused_at' => null,
        ]);

        $this->workout->refresh();
    }

    public function cancelWorkout()
    {
        $this->workout->update([
            'status' => 'cancelled',
            'finished_at' => now(),
        ]);

        return redirect()->route('routines.index');
    }

    public function finishWorkout()
    {
        $this->workout->update([
            'status' => 'completed',
            'finished_at' => now(),
        ]);

        return redirect()->route('dashboard');
    }

    public function addSet($workoutExerciseId)
    {
        $workoutExercise = WorkoutExercise::findOrFail($workoutExerciseId);

        $lastSetNumber = $workoutExercise->sets()->max('set_number') ?? 0;

        WorkoutSet::create([
            'workout_exercise_id' => $workoutExercise->id,
            'set_number' => $lastSetNumber + 1,
            'weight' => 0,
            'reps' => 0,
        ]);

        $this->workout->refresh();
    }

    public function removeSet($setId)
    {
        $set = WorkoutSet::findOrFail($setId);
        $exercise = $set->workoutExercise;

        $set->delete();

        // Reordenar números
        $exercise->sets()->orderBy('set_number')
            ->get()
            ->values()
            ->each(function ($set, $index) {
                $set->update([
                    'set_number' => $index + 1
                ]);
            });

        $this->workout->refresh();
    }

    public function removeExercise($workoutExerciseId)
    {
        $exercise = WorkoutExercise::findOrFail($workoutExerciseId);

        $exercise->delete(); // se tiver cascade deletes melhor ainda

        $this->workout->refresh();
    }

    public function render()
    {
        return view('livewire.workout-session');
    }
}