<div class="flex flex-col h-full">

    {{-- Header desktop --}}
    <div class="shrink-0 hidden lg:flex items-center justify-between px-5 py-4 border-b border-zinc-800">
        <h2 class="font-bold text-white text-base">{{ __('app.library') }}</h2>
    </div>

    {{-- Header mobile --}}
    <div class="lg:hidden shrink-0 p-4 border-b border-zinc-800">
        <h2 class="font-semibold text-white text-sm mb-3">{{ __('app.exercises') }}</h2>
        {{-- Search mobile --}}
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" wire:model.live.debounce.300ms="search"
                placeholder="{{ __('app.browse_exercises') }}"
                class="w-full bg-zinc-800 border border-zinc-700/60 rounded-xl pl-9 pr-9 py-2.5 text-sm placeholder-zinc-600 focus:outline-none focus:border-zinc-500 transition">
            @if($search)
                <button wire:click="$set('search', '')" class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-500 hover:text-zinc-300 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            @endif
        </div>
    </div>

    {{-- Filtros --}}
    <div class="shrink-0 px-4 py-3 space-y-2 border-b border-zinc-800">
        <select wire:model.live="equipment"
            class="w-full bg-zinc-800 border border-zinc-700/60 rounded-xl px-3 py-2.5 text-sm text-zinc-300 focus:outline-none focus:border-zinc-500 transition">
            <option value="">{{ __('app.all_equipment') }}</option>
            @foreach($equipmentList as $item)
                <option value="{{ $item->id }}">{{ $item->translate()->name }}</option>
            @endforeach
        </select>
        <select wire:model.live="muscle"
            class="w-full bg-zinc-800 border border-zinc-700/60 rounded-xl px-3 py-2.5 text-sm text-zinc-300 focus:outline-none focus:border-zinc-500 transition">
            <option value="">{{ __('app.all_muscles') }}</option>
            @foreach($musclesList as $m)
                <option value="{{ $m->id }}">{{ $m->translate()->name }}</option>
            @endforeach
        </select>

        {{-- Search desktop --}}
        <div class="relative hidden lg:block">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" wire:model.live.debounce.300ms="search"
                placeholder="{{ __('app.browse_exercises') }}"
                class="w-full bg-zinc-800 border border-zinc-700/60 rounded-xl pl-9 pr-9 py-2.5 text-sm placeholder-zinc-600 focus:outline-none focus:border-zinc-500 transition">
            @if($search)
                <button wire:click="$set('search', '')" class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-500 hover:text-zinc-300 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            @endif
        </div>
    </div>

    {{-- Label --}}
    <div class="shrink-0 px-5 py-2.5 flex items-center justify-between">
        <span class="text-xs font-semibold text-zinc-500 uppercase tracking-wider">{{ __('app.exercises') }}</span>
        <span class="text-xs text-zinc-600">{{ $exercises->count() }}</span>
    </div>

    {{-- Lista --}}
    <div class="flex-1 overflow-y-auto">
        @forelse($exercises as $exercise)
            <div wire:key="ex-{{ $exercise->id }}"
                wire:click="selectExercise({{ $exercise->id }})"
                class="group flex items-center gap-3 px-4 py-2.5 cursor-pointer transition
                    {{ $activeExerciseId == $exercise->id
                        ? 'bg-zinc-800'
                        : 'hover:bg-zinc-800/50' }}">

                {{-- Thumbnail circular --}}
                <div class="w-12 h-12 rounded-full overflow-hidden bg-zinc-800 shrink-0 ring-1 ring-zinc-700/50">
                    @if($exercise->thumbnail_path)
                        <img src="{{ asset($exercise->thumbnail_path) }}"
                            alt="{{ $exercise->translate()->name }}"
                            class="w-full h-full object-cover" loading="lazy">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-lg">&#x1F4AA;</div>
                    @endif
                </div>

                {{-- Nome + músculo --}}
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-semibold truncate
                        {{ $activeExerciseId == $exercise->id ? 'text-white' : 'text-zinc-200' }}">
                        {{ $exercise->translate()->name }}
                    </div>
                    <div class="text-xs text-zinc-500 truncate mt-0.5">
                        {{ $exercise->primaryMuscle?->translate()?->name ?? '' }}
                    </div>
                </div>

                {{-- Botão + --}}
                <button wire:click.stop="openRoutineModal({{ $exercise->id }})"
                    class="shrink-0 w-7 h-7 flex items-center justify-center rounded-lg text-zinc-600 hover:bg-violet-600/20 hover:text-violet-400 transition opacity-0 group-hover:opacity-100 {{ $activeExerciseId == $exercise->id ? 'opacity-100' : '' }}"
                    title="{{ __('app.add') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                </button>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-16 text-center px-6">
                <svg class="w-10 h-10 text-zinc-700 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <p class="text-zinc-500 text-sm">{{ __('app.no_exercises_yet') }}</p>
            </div>
        @endforelse
    </div>

    {{-- Modal: adicionar a rotina --}}
    @if($showRoutineModal)
        <div class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-end sm:items-center justify-center z-50 p-4">
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 w-full max-w-sm space-y-4">
                <div>
                    <h3 class="font-semibold text-white">{{ __('app.select_routine') }}</h3>
                    <p class="text-xs text-zinc-500 mt-0.5">{{ __('app.choose_routine') }}</p>
                </div>
                <select wire:model="selectedRoutineId"
                    class="w-full bg-zinc-800 border border-zinc-700 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-zinc-500 transition">
                    <option value="">{{ __('app.choose_routine') }}</option>
                    @foreach(auth()->user()->routines as $routine)
                        <option value="{{ $routine->id }}">{{ $routine->name }}</option>
                    @endforeach
                </select>
                <div class="flex gap-3">
                    <button wire:click="$set('showRoutineModal', false)"
                        class="flex-1 py-2.5 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-sm transition">
                        {{ __('app.cancel') }}
                    </button>
                    <button wire:click="addToSelectedRoutine"
                        class="flex-1 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-500 text-sm font-semibold transition">
                        {{ __('app.add') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
