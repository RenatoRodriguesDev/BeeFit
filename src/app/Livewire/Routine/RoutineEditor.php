<?php

namespace App\Livewire\Routine;

use Livewire\Component;
use App\Models\Routine;
use App\Models\RoutineExercise;
use App\Models\RoutineSet;

class RoutineEditor extends Component
{
    public Routine $routine;

    public ?int $expandedExerciseId = null;

    protected $rules = [
        'routine.exercises.*.sets.*.weight' => 'nullable|numeric',
        'routine.exercises.*.sets.*.reps' => 'nullable|integer',
    ];

    public function mount(Routine $routine)
    {
        abort_unless($routine->user_id === auth()->id(), 403);

        $this->routine = $routine->load([
            'exercises.exercise.translations',
            'exercises.sets'
        ]);
    }

    public function toggleExercise($exerciseId)
    {
        $this->expandedExerciseId =
            $this->expandedExerciseId === $exerciseId
            ? null
            : $exerciseId;
    }

    public function addSet($routineExerciseId)
    {
        $routineExercise = RoutineExercise::with('sets')
            ->findOrFail($routineExerciseId);

        $lastSetNumber = $routineExercise->sets->max('set_number') ?? 0;

        RoutineSet::create([
            'routine_exercise_id' => $routineExercise->id,
            'set_number' => $lastSetNumber + 1,
            'weight' => null,
            'reps' => null,
        ]);

        $this->refreshRoutine();
    }

    public function deleteSet($setId)
    {
        RoutineSet::findOrFail($setId)->delete();
        $this->refreshRoutine();
    }

    public function updateWeight($setId, $value)
    {
        RoutineSet::findOrFail($setId)->update([
            'weight' => $value
        ]);
    }

    public function updateReps($setId, $value)
    {
        $value = $value === '' ? null : (int) $value;

        RoutineSet::findOrFail($setId)->update([
            'reps' => $value
        ]);
    }

    private function refreshRoutine()
    {
        $this->routine = Routine::with([
            'exercises.exercise',
            'exercises.sets'
        ])->findOrFail($this->routine->id);
    }

    public function render()
    {
        return view('livewire.routine-editor');
    }
}