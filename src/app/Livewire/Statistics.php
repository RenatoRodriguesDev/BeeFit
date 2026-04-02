<?php

namespace App\Livewire;

use App\Models\PersonalRecord;
use App\Models\Workout;
use App\Models\WorkoutExercise;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

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

        // Carrega o histórico de TODOS os exercícios de uma vez (1 query em vez de N)
        $exerciseIds = $records->pluck('exercise_id')->all();

        // Subquery para obter os 8 treinos mais recentes por exercício
        $rankedRows = DB::table('workout_exercises as we')
            ->join('workouts as w', 'w.id', '=', 'we.workout_id')
            ->join('workout_sets as ws', 'ws.workout_exercise_id', '=', 'we.id')
            ->where('w.user_id', auth()->id())
            ->where('w.status', 'completed')
            ->whereIn('we.exercise_id', $exerciseIds)
            ->select(
                'we.exercise_id',
                'w.started_at',
                DB::raw('MAX(ws.weight) as max_weight'),
                DB::raw('ROW_NUMBER() OVER (PARTITION BY we.exercise_id ORDER BY w.started_at DESC) as rn')
            )
            ->groupBy('we.exercise_id', 'w.id', 'w.started_at')
            ->get();

        // Agrupa por exercício, mantém os 8 mais recentes, ordena cronologicamente
        $historyByExercise = $rankedRows
            ->where('rn', '<=', 8)
            ->groupBy('exercise_id')
            ->map(fn($rows) => $rows->sortBy('started_at')->values());

        $chartData = [];
        foreach ($records as $pr) {
            $history = $historyByExercise->get($pr->exercise_id, collect());
            $chartData[$pr->exercise_id] = [
                'labels' => $history->map(fn($row) => \Carbon\Carbon::parse($row->started_at)->format('d M'))->toArray(),
                'data'   => $history->map(fn($row) => (float) ($row->max_weight ?? 0))->toArray(),
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