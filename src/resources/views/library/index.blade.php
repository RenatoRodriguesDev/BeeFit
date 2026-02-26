@extends('layouts.app')

@section('content')

    <div class="flex h-full">

        {{-- ========================= --}}
        {{-- Painel Central --}}
        {{-- ========================= --}}
        <div class="flex-1">
            @livewire('exercise-viewer')
        </div>

        {{-- Painel Direita --}}
        {{-- ========================= --}}
        {{-- Desktop Sidebar (>= lg) --}}
        {{-- ========================= --}}
        <div class="hidden lg:block w-96">
            @livewire('library-panel')
        </div>

    </div>

    {{-- ========================= --}}
    {{-- Mobile Floating Button + Drawer --}}
    {{-- ========================= --}}

    <div x-data="{ open: false }" x-on:close-mobile-drawer.window="open = false"
        x-on:open-mobile-drawer.window="open = true" @keydown.escape.window="open = false" class="lg:hidden">

        {{-- Floating Button Mobile --}}
        <button @click="open = true"
            class="fixed bottom-20 right-6 z-40 bg-blue-600 w-14 h-14 rounded-2xl shadow-xl flex items-center justify-center">
            📚
        </button>

        {{-- Overlay --}}
        <div x-show="open" x-transition.opacity class="fixed inset-0 bg-black/70 z-50" @click="open = false">
        </div>

        {{-- Drawer --}}
        <div x-show="open" x-transition:enter="transition transform duration-300"
            x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
            x-transition:leave="transition transform duration-300" x-transition:leave-start="translate-y-0"
            x-transition:leave-end="translate-y-full"
            class="fixed bottom-0 left-0 right-0 z-50 bg-zinc-900 rounded-t-3xl p-6 max-h-[85vh] overflow-y-auto">

            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold text-white">
                    {{ __('app.library') }}
                </h2>

                <button @click="open = false" class="text-zinc-400 text-xl">
                    ✕
                </button>
            </div>

            @livewire('library-panel')

        </div>

    </div>

@endsection