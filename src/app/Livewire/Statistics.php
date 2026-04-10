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

        // Histórico de força (weight) e cardio (distance) por exercício
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
                DB::raw('MAX(ws.distance_meters) as max_distance'),
                DB::raw('ROW_NUMBER() OVER (PARTITION BY we.exercise_id ORDER BY w.started_at DESC) as rn')
            )
            ->groupBy('we.exercise_id', 'w.id', 'w.started_at')
            ->get();

        $historyByExercise = $rankedRows
            ->where('rn', '<=', 8)
            ->groupBy('exercise_id')
            ->map(fn($rows) => $rows->sortBy('started_at')->values());

        $chartData = [];
        foreach ($records as $pr) {
            $history  = $historyByExercise->get($pr->exercise_id, collect());
            $isCardio = $pr->exercise?->isCardio();
            $chartData[$pr->exercise_id] = [
                'labels'   => $history->map(fn($row) => \Carbon\Carbon::parse($row->started_at)->format('d M'))->toArray(),
                'data'     => $isCardio
                    ? $history->map(fn($row) => $row->max_distance ? round($row->max_distance / 1000, 2) : null)->toArray()
                    : $history->map(fn($row) => (float) ($row->max_weight ?? 0))->toArray(),
                'isCardio' => $isCardio,
            ];
        }

        $strengthRecords = $records->filter(fn($pr) => ! $pr->exercise?->isCardio());
        $cardioRecords   = $records->filter(fn($pr) => $pr->exercise?->isCardio());

        $totals = [
            'count'        => $records->count(),
            'best_1rm'     => $strengthRecords->max('estimated_1rm') ?? 0,
            'max_weight'   => $strengthRecords->max('max_weight') ?? 0,
            'max_reps'     => $strengthRecords->max('max_reps') ?? 0,
            'max_distance' => $cardioRecords->max('max_distance') ?? 0,
            'best_pace'    => $cardioRecords->filter(fn($pr) => $pr->best_pace)->min('best_pace'),
        ];

        return view('livewire.statistics', compact('records', 'chartData', 'totals'))
            ->title(__('app.statistics'));
    }
}