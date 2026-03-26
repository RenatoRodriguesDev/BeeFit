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

            <button wire:click="promptFinish" class="bg-green-600 px-4 py-2 rounded-xl">
                {{__('app.finish')}}
            </button>

        </div>
        <div class="flex justify-end">
            <button wire:click="openAddExerciseModal" class="bg-blue-600 px-4 py-2 rounded-xl text-sm">
                + {{ __('app.add_exercise') }}
            </button>
        </div>
        @foreach($workout->exercises as $workoutExercise)

            <div class="bg-zinc-600 p-6 rounded-3xl space-y-4">

                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold">
                        {{ $workoutExercise->exercise->translate()->name }}
                    </h2>

                    <button wire:click="removeExercise({{ $workoutExercise->id }})" class="text-red-500 text-sm">
                        {{__('app.remove_exercise')}}
                    </button>
                </div>

                @foreach($workoutExercise->sets as $set)

                    <div class="flex items-end gap-6">

                        {{-- SET NUMBER --}}
                        <div class="flex flex-col items-center">
                            <span class="text-xs text-zinc-400 mb-1">
                                {{ __('app.set') }}
                            </span>
                            <div class="w-10 h-10 bg-zinc-800 rounded-lg flex items-center justify-center">
                                {{ $set->set_number }}
                            </div>
                        </div>

                        {{-- WEIGHT --}}
                        <div class="flex flex-col">
                            <span class="text-xs text-zinc-400 mb-1">
                                {{ __('app.weight') }}
                            </span>
                            <input type="number" value="{{ $set->weight }}"
                                wire:blur="updateWeight({{ $set->id }}, $event.target.value)"
                                class="bg-zinc-800 rounded-xl px-4 py-2 w-24">
                        </div>

                        {{-- REPS --}}
                        <div class="flex flex-col">
                            <span class="text-xs text-zinc-400 mb-1">
                                {{ __('app.reps') }}
                            </span>
                            <input type="number" value="{{ $set->reps }}"
                                wire:blur="updateReps({{ $set->id }}, $event.target.value)"
                                class="bg-zinc-800 rounded-xl px-4 py-2 w-24">
                        </div>

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
    {{-- Share prompt modal --}}
    @if($showSharePrompt)
        <div class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-[100] p-4">
            <div class="bg-zinc-900 rounded-2xl p-6 w-full max-w-sm space-y-5">

                <div class="text-center">
                    <div class="text-5xl mb-3">🏆</div>
                    <h2 class="text-xl font-bold text-white">{{ __('app.workout_done') }}</h2>
                    <p class="text-sm text-zinc-400 mt-1">{{ __('app.share_workout_prompt') }}</p>
                </div>

                <div class="flex flex-col gap-3">
                    <button wire:click="finishWorkout(true)"
                        class="w-full py-3 rounded-xl bg-white text-black font-semibold text-sm hover:bg-zinc-200 transition">
                        📸 {{ __('app.yes_share') }}
                    </button>
                    <button wire:click="finishWorkout(false)"
                        class="w-full py-3 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-sm transition">
                        {{ __('app.skip') }}
                    </button>
                </div>

            </div>
        </div>
    @endif

    @if($showAddExerciseModal)
        <div class="fixed inset-0 bg-black/70 flex items-center justify-center z-50">
            <div class="bg-zinc-800 p-6 rounded-2xl w-96 max-h-[500px] overflow-y-auto">

                <div class="flex justify-between mb-4">
                    <h3 class="font-semibold">{{ __('app.add_exercise') }}</h3>
                    <button wire:click="closeAddExerciseModal">✕</button>
                </div>

                @foreach($this->availableExercises as $exercise)
                    <button wire:click="addExerciseToWorkout({{ $exercise->id }})"
                        class="block w-full text-left px-4 py-2 rounded-lg hover:bg-zinc-700">
                        {{ $exercise->translate()->name }}
                    </button>
                @endforeach

            </div>
        </div>
    @endif
</div>