<?php

namespace App\Livewire\Routine;

use App\Models\Workout;
use App\Models\WorkoutExercise;
use App\Models\WorkoutSet;
use Livewire\Component;
use App\Models\Routine;
use Illuminate\Support\Facades\Auth;

class RoutineList extends Component
{
    public $showDeleteModal = false;
    public $routineToDelete;

    protected $listeners = ['refreshRoutines' => '$refresh'];

    public function confirmDelete($id)
    {
        $this->routineToDelete = $id;
        $this->showDeleteModal = true;
    }

    public function deleteRoutine()
    {
        Routine::where('user_id', Auth::id())
            ->where('id', $this->routineToDelete)
            ->delete();

        $this->reset(['showDeleteModal', 'routineToDelete']);

        $this->dispatch(
            'toast',
            message: __('app.routine_deleted_success'),
            type: 'success'
        );

        $this->dispatch('refreshRoutines');
    }

    public function closeDeleteModal()
    {
        $this->reset(['showDeleteModal', 'routineToDelete']);
    }

    public function startWorkout($routineId)
    {
        $routine = Routine::with('exercises.sets')
            ->where('user_id', auth()->id())
            ->findOrFail($routineId);

        $activeWorkout = Workout::where('user_id', auth()->id())
            ->whereIn('status', ['active', 'paused'])
            ->first();

        if ($activeWorkout) {
            return redirect()->route('workouts.session', $activeWorkout);
        }

        $workout = Workout::create([
            'user_id' => auth()->id(),
            'routine_id' => $routine->id,
            'started_at' => now(),
            'status' => 'active',
        ]);

        foreach ($routine->exercises as $routineExercise) {

            $workoutExercise = WorkoutExercise::create([
                'workout_id' => $workout->id,
                'exercise_id' => $routineExercise->exercise_id,
                'order' => $routineExercise->order,
            ]);

            $sets = $routineExercise->sets;


            foreach ($sets as $set) {
                WorkoutSet::create([
                    'workout_exercise_id' => $workoutExercise->id,
                    'set_number' => $set->set_number,
                    'weight' => $set->weight,
                    'reps' => $set->reps,
                ]);
            }

        }

        return redirect()->route('workouts.session', $workout);
    }

    public function render()
    {
        return view('livewire.routine-list', [
            'routines' => Routine::where('user_id', Auth::id())
                ->withCount('exercises')
                ->get()
        ]);
    }
}