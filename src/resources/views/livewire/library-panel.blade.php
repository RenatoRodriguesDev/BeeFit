<div class="flex flex-col h-full bg-zinc-900 border-r border-zinc-800">

    {{-- Header --}}
    <div class="px-4 py-4 border-b border-zinc-800">
        <h2 class="font-semibold text-white mb-3">{{ __('app.library') }}</h2>

        {{-- Search --}}
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="{{ __('app.browse_exercises') }}"
                class="w-full bg-zinc-800 border border-zinc-700 rounded-xl pl-9 pr-4 py-2.5 text-sm placeholder-zinc-500 focus:outline-none focus:border-zinc-500 transition">
        </div>
    </div>

    {{-- Filtros --}}
    <div class="px-4 py-3 border-b border-zinc-800 flex gap-2">
        <select wire:model.live="muscle"
            class="flex-1 bg-zinc-800 border border-zinc-700 rounded-xl px-3 py-2 text-xs text-zinc-300 focus:outline-none focus:border-zinc-500 transition">
            <option value="">{{ __('app.all_muscles') }}</option>
            @foreach($musclesList as $m)
                <option value="{{ $m->id }}">{{ $m->translate()->name }}</option>
            @endforeach
        </select>
        <select wire:model.live="equipment"
            class="flex-1 bg-zinc-800 border border-zinc-700 rounded-xl px-3 py-2 text-xs text-zinc-300 focus:outline-none focus:border-zinc-500 transition">
            <option value="">{{ __('app.all_equipment') }}</option>
            @foreach($equipmentList as $item)
                <option value="{{ $item->id }}">{{ $item->translate()->name }}</option>
            @endforeach
        </select>
    </div>

    {{-- Lista de exercícios --}}
    <div class="flex-1 overflow-y-auto py-2">
        @forelse($exercises as $exercise)
            <div wire:key="ex-{{ $exercise->id }}"
                class="flex items-center gap-3 px-4 py-2.5 cursor-pointer transition
                    {{ $activeExerciseId == $exercise->id
                        ? 'bg-zinc-800 border-l-2 border-white'
                        : 'border-l-2 border-transparent hover:bg-zinc-800/60' }}">

                <button wire:click="selectExercise({{ $exercise->id }})" class="flex items-center gap-3 flex-1 text-left min-w-0">
                    <div class="w-9 h-9 rounded-lg bg-zinc-800 overflow-hidden flex items-center justify-center shrink-0">
                        @if($exercise->thumbnail_path)
                            <img src="{{ asset($exercise->thumbnail_path) }}"
                                alt="{{ $exercise->translate()->name }}"
                                class="w-full h-full object-cover" loading="lazy">
                        @else
                            <span class="text-zinc-600 text-xs">?</span>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <div class="text-sm font-medium text-white truncate">{{ $exercise->translate()->name }}</div>
                        <div class="text-xs text-zinc-500 truncate">{{ $exercise->primaryMuscle->translate()->name ?? '' }}</div>
                    </div>
                </button>

                <button wire:click.stop="openRoutineModal({{ $exercise->id }})"
                    class="shrink-0 w-7 h-7 flex items-center justify-center rounded-lg bg-zinc-700 hover:bg-blue-600 text-zinc-300 hover:text-white transition text-sm font-bold"
                    title="{{ __('app.add') }}">
                    +
                </button>
            </div>
        @empty
            <div class="text-center text-zinc-600 text-sm py-12">
                {{ __('app.no_exercises_yet') }}
            </div>
        @endforelse
    </div>

    {{-- Modal: adicionar a rotina --}}
    @if($showRoutineModal)
        <div class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-end sm:items-center justify-center z-50 p-4">
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 w-full max-w-sm space-y-4">
                <h3 class="font-semibold text-white">{{ __('app.select_routine') }}</h3>
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
                        class="flex-1 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-sm font-semibold transition">
                        {{ __('app.add') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
