<div class="space-y-4">
    <div class="flex  mb-8">
        <a href="{{ url('routines') }}"
            class="text-3xl text-zinc-400 hover:text-white transition flex items-center gap-2">
            ←
        </a>

        <h1 class="text-3xl font-bold pl-4">
            {{ $routine->name }}
        </h1>

        <div></div>
    </div>
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
            <button wire:click="confirmDeleteExercise({{ $routineExercise->id }})"
                class="text-red-500 hover:text-red-400 transition p-2">
                {{ __('app.delete') }}
            </button>
        </div>

    @endforeach
    @if($showDeleteExerciseModal)

        <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50">
            <div class="bg-zinc-900 p-6 rounded-2xl w-96 space-y-6">

                <div>
                    <h2 class="text-xl font-semibold">
                        {{ __('app.confirm_delete') }}
                    </h2>

                    <p class="text-zinc-400 mt-2">
                        {{ __('app.confirm_delete_message') }}
                    </p>
                </div>

                <div class="flex justify-end gap-3">

                    <button wire:click="closeDeleteExerciseModal"
                        class="px-4 py-2 rounded-xl bg-zinc-800 hover:bg-zinc-700 transition">
                        {{ __('app.cancel') }}
                    </button>

                    <button wire:click="deleteExercise" class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-500 transition">
                        {{ __('app.delete') }}
                    </button>

                </div>
            </div>
        </div>

    @endif
</div>