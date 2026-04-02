<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin — BeeFit</title>
    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-zinc-950 text-white">

<div class="flex h-screen">

    {{-- SIDEBAR --}}
    <aside class="hidden md:flex w-60 bg-zinc-950 border-r border-zinc-800/60 flex-col shrink-0">

        {{-- Logo + badge --}}
        <div class="px-5 pt-5 pb-4">
            <a href="{{ route('admin.dashboard') }}" class="block">
                <img src="{{ asset('images/beefit_v2_nobg.png') }}" alt="BeeFit" class="h-16 w-auto">
            </a>
            <span class="mt-1 inline-block text-xs font-semibold bg-violet-600 text-white px-2 py-0.5 rounded-full">Admin</span>
        </div>

        <nav class="flex-1 px-3 space-y-0.5 overflow-y-auto">
            @php
                $nav = fn($active) => 'flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition ' .
                    ($active ? 'bg-zinc-800 text-white' : 'text-zinc-400 hover:text-white hover:bg-zinc-800/60');
            @endphp

            <p class="px-3 pt-3 pb-1 text-xs font-semibold text-zinc-600 uppercase tracking-wider">Overview</p>

            <a href="{{ route('admin.dashboard') }}" class="{{ $nav(request()->routeIs('admin.dashboard')) }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>

            <p class="px-3 pt-4 pb-1 text-xs font-semibold text-zinc-600 uppercase tracking-wider">Conteúdo</p>

            <a href="{{ route('admin.exercises') }}" class="{{ $nav(request()->routeIs('admin.exercises*')) }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12a7.5 7.5 0 0015 0m-15 0a7.5 7.5 0 1115 0m-15 0H3m1.5 0H21m-1.5 0H21M6.75 19.5A7.5 7.5 0 014.5 12"/>
                </svg>
                Exercícios
            </a>

            <a href="{{ route('admin.catalog') }}" class="{{ $nav(request()->routeIs('admin.catalog*')) }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                Catálogo
            </a>

            <p class="px-3 pt-4 pb-1 text-xs font-semibold text-zinc-600 uppercase tracking-wider">Gestão</p>

            <a href="{{ route('admin.users') }}" class="{{ $nav(request()->routeIs('admin.users*')) }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Utilizadores
            </a>

        </nav>

        {{-- Bottom: back to app + user --}}
        <div class="border-t border-zinc-800 p-3 space-y-1">
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-zinc-400 hover:text-white hover:bg-zinc-800/60 transition">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                </svg>
                Voltar à App
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-red-400 hover:bg-red-500/10 transition">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Logout
                </button>
            </form>
        </div>

    </aside>

    {{-- MAIN CONTENT --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- Top bar (mobile logo + title) --}}
        <header class="md:hidden flex items-center justify-between px-4 py-3 border-b border-zinc-800 bg-zinc-950">
            <img src="{{ asset('images/beefit_v2_nobg.png') }}" alt="BeeFit" class="h-9 w-auto">
            <span class="text-xs font-semibold bg-violet-600 text-white px-2 py-0.5 rounded-full">Admin</span>
        </header>

        <main class="flex-1 overflow-y-auto p-6 md:p-10">
            @isset($slot)
                {{ $slot }}
            @else
                @yield('content')
            @endisset
        </main>

    </div>

</div>

@livewireScripts
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('toast', (event) => {
            window.toast(event.message, event.type ?? 'success');
        });
    });
</script>
</body>
</html>
