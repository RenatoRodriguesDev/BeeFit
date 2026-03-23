<div class="space-y-4">
    <h1 class="text-3xl font-bold mb-8">
        {{ __('app.exercises') }}
    </h1>
    @if(!$exercise)
        <div class="bg-zinc-600 rounded-3xl p-8 text-zinc-400">
            {{ __('app.select_exercise_from_library') }}
        </div>
    @else

        <div x-data="{ show: false, videoUrl: '{{ $exercise->video_path }}' }" x-init="setTimeout(() => show = true, 50)"
            x-show="show" x-transition.opacity.duration.300ms class="p-6 lg:p-10 bg-zinc-900">

            {{-- Header --}}
            <div>
                <h1 class="text-3xl font-bold">
                    {{ $exercise->translate()->name }}
                </h1>

                <div class="text-zinc-400 mt-2">
                    {{ $exercise->equipment->translate()->name ?? '' }}
                    •
                    {{ $exercise->primaryMuscle->translate()->name ?? '' }}
                </div>
            </div>

            {{-- Video Placeholder --}}
            <div class="bg-black rounded-3xl overflow-hidden w-full lg:w-2/3 mx-auto py-4"
                wire:key="media-{{ $exercise->id }}">

                @if($exercise->has_video)
                    <video controls autoplay loop class="w-full h-64 object-cover">
                        <source src="{{ asset($exercise->video_path) }}" type="video/mp4">
                    </video>
                @else
                    <img src="{{ asset($exercise->thumbnail_path) }}" class="w-full h-64 object-cover"
                        alt="{{ $exercise->translate()->name }}">
                @endif

            </div>

            {{-- Tabs estilo Hevy --}}
            <div class="flex gap-6 border-b border-zinc-800 pb-3 text-sm">

                <button wire:click="setTab('howto')"
                    class="{{ $tab === 'howto' ? 'text-white border-b-2 border-white pb-2' : 'text-zinc-400' }}">
                    {{ __('app.how_to') }}
                </button>

                <button wire:click="setTab('history')"
                    class="{{ $tab === 'history' ? 'text-white border-b-2 border-white pb-2' : 'text-zinc-400' }}">
                    {{ __('app.history') }}
                </button>

                <button wire:click="setTab('stats')"
                    class="{{ $tab === 'stats' ? 'text-white border-b-2 border-white pb-2' : 'text-zinc-400' }}">
                    {{ __('app.statistics') }}
                </button>

            </div>

            {{-- Conteúdo --}}
            <div class="text-zinc-300">
                @if($tab === 'howto')
                    <div class="text-zinc-300 mt-4">
                        {{ __('app.exercise_instructions_here') }}
                    </div>
                @endif

                @if($tab === 'history')
                    @if($exercise)
                        @livewire('exercise-history', ['exerciseId' => $exercise->id], key($exercise->id))
                    @endif
                @endif

                @if($tab === 'stats')
                    <div class="space-y-4 mt-4">
                        @php
                            $pr = \App\Models\PersonalRecord::where('user_id', auth()->id())
                                ->where('exercise_id', $exercise->id)
                                ->with('workout')
                                ->first();
                        @endphp

                        @if(!$pr)
                            <p class="text-zinc-400 text-sm">{{ __('app.no_records_yet') }}</p>
                        @else
                            <div class="grid grid-cols-2 gap-3">
                                <div class="bg-zinc-800 rounded-2xl p-4 space-y-1">
                                    <div class="text-xs text-zinc-400 uppercase tracking-wide">{{ __('app.pr_max_weight') }}</div>
                                    <div class="text-2xl font-bold text-white">{{ $pr->max_weight }} <span
                                            class="text-sm font-normal text-zinc-400">kg</span></div>
                                    <div class="text-xs text-zinc-500">{{ $pr->reps_at_max_weight }} reps</div>
                                </div>
                                <div class="bg-zinc-800 rounded-2xl p-4 space-y-1">
                                    <div class="text-xs text-zinc-400 uppercase tracking-wide">{{ __('app.pr_1rm') }}</div>
                                    <div class="text-2xl font-bold text-yellow-400">{{ $pr->estimated_1rm }} <span
                                            class="text-sm font-normal text-zinc-400">kg</span></div>
                                    <div class="text-xs text-zinc-500">Epley formula</div>
                                </div>
                                <div class="bg-zinc-800 rounded-2xl p-4 space-y-1">
                                    <div class="text-xs text-zinc-400 uppercase tracking-wide">{{ __('app.pr_max_volume') }}</div>
                                    <div class="text-2xl font-bold text-white">{{ number_format($pr->max_volume_set, 0) }} <span
                                            class="text-sm font-normal text-zinc-400">kg</span></div>
                                    <div class="text-xs text-zinc-500">{{ __('app.pr_single_set') }}</div>
                                </div>
                                <div class="bg-zinc-800 rounded-2xl p-4 space-y-1">
                                    <div class="text-xs text-zinc-400 uppercase tracking-wide">{{ __('app.pr_max_reps') }}</div>
                                    <div class="text-2xl font-bold text-white">{{ $pr->max_reps }} <span
                                            class="text-sm font-normal text-zinc-400">reps</span></div>
                                    <div class="text-xs text-zinc-500">{{ $pr->weight_at_max_reps }} kg</div>
                                </div>
                            </div>
                            @if($pr->workout)
                                <p class="text-xs text-zinc-500 text-right">
                                    {{ __('app.pr_achieved_on') }} {{ $pr->workout->started_at->format('d M Y') }}
                                </p>
                            @endif
                        @endif
                    </div>
                @endif
            </div>

        </div>

    @endif
</div>