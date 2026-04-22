@extends('layouts.app')

@section('content')

<div x-data="{
        modal: false,
        dragY: 0,
        dragStartY: 0,
        dragging: false,
        startDrag(e) {
            this.dragging = true;
            this.dragStartY = e.touches ? e.touches[0].clientY : e.clientY;
        },
        onDrag(e) {
            if (!this.dragging) return;
            const y = e.touches ? e.touches[0].clientY : e.clientY;
            this.dragY = Math.max(0, y - this.dragStartY);
        },
        endDrag() {
            if (this.dragY > 100) { this.modal = false; }
            this.dragY = 0;
            this.dragging = false;
        },
        close() { this.modal = false; this.dragY = 0; }
     }"
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

        {{-- Backdrop --}}
        <div @click="close()"
             :style="{ opacity: 1 - (dragY / 300) }"
             class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>

        {{-- Sheet --}}
        <div x-show="modal"
             x-transition:enter="transition duration-300 ease-out transform"
             x-transition:enter-start="translate-y-full"
             x-transition:enter-end="translate-y-0"
             x-transition:leave="transition duration-200 ease-in transform"
             x-transition:leave-start="translate-y-0"
             x-transition:leave-end="translate-y-full"
             :style="{ transform: `translateY(${dragY}px)`, transition: dragging ? 'none' : '' }"
             @touchend.window="endDrag()"
             @touchmove.window.passive="onDrag($event)"
             class="absolute bottom-0 left-0 right-0 bg-zinc-900 rounded-t-3xl flex flex-col shadow-2xl"
             style="height: 92vh">

            {{-- Drag handle zone --}}
            <div class="shrink-0 flex flex-col items-center pt-3 pb-2 cursor-grab active:cursor-grabbing select-none"
                 @touchstart.passive="startDrag($event)"
                 @mousedown="startDrag($event)"
                 @mouseup.window="endDrag()"
                 @mousemove.window="onDrag($event)">
                <div class="w-12 h-1.5 rounded-full bg-zinc-600 mb-2"></div>
            </div>

            {{-- Top bar: title + close --}}
            <div class="shrink-0 flex items-center justify-between px-5 pb-3">
                <p class="text-xs font-semibold text-zinc-500 uppercase tracking-widest">{{ __('app.exercise') }}</p>
                <button @click="close()"
                    class="w-8 h-8 flex items-center justify-center rounded-full bg-zinc-800 text-zinc-400 hover:text-white hover:bg-zinc-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
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
