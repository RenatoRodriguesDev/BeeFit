<div class="max-w-xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-4 pb-4 border-b border-zinc-800">
        <a href="{{ route('routines.index') }}"
            class="p-2.5 rounded-xl bg-zinc-900 border border-zinc-800 hover:bg-zinc-800 text-zinc-400 hover:text-white transition shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div class="min-w-0 flex-1">
            <h1 class="text-2xl font-bold text-white truncate">{{ $routine->name }}</h1>
            <p class="text-sm text-zinc-500 mt-0.5">
                {{ $routine->exercises->count() }} {{ __('app.exercises') }}
                &middot; {{ $routine->exercises->sum(fn($e) => $e->sets->count()) }} {{ __('app.sets') }}
            </p>
        </div>
        @if(auth()->user()->isPremium() || auth()->user()->isTrainer() || auth()->user()->isAdmin())
            <button wire:click="toggleShare"
                class="p-2.5 rounded-xl border transition shrink-0 {{ $routine->share_token ? 'bg-yellow-500/10 border-yellow-500/40 text-yellow-400 hover:bg-yellow-500/20' : 'bg-zinc-900 border-zinc-800 hover:bg-zinc-800 text-zinc-400 hover:text-white' }}"
                title="{{ $routine->share_token ? __('app.routine_sharing_active') : __('app.share_routine') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                </svg>
            </button>
        @endif
    </div>

    {{-- Share panel --}}
    @if($routine->share_token)
        <div x-data="{ copied: false }" class="bg-yellow-500/5 border border-yellow-500/20 rounded-2xl px-4 py-3 flex items-center gap-3">
            <svg class="w-4 h-4 text-yellow-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
            </svg>
            <p class="text-xs text-yellow-300/80 flex-1 truncate">{{ $routine->shareUrl() }}</p>
            <button
                x-on:click="navigator.clipboard.writeText('{{ $routine->shareUrl() }}'); copied = true; setTimeout(() => copied = false, 2000)"
                class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-yellow-500/20 hover:bg-yellow-500/30 text-yellow-300 transition shrink-0">
                <span x-show="!copied">{{ __('app.copy_link') }}</span>
                <span x-show="copied" x-cloak>{{ __('app.copied') }}</span>
            </button>
        </div>
    @endif

    {{-- Exercícios --}}
    <div id="sortable-exercises" class="space-y-6">
    @forelse($routine->exercises->sortBy('order') as $routineExercise)
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden" data-id="{{ $routineExercise->id }}">

            {{-- Cabeçalho — flex row com botões separados (sem nesting) --}}
            <div class="flex items-center">

                {{-- Drag handle --}}
                <div class="drag-handle pl-4 pr-1 py-5 text-zinc-600 hover:text-zinc-400 cursor-grab active:cursor-grabbing shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 8h16M4 16h16"/>
                    </svg>
                </div>

                {{-- Área clicável principal (expand) — inclui chevron --}}
                <button wire:click="toggleExercise({{ $routineExercise->id }})"
                    class="flex-1 flex items-center gap-4 px-3 py-5 hover:bg-zinc-800/40 transition text-left min-w-0">

                    <div class="w-14 h-14 rounded-xl overflow-hidden bg-zinc-800 shrink-0">
                        @if($routineExercise->exercise->thumbnail_path)
                            <img src="{{ asset($routineExercise->exercise->thumbnail_path) }}"
                                alt="{{ $routineExercise->exercise->translate()->name }}"
                                class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-2xl">💪</div>
                        @endif
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="font-semibold text-base text-white truncate">
                            {{ $routineExercise->exercise->translate()->name }}
                        </div>
                        <div class="text-sm text-zinc-500 mt-1">
                            {{ $routineExercise->sets->count() }} {{ __('app.sets') }}
                            @if($routineExercise->sets->isNotEmpty())
                                &middot;
                                @php
                                    $minReps = $routineExercise->sets->min('reps');
                                    $maxReps = $routineExercise->sets->max('reps');
                                @endphp
                                {{ $minReps == $maxReps ? $minReps : $minReps . '–' . $maxReps }} reps
                            @endif
                        </div>
                    </div>

                    {{-- Chevron dentro do botão --}}
                    <svg class="w-4 h-4 text-zinc-500 shrink-0 transition-transform duration-200 {{ $expandedExerciseId === $routineExercise->id ? 'rotate-180' : '' }}"
                        fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>

                </button>

                {{-- Botão apagar (separado, fora do button anterior) --}}
                <button wire:click="confirmDeleteExercise({{ $routineExercise->id }})"
                    class="px-4 py-5 text-zinc-600 hover:text-red-400 hover:bg-red-500/10 transition shrink-0"
                    title="{{ __('app.delete') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>

            </div>

            {{-- Sets expandidos --}}
            @if($expandedExerciseId === $routineExercise->id)
                <div class="border-t border-zinc-800">

                    @php $isCardio = $routineExercise->exercise->isCardio(); @endphp

                    @if($isCardio)
                        <div class="grid grid-cols-[3rem_1fr_1fr_2.5rem] gap-3 px-5 py-3 text-[11px] font-medium text-zinc-500 uppercase tracking-wider">
                            <span class="text-center">Set</span>
                            <span>{{ __('app.duration') }} (mm:ss)</span>
                            <span>{{ __('app.distance') }} (km)</span>
                            <span></span>
                        </div>
                    @else
                        <div class="grid grid-cols-[3rem_1fr_1fr_2.5rem] gap-3 px-5 py-3 text-[11px] font-medium text-zinc-500 uppercase tracking-wider">
                            <span class="text-center">Set</span>
                            <span>{{ __('app.weight') }} (kg)</span>
                            <span>{{ __('app.reps') }}</span>
                            <span></span>
                        </div>
                    @endif

                    @foreach($routineExercise->sets->sortBy('set_number') as $set)
                        <div class="grid grid-cols-[3rem_1fr_1fr_2.5rem] gap-3 px-5 py-2.5 items-center border-t border-zinc-800/50">

                            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-zinc-800 text-xs font-semibold text-zinc-300 mx-auto">
                                {{ $set->set_number }}
                            </div>

                            @if($isCardio)
                                <input type="text"
                                    value="{{ $set->duration_seconds ? sprintf('%02d:%02d', intdiv($set->duration_seconds, 60), $set->duration_seconds % 60) : '' }}"
                                    wire:blur="updateDuration({{ $set->id }}, $event.target.value)"
                                    placeholder="00:00"
                                    inputmode="numeric"
                                    class="w-full bg-zinc-800 border border-zinc-700 rounded-xl px-3 py-2.5 text-sm text-center focus:outline-none focus:border-zinc-600 transition">

                                <input type="number"
                                    value="{{ $set->distance_meters ? number_format($set->distance_meters / 1000, 2, '.', '') : '' }}"
                                    wire:blur="updateDistance({{ $set->id }}, $event.target.value)"
                                    placeholder="0.00"
                                    inputmode="decimal"
                                    step="0.01"
                                    class="w-full bg-zinc-800 border border-zinc-700 rounded-xl px-3 py-2.5 text-sm text-center focus:outline-none focus:border-zinc-600 transition">
                            @else
                                <input type="number"
                                    value="{{ $set->weight }}"
                                    wire:blur="updateWeight({{ $set->id }}, $event.target.value)"
                                    inputmode="decimal"
                                    placeholder="—"
                                    class="w-full bg-zinc-800 border border-zinc-700 rounded-xl px-3 py-2.5 text-sm text-center focus:outline-none focus:border-zinc-600 transition">

                                <input type="number"
                                    value="{{ $set->reps }}"
                                    wire:blur="updateReps({{ $set->id }}, $event.target.value)"
                                    inputmode="numeric"
                                    placeholder="—"
                                    class="w-full bg-zinc-800 border border-zinc-700 rounded-xl px-3 py-2.5 text-sm text-center focus:outline-none focus:border-zinc-600 transition">
                            @endif

                            <button wire:click="deleteSet({{ $set->id }})"
                                class="flex items-center justify-center text-zinc-600 hover:text-red-400 transition mx-auto">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>

                        </div>
                    @endforeach

                    <div class="px-5 py-4 border-t border-zinc-800/50">
                        <button wire:click="addSet({{ $routineExercise->id }})"
                            class="w-full py-2.5 rounded-xl border border-dashed border-zinc-700 text-zinc-500 hover:border-zinc-500 hover:text-zinc-300 text-sm transition">
                            + {{ __('app.add_set') }}
                        </button>
                    </div>

                </div>
            @endif

        </div>
    @empty
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="text-6xl mb-4">📋</div>
            <p class="text-zinc-400 text-sm">{{ __('app.no_exercises_yet') }}</p>
            <p class="text-zinc-600 text-xs mt-2">{{ __('app.add_exercises_from_library') }}</p>
        </div>
    @endforelse
    </div>{{-- /sortable-exercises --}}

    {{-- Link para biblioteca --}}
    <a href="{{ route('library.index', app()->getLocale()) }}"
        class="flex items-center justify-center gap-2 w-full py-4 rounded-2xl border border-dashed border-zinc-800 text-zinc-500 hover:border-zinc-600 hover:text-zinc-300 text-sm font-medium transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        {{ __('app.browse_exercises') }}
    </a>

    {{-- Modal: confirmar delete exercício --}}
    @if($showDeleteExerciseModal)
        <div class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-end sm:items-center justify-center z-50 p-4">
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 w-full max-w-sm space-y-4">
                <div>
                    <h2 class="text-lg font-semibold text-white">{{ __('app.confirm_delete') }}</h2>
                    <p class="text-sm text-zinc-400 mt-1">{{ __('app.confirm_delete_message') }}</p>
                </div>
                <div class="flex gap-3">
                    <button wire:click="closeDeleteExerciseModal"
                        class="flex-1 py-2.5 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-sm transition">
                        {{ __('app.cancel') }}
                    </button>
                    <button wire:click="deleteExercise"
                        class="flex-1 py-2.5 rounded-xl bg-red-600 hover:bg-red-500 text-sm font-semibold transition">
                        {{ __('app.delete') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
