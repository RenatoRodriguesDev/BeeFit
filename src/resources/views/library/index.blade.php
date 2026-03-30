@extends('layouts.app')

@section('content')

<div x-data="{ modal: false }"
     @exercise-clicked.window="modal = true"
     class="-mx-6 -mt-6 -mb-24 md:-mx-10 md:-mt-10 md:-mb-10 lg:h-screen lg:overflow-hidden">

    {{-- DESKTOP: viewer à esquerda, painel à direita --}}
    <div class="hidden lg:flex h-full">

        {{-- Viewer (área principal) --}}
        <div class="flex-1 overflow-y-auto bg-zinc-950">
            @livewire('exercise-viewer', [], key('desktop'))
        </div>

        {{-- Painel da biblioteca (sidebar direita) --}}
        <div class="w-96 shrink-0 border-l border-zinc-800 flex flex-col overflow-hidden bg-zinc-900">
            @livewire('library-panel')
        </div>

    </div>

    {{-- MOBILE: só o painel --}}
    <div class="lg:hidden">
        @livewire('library-panel', [], key('mobile-panel'))
    </div>

    {{-- MOBILE: bottom sheet modal --}}
    <div x-show="modal"
         x-transition:enter="transition duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="lg:hidden fixed inset-0 z-50"
         style="display: none">

        <div @click="modal = false" class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>

        <div x-show="modal"
             x-transition:enter="transition duration-300 transform"
             x-transition:enter-start="translate-y-full"
             x-transition:enter-end="translate-y-0"
             x-transition:leave="transition duration-200 transform"
             x-transition:leave-start="translate-y-0"
             x-transition:leave-end="translate-y-full"
             class="absolute bottom-0 left-0 right-0 bg-zinc-900 rounded-t-3xl overflow-hidden flex flex-col"
             style="height: 92vh">

            <div class="shrink-0 relative flex items-center justify-center px-5 pt-3 pb-3 border-b border-zinc-800">
                <div class="w-10 h-1 rounded-full bg-zinc-700"></div>
                <button @click="modal = false"
                    class="absolute right-4 top-2.5 p-1.5 rounded-xl text-zinc-500 hover:text-white hover:bg-zinc-800 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto">
                @livewire('exercise-viewer', [], key('mobile'))
            </div>

        </div>
    </div>

</div>

@endsection
