<?php

namespace App\Livewire\Workout;

use Livewire\Component;
use App\Models\Workout;


class ActiveWorkoutBanner extends Component
{

    public $activeWorkout = null;

    public function mount()
    {
        $this->activeWorkout = Workout::where('user_id', auth()->id())
            ->whereIn('status', ['active', 'paused'])
            ->first();
    }

    public function render()
    {
        return view('livewire.active-workout-banner');
    }

}