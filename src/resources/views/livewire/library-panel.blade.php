<div class="bg-zinc-950 w-full lg:w-96 h-screen border-l border-zinc-800 flex flex-col">

    {{-- Header --}}
    <div class="p-5 border-b border-zinc-800 flex justify-between items-center">
        <h2 class="font-semibold text-lg">{{ __('app.library') }}</h2>
        <button class="text-blue-500 text-sm">+ {{ __('app.custom_exercise') }}</button>
    </div>

    {{-- Filters --}}
    <div class="p-4 space-y-3 border-b border-zinc-800">

        <select wire:model.live="equipment" class="w-full bg-zinc-900 rounded-xl p-3 border border-zinc-800">
            <option value="">{{ __('app.all_equipment') }}</option>
            @foreach($equipmentList as $item)
                <option value="{{ $item->id }}">
                    {{ $item->translate()->name }}
                </option>
            @endforeach
        </select>

        <select wire:model.live="muscle" class="w-full bg-zinc-900 rounded-xl p-3 border border-zinc-800">
            <option value="">{{ __('app.all_muscles') }}</option>
            @foreach($musclesList as $muscle)
                <option value="{{ $muscle->id }}">
                    {{ $muscle->translate()->name }}
                </option>
            @endforeach
        </select>

        <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('app.browse_exercises') }}"
            class="w-full bg-zinc-900 rounded-xl p-3 border border-zinc-800">
    </div>

    {{-- List --}}
    <div class="flex-1 overflow-y-auto p-3 space-y-2">

        @foreach($exercises as $exercise)

            <div class="flex items-center justify-between gap-3 p-3 rounded-xl cursor-pointer transition
                {{ $activeExerciseId == $exercise->id
            ? 'bg-zinc-800 ring-1 ring-white'
            : 'hover:bg-zinc-900' }}">

                <div wire:click="$dispatch('exerciseSelected', { exerciseId: {{ $exercise->id }} })"
                    class="flex items-center gap-3 flex-1">

                    <div class="w-12 h-12 bg-zinc-800 rounded-full flex items-center justify-center text-xs object-contain">
                        <img src="{{ asset($exercise->thumbnail_path) }}" alt="{{ $exercise->translate()->name }}"
                            class="w-full h-full object-cover rounded-full">
                    </div>

                    <div>
                        <div class="font-medium">
                            {{ $exercise->translate()->name }}
                        </div>
                        <div class="text-sm text-zinc-400">
                            {{ $exercise->primaryMuscle->translate()->name ?? '' }}
                        </div>
                    </div>
                </div>

                {{-- ⭐ Botão (+) --}}
                <button wire:click="openRoutineModal({{ $exercise->id }})"
                    class="text-blue-500 hover:text-blue-400 text-xl px-2">
                    +
                </button>

            </div>

        @endforeach

    </div>
    @if($showRoutineModal)
        <div class="fixed inset-0 bg-black/70 flex items-center justify-center z-50">
            <div class="bg-zinc-900 p-6 rounded-2xl w-96">

                <h2 class="text-lg font-semibold mb-4">
                    {{__('app.select_routine')}}
                </h2>

                <select wire:model="selectedRoutineId" class="w-full bg-zinc-800 p-3 rounded-xl mb-4">
                    <option value="">{{__('app.choose_routine')}}</option>
                    @foreach(auth()->user()->routines as $routine)
                        <option value="{{ $routine->id }}">
                            {{ $routine->name }}
                        </option>
                    @endforeach
                </select>

                <div class="flex justify-end gap-3">
                    <button wire:click="$set('showRoutineModal', false)" class="text-zinc-400">
                        {{__('app.cancel')}}
                    </button>

                    <button wire:click="addToSelectedRoutine" class="bg-blue-600 px-4 py-2 rounded-xl">
                        {{__('app.add')}}
                    </button>
                </div>

            </div>
        </div>
    @endif
</div>