<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>BeeFit</title>
    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-zinc-950 text-white">

<div class="flex h-screen">

    {{-- ── SIDEBAR DESKTOP ────────────────────────────────────────────── --}}
    <aside class="hidden md:flex w-60 bg-zinc-950 border-r border-zinc-800/60 flex-col shrink-0">

        {{-- Logo --}}
        <div class="px-5 pt-5 pb-4">
            <a href="{{ route('dashboard') }}" class="block">
                <img src="{{ asset('images/beefit_v2_nobg.png') }}" alt="BeeFit" class="h-20 w-auto">
            </a>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 px-3 space-y-0.5 overflow-y-auto">

            @php
                $navLink = fn($active) => 'flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition ' .
                    ($active ? 'bg-zinc-800 text-white' : 'text-zinc-400 hover:text-white hover:bg-zinc-800/60');
            @endphp

            <a href="{{ route('dashboard') }}" class="{{ $navLink(request()->routeIs('dashboard')) }}">
                <svg class="w-4.5 h-4.5 w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                {{ __('app.dashboard') }}
            </a>

            <a href="{{ route('routines.index') }}" class="{{ $navLink(request()->routeIs('routines.*')) }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                {{ __('app.routines') }}
            </a>

            <a href="{{ route('library.index', app()->getLocale()) }}" class="{{ $navLink(request()->routeIs('library.*')) }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                {{ __('app.exercises') }}
            </a>

            <a href="{{ route('statistics', app()->getLocale()) }}" class="{{ $navLink(request()->routeIs('statistics')) }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                {{ __('app.statistics') }}
            </a>

            {{-- Social --}}
            <div class="pt-4 pb-1">
                <p class="text-[10px] font-semibold text-zinc-600 uppercase tracking-widest px-3 mb-1">{{ __('app.social') }}</p>
            </div>

            <a href="{{ route('social.feed') }}" class="{{ $navLink(request()->routeIs('social.feed')) }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                </svg>
                {{ __('app.feed') }}
            </a>

            <a href="{{ route('social.profile') }}" class="{{ $navLink(request()->routeIs('social.profile') && !request()->route('username')) }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                {{ __('app.my_profile') }}
            </a>

        </nav>

        {{-- Notificações + User --}}
        <div class="px-3 pb-4 pt-3 border-t border-zinc-800/60 space-y-1">

            {{-- Bell --}}
            <div class="flex items-center gap-3 px-3 py-2 text-zinc-400">
                <livewire:notification-bell />
                <span class="text-sm">{{ __('app.notifications') }}</span>
            </div>

            {{-- User dropdown --}}
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-zinc-800/60 transition">

                    <div class="w-8 h-8 rounded-full overflow-hidden bg-gradient-to-br from-violet-500 to-pink-500 flex items-center justify-center shrink-0">
                        @if(auth()->user()->avatar_path)
                            <img src="{{ asset('storage/' . auth()->user()->avatar_path) }}"
                                class="w-full h-full object-cover" alt="">
                        @else
                            <span class="text-xs font-bold text-white">{{ auth()->user()->initials() }}</span>
                        @endif
                    </div>

                    <div class="flex-1 text-left min-w-0">
                        <div class="text-sm font-medium text-white truncate">{{ strtok(auth()->user()->name, ' ') }}</div>
                        <div class="text-xs text-zinc-500">{{ auth()->user()->plan ?? 'free' }}</div>
                    </div>

                    <svg class="w-4 h-4 text-zinc-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </button>

                <div x-show="open" @click.away="open = false" x-transition
                    class="absolute bottom-full left-0 mb-1 w-full bg-zinc-900 border border-zinc-800 rounded-xl shadow-2xl overflow-hidden z-50">

                    <a href="{{ route('profile.edit') }}"
                        class="flex items-center gap-3 px-4 py-3 text-sm text-zinc-300 hover:bg-zinc-800 hover:text-white transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        {{ __('app.change_profile') }}
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="w-full flex items-center gap-3 px-4 py-3 text-sm text-red-400 hover:bg-red-500/10 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            {{ __('app.logout') }}
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </aside>

    <livewire:active-workout-banner />

    {{-- Main --}}
    <div class="flex-1 flex flex-col min-w-0">
        <main id="app-content" class="flex-1 p-6 md:p-10 overflow-y-auto pb-24 md:pb-10">
            @isset($slot)
                {{ $slot }}
            @else
                @yield('content')
            @endisset
        </main>
    </div>

</div>


{{-- ── BOTTOM NAV MOBILE ───────────────────────────────────────────── --}}
<nav class="md:hidden fixed bottom-0 left-0 right-0 bg-zinc-950/95 backdrop-blur-xl border-t border-zinc-800/80 z-40">
    <div class="flex items-stretch h-16">

        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}"
            class="flex-1 flex flex-col items-center justify-center gap-1 transition
                {{ request()->routeIs('dashboard') ? 'text-white' : 'text-zinc-500' }}">
            <svg class="w-5 h-5" fill="{{ request()->routeIs('dashboard') ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span class="text-[10px] font-medium">{{ __('app.dashboard') }}</span>
        </a>

        {{-- Rotinas --}}
        <a href="{{ route('routines.index') }}"
            class="flex-1 flex flex-col items-center justify-center gap-1 transition
                {{ request()->routeIs('routines.*') ? 'text-white' : 'text-zinc-500' }}">
            <svg class="w-5 h-5" fill="{{ request()->routeIs('routines.*') ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <span class="text-[10px] font-medium">{{ __('app.routines') }}</span>
        </a>

        {{-- Biblioteca --}}
        <a href="{{ route('library.index', app()->getLocale()) }}"
            class="flex-1 flex flex-col items-center justify-center gap-1 transition
                {{ request()->routeIs('library.*') ? 'text-white' : 'text-zinc-500' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <span class="text-[10px] font-medium">{{ __('app.exercises') }}</span>
        </a>

        {{-- Social --}}
        <a href="{{ route('social.feed') }}"
            class="flex-1 flex flex-col items-center justify-center gap-1 transition
                {{ request()->routeIs('social.*') ? 'text-white' : 'text-zinc-500' }}">
            <svg class="w-5 h-5" fill="{{ request()->routeIs('social.*') ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span class="text-[10px] font-medium">{{ __('app.social') }}</span>
        </a>

        {{-- Notificações --}}
        <div class="flex-1 flex flex-col items-center justify-center">
            <livewire:notification-bell />
        </div>

        {{-- Perfil / Definições --}}
        <div x-data="{ open: false }" class="flex-1">
            <button @click="open = true"
                class="w-full h-full flex flex-col items-center justify-center gap-1 transition
                    {{ request()->routeIs('profile.*') ? 'text-white' : 'text-zinc-500' }}">
                <div class="w-6 h-6 rounded-full overflow-hidden bg-gradient-to-br from-violet-500 to-pink-500 flex items-center justify-center">
                    @if(auth()->user()->avatar_path)
                        <img src="{{ asset('storage/' . auth()->user()->avatar_path) }}"
                            class="w-full h-full object-cover" alt="">
                    @else
                        <span class="text-[9px] font-bold text-white">{{ auth()->user()->initials() }}</span>
                    @endif
                </div>
                <span class="text-[10px] font-medium">{{ __('app.profile') }}</span>
            </button>

            {{-- Overlay --}}
            <div x-show="open" @click="open = false" x-transition:enter.opacity.duration.200ms x-transition:leave.opacity.duration.150ms
                class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50"></div>

            {{-- Sheet --}}
            <div x-show="open"
                x-transition:enter="transition duration-300 transform"
                x-transition:enter-start="translate-y-full"
                x-transition:enter-end="translate-y-0"
                x-transition:leave="transition duration-200 transform"
                x-transition:leave-start="translate-y-0"
                x-transition:leave-end="translate-y-full"
                class="fixed bottom-0 left-0 right-0 bg-zinc-900 border-t border-zinc-800 rounded-t-2xl z-50 pb-safe">

                {{-- Handle --}}
                <div class="flex justify-center pt-3 pb-1">
                    <div class="w-10 h-1 rounded-full bg-zinc-700"></div>
                </div>

                {{-- User info --}}
                <div class="flex items-center gap-3 px-6 py-4 border-b border-zinc-800">
                    <div class="w-11 h-11 rounded-full overflow-hidden bg-gradient-to-br from-violet-500 to-pink-500 flex items-center justify-center shrink-0">
                        @if(auth()->user()->avatar_path)
                            <img src="{{ asset('storage/' . auth()->user()->avatar_path) }}"
                                class="w-full h-full object-cover" alt="">
                        @else
                            <span class="text-sm font-bold text-white">{{ auth()->user()->initials() }}</span>
                        @endif
                    </div>
                    <div>
                        <div class="font-semibold text-white">{{ auth()->user()->name }}</div>
                        <div class="text-xs text-zinc-500">{{ auth()->user()->plan ?? 'free' }}</div>
                    </div>
                </div>

                <div class="p-4 space-y-1">
                    <a href="{{ route('profile.edit') }}" @click="open = false"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-zinc-300 hover:bg-zinc-800 hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        {{ __('app.change_profile') }}
                    </a>

                    <a href="{{ route('social.profile') }}" @click="open = false"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-zinc-300 hover:bg-zinc-800 hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        {{ __('app.my_profile') }}
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-red-400 hover:bg-red-500/10 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            {{ __('app.logout') }}
                        </button>
                    </form>
                </div>

                <div class="px-4 pb-6">
                    <button @click="open = false"
                        class="w-full py-3 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-sm text-zinc-400 transition">
                        {{ __('app.close') }}
                    </button>
                </div>
            </div>
        </div>

    </div>
</nav>

@livewireScripts
</body>
</html>
<script>
    document.addEventListener('livewire:init', () => {
        const userId = {{ auth()->id() ?? 0 }};
        if (userId && window.Echo) {
            window.Echo.private(`App.Models.User.${userId}`)
                .notification(() => {
                    Livewire.dispatch('notificationReceived');
                });
        }

        Livewire.on('toast', (event) => {
            window.toast(event.message, event.type ?? 'success');
        });
    });
</script>
