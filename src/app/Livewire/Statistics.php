<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PersonalRecord;
use App\Models\WorkoutExercise;

class Statistics extends Component
{
    public string $search = '';

    public function render()
    {
        $records = PersonalRecord::where('user_id', auth()->id())
            ->with(['exercise.translations', 'workout'])
            ->get()
            ->filter(function ($pr) {
                if (!$this->search) return true;
                $name = $pr->exercise?->translate()?->name ?? '';
                return str_contains(strtolower($name), strtolower($this->search));
            })
            ->sortByDesc('estimated_1rm')
            ->values();

        // Para cada exercício, busca os últimos 8 treinos para o mini-gráfico
        $chartData = [];
        foreach ($records as $pr) {
            $history = WorkoutExercise::where('exercise_id', $pr->exercise_id)
                ->whereHas('workout', fn($q) => $q
                    ->where('user_id', auth()->id())
                    ->where('status', 'completed')
                )
                ->with(['workout', 'sets'])
                ->orderByDesc(
                    \App\Models\Workout::select('started_at')
                        ->whereColumn('id', 'workout_exercises.workout_id')
                )
                ->limit(8)
                ->get()
                ->sortBy('workout.started_at')
                ->values();

            $chartData[$pr->exercise_id] = [
                'labels' => $history->map(fn($we) => $we->workout->started_at->format('d M'))->toArray(),
                'data'   => $history->map(fn($we) => (float) $we->sets->max('weight'))->toArray(),
            ];
        }

        $totals = [
            'count'      => $records->count(),
            'best_1rm'   => $records->max('estimated_1rm') ?? 0,
            'max_weight' => $records->max('max_weight') ?? 0,
            'max_reps'   => $records->max('max_reps') ?? 0,
        ];

        return view('livewire.statistics', compact('records', 'chartData', 'totals'));
    }
}