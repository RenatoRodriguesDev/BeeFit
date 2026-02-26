<div class="space-y-8">

    {{-- Título --}}
    <div class="mb-6">
        <h2 class="text-3xl font-bold mb-8">
            {{ $routine->name }}
        </h2>
    </div>

    {{-- Exercícios --}}
    @foreach($routine->exercises as $routineExercise)

        <div class="bg-zinc-950 border border-zinc-800 rounded-2xl p-6 shadow-lg space-y-4">

            {{-- Nome do exercício --}}
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-semibold">
                    {{ $routineExercise->exercise->translate()->name }}
                </h2>

                <button wire:click="addSet({{ $routineExercise->id }})"
                    class="bg-zinc-800 hover:bg-zinc-700 text-sm px-4 py-2 rounded-lg transition">
                    + {{__('app.add')}} Set
                </button>
            </div>

            {{-- Header --}}
            <div class="grid grid-cols-4 text-xs text-zinc-400 uppercase tracking-wide px-4">
                <div>Set</div>
                <div>Kg</div>
                <div>Reps</div>
                <div></div>
            </div>

            {{-- Sets --}}
            <div class="space-y-2">

                @foreach($routineExercise->sets->sortBy('set_number') as $set)

                    <div class="grid grid-cols-4 items-center bg-zinc-900 rounded-xl px-4 py-3">

                        {{-- Número do set --}}
                        <div>
                            <div class="w-8 h-8 flex items-center justify-center border border-zinc-700 rounded-md text-sm">
                                {{ $set->set_number }}
                            </div>
                        </div>

                        {{-- Peso --}}
                        <div>
                            <input type="number" value="{{ $set->weight }}"
                                wire:blur="updateWeight({{ $set->id }}, $event.target.value)"
                                class="bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 w-24 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        {{-- Reps --}}
                        <div>
                            <input type="number" value="{{ $set->reps }}"
                                wire:blur="updateReps({{ $set->id }}, $event.target.value)"
                                class="bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 w-24 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        {{-- Delete --}}
                        <div class="text-right">
                            <button wire:click="deleteSet({{ $set->id }})" class="text-zinc-500 hover:text-red-500 transition">
                                ✕
                            </button>
                        </div>

                    </div>

                @endforeach

            </div>

        </div>

    @endforeach

</div>