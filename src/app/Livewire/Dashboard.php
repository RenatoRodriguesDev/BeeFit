<?php

namespace App\Livewire;

use Livewire\Component;
use Carbon\Carbon;
use App\Models\Workout;

class Dashboard extends Component
{
    public $currentMonth;
    public $workoutsByDate = [];
    public $selectedDate = null;
    public $selectedWorkouts = [];

    public function mount()
    {
        $this->currentMonth = Carbon::now()->startOfMonth();
        $this->loadWorkouts();
    }

    public function loadWorkouts()
    {
        $workouts = Workout::where('user_id', auth()->id())
            ->where('status', 'completed')
            ->get()
            ->groupBy(function ($workout) {
                return $workout->started_at->format('Y-m-d');
            });

        $this->workoutsByDate = $workouts->map(function ($dayWorkouts) {
            return $dayWorkouts->count();
        })->toArray();
    }

    public function previousMonth()
    {
        $this->currentMonth = $this->currentMonth->copy()->subMonth();
        $this->selectedWorkouts = null;
    }

    public function nextMonth()
    {
        $this->currentMonth = $this->currentMonth->copy()->addMonth();
        $this->selectedWorkouts = null;
    }

    public function selectDate($date)
    {
        $this->selectedDate = $date;

        $this->selectedWorkouts = Workout::where('user_id', auth()->id())
            ->whereDate('started_at', $date)
            ->where('status', 'completed')
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}