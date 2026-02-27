<?php

namespace App\Livewire\Workout;

use Livewire\Component;
use App\Models\Workout;


class WorkoutShow extends Component
{
    public Workout $workout;

    public function mount(Workout $workout)
    {
        abort_if($workout->user_id !== auth()->id(), 403);

        $this->workout = $workout->load(
            'exercises.exercise',
            'exercises.sets'
        );
    }

    public function render()
    {
        return view('livewire.workout-show');
    }
    
}