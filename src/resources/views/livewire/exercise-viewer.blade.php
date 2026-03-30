<div class="h-full flex flex-col">

    @if(!$exercise)
        {{-- Empty state --}}
        <div class="flex-1 flex flex-col items-center justify-center text-center p-8">
            <div class="text-6xl mb-4">🔍</div>
            <p class="text-zinc-400 text-sm">{{ __('app.select_exercise_from_library') }}</p>
        </div>
    @else
        <div x-data="{ show: false }" x-init="setTimeout(() => show = true, 50)"
            x-show="show" x-transition:enter.opacity.duration.200ms
            class="flex-1 overflow-y-auto">

            {{-- Media --}}
            <div class="bg-zinc-950 w-full aspect-video flex items-center justify-center overflow-hidden"
                wire:key="media-{{ $exercise->id }}">
                @if($exercise->has_video)
                    <video controls autoplay loop muted class="w-full h-full object-cover">
                        <source src="{{ asset($exercise->video_path) }}" type="video/mp4">
                    </video>
                @elseif($exercise->thumbnail_path)
                    <img src="{{ asset($exercise->thumbnail_path) }}"
                        class="w-full h-full object-cover"
                        alt="{{ $exercise->translate()->name }}">
                @else
                    <div class="text-zinc-700 text-sm">{{ __('app.no_media') }}</div>
                @endif
            </div>

            <div class="px-5 py-5 space-y-5">

                {{-- Header --}}
                <div>
                    <h1 class="text-xl font-bold text-white">{{ $exercise->translate()->name }}</h1>
                    <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                        @if($exercise->equipment?->translate()?->name)
                            <span class="text-xs px-2.5 py-1 rounded-full bg-zinc-800 text-zinc-400">
                                {{ $exercise->equipment->translate()->name }}
                            </span>
                        @endif
                        @if($exercise->primaryMuscle?->translate()?->name)
                            <span class="text-xs px-2.5 py-1 rounded-full bg-blue-500/10 text-blue-400 border border-blue-500/20">
                                {{ $exercise->primaryMuscle->translate()->name }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Tabs --}}
                <div class="flex gap-1 bg-zinc-800 rounded-xl p-1">
                    <button wire:click="setTab('howto')"
                        class="flex-1 py-2 rounded-lg text-xs font-medium transition
                            {{ $tab === 'howto' ? 'bg-zinc-700 text-white' : 'text-zinc-500 hover:text-zinc-300' }}">
                        {{ __('app.how_to') }}
                    </button>
                    <button wire:click="setTab('history')"
                        class="flex-1 py-2 rounded-lg text-xs font-medium transition
                            {{ $tab === 'history' ? 'bg-zinc-700 text-white' : 'text-zinc-500 hover:text-zinc-300' }}">
                        {{ __('app.history') }}
                    </button>
                    <button wire:click="setTab('stats')"
                        class="flex-1 py-2 rounded-lg text-xs font-medium transition
                            {{ $tab === 'stats' ? 'bg-zinc-700 text-white' : 'text-zinc-500 hover:text-zinc-300' }}">
                        {{ __('app.statistics') }}
                    </button>
                </div>

                {{-- Conteúdo das tabs --}}
                @if($tab === 'howto')
                    <p class="text-sm text-zinc-400 leading-relaxed">
                        {{ $exercise->translate()->description ?? __('app.exercise_instructions_here') }}
                    </p>
                @endif

                @if($tab === 'history')
                    @livewire('exercise-history', ['exerciseId' => $exercise->id], key($exercise->id))
                @endif

                @if($tab === 'stats')
                    @php
                        $pr = \App\Models\PersonalRecord::where('user_id', auth()->id())
                            ->where('exercise_id', $exercise->id)
                            ->with('workout')
                            ->first();
                    @endphp

                    @if(!$pr)
                        <div class="flex flex-col items-center py-8 text-center">
                            <div class="text-4xl mb-3">📊</div>
                            <p class="text-sm text-zinc-500">{{ __('app.no_records_yet') }}</p>
                        </div>
                    @else
                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-zinc-800 rounded-xl p-4">
                                <div class="text-[10px] text-zinc-500 uppercase tracking-wider mb-1">{{ __('app.pr_max_weight') }}</div>
                                <div class="text-2xl font-bold text-white">{{ $pr->max_weight }}<span class="text-sm font-normal text-zinc-400 ml-1">kg</span></div>
                                <div class="text-xs text-zinc-500 mt-0.5">× {{ $pr->reps_at_max_weight }} reps</div>
                            </div>
                            <div class="bg-zinc-800 rounded-xl p-4">
                                <div class="text-[10px] text-zinc-500 uppercase tracking-wider mb-1">{{ __('app.pr_1rm') }}</div>
                                <div class="text-2xl font-bold text-yellow-400">{{ number_format($pr->estimated_1rm, 1) }}<span class="text-sm font-normal text-zinc-400 ml-1">kg</span></div>
                                <div class="text-xs text-zinc-500 mt-0.5">Epley</div>
                            </div>
                            <div class="bg-zinc-800 rounded-xl p-4">
                                <div class="text-[10px] text-zinc-500 uppercase tracking-wider mb-1">{{ __('app.pr_max_reps') }}</div>
                                <div class="text-2xl font-bold text-white">{{ $pr->max_reps }}<span class="text-sm font-normal text-zinc-400 ml-1">reps</span></div>
                                <div class="text-xs text-zinc-500 mt-0.5">@ {{ $pr->weight_at_max_reps }} kg</div>
                            </div>
                            @if($pr->max_volume_set)
                            <div class="bg-zinc-800 rounded-xl p-4">
                                <div class="text-[10px] text-zinc-500 uppercase tracking-wider mb-1">{{ __('app.pr_max_volume') }}</div>
                                <div class="text-2xl font-bold text-white">{{ number_format($pr->max_volume_set) }}<span class="text-sm font-normal text-zinc-400 ml-1">kg</span></div>
                                <div class="text-xs text-zinc-500 mt-0.5">{{ __('app.pr_single_set') }}</div>
                            </div>
                            @endif
                        </div>

                        @if($pr->workout)
                            <p class="text-xs text-zinc-600 text-right">
                                {{ __('app.pr_achieved_on') }} {{ $pr->workout->started_at->format('d M Y') }}
                            </p>
                        @endif
                    @endif
                @endif

            </div>
        </div>
    @endif
</div>
