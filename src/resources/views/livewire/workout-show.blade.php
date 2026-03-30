<div class="max-w-2xl mx-auto px-4 py-8 space-y-6">

    {{-- ── HEADER ──────────────────────────────────────────────────────── --}}
    <div class="space-y-1">
        <p class="text-xs text-zinc-500 uppercase tracking-widest font-medium">
            {{ $workout->started_at->format('d M Y') }}
        </p>
        <h1 class="text-2xl font-bold text-white">
            {{ $workout->routine->name }}
        </h1>
    </div>

    {{-- ── STATS ────────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-3 gap-3">

        @if($duration !== null)
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl px-4 py-4 text-center">
                <div class="text-xl font-bold text-white">{{ $duration }}<span class="text-sm font-normal text-zinc-400 ml-0.5">min</span></div>
                <div class="text-xs text-zinc-500 mt-1">{{ __('app.duration') }}</div>
            </div>
        @endif

        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl px-4 py-4 text-center">
            <div class="text-xl font-bold text-white">{{ $totalSets }}</div>
            <div class="text-xs text-zinc-500 mt-1">{{ __('app.sets') }}</div>
        </div>

        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl px-4 py-4 text-center">
            <div class="text-xl font-bold text-white">
                {{ number_format($totalVolume, 0, ',', ' ') }}<span class="text-sm font-normal text-zinc-400 ml-0.5">kg</span>
            </div>
            <div class="text-xs text-zinc-500 mt-1">{{ __('app.volume') }}</div>
        </div>

    </div>

    {{-- ── EXERCÍCIOS ────────────────────────────────────────────────────── --}}
    <div class="space-y-4">
        @foreach($workout->exercises as $workoutExercise)
            @php $pr = $records->get($workoutExercise->exercise_id); @endphp

            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden">

                {{-- Cabeçalho do exercício --}}
                <div class="flex items-center justify-between px-4 py-3 border-b border-zinc-800">
                    <h2 class="font-semibold text-sm text-white">
                        {{ $workoutExercise->exercise->translate()->name }}
                    </h2>

                    @if($pr)
                        <span class="inline-flex items-center gap-1 text-xs text-yellow-400 font-medium">
                            🏆 PR
                        </span>
                    @endif
                </div>

                {{-- Cabeçalho das colunas --}}
                <div class="grid grid-cols-3 gap-2 px-4 py-2 text-[11px] font-medium text-zinc-500 uppercase tracking-wider">
                    <span>{{ __('app.set') }}</span>
                    <span>{{ __('app.weight') }} (kg)</span>
                    <span>{{ __('app.reps') }}</span>
                </div>

                {{-- Sets --}}
                @foreach($workoutExercise->sets as $set)
                    <div class="grid grid-cols-3 gap-2 px-4 py-2.5 border-t border-zinc-800/60 text-sm">
                        <span class="text-zinc-500">{{ $set->set_number }}</span>
                        <span class="text-white font-medium">{{ $set->weight ?? '—' }}</span>
                        <span class="text-white font-medium">{{ $set->reps ?? '—' }}</span>
                    </div>
                @endforeach

                {{-- PR badge --}}
                @if($pr)
                    <div class="px-4 py-3 border-t border-zinc-800/60 flex flex-wrap gap-3">
                        @if($pr->max_weight)
                            <span class="text-xs bg-yellow-500/10 text-yellow-400 px-2.5 py-1 rounded-full">
                                Max {{ $pr->max_weight }}kg × {{ $pr->reps_at_max_weight }} reps
                            </span>
                        @endif
                        @if($pr->estimated_1rm)
                            <span class="text-xs bg-blue-500/10 text-blue-400 px-2.5 py-1 rounded-full">
                                1RM ~{{ number_format($pr->estimated_1rm, 1) }}kg
                            </span>
                        @endif
                    </div>
                @endif

            </div>
        @endforeach
    </div>

    {{-- ── AÇÕES ────────────────────────────────────────────────────────── --}}
    <div class="flex gap-3 pt-2">
        <a href="{{ route('social.create-post-workout', $workout->id) }}"
            class="flex-1 flex items-center justify-center gap-2 bg-zinc-800 hover:bg-zinc-700 text-white px-5 py-3 rounded-xl transition text-sm font-medium">
            📸 {{ __('app.share_workout') }}
        </a>
        <a href="{{ route('dashboard') }}"
            class="flex items-center justify-center gap-2 bg-zinc-900 hover:bg-zinc-800 border border-zinc-700 text-zinc-300 px-5 py-3 rounded-xl transition text-sm font-medium">
            {{ __('app.dashboard') }}
        </a>
    </div>

</div>
