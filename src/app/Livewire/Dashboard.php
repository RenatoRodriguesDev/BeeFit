<?php

namespace App\Livewire;

use Livewire\Attributes\Lazy;
use Livewire\Component;
use Carbon\Carbon;
use App\Models\PersonalRecord;
use App\Models\Workout;
use App\Models\WorkoutSet;

#[Lazy]
class Dashboard extends Component
{
    public $currentMonth;
    public $workoutsByDate  = [];
    public $selectedDate    = null;
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
            ->groupBy(fn ($w) => $w->started_at->format('Y-m-d'));

        $this->workoutsByDate = $workouts->map(fn ($g) => $g->count())->toArray();
    }

    public function previousMonth()
    {
        $this->currentMonth   = $this->currentMonth->copy()->subMonth();
        $this->selectedWorkouts = null;
    }

    public function nextMonth()
    {
        $this->currentMonth   = $this->currentMonth->copy()->addMonth();
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

    public function placeholder(): string
    {
        return <<<'HTML'
        <div class="space-y-6 animate-pulse">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                @foreach(range(1,4) as $i)
                <div class="bg-zinc-900 rounded-2xl p-4 h-20"></div>
                @endforeach
            </div>
            <div class="bg-zinc-900 rounded-2xl h-64"></div>
            <div class="bg-zinc-900 rounded-2xl h-40"></div>
        </div>
        HTML;
    }

    public function render()
    {
        $userId     = auth()->id();
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd   = Carbon::now()->endOfMonth();

        $totalWorkouts = Workout::where('user_id', $userId)
            ->where('status', 'completed')
            ->count();

        $workoutsThisMonth = Workout::where('user_id', $userId)
            ->where('status', 'completed')
            ->whereBetween('started_at', [$monthStart, $monthEnd])
            ->count();

        $volumeThisMonth = (int) WorkoutSet::whereHas('workoutExercise.workout', function ($q) use ($userId, $monthStart, $monthEnd) {
            $q->where('user_id', $userId)
              ->where('status', 'completed')
              ->whereBetween('started_at', [$monthStart, $monthEnd]);
        })->selectRaw('COALESCE(SUM(weight * reps), 0) as total')->value('total');

        $streak = $this->calculateStreak($userId);

        $recentWorkouts = Workout::with(['routine', 'exercises.sets'])
            ->where('user_id', $userId)
            ->where('status', 'completed')
            ->orderByDesc('started_at')
            ->limit(5)
            ->get();

        $recentPRs = PersonalRecord::with(['exercise.translations'])
            ->where('user_id', $userId)
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get();

        return view('livewire.dashboard', compact(
            'totalWorkouts',
            'workoutsThisMonth',
            'volumeThisMonth',
            'streak',
            'recentWorkouts',
            'recentPRs',
        ))
            ->title(__('app.dashboard'));
    }

    private function calculateStreak(int $userId): int
    {
        $tz = config('app.timezone', 'UTC');

        $dates = Workout::where('user_id', $userId)
            ->where('status', 'completed')
            ->where('started_at', '>=', now()->subDays(60))
            ->orderByDesc('started_at')
            ->pluck('started_at')
            ->map(fn ($d) => $d->setTimezone($tz)->format('Y-m-d'))
            ->unique()
            ->values();

        if ($dates->isEmpty()) {
            return 0;
        }

        $streak = 0;
        $check  = Carbon::today($tz);

        // Aceita que o último treino seja hoje ou ontem para manter streak
        if ($dates->first() !== $check->format('Y-m-d')) {
            $check = $check->subDay();
        }

        foreach ($dates as $date) {
            if ($date === $check->format('Y-m-d')) {
                $streak++;
                $check = $check->subDay();
            } else {
                break;
            }
        }

        return $streak;
    }
}
