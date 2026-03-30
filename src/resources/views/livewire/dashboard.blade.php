<div class="space-y-6">

    {{-- ── STATS ────────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">

        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl px-4 py-4">
            <div class="text-2xl mb-1">🔥</div>
            <div class="text-2xl font-bold text-white">{{ $streak }}</div>
            <div class="text-xs text-zinc-500 mt-0.5">{{ __('app.streak_days') }}</div>
        </div>

        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl px-4 py-4">
            <div class="text-2xl mb-1">📅</div>
            <div class="text-2xl font-bold text-white">{{ $workoutsThisMonth }}</div>
            <div class="text-xs text-zinc-500 mt-0.5">{{ __('app.this_month') }}</div>
        </div>

        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl px-4 py-4">
            <div class="text-2xl mb-1">💪</div>
            <div class="text-2xl font-bold text-white">
                {{ $volumeThisMonth >= 1000
                    ? number_format($volumeThisMonth / 1000, 1) . 't'
                    : number_format($volumeThisMonth, 0) . 'kg' }}
            </div>
            <div class="text-xs text-zinc-500 mt-0.5">{{ __('app.volume_month') }}</div>
        </div>

        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl px-4 py-4">
            <div class="text-2xl mb-1">🏋️</div>
            <div class="text-2xl font-bold text-white">{{ $totalWorkouts }}</div>
            <div class="text-xs text-zinc-500 mt-0.5">{{ __('app.total_workouts') }}</div>
        </div>

    </div>

    {{-- ── DOIS PAINÉIS ─────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-[auto_1fr] gap-6 items-start">

        {{-- ── CALENDÁRIO ──────────────────────────────────────────────── --}}
        <div class="bg-zinc-900 border border-zinc-800 p-4 md:p-5 rounded-2xl w-full lg:w-[340px]">

            <div class="flex justify-between items-center mb-4">
                <button wire:click="previousMonth"
                    class="w-8 h-8 flex items-center justify-center bg-zinc-800 hover:bg-zinc-700 rounded-full transition text-lg leading-none">
                    ‹
                </button>
                <h2 class="text-sm font-semibold tracking-wide capitalize">
                    {{ $currentMonth->translatedFormat('F Y') }}
                </h2>
                <button wire:click="nextMonth"
                    class="w-8 h-8 flex items-center justify-center bg-zinc-800 hover:bg-zinc-700 rounded-full transition text-lg leading-none">
                    ›
                </button>
            </div>

            <div class="grid grid-cols-7 text-[11px] text-zinc-500 mb-2 text-center font-medium">
                <div>{{ __('app.sunday') }}</div>
                <div>{{ __('app.monday') }}</div>
                <div>{{ __('app.tuesday') }}</div>
                <div>{{ __('app.wednesday') }}</div>
                <div>{{ __('app.thursday') }}</div>
                <div>{{ __('app.friday') }}</div>
                <div>{{ __('app.saturday') }}</div>
            </div>

            @php
                $startOfMonth  = $currentMonth->copy()->startOfMonth();
                $endOfMonth    = $currentMonth->copy()->endOfMonth();
                $startDayOfWeek = $startOfMonth->dayOfWeek;
            @endphp

            <div class="grid grid-cols-7 gap-y-1 text-sm text-center">

                @for ($i = 0; $i < $startDayOfWeek; $i++)
                    <div></div>
                @endfor

                @for ($day = 1; $day <= $endOfMonth->day; $day++)
                    @php
                        $date       = $currentMonth->copy()->day($day);
                        $dateKey    = $date->format('Y-m-d');
                        $hasWorkout = array_key_exists($dateKey, $workoutsByDate);
                        $isToday    = $date->isToday();
                        $isSelected = $selectedDate === $dateKey;
                    @endphp

                    <div wire:key="day-{{ $currentMonth->format('Y-m') }}-{{ $dateKey }}"
                        wire:click="selectDate('{{ $dateKey }}')"
                        class="relative flex items-center justify-center w-9 h-9 mx-auto cursor-pointer rounded-full transition
                            {{ $isSelected ? 'bg-blue-600 text-white' : '' }}
                            {{ !$isSelected && $isToday ? 'ring-1 ring-blue-500' : '' }}
                            {{ !$isSelected && $hasWorkout ? 'text-white font-semibold' : 'text-zinc-400' }}
                            hover:bg-zinc-800">
                        {{ $day }}
                        @if($hasWorkout)
                            <span class="absolute bottom-0.5 w-1.5 h-1.5 rounded-full {{ $isSelected ? 'bg-white' : 'bg-blue-500' }}"></span>
                        @endif
                    </div>
                @endfor

            </div>

            {{-- Treinos do dia seleccionado --}}
            @if(!empty($selectedWorkouts))
                <div class="mt-4 space-y-2 border-t border-zinc-800 pt-4">
                    @foreach($selectedWorkouts as $w)
                        <a href="{{ route('workouts.show', $w) }}"
                            class="flex items-center justify-between px-3 py-2.5 rounded-xl bg-zinc-800 hover:bg-zinc-700 transition">
                            <div>
                                <div class="text-sm font-medium text-white">{{ $w->routine->name }}</div>
                                <div class="text-xs text-zinc-500">{{ $w->started_at->format('H:i') }}</div>
                            </div>
                            <svg class="w-4 h-4 text-zinc-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @endforeach
                </div>
            @endif

        </div>

        {{-- ── COLUNA DIREITA ──────────────────────────────────────────── --}}
        <div class="space-y-5">

            {{-- Treinos recentes --}}
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden">

                <div class="px-5 py-4 border-b border-zinc-800">
                    <h3 class="font-semibold text-sm text-white">{{ __('app.recent_workouts') }}</h3>
                </div>

                @forelse($recentWorkouts as $w)
                    @php
                        $dur  = $w->finished_at ? (int) $w->started_at->diffInMinutes($w->finished_at) : null;
                        $sets = $w->exercises->sum(fn ($e) => $e->sets->count());
                    @endphp
                    <a href="{{ route('workouts.show', $w) }}"
                        class="flex items-center justify-between px-5 py-3.5 border-t border-zinc-800/60 hover:bg-zinc-800/50 transition">
                        <div class="min-w-0">
                            <div class="text-sm font-medium text-white truncate">{{ $w->routine->name }}</div>
                            <div class="text-xs text-zinc-500 mt-0.5">
                                {{ $w->started_at->diffForHumans() }}
                                @if($dur) · {{ $dur }}min @endif
                                · {{ $sets }} {{ __('app.sets') }}
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-zinc-600 shrink-0 ml-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                @empty
                    <div class="px-5 py-8 text-center text-sm text-zinc-600">
                        {{ __('app.no_workouts_yet') }}
                    </div>
                @endforelse

            </div>

            {{-- Recordes pessoais recentes --}}
            @if($recentPRs->isNotEmpty())
                <div class="bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden">

                    <div class="px-5 py-4 border-b border-zinc-800 flex items-center gap-2">
                        <span class="text-base">🏆</span>
                        <h3 class="font-semibold text-sm text-white">{{ __('app.recent_prs') }}</h3>
                    </div>

                    @foreach($recentPRs as $pr)
                        <div class="flex items-center justify-between px-5 py-3.5 border-t border-zinc-800/60">
                            <div class="min-w-0">
                                <div class="text-sm font-medium text-white truncate">
                                    {{ $pr->exercise->translate()->name }}
                                </div>
                                <div class="text-xs text-zinc-500 mt-0.5">
                                    {{ $pr->updated_at->diffForHumans() }}
                                </div>
                            </div>
                            <div class="text-right shrink-0 ml-3 space-y-0.5">
                                @if($pr->max_weight)
                                    <div class="text-xs font-semibold text-white">
                                        {{ $pr->max_weight }}kg × {{ $pr->reps_at_max_weight }}
                                    </div>
                                @endif
                                @if($pr->estimated_1rm)
                                    <div class="text-xs text-blue-400">
                                        1RM ~{{ number_format($pr->estimated_1rm, 1) }}kg
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach

                </div>
            @endif

        </div>

    </div>

</div>
