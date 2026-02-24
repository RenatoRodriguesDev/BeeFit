<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div class="flex h-screen bg-gray-900 text-white">

    {{-- Sidebar --}}
    <div class="w-64 bg-gray-800 p-4">
        <h2 class="text-xl font-bold mb-6">BeeFit</h2>
    </div>

    {{-- Main --}}
    <div class="flex-1 p-8">
        <h1 class="text-2xl font-bold">
            {{ __('app.select_exercise') }}
        </h1>
    </div>

    {{-- Library Panel --}}
    <div class="w-96 bg-gray-800 p-4 overflow-y-auto">

        {{-- Filters --}}
        <div class="space-y-3 mb-6">

            <select wire:model.live="equipment"
                class="w-full bg-gray-700 rounded p-2">
                <option value="">{{ __('app.all_equipment') }}</option>
                @foreach($equipments as $eq)
                    <option value="{{ $eq->id }}">{{ $eq->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="muscle"
                class="w-full bg-gray-700 rounded p-2">
                <option value="">{{ __('app.all_muscles') }}</option>
                @foreach($muscles as $mus)
                    <option value="{{ $mus->id }}">{{ $mus->name }}</option>
                @endforeach
            </select>

            <input type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="{{ __('app.search_exercises') }}"
                class="w-full bg-gray-700 rounded p-2">
        </div>

        {{-- Exercise List --}}
        <div class="space-y-3">
            @foreach($exercises as $exercise)
                <div class="flex items-center gap-3 p-2 hover:bg-gray-700 rounded cursor-pointer">

                    <img src="{{ asset('storage/'.$exercise->thumbnail_path) }}"
                         class="w-12 h-12 rounded-full object-cover">

                    <div>
                        <div class="font-semibold">
                            {{ $exercise->name }}
                        </div>
                        <div class="text-sm text-gray-400">
                            {{ $exercise->primaryMuscle->name }}
                        </div>
                    </div>

                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $exercises->links() }}
        </div>

    </div>

</div>