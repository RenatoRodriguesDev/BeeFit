<div class="h-full flex flex-col">

    @if(!$exercise)

        {{-- Empty state --}}
        <div class="flex-1 flex flex-col items-center justify-center text-center p-10">
            <div class="w-20 h-20 rounded-2xl bg-zinc-800/40 flex items-center justify-center mb-5">
                <svg class="w-9 h-9 text-zinc-700" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/>
                </svg>
            </div>
            <p class="text-zinc-400 text-sm font-medium">{{ __('app.select_exercise_from_library') }}</p>
            <p class="text-zinc-600 text-xs mt-1">{{ __('app.browse_exercises') }}</p>
        </div>

    @else

        {{-- DESKTOP --}}
        <div class="hidden lg:block flex-1 overflow-y-auto p-8 space-y-5">

            {{-- Heading --}}
            <h1 class="text-2xl font-bold text-white">{{ __('app.exercise') }}</h1>

            {{-- Card principal: info + imagem --}}
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 flex items-center gap-6"
                 wire:key="desktop-card-{{ $exercise->id }}">

                {{-- Info --}}
                <div class="flex-1 min-w-0 space-y-3">
                    <h2 class="text-2xl font-bold text-white">{{ $exercise->translate()->name }}</h2>

                    <div class="space-y-1.5">
                        @if($exercise->equipment?->translate()?->name)
                            <div class="text-sm text-zinc-400">
                                <span class="text-zinc-600">{{ __('app.equipment') }}:</span>
                                <span class="text-white font-medium ml-1">{{ $exercise->equipment->translate()->name }}</span>
                            </div>
                        @endif
                        @if($exercise->primaryMuscle?->translate()?->name)
                            <div class="text-sm text-zinc-400">
                                <span class="text-zinc-600">{{ __('app.primary_muscle') }}:</span>
                                <span class="text-white font-medium ml-1">{{ $exercise->primaryMuscle->translate()->name }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Imagem/vídeo --}}
                <div class="shrink-0 w-72 h-52 rounded-xl overflow-hidden bg-zinc-800 flex items-center justify-center">
                    @if($exercise->has_video)
                        <video autoplay loop muted class="w-full h-full object-cover">
                            <source src="{{ asset($exercise->video_path) }}" type="video/mp4">
                        </video>
                    @elseif($exercise->thumbnail_path)
                        <img src="{{ asset($exercise->thumbnail_path) }}"
                            class="w-full h-full object-contain"
                            alt="{{ $exercise->translate()->name }}">
                    @else
                        <svg class="w-12 h-12 text-zinc-700" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909"/>
                        </svg>
                    @endif
                </div>
            </div>

            {{-- Card tabs --}}
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden">

                {{-- Tab bar --}}
                <div class="flex border-b border-zinc-800 px-2">
                    <button wire:click="setTab('stats')"
                        class="px-5 py-4 text-sm font-medium border-b-2 transition -mb-px
                            {{ $tab === 'stats' ? 'border-blue-500 text-blue-400' : 'border-transparent text-zinc-500 hover:text-zinc-300' }}">
                        {{ __('app.statistics') }}
                    </button>
                    <button wire:click="setTab('history')"
                        class="px-5 py-4 text-sm font-medium border-b-2 transition -mb-px
                            {{ $tab === 'history' ? 'border-blue-500 text-blue-400' : 'border-transparent text-zinc-500 hover:text-zinc-300' }}">
                        {{ __('app.history') }}
                    </button>
                    <button wire:click="setTab('howto')"
                        class="px-5 py-4 text-sm font-medium border-b-2 transition -mb-px
                            {{ $tab === 'howto' ? 'border-blue-500 text-blue-400' : 'border-transparent text-zinc-500 hover:text-zinc-300' }}">
                        {{ __('app.how_to') }}
                    </button>
                </div>

                {{-- Tab content --}}
                <div class="p-6">

                    @if($tab === 'stats')
                        @include('livewire.partials.exercise-stats', ['exercise' => $exercise])
                    @endif

                    @if($tab === 'history')
                        @livewire('exercise-history', ['exerciseId' => $exercise->id], key('d-h-'.$exercise->id))
                    @endif

                    @if($tab === 'howto')
                        @php $desc = $exercise->translate()->description ?? null; @endphp
                        @if($desc)
                            <p class="text-sm text-zinc-400 leading-relaxed">{{ $desc }}</p>
                        @else
                            <div class="py-8 text-center">
                                <p class="text-sm text-zinc-600">{{ __('app.exercise_instructions_here') }}</p>
                            </div>
                        @endif
                    @endif

                </div>
            </div>

        </div>

        {{-- MOBILE --}}
        <div class="lg:hidden" wire:key="mobile-{{ $exercise->id }}">

            {{-- Media --}}
            <div class="bg-zinc-950 w-full overflow-hidden flex items-center justify-center" style="height: 240px">
                @if($exercise->has_video)
                    <video autoplay loop muted playsinline class="w-full h-full object-cover">
                        <source src="{{ asset($exercise->video_path) }}" type="video/mp4">
                    </video>
                @elseif($exercise->thumbnail_path)
                    <img src="{{ asset($exercise->thumbnail_path) }}"
                        class="w-full h-full object-contain"
                        alt="{{ $exercise->translate()->name }}">
                @else
                    <div class="flex flex-col items-center gap-2 text-zinc-700">
                        <svg class="w-14 h-14" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159"/>
                        </svg>
                    </div>
                @endif
            </div>

            {{-- Info + tabs --}}
            <div class="px-5 pt-4 pb-6 space-y-4">

                {{-- Name + tags --}}
                <div>
                    <h1 class="text-xl font-bold text-white leading-tight">{{ $exercise->translate()->name }}</h1>
                    <div class="flex flex-wrap gap-2 mt-2.5">
                        @if($exercise->equipment?->translate()?->name)
                            <span class="inline-flex items-center gap-1 text-xs px-3 py-1 rounded-full bg-zinc-800 text-zinc-400 font-medium">
                                {{ $exercise->equipment->translate()->name }}
                            </span>
                        @endif
                        @if($exercise->primaryMuscle?->translate()?->name)
                            <span class="inline-flex items-center gap-1 text-xs px-3 py-1 rounded-full bg-yellow-500/10 text-yellow-400 border border-yellow-500/20 font-medium">
                                {{ $exercise->primaryMuscle->translate()->name }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Tabs --}}
                <div class="flex gap-1 bg-zinc-800/50 rounded-xl p-1">
                    <button wire:click="setTab('stats')"
                        class="flex-1 py-2 rounded-lg text-xs font-semibold transition
                            {{ $tab === 'stats' ? 'bg-zinc-700 text-white shadow-sm' : 'text-zinc-500 hover:text-zinc-300' }}">
                        {{ __('app.statistics') }}
                    </button>
                    <button wire:click="setTab('history')"
                        class="flex-1 py-2 rounded-lg text-xs font-semibold transition
                            {{ $tab === 'history' ? 'bg-zinc-700 text-white shadow-sm' : 'text-zinc-500 hover:text-zinc-300' }}">
                        {{ __('app.history') }}
                    </button>
                    <button wire:click="setTab('howto')"
                        class="flex-1 py-2 rounded-lg text-xs font-semibold transition
                            {{ $tab === 'howto' ? 'bg-zinc-700 text-white shadow-sm' : 'text-zinc-500 hover:text-zinc-300' }}">
                        {{ __('app.how_to') }}
                    </button>
                </div>

                {{-- Tab content --}}
                <div>
                    @if($tab === 'stats')
                        @include('livewire.partials.exercise-stats', ['exercise' => $exercise])
                    @endif

                    @if($tab === 'history')
                        @livewire('exercise-history', ['exerciseId' => $exercise->id], key('m-h-'.$exercise->id))
                    @endif

                    @if($tab === 'howto')
                        @php $desc = $exercise->translate()->description ?? null; @endphp
                        @if($desc)
                            <p class="text-sm text-zinc-400 leading-relaxed">{{ $desc }}</p>
                        @else
                            <div class="py-8 text-center">
                                <p class="text-sm text-zinc-600">{{ __('app.exercise_instructions_here') }}</p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

    @endif
</div>
