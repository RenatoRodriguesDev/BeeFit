<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div id="sortable-exercises">

    @foreach($routine->exercises as $routineExercise)
        <div
            class="bg-gray-800 p-4 rounded mb-4 flex gap-4 items-start"
            data-id="{{ $routineExercise->id }}"
        >

            {{-- Drag Handle --}}
            <div class="drag-handle cursor-move text-gray-400">
                ☰
            </div>

            <div class="flex-1">

                <h3 class="font-bold text-lg">
                    {{ $routineExercise->exercise->name }}
                </h3>

                @foreach($routineExercise->sets as $set)
                    <div class="flex gap-3 mt-2">
                        <input type="number"
                            value="{{ $set->weight }}"
                            wire:change="updateSet({{ $set->id }}, 'weight', $event.target.value)"
                            class="bg-gray-700 p-2 rounded w-24"
                            placeholder="kg">

                        <input type="number"
                            value="{{ $set->reps }}"
                            wire:change="updateSet({{ $set->id }}, 'reps', $event.target.value)"
                            class="bg-gray-700 p-2 rounded w-24"
                            placeholder="reps">
                    </div>
                @endforeach

            </div>
        </div>
    @endforeach

</div>