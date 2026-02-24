<?php

namespace App\Livewire\Routine;

use Livewire\Component;
use App\Models\Routine;
use App\Models\RoutineExercise;
use App\Models\RoutineSet;

class RoutineBuilder extends Component
{
    public Routine $routine;

    protected $listeners = ['exerciseSelected', 'reorderExercises'];

    public function mount()
    {
        $this->routine = Routine::firstOrCreate(
            [
                'user_id' => auth()->id(),
                'is_active' => true
            ],
            [
                'name' => 'New Routine'
            ]
        );
    }

    public function exerciseSelected($id)
    {
        $order = $this->routine->exercises()->count() + 1;

        $routineExercise = $this->routine->exercises()->create([
            'exercise_id' => $id,
            'order' => $order
        ]);

        // Criar 3 sets default
        for ($i = 1; $i <= 3; $i++) {
            $routineExercise->sets()->create([
                'set_number' => $i,
                'weight' => null,
                'reps' => null,
            ]);
        }
    }

    public function updateSet($setId, $field, $value)
    {
        RoutineSet::where('id', $setId)->update([
            $field => $value
        ]);
    }

    public function reorderExercises($payload)
    {
        foreach ($payload['order'] as $item) {
            RoutineExercise::where('id', $item['id'])
                ->update(['order' => $item['order']]);
        }
    }

    public function render()
    {
        $routine = $this->routine->load(
            'exercises.exercise.translations',
            'exercises.sets'
        );

        return view('livewire.routine.routine-builder', compact('routine'));
    }
}