<?php

namespace App\Livewire\Routine;

use Livewire\Component;
use App\Models\Routine;
use App\Models\RoutineExercise;
use App\Models\RoutineSet;

class SharedRoutine extends Component
{
    public Routine $routine;
    public bool $saved = false;

    public function mount(string $token): void
    {
        $this->routine = Routine::with([
            'user',
            'exercises.exercise.translations',
            'exercises.sets',
        ])->where('share_token', $token)->firstOrFail();
    }

    public function saveRoutine(): void
    {
        $user = auth()->user();

        if (! $user) {
            $this->redirect(route('login'));
            return;
        }

        $existing = Routine::where('user_id', $user->id)
            ->where('name', $this->routine->name)
            ->exists();

        if ($existing) {
            $this->dispatch('toast', message: __('app.routine_already_saved'), type: 'warning');
            return;
        }

        $newRoutine = Routine::create([
            'user_id'   => $user->id,
            'name'      => $this->routine->name,
            'emoji'     => $this->routine->emoji,
            'is_active' => false,
        ]);

        foreach ($this->routine->exercises->sortBy('order') as $i => $re) {
            $newRe = RoutineExercise::create([
                'routine_id'  => $newRoutine->id,
                'exercise_id' => $re->exercise_id,
                'order'       => $i,
            ]);

            foreach ($re->sets->sortBy('set_number') as $j => $set) {
                RoutineSet::create([
                    'routine_exercise_id' => $newRe->id,
                    'set_number'          => $j + 1,
                    'weight'              => $set->weight,
                    'reps'                => $set->reps,
                    'duration_seconds'    => $set->duration_seconds,
                    'distance_meters'     => $set->distance_meters,
                ]);
            }
        }

        $this->saved = true;
        $this->dispatch('toast', message: __('app.routine_saved'), type: 'success');
    }

    public function render()
    {
        return view('livewire.routine.shared-routine')
            ->layout('layouts.app');
    }
}
