<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>BeeFit</title>

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
        <aside class="hidden md:flex w-64 bg-zinc-900 p-6 flex-col justify-between">

            <div>
                <h1 class="text-2xl font-bold mb-8">🐝 BeeFit</h1>

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
                        class="block px-4 py-2 rounded-lg hover:bg-zinc-800">
                        {{ __('app.exercises') }}
                    </a>
                </nav>
            </div>

            <div class="text-sm text-zinc-400">
                {{ auth()->user()->name }}
            </div>

        </aside>


        {{-- Main --}}
        <div class="flex-1 flex flex-col">

            <main class="flex-1 p-6 md:p-10 overflow-y-auto pb-24 md:pb-10">

                @isset($slot)
                    {{ $slot }}
                @else
                    @yield('content')
                @endisset

            </main>

        </div>

    </div>


    {{-- 📱 Bottom Navigation Mobile --}}
    <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-zinc-900 border-t border-zinc-800 flex justify-around py-3">

        <a href="{{ route('dashboard') }}"
            class="flex flex-col items-center text-xs {{ request()->routeIs('dashboard') ? 'text-white' : 'text-zinc-500' }}">
            <span>🏠</span>
            {{ __('app.dashboard') }}
        </a>

        <a href="{{ route('routines.index') }}"
            class="flex flex-col items-center text-xs {{ request()->routeIs('routines.*') ? 'text-white' : 'text-zinc-500' }}">
            <span>💪</span>
            {{ __('app.routines') }}
        </a>

        <a href="{{ route('library.index', app()->getLocale()) }}"
            class="flex flex-col items-center text-xs text-zinc-500">
            <span>📚</span>
            {{ __('app.exercises') }}
        </a>

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