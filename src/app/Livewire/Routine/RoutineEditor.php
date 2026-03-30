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
    public ?int $exerciseToDelete = null;
    public bool $showDeleteExerciseModal = false;

    protected $listeners = ['reorderExercises'];

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

    public function reorderExercises(array $order): void
    {
        foreach ($order as $item) {
            RoutineExercise::where('id', $item['id'])
                ->where('routine_id', $this->routine->id)
                ->update(['order' => $item['order']]);
        }

        $this->refreshRoutine();
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
        $set = RoutineSet::findOrFail($setId);

        $totalSets = RoutineSet::where(
            'routine_exercise_id',
            $set->routine_exercise_id
        )->count();

        if ($totalSets <= 1) {
            return;
        }

        $set->delete();

        $this->refreshRoutine();
    }

    public function updateWeight($setId, $value)
    {
        $value = $value === '' ? null : (int) $value;

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
            'exercises.exercise.translations',
            'exercises.sets'
        ])->findOrFail($this->routine->id);
    }

    public function confirmDeleteExercise($exerciseId)
    {
        $this->exerciseToDelete = $exerciseId;
        $this->showDeleteExerciseModal = true;
    }

    public function closeDeleteExerciseModal()
    {
        $this->reset(['showDeleteExerciseModal', 'exerciseToDelete']);
    }

    public function deleteExercise()
    {
        if (!$this->exerciseToDelete) {
            return;
        }

        $routineExercise = RoutineExercise::with('sets')
            ->findOrFail($this->exerciseToDelete);

        foreach ($routineExercise->sets as $set) {
            $set->delete();
        }

        $routineExercise->delete();

        $this->refreshRoutine();

        $this->dispatch(
            'toast',
            message: __('app.exercise_deleted_success'),
            type: 'success'
        );

        $this->closeDeleteExerciseModal();
    }

    public function render()
    {
        return view('livewire.routine-editor');
    }
}
