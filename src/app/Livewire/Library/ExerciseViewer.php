<?php

namespace App\Livewire\Library;

use Livewire\Component;
use App\Models\Exercise;

class ExerciseViewer extends Component
{
    public $exercise;
    public $tab = 'howto';
    public $showRoutineModal = false;
    public $selectedRoutineId = null;

    protected $listeners = ['exerciseSelected'];

    public function openRoutineModal()
    {
        $this->showRoutineModal = true;
    }

    public function addToSelectedRoutine()
    {
        $user = auth()->user();

        if (!$user || !$this->selectedRoutineId) {
            return;
        }

        $routine = $user->routines()->find($this->selectedRoutineId);

        if (!$routine) {
            return;
        }

        // evitar duplicados
        if (
            !$routine->exercises()
                ->where('exercise_id', $this->exercise->id)
                ->exists()
        ) {

            $routine->exercises()->attach($this->exercise->id);
        }

        $this->showRoutineModal = false;
        $this->selectedRoutineId = null;

        session()->flash('added', 'Exercise added to routine!');
    }

    public function addToWorkout()
    {
        if (!$this->exercise) {
            return;
        }

        $user = auth()->user();

        if (!$user) {
            return;
        }

        $activeRoutine = $user->routines()
            ->where('is_active', true)
            ->first();

        if (!$activeRoutine) {
            session()->flash('added', 'No active routine found.');
            return;
        }

        // Evitar duplicados
        if (!$activeRoutine->exercises()->where('exercise_id', $this->exercise->id)->exists()) {
            $activeRoutine->exercises()->attach($this->exercise->id);
        }

        session()->flash('added', $this->exercise->translate()->name . ' added to routine');
    }

    public function setTab($tab)
    {
        $this->tab = $tab;
    }

    public function exerciseSelected($exerciseId)
    {
        $this->exercise = Exercise::with([
            'translations',
            'equipment.translations',
            'primaryMuscle.translations'
        ])->find($exerciseId);
    }

    public function render()
    {
        return view('livewire.exercise-viewer');
    }
}