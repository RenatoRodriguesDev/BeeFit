<?php

namespace App\Livewire\Library;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Exercise;
use App\Models\Equipment;
use App\Models\Muscle;

class ExerciseLibrary extends Component
{
    use WithPagination;

    public $equipment = null;
    public $muscle = null;
    public $search = '';

    protected $paginationTheme = 'tailwind';

    public function updatingSearch() { $this->resetPage(); }
    public function updatingEquipment() { $this->resetPage(); }
    public function updatingMuscle() { $this->resetPage(); }

    public function render()
    {
        $exercises = Exercise::query()
            ->with(['translations', 'equipment.translations', 'primaryMuscle.translations'])
            ->where(function ($q) {
                $q->whereNull('created_by')
                  ->orWhere('created_by', auth()->id());
            })
            ->when($this->equipment, fn($q) =>
                $q->where('equipment_id', $this->equipment)
            )
            ->when($this->muscle, fn($q) =>
                $q->where('primary_muscle_id', $this->muscle)
            )
            ->when($this->search, fn($q) =>
                $q->whereHas('translations', function ($q) {
                    $q->where('locale', app()->getLocale())
                      ->where('name', 'like', "%{$this->search}%");
                })
            )
            ->paginate(15);

        return view('livewire.library.exercise-library', [
            'exercises' => $exercises,
            'equipments' => Equipment::with('translations')->get(),
            'muscles' => Muscle::with('translations')->get(),
        ]);
    }
}