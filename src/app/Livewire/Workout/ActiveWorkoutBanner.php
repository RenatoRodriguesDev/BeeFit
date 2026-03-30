<?php

namespace App\Livewire\Workout;

use Livewire\Component;
use App\Models\Workout;


class ActiveWorkoutBanner extends Component
{

    public $activeWorkout = null;

    public function mount()
    {
        $activeWorkout = Workout::where('user_id', auth()->id())
            ->whereIn('status', ['active', 'paused'])
            ->first();

        // Não mostrar o banner se já estivermos na página da sessão
        if ($activeWorkout && request()->routeIs('workouts.session')) {
            return;
        }

        $this->activeWorkout = $activeWorkout;
    }

    public function render()
    {
        return view('livewire.active-workout-banner');
    }

}