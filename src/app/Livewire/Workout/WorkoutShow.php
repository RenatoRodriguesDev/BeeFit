<?php

namespace App\Livewire\Workout;

use Livewire\Component;
use App\Models\PersonalRecord;
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
        $exerciseIds = $this->workout->exercises->pluck('exercise_id');

        $records = PersonalRecord::where('workout_id', $this->workout->id)
            ->whereIn('exercise_id', $exerciseIds)
            ->get()
            ->keyBy('exercise_id');

        $duration = $this->workout->finished_at
            ? (int) $this->workout->started_at->diffInMinutes($this->workout->finished_at)
            : null;

        $totalSets   = $this->workout->exercises->sum(fn ($e) => $e->sets->count());
        $totalVolume = $this->workout->exercises->sum(
            fn ($e) => $e->sets->sum(fn ($s) => ($s->weight ?? 0) * ($s->reps ?? 0))
        );

        return view('livewire.workout-show', compact('records', 'duration', 'totalSets', 'totalVolume'));
    }
    
}