<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>BeeFit</title>
    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-black text-white">

    <div class="flex h-screen">

        {{-- Sidebar Desktop --}}
        <aside class="hidden md:flex w-64 bg-zinc-600 p-6 flex-col justify-between">

            <div>
                <a href="{{ route('dashboard') }}">
                    <img src="{{ asset('images/logo_nobg.png') }}" alt="BeeFit Logo" class="mb-8">
                </a>

                <nav class="space-y-3">
                    <a href="{{ route('dashboard') }}"
                        class="block px-4 py-2 rounded-lg hover:bg-zinc-800 {{ request()->routeIs('dashboard') ? 'bg-zinc-800' : '' }}">
                        {{ __('app.dashboard') }}
                    </a>

                    <a href="{{ route('routines.index') }}"
                        class="block px-4 py-2 rounded-lg hover:bg-zinc-800 {{ request()->routeIs('routines.*') ? 'bg-zinc-800' : '' }}">
                        {{ __('app.routines') }}
                    </a>

                    <a href="{{ route('library.index', app()->getLocale()) }}"
                        class="block px-4 py-2 rounded-lg hover:bg-zinc-800 {{ request()->routeIs('library.*') ? 'bg-zinc-800' : '' }}">
                        {{ __('app.exercises') }}
                    </a>

                    <a href="{{ route('statistics', app()->getLocale()) }}"
                        class="block px-4 py-2 rounded-lg hover:bg-zinc-800 {{ request()->routeIs('statistics') ? 'bg-zinc-800' : '' }}">
                        {{ __('app.statistics') }}
                    </a>

                    {{-- Social section --}}
                    <div class="pt-2 border-t border-zinc-700">
                        <p class="text-xs text-zinc-500 uppercase tracking-widest px-4 mb-2">{{ __('app.social') }}</p>
                        <a href="{{ route('social.feed') }}"
                            class="block px-4 py-2 rounded-lg hover:bg-zinc-800 {{ request()->routeIs('social.feed') ? 'bg-zinc-800' : '' }}">
                            📰 {{ __('app.feed') }}
                        </a>
                        <a href="{{ route('social.profile') }}"
                            class="block px-4 py-2 rounded-lg hover:bg-zinc-800 {{ request()->routeIs('social.profile') && !request()->route('userId') ? 'bg-zinc-800' : '' }}">
                            👤 {{ __('app.my_profile') }}
                        </a>
                    </div>
                </nav>
            </div>

            {{-- Notification Bell (Desktop) --}}
            <div class="py-3 border-t border-zinc-700 flex items-center gap-2">
                <livewire:notification-bell />
                <span class="text-xs text-zinc-400">{{ __('app.notifications') }}</span>
            </div>

            <div x-data="{ open: false }" class="relative">

                <!-- User Button -->
                <button @click="open = !open" class="w-full flex items-center gap-3 p-3 rounded-xl
               bg-zinc-800/40 backdrop-blur-lg
               hover:bg-zinc-800 transition">

                    <!-- Avatar -->
                    <div class="w-9 h-9 overflow-hidden rounded-full bg-gradient-to-br from-purple-500 to-pink-500
                    flex items-center justify-center text-white font-semibold">

                        @if(auth()->user()->avatar_path)
                            <img src="{{ asset('storage/' . auth()->user()->avatar_path) }}" alt="{{ auth()->user()->name }}"
                                class="w-full h-full object-cover">
                        @else
                            <span class="text-2xl font-semibold text-zinc-400">
                                {{ auth()->user()->initials() }}
                            </span>
                        @endif
                    </div>

                    <div class="flex-1 text-left">
                        <div class="text-white text-sm font-medium">
                            {{ strtok(auth()->user()->name, ' ') }}
                        </div>

                        <div class="text-xs text-zinc-400">
                            {{ auth()->user()->plan ?? '' }}
                        </div>
                    </div>

                    <span class="text-zinc-400">⚙️</span>
                </button>

                <!-- Dropdown -->
                <div x-show="open" @click.away="open = false" x-transition class="absolute bottom-16 left-0 w-full
               bg-zinc-600/95 backdrop-blur-xl
               border border-zinc-800
               rounded-xl shadow-2xl overflow-hidden z-50">

                    <a href="{{ route('profile.edit') }}"
                        class="flex items-center gap-3 px-4 py-3 text-sm hover:bg-zinc-800 transition">
                        ✏️ <span>{{ __('app.change_profile') }}</span>
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button class="w-full flex items-center gap-3 px-4 py-3 text-sm hover:bg-red-600 transition">
                            🚪 <span>{{ __('app.logout') }}</span>
                        </button>
                    </form>

                </div>
            </div>

        </aside>

        <livewire:active-workout-banner />


        {{-- Main --}}
        <div class="flex-1 flex flex-col">

            <main id="app-content" class="flex-1 p-6 md:p-10 overflow-y-auto pb-24 md:pb-10">

                @isset($slot)
                    {{ $slot }}
                @else
                    @yield('content')
                @endisset

            </main>

        </div>

    </div>


    {{-- 📱 Bottom Navigation Mobile --}}
    <nav class="md:hidden fixed bottom-0 left-0 right-0
            bg-zinc-600/95 backdrop-blur-xl
            border-t border-zinc-800
            flex justify-around py-2 z-40">

        <a href="{{ route('dashboard') }}" class="flex flex-col items-center text-[10px] gap-0.5 px-1
              {{ request()->routeIs('dashboard') ? 'text-white' : 'text-zinc-500' }}">
            <span class="text-xl leading-none">🏠</span>
            {{ __('app.dashboard') }}
        </a>

        <a href="{{ route('routines.index') }}" class="flex flex-col items-center text-[10px] gap-0.5 px-1
              {{ request()->routeIs('routines.*') ? 'text-white' : 'text-zinc-500' }}">
            <span class="text-xl leading-none">💪</span>
            {{ __('app.routines') }}
        </a>

        <a href="{{ route('library.index', app()->getLocale()) }}" class="flex flex-col items-center text-[10px] gap-0.5 px-1
              {{ request()->routeIs('library.*') ? 'text-white' : 'text-zinc-500' }}">
            <span class="text-xl leading-none">📚</span>
            {{ __('app.exercises') }}
        </a>

        <a href="{{ route('statistics', app()->getLocale()) }}" class="flex flex-col items-center text-[10px] gap-0.5 px-1
              {{ request()->routeIs('statistics') ? 'text-white' : 'text-zinc-500' }}">
            <span class="text-xl leading-none">📊</span>
            {{ __('app.statistics') }}
        </a>

        {{-- Notification Bell Mobile --}}
        <livewire:notification-bell />

        {{-- Social Dropdown --}}
        <div x-data="{ socialOpen: false }">

            <button @click="socialOpen = !socialOpen"
                class="flex flex-col items-center text-[10px] gap-0.5 px-1
                    {{ request()->routeIs('social.*') ? 'text-white' : 'text-zinc-500' }}">
                <span class="text-xl leading-none">👥</span>
                {{ __('app.social') }}
            </button>

            <!-- Overlay -->
            <div x-show="socialOpen" class="fixed inset-0 bg-black/70 backdrop-blur-md z-50"
                x-transition.opacity @click="socialOpen = false"></div>

            <!-- Social Sheet (Slide Up) -->
            <div x-show="socialOpen"
                x-transition:enter="transition transform duration-300"
                x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
                x-transition:leave="transition transform duration-300" x-transition:leave-start="translate-y-0"
                x-transition:leave-end="translate-y-full"
                class="fixed bottom-0 left-0 right-0
                       bg-zinc-600/95 backdrop-blur-xl
                       border-t border-zinc-800
                       rounded-t-3xl p-6 z-50">

                <div class="flex flex-col gap-4">

                    <div class="text-center text-white font-medium border-b border-zinc-800 pb-3">
                        👥 {{ __('app.social') }}
                    </div>

                    <a href="{{ route('social.feed') }}" @click="socialOpen = false"
                        class="flex items-center gap-3 p-3 rounded-xl hover:bg-zinc-800 transition">
                        📰 {{ __('app.feed') }}
                    </a>

                    <a href="{{ route('social.profile') }}" @click="socialOpen = false"
                        class="flex items-center gap-3 p-3 rounded-xl hover:bg-zinc-800 transition">
                        👤 {{ __('app.my_profile') }}
                    </a>

                    <button @click="socialOpen = false"
                        class="w-full p-3 text-center text-zinc-400 hover:text-white transition">
                        ✕ {{ __('app.close') }}
                    </button>

                </div>
            </div>

        </div>

        {{-- Profile Modal Trigger --}}
        <div x-data="{ open: false }">

            <button @click="open = true" class="flex flex-col items-center text-[10px] gap-0.5 px-1
                {{ request()->routeIs('profile.*') ? 'text-white' : 'text-zinc-500' }}">
                <span class="text-xl leading-none">⚙️</span>
                {{ __('app.profile') }}
            </button>

            <!-- Overlay -->
            <div x-show="open" class="fixed inset-0 bg-black/70 backdrop-blur-md z-50" x-transition.opacity
                @click="open = false"></div>

            <!-- Modal Sheet (Slide Up) -->
            <div x-show="open" x-transition:enter="transition transform duration-300"
                x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
                x-transition:leave="transition transform duration-300" x-transition:leave-start="translate-y-0"
                x-transition:leave-end="translate-y-full" class="fixed bottom-0 left-0 right-0
                   bg-zinc-600/95 backdrop-blur-xl
                   border-t border-zinc-800
                   rounded-t-3xl p-6 z-50">

                <div class="flex flex-col gap-4">

                    <div class="text-center text-white font-medium border-b border-zinc-800 pb-3">
                        ⚙️ {{ __('app.profile') }}
                    </div>

                    <a href="{{ route('profile.edit') }}"
                        class="flex items-center gap-3 p-3 rounded-xl hover:bg-zinc-800 transition">
                        ✏️ {{ __('app.change_profile') }}
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button class="w-full flex items-center gap-3 p-3 rounded-xl hover:bg-red-600 transition">
                            🚪 {{ __('app.logout') }}
                        </button>
                    </form>

                    <button @click="open = false"
                        class="w-full p-3 text-center text-zinc-400 hover:text-white transition">
                        ✕ {{ __('app.close') }}
                    </button>

                </div>
            </div>

        </div>
    </nav>
    @livewireScripts
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>

</html>
<script>
    document.addEventListener('livewire:init', () => {

        Livewire.on('toast', (event) => {
            window.toast(event.message, event.type ?? 'success');
        });

    });
</script>