<div class="max-w-xl mx-auto space-y-6 py-8 px-4">

    {{-- Header --}}
    <div class="text-center space-y-1 pb-4 border-b border-zinc-800">
        <div class="text-5xl mb-3">{{ $routine->emoji ?? '💪' }}</div>
        <h1 class="text-2xl font-bold text-white">{{ $routine->name }}</h1>
        <p class="text-sm text-zinc-500">
            {{ __('app.shared_by') }}
            <span class="text-zinc-400 font-medium">{{ $routine->user->name }}</span>
            &middot;
            {{ $routine->exercises->count() }} {{ __('app.exercises') }}
            &middot;
            {{ $routine->exercises->sum(fn($e) => $e->sets->count()) }} {{ __('app.sets') }}
        </p>
    </div>

    {{-- Save button --}}
    <div class="flex justify-center">
        @auth
            @if($saved)
                <div class="flex items-center gap-2 px-6 py-3 rounded-2xl bg-green-500/10 border border-green-500/30 text-green-400 text-sm font-semibold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ __('app.routine_saved') }}
                </div>
            @else
                <button wire:click="saveRoutine"
                    class="flex items-center gap-2 px-6 py-3 rounded-2xl bg-yellow-500 hover:bg-yellow-400 text-black text-sm font-semibold transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                    {{ __('app.save_routine') }}
                </button>
            @endif
        @else
            <a href="{{ route('login') }}"
                class="flex items-center gap-2 px-6 py-3 rounded-2xl bg-yellow-500 hover:bg-yellow-400 text-black text-sm font-semibold transition">
                {{ __('app.login_to_save_routine') }}
            </a>
        @endauth
    </div>

    {{-- Exercise list --}}
    <div class="space-y-4">
        @foreach($routine->exercises->sortBy('order') as $routineExercise)
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden">

                {{-- Exercise header --}}
                <div class="flex items-center gap-4 px-4 py-4">
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
                        <div class="text-sm text-zinc-500 mt-0.5">
                            {{ $routineExercise->sets->count() }} {{ __('app.sets') }}
                            @if($routineExercise->sets->isNotEmpty())
                                @php
                                    $minReps = $routineExercise->sets->min('reps');
                                    $maxReps = $routineExercise->sets->max('reps');
                                @endphp
                                &middot;
                                {{ $minReps == $maxReps ? $minReps : $minReps . '–' . $maxReps }} reps
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Sets table --}}
                @if($routineExercise->sets->isNotEmpty())
                    @php $isCardio = $routineExercise->exercise->isCardio(); @endphp
                    <div class="border-t border-zinc-800">
                        <div class="grid grid-cols-[3rem_1fr_1fr] gap-3 px-5 py-2.5 text-[11px] font-medium text-zinc-500 uppercase tracking-wider">
                            <span class="text-center">Set</span>
                            @if($isCardio)
                                <span>{{ __('app.duration') }}</span>
                                <span>{{ __('app.distance') }}</span>
                            @else
                                <span>{{ __('app.weight') }} (kg)</span>
                                <span>{{ __('app.reps') }}</span>
                            @endif
                        </div>
                        @foreach($routineExercise->sets->sortBy('set_number') as $set)
                            <div class="grid grid-cols-[3rem_1fr_1fr] gap-3 px-5 py-2 items-center border-t border-zinc-800/50">
                                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-zinc-800 text-xs font-semibold text-zinc-300 mx-auto">
                                    {{ $set->set_number }}
                                </div>
                                @if($isCardio)
                                    <span class="text-sm text-zinc-300 text-center">
                                        {{ $set->duration_seconds ? sprintf('%02d:%02d', intdiv($set->duration_seconds, 60), $set->duration_seconds % 60) : '—' }}
                                    </span>
                                    <span class="text-sm text-zinc-300 text-center">
                                        {{ $set->distance_meters ? number_format($set->distance_meters / 1000, 2) . ' km' : '—' }}
                                    </span>
                                @else
                                    <span class="text-sm text-zinc-300 text-center">{{ $set->weight ?? '—' }}</span>
                                    <span class="text-sm text-zinc-300 text-center">{{ $set->reps ?? '—' }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

            </div>
        @endforeach
    </div>

    {{-- Footer CTA --}}
    <div class="text-center pt-4 border-t border-zinc-800">
        <a href="{{ route('home') }}" class="text-sm text-zinc-500 hover:text-zinc-300 transition">
            BeeFit — {{ __('app.get_started_start_by_creating_a_routine') }}
        </a>
    </div>

</div>
