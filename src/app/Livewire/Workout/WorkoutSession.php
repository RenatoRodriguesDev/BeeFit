<?php

namespace App\Livewire\Workout;

use Livewire\Component;
use App\Models\Workout;
use App\Models\WorkoutExercise;
use App\Models\WorkoutSet;
use App\Models\Exercise;
use App\Services\XpService;


class WorkoutSession extends Component
{
    public Workout $workout;
    public array $completedSets = [];
    public ?array $xpResult = null;

    public function mount(Workout $workout)
    {
        abort_unless($workout->user_id === auth()->id(), 403);

        $this->workout = $workout->load(
            'exercises.exercise',
            'exercises.sets'
        );
    }

    public function toggleSetDone(int $setId): void
    {
        if (in_array($setId, $this->completedSets)) {
            $this->completedSets = array_values(array_filter($this->completedSets, fn($id) => $id !== $setId));
        } else {
            $this->completedSets[] = $setId;
        }
    }

    public function updateWeight($setId, $value)
    {
        $this->ownedSet($setId)->update(['weight' => $value ?: null]);
    }

    public function updateReps($setId, $value)
    {
        $this->ownedSet($setId)->update(['reps' => $value ?: null]);
    }

    public function updateDuration($setId, $value)
    {
        // value vem em "mm:ss" ou segundos
        $this->ownedSet($setId)->update(['duration_seconds' => $this->parseToSeconds($value)]);
    }

    public function updateDistance($setId, $value)
    {
        // value em km, guarda em metros
        $meters = $value ? round((float) $value * 1000, 1) : null;
        $this->ownedSet($setId)->update(['distance_meters' => $meters]);
    }

    private function parseToSeconds(?string $value): ?int
    {
        if (! $value) return null;
        if (str_contains($value, ':')) {
            [$min, $sec] = explode(':', $value);
            return (int)$min * 60 + (int)$sec;
        }
        // Número sem ":" → assume minutos
        return (int) $value * 60;
    }

    public function pauseWorkout()
    {
        $this->workout->update([
            'status' => 'paused',
            'paused_at' => now(),
        ]);

        $this->workout->refresh();
    }

    public function resumeWorkout()
    {
        $this->workout->update([
            'status' => 'active',
            'paused_at' => null,
        ]);

        $this->workout->refresh();
    }

    public function cancelWorkout()
    {
        $this->workout->delete();

        return redirect()->route('routines.index');
    }

    public function finishWorkout(bool $share = false)
    {
        $this->workout->update([
            'status' => 'completed',
            'finished_at' => now(),
        ]);

        // Calcular recordes pessoais para cada exercício do treino
        $this->workout->load('exercises.sets');

        foreach ($this->workout->exercises as $workoutExercise) {
            \App\Models\PersonalRecord::updateFromWorkout(
                userId: auth()->id(),
                exerciseId: $workoutExercise->exercise_id,
                workoutId: $this->workout->id,
                sets: $workoutExercise->sets
            );
        }

        // Award XP and check achievements
        $user = auth()->user();
        $this->xpResult = app(XpService::class)->processWorkout($user, $this->workout);

        if ($share) {
            return redirect()->route('social.create-post-workout', $this->workout->id);
        }

        // Show XP modal instead of redirecting immediately
        $this->showSharePrompt = false;
        $this->showXpModal = true;
    }

    public function goToDashboard(): mixed
    {
        return redirect()->route('dashboard');
    }

    public function addSet($workoutExerciseId)
    {
        $workoutExercise = $this->ownedExercise($workoutExerciseId);

        $lastSetNumber = $workoutExercise->sets()->max('set_number') ?? 0;

        $isCardio = $workoutExercise->exercise->isCardio();

        WorkoutSet::create([
            'workout_exercise_id' => $workoutExercise->id,
            'set_number'          => $lastSetNumber + 1,
            'weight'              => $isCardio ? null : 0,
            'reps'                => $isCardio ? null : 0,
        ]);

        $this->workout->refresh();
    }

    public function removeSet($setId)
    {
        $set = $this->ownedSet($setId);
        $exercise = $set->workoutExercise;

        $set->delete();

        // Reordenar números
        $exercise->sets()->orderBy('set_number')
            ->get()
            ->values()
            ->each(function ($set, $index) {
                $set->update([
                    'set_number' => $index + 1
                ]);
            });

        $this->workout->refresh();
    }

    public function removeExercise($workoutExerciseId)
    {
        $exercise = $this->ownedExercise($workoutExerciseId);

        $exercise->delete(); // se tiver cascade deletes melhor ainda

        $this->workout->refresh();
    }

    public bool $showSharePrompt = false;
    public bool $showXpModal = false;

    public function promptFinish(): void
    {
        $this->showSharePrompt = true;
    }

    public $showAddExerciseModal = false;

    public function openAddExerciseModal()
    {
        $this->showAddExerciseModal = true;
    }

    public function closeAddExerciseModal()
    {
        $this->showAddExerciseModal = false;
    }

    public function getAvailableExercisesProperty()
    {
        return Exercise::query()
            ->join('exercise_translations', function ($join) {
                $join->on('exercises.id', '=', 'exercise_translations.exercise_id')
                    ->where('exercise_translations.locale', app()->getLocale());
            })
            ->orderBy('exercise_translations.name')
            ->select('exercises.*')
            ->get();
    }

    public function addExerciseToWorkout($exerciseId)
    {
        $workoutExercise = WorkoutExercise::create([
            'workout_id' => $this->workout->id,
            'exercise_id' => $exerciseId,
            'order' => $this->workout->exercises()->count() + 1,
        ]);

        $this->workout->refresh();
        $this->showAddExerciseModal = false;
    }

    private function ownedExercise(int $id): WorkoutExercise
    {
        return WorkoutExercise::where('id', $id)
            ->where('workout_id', $this->workout->id)
            ->firstOrFail();
    }

    private function ownedSet(int $id): WorkoutSet
    {
        return WorkoutSet::whereHas('workoutExercise', fn($q) => $q->where('workout_id', $this->workout->id))
            ->where('id', $id)
            ->firstOrFail();
    }

    public function render()
    {
        return view('livewire.workout-session')
            ->title((__('app.workout') . ' — ' . ($this->workout->routine->name ?? __('app.workout'))));
    }
}