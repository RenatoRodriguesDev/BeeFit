<?php

namespace App\Livewire\Library;

use Livewire\Component;
use App\Models\Exercise;
use App\Models\Equipment;
use App\Models\Muscle;

class LibraryPanel extends Component
{
    public $search = '';
    public $equipment = '';
    public $muscle = '';
    public $activeExerciseId = null;

    public $showRoutineModal = false;
    public $selectedRoutineId = null;
    public $exerciseToAddId = null;

    protected $listeners = ['exerciseSelected'];

    public function exerciseSelected($exerciseId)
    {
        $this->activeExerciseId = $exerciseId;
    }

    public function selectExercise(int $exerciseId): void
    {
        $this->activeExerciseId = $exerciseId;
        $this->dispatch('exerciseSelected', exerciseId: $exerciseId);
        $this->js("window.dispatchEvent(new CustomEvent('exercise-clicked'))");
    }

    public function openRoutineModal($exerciseId)
    {
        $this->exerciseToAddId = $exerciseId;
        $this->showRoutineModal = true;
    }

    public function addToSelectedRoutine()
    {
        $user = auth()->user();

        if (!$user || !$this->selectedRoutineId || !$this->exerciseToAddId) {
            return;
        }

        $routine = $user->routines()->find($this->selectedRoutineId);

        if (!$routine) return;

        $exists = $routine->exercises()
            ->where('exercise_id', $this->exerciseToAddId)
            ->exists();

        if ($exists) {
            $this->dispatch('toast', message: __('app.exercise_duplicated'), type: 'error');
        } else {
            $routine->exercises()->create([
                'exercise_id' => $this->exerciseToAddId,
                'order'       => $routine->exercises()->count() + 1,
            ]);
            $this->dispatch('toast', message: __('app.exercise_added_toast'), type: 'success');
        }

        $this->showRoutineModal = false;
        $this->selectedRoutineId = null;
        $this->exerciseToAddId = null;
    }

    public function render()
    {
        $query = Exercise::query()
            ->with([
                'translations',
                'equipment.translations',
                'primaryMuscle.translations',
            ]);

        if ($this->equipment) {
            $query->where('equipment_id', $this->equipment);
        }

        if ($this->muscle) {
            $query->where('primary_muscle_id', $this->muscle);
        }

        if ($this->search) {
            $term = strtolower($this->search);
            $query->whereHas('translations', function ($q) use ($term) {
                $q->whereIn('locale', [app()->getLocale(), config('app.fallback_locale')])
                  ->whereRaw('LOWER(name) LIKE ?', ['%' . $term . '%']);
            });
        }

        return view('livewire.library-panel', [
            'exercises'     => $query->get(),
            'equipmentList' => Equipment::with('translations')->get(),
            'musclesList'   => Muscle::with('translations')->get(),
        ]);
    }
}
