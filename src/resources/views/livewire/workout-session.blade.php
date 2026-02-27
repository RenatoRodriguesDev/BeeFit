<div class="fixed inset-0 bg-black/80 z-50 overflow-y-auto">

    <div class="max-w-4xl mx-auto py-12 space-y-8">

        <h1 class="text-3xl font-bold">
            {{ $workout->routine->name }}
        </h1>

        <div class="flex gap-4">

            @if($workout->status === 'active')
                <button wire:click="pauseWorkout" class="bg-yellow-500 px-4 py-2 rounded-xl">
                    {{__('app.pause')}}
                </button>
            @endif

            @if($workout->status === 'paused')
                <button wire:click="resumeWorkout" class="bg-blue-500 px-4 py-2 rounded-xl">
                    {{__('app.resume')}}
                </button>
            @endif

            <button wire:click="cancelWorkout" class="bg-red-600 px-4 py-2 rounded-xl">
                {{__('app.cancel')}}
            </button>

            <button wire:click="finishWorkout" class="bg-green-600 px-4 py-2 rounded-xl">
                {{__('app.finish')}}
            </button>

        </div>

        @foreach($workout->exercises as $workoutExercise)

            <div class="bg-zinc-900 p-6 rounded-3xl space-y-4">

                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold">
                        {{ $workoutExercise->exercise->translate()->name }}
                    </h2>

                    <button wire:click="removeExercise({{ $workoutExercise->id }})" class="text-red-500 text-sm">
                        {{__('app.remove_exercise')}}
                    </button>
                </div>

                @foreach($workoutExercise->sets as $set)

                    <div class="flex items-center gap-6">

                        <div class="w-10 h-10 bg-zinc-800 rounded-lg flex items-center justify-center">
                            {{ $set->set_number }}
                        </div>

                        <input type="number" value="{{ $set->weight }}"
                            wire:blur="updateWeight({{ $set->id }}, $event.target.value)"
                            class="bg-zinc-800 rounded-xl px-4 py-2 w-24">

                        <input type="number" value="{{ $set->reps }}"
                            wire:blur="updateReps({{ $set->id }}, $event.target.value)"
                            class="bg-zinc-800 rounded-xl px-4 py-2 w-24">

                    </div>
                    <button wire:click="removeSet({{ $set->id }})" class="text-red-500 text-sm">
                        {{ __('app.remove') }}
                    </button>
                @endforeach
                <button wire:click="addSet({{ $workoutExercise->id }})" class="bg-zinc-700 px-4 py-2 rounded-xl text-sm">
                    + {{ __('app.add_set') }}
                </button>

            </div>

        @endforeach

    </div>
</div>