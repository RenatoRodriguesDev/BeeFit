<div class="space-y-4">
    <h1 class="text-3xl font-bold mb-8">
        {{ $routine->name }}
    </h1>
    @foreach($routine->exercises as $routineExercise)

        <div class="bg-zinc-950 border border-zinc-800 rounded-2xl shadow-lg overflow-hidden">

            {{-- HEADER DO EXERCÍCIO (CLICK PARA EXPANDIR) --}}
            <button wire:click="toggleExercise({{ $routineExercise->id }})"
                class="w-full flex justify-between items-center p-6 hover:bg-zinc-900 transition">

                {{-- Thumbnail do Exercício --}}
                <img src="{{ asset($routineExercise->exercise->thumbnail_path) }}"
                    alt="{{ $routineExercise->exercise->translate()->name }}"
                    class="w-16 h-16 rounded-full object-cover mr-4">

                <div class="flex-1">
                    <h2 class="text-lg font-semibold">
                        {{ $routineExercise->exercise->translate()->name }}
                    </h2>

                    {{-- Sets e Reps --}}
                    <p class="text-sm text-zinc-400">
                        {{ $routineExercise->sets->count() }} sets ·
                        {{ $routineExercise->sets->min('reps') }}-{{ $routineExercise->sets->max('reps') }} reps
                    </p>
                </div>

                <span class="text-zinc-400">
                    {{ $expandedExerciseId === $routineExercise->id ? '▾' : '▸' }}
                </span>
            </button>

            {{-- CONTEÚDO EXPANDIDO --}}
            @if($expandedExerciseId === $routineExercise->id)

                <div class="border-t border-zinc-800 p-6 space-y-4">

                    {{-- Header --}}
                    <div class="grid grid-cols-4 text-xs text-zinc-400 uppercase tracking-wide px-4">
                        <div>Set</div>
                        <div>Kg</div>
                        <div>Reps</div>
                        <div class="text-right">
                            <button wire:click="addSet({{ $routineExercise->id }})"
                                class="bg-zinc-800 hover:bg-zinc-700 text-sm px-4 py-2 rounded-lg transition">
                                + {{__('app.add')}} Set
                            </button>
                        </div>
                    </div>

                    {{-- Sets --}}
                    <div class="space-y-2">

                        @foreach($routineExercise->sets->sortBy('set_number') as $set)

                            <div class="grid grid-cols-4 items-center bg-zinc-900 rounded-xl px-4 py-3">

                                {{-- Set number --}}
                                <div>
                                    <div class="w-8 h-8 flex items-center justify-center border border-zinc-700 rounded-md text-sm">
                                        {{ $set->set_number }}
                                    </div>
                                </div>

                                {{-- Weight --}}
                                <div>
                                    <input type="number" value="{{ $set->weight }}"
                                        wire:blur="updateWeight({{ $set->id }}, $event.target.value)"
                                        class="bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 w-24">
                                </div>

                                {{-- Reps --}}
                                <div>
                                    <input type="number" value="{{ $set->reps }}"
                                        wire:blur="updateReps({{ $set->id }}, $event.target.value)"
                                        class="bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 w-24">
                                </div>

                                {{-- Delete --}}
                                <div class="text-right">
                                    <button wire:click="deleteSet({{ $set->id }})"
                                        class="text-zinc-500 hover:text-red-500 transition">
                                        ✕
                                    </button>
                                </div>

                            </div>

                        @endforeach

                    </div>

                </div>

            @endif

        </div>

    @endforeach

</div>