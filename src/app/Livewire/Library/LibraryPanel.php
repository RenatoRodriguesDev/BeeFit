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

    protected $listeners = ['exerciseSelected'];

    public function exerciseSelected($exerciseId)
    {
        $this->activeExerciseId = $exerciseId;
    }

    public function render()
    {
        $query = Exercise::query()
            ->with([
                'translations',
                'equipment.translations',
                'primaryMuscle.translations'
            ]);

        if ($this->equipment) {
            $query->where('equipment_id', $this->equipment);
        }

        if ($this->muscle) {
            $query->where('primary_muscle_id', $this->muscle);
        }

        if ($this->search) {
            $query->whereHas('translations', function ($q) {
                $q->where('locale', app()->getLocale())
                    ->where('name', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.library-panel', [
            'exercises' => $query->limit(20)->get(),
            'equipmentList' => Equipment::with('translations')->get(),
            'musclesList' => Muscle::with('translations')->get(),
        ]);
    }
}