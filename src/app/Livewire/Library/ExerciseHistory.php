<?php

namespace App\Livewire\Library;

use Livewire\Component;
use App\Models\WorkoutExercise;
use App\Models\PersonalRecord;

class ExerciseHistory extends Component
{
    public int $exerciseId;

    public function mount(int $exerciseId): void
    {
        $this->exerciseId = $exerciseId;
    }

    public function render()
    {
        $history = WorkoutExercise::where('exercise_id', $this->exerciseId)
            ->whereHas('workout', fn($q) => $q
                ->where('user_id', auth()->id())
                ->where('status', 'completed')
            )
            ->with(['workout', 'sets' => fn($q) => $q->orderBy('set_number')])
            ->orderByDesc(
                \App\Models\Workout::select('started_at')
                    ->whereColumn('id', 'workout_exercises.workout_id')
            )
            ->limit(10)
            ->get();

        $pr = PersonalRecord::where('user_id', auth()->id())
            ->where('exercise_id', $this->exerciseId)
            ->first();

        // Dados para o gráfico: peso máximo de cada sessão (ordem cronológica)
        $chartData = $history->sortBy('workout.started_at')->map(fn($we) => [
            'label' => $we->workout->started_at->format('d M'),
            'max'   => (float) $we->sets->max('weight'),
        ])->values();

        return view('livewire.exercise-history', compact('history', 'pr', 'chartData'));
    }
}