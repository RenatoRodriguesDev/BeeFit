<div class="min-h-screen bg-zinc-950 text-white flex flex-col">

    {{-- ── HEADER FIXO ─────────────────────────────────────────────────── --}}
    <header class="sticky top-0 z-20 bg-zinc-900/95 backdrop-blur-sm border-b border-zinc-800">
        <div class="max-w-2xl mx-auto px-4 py-3 flex items-center justify-between gap-3">

            {{-- Nome + status --}}
            <div class="min-w-0">
                <h1 class="font-bold text-base leading-tight truncate">
                    {{ $workout->routine->name }}
                </h1>
                <div class="flex items-center gap-2 mt-0.5">
                    @if($workout->status === 'active')
                        <span class="inline-flex items-center gap-1 text-xs text-green-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></span>
                            {{ __('app.active') }}
                        </span>
                    @elseif($workout->status === 'paused')
                        <span class="inline-flex items-center gap-1 text-xs text-yellow-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-yellow-400"></span>
                            {{ __('app.paused') }}
                        </span>
                    @endif

                    <span class="text-zinc-600 text-xs">·</span>

                    <span class="text-xs text-zinc-400 font-mono"
                        x-data="{
                            start: new Date('{{ $workout->started_at->toIso8601String() }}'),
                            t: '',
                            tick() {
                                let s = Math.floor((new Date() - this.start) / 1000);
                                let h = Math.floor(s / 3600);
                                let m = Math.floor((s % 3600) / 60);
                                let sec = s % 60;
                                this.t = (h ? String(h).padStart(2,'0')+':' : '') +
                                         String(m).padStart(2,'0') + ':' +
                                         String(sec).padStart(2,'0');
                            }
                        }"
                        x-init="tick(); setInterval(() => tick(), 1000)"
                        x-text="t">
                    </span>
                </div>
            </div>

            {{-- Controlos --}}
            <div class="flex items-center gap-2 shrink-0">
                @if($workout->status === 'active')
                    <button wire:click="pauseWorkout"
                        class="p-2 rounded-xl bg-yellow-500/10 text-yellow-400 hover:bg-yellow-500/20 transition"
                        title="{{ __('app.pause') }}">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
                        </svg>
                    </button>
                @endif

                @if($workout->status === 'paused')
                    <button wire:click="resumeWorkout"
                        class="p-2 rounded-xl bg-blue-500/10 text-blue-400 hover:bg-blue-500/20 transition"
                        title="{{ __('app.resume') }}">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                    </button>
                @endif

                <button wire:click="cancelWorkout"
                    class="p-2 rounded-xl bg-red-500/10 text-red-400 hover:bg-red-500/20 transition"
                    title="{{ __('app.cancel') }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <button wire:click="promptFinish"
                    class="px-4 py-2 rounded-xl bg-green-500 hover:bg-green-400 text-black font-semibold text-sm transition">
                    {{ __('app.finish') }}
                </button>
            </div>
        </div>
    </header>

    {{-- ── CORPO ───────────────────────────────────────────────────────── --}}
    <main class="flex-1 max-w-2xl mx-auto w-full px-4 py-6 space-y-4">

        @foreach($workout->exercises as $workoutExercise)
            <div class="bg-zinc-900 rounded-2xl border border-zinc-800 overflow-hidden">

                <div class="flex items-center justify-between px-4 py-3">
                    <h2 class="font-semibold text-sm">
                        {{ $workoutExercise->exercise->translate()->name }}
                    </h2>
                    <button wire:click="removeExercise({{ $workoutExercise->id }})"
                        class="text-zinc-600 hover:text-red-400 transition p-1"
                        title="{{ __('app.remove_exercise') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                @php $isCardio = $workoutExercise->exercise->isCardio(); @endphp

                {{-- Cabeçalho --}}
                @if($isCardio)
                    <div class="grid grid-cols-[1.25rem_2.5rem_1fr_1fr_2rem] gap-2 px-4 pb-1 text-[11px] font-medium text-zinc-500 uppercase tracking-wider">
                        <span></span>
                        <span class="text-center">Set</span>
                        <span>{{ __('app.duration') }} (mm:ss)</span>
                        <span>{{ __('app.distance') }} (km)</span>
                        <span></span>
                    </div>
                @else
                    <div class="grid grid-cols-[1.25rem_2.5rem_1fr_1fr_2rem] gap-2 px-4 pb-1 text-[11px] font-medium text-zinc-500 uppercase tracking-wider">
                        <span></span>
                        <span class="text-center">Set</span>
                        <span>{{ __('app.weight') }} (kg)</span>
                        <span>{{ __('app.reps') }}</span>
                        <span></span>
                    </div>
                @endif

                @foreach($workoutExercise->sets as $set)
                    @php $isDone = in_array($set->id, $completedSets); @endphp
                    <div class="grid grid-cols-[1.25rem_2.5rem_1fr_1fr_2rem] gap-2 px-4 py-2 items-center border-t border-zinc-800/60 transition-colors {{ $isDone ? 'bg-green-950/30' : '' }}">

                        {{-- Marcador de série concluída --}}
                        <button wire:click="toggleSetDone({{ $set->id }})"
                            class="flex items-center justify-center w-5 h-5 rounded-full border-2 transition-colors shrink-0 {{ $isDone ? 'bg-green-500 border-green-500' : 'border-zinc-600 hover:border-green-500' }}"
                            title="{{ $isDone ? __('app.mark_undone') : __('app.mark_done') }}">
                            @if($isDone)
                                <svg class="w-3 h-3 text-black" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            @endif
                        </button>

                        <div class="flex items-center justify-center w-7 h-7 rounded-lg bg-zinc-800 text-xs font-semibold text-zinc-300 mx-auto">
                            {{ $set->set_number }}
                        </div>

                        @if($isCardio)
                            {{-- Duração mm:ss --}}
                            <input type="text"
                                value="{{ $set->duration_seconds ? sprintf('%02d:%02d', intdiv($set->duration_seconds, 60), $set->duration_seconds % 60) : '' }}"
                                wire:blur="updateDuration({{ $set->id }}, $event.target.value)"
                                placeholder="00:00"
                                inputmode="numeric"
                                class="w-full bg-zinc-800 border border-zinc-700 rounded-xl px-3 py-2 text-sm text-center focus:outline-none focus:border-zinc-500 transition">

                            {{-- Distância km --}}
                            <input type="number"
                                value="{{ $set->distance_meters ? number_format($set->distance_meters / 1000, 2, '.', '') : '' }}"
                                wire:blur="updateDistance({{ $set->id }}, $event.target.value)"
                                placeholder="0.00"
                                inputmode="decimal"
                                step="0.01"
                                class="w-full bg-zinc-800 border border-zinc-700 rounded-xl px-3 py-2 text-sm text-center focus:outline-none focus:border-zinc-500 transition">
                        @else
                            {{-- Peso --}}
                            <input type="number"
                                value="{{ $set->weight }}"
                                wire:blur="updateWeight({{ $set->id }}, $event.target.value)"
                                inputmode="decimal"
                                class="w-full bg-zinc-800 border border-zinc-700 rounded-xl px-3 py-2 text-sm text-center focus:outline-none focus:border-zinc-500 transition">

                            {{-- Reps --}}
                            <input type="number"
                                value="{{ $set->reps }}"
                                wire:blur="updateReps({{ $set->id }}, $event.target.value)"
                                inputmode="numeric"
                                class="w-full bg-zinc-800 border border-zinc-700 rounded-xl px-3 py-2 text-sm text-center focus:outline-none focus:border-zinc-500 transition">
                        @endif

                        <button wire:click="removeSet({{ $set->id }})"
                            class="flex items-center justify-center text-zinc-600 hover:text-red-400 transition mx-auto">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                @endforeach

                <div class="px-4 py-3 border-t border-zinc-800/60">
                    <button wire:click="addSet({{ $workoutExercise->id }})"
                        class="w-full py-2 rounded-xl border border-dashed border-zinc-700 text-zinc-500 hover:border-zinc-500 hover:text-zinc-300 text-sm transition">
                        + {{ __('app.add_set') }}
                    </button>
                </div>
            </div>
        @endforeach

        <button wire:click="openAddExerciseModal"
            class="w-full py-3 rounded-2xl border border-dashed border-zinc-700 text-zinc-400 hover:border-zinc-500 hover:text-zinc-200 text-sm font-medium transition">
            + {{ __('app.add_exercise') }}
        </button>

    </main>

    {{-- ── MODAL: CONFIRMAR FIM ────────────────────────────────────────── --}}
    @if($showSharePrompt)
        <div class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-end sm:items-center justify-center z-50 p-4">
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 w-full max-w-sm space-y-5">
                <div class="text-center">
                    <div class="text-5xl mb-3">🏆</div>
                    <h2 class="text-xl font-bold">{{ __('app.workout_done') }}</h2>
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

    {{-- ── MODAL: ADICIONAR EXERCÍCIO ─────────────────────────────────── --}}
    @if($showAddExerciseModal)
        <div class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-end sm:items-center justify-center z-50 p-4">
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl w-full max-w-sm flex flex-col max-h-[70vh]">

                <div class="flex items-center justify-between px-5 py-4 border-b border-zinc-800">
                    <h3 class="font-semibold">{{ __('app.add_exercise') }}</h3>
                    <button wire:click="closeAddExerciseModal"
                        class="text-zinc-500 hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="overflow-y-auto flex-1 py-2">
                    @foreach($this->availableExercises as $exercise)
                        <button wire:click="addExerciseToWorkout({{ $exercise->id }})"
                            class="w-full text-left px-5 py-3 text-sm hover:bg-zinc-800 transition">
                            {{ $exercise->translate()->name }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

</div>
