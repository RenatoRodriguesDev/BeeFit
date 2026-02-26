<?php

namespace App\Livewire\Library;

use Livewire\Component;
use App\Models\Exercise;

class ExerciseViewer extends Component
{
    public $exercise;
    public $tab = 'howto';

    protected $listeners = ['exerciseSelected'];

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
            session()->flash('added', __('app.no_active_routine'));
            return;
        }

        // Evitar duplicados
        if (!$activeRoutine->exercises()->where('exercise_id', $this->exercise->id)->exists()) {
            $activeRoutine->exercises()->attach($this->exercise->id);
        }

        session()->flash('added', $this->exercise->translate()->name . __('app.added_to_routine'));
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