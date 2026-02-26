<div class="p-6 lg:p-10">

    @if(!$exercise)
        <div class="bg-zinc-900 rounded-3xl p-8 text-zinc-400">
            {{ __('app.select_exercise_from_library') }}
        </div>
    @else

        <div x-data="{ show: false, videoUrl: '{{ $exercise->video_path }}' }" x-init="setTimeout(() => show = true, 50)"
            x-show="show" x-transition.opacity.duration.300ms class="p-6 lg:p-10">

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
                    <img
                        src="{{ asset($exercise->thumbnail_path) }}"
                        class="w-full h-64 object-cover"
                        alt="{{ $exercise->translate()->name }}"
                    >
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
                    <div class="text-zinc-300 mt-4">
                        {{ __('app.user_workout_history_here') }}
                    </div>
                @endif

                @if($tab === 'stats')
                    <div class="text-zinc-300 mt-4">
                        {{ __('app.graphs_progress_stats_here') }}
                    </div>
                @endif
            </div>

        </div>

    @endif
</div>