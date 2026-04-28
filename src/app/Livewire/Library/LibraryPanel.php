<?php

namespace App\Livewire\Library;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
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
    public int $limit = 30;

    public $showRoutineModal = false;
    public $selectedRoutineId = null;
    public $exerciseToAddId = null;

    protected $listeners = ['exerciseSelected'];

    public function updatedSearch(): void { $this->limit = 30; }
    public function updatedEquipment(): void { $this->limit = 30; }
    public function updatedMuscle(): void { $this->limit = 30; }

    public function loadMore(): void
    {
        $this->limit += 30;
    }

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
        $locale   = app()->getLocale();
        $fallback = config('app.fallback_locale');

        $equipmentList = Cache::remember("equipment_list_{$locale}", 3600, fn () =>
            Equipment::with('translations')->get()
        );

        $musclesList = Cache::remember("muscles_list_{$locale}", 3600, fn () =>
            Muscle::with('translations')->get()
        );

        $query = Exercise::query()
            ->with(['translations', 'equipment.translations', 'primaryMuscle.translations']);

        if ($this->equipment) {
            $query->where('equipment_id', $this->equipment);
        }

        if ($this->muscle) {
            $query->where('primary_muscle_id', $this->muscle);
        }

        if ($this->search) {
            $term = $this->search;
            $query->whereHas('translations', function ($q) use ($term, $locale, $fallback) {
                $q->whereIn('locale', [$locale, $fallback]);
                if (DB::getDriverName() === 'mysql') {
                    $q->whereRaw('MATCH(name) AGAINST(? IN BOOLEAN MODE)', ["{$term}*"]);
                } else {
                    $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($term) . '%']);
                }
            });
        }

        $total   = $query->count();
        $exercises = $query->limit($this->limit)->get();
        $hasMore = $total > $this->limit;

        return view('livewire.library-panel', compact(
            'exercises', 'equipmentList', 'musclesList', 'hasMore', 'total'
        ))->title(__('app.library'));
    }
}
