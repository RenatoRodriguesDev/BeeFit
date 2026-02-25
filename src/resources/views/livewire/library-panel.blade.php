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

            <div wire:click="$dispatch('exerciseSelected', { exerciseId: {{ $exercise->id }} })" class="flex items-center gap-3 p-3 rounded-xl cursor-pointer transition
            {{ $activeExerciseId == $exercise->id
            ? 'bg-zinc-800 ring-1 ring-white'
            : 'hover:bg-zinc-900' }}">

                <div class="w-12 h-12 bg-zinc-800 rounded-full flex items-center justify-center text-xs">
                    IMG
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

        @endforeach

    </div>

</div>