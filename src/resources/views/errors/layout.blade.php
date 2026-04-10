<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('code') • BeeFit</title>
    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.bunny.net/css?family=figtree:400;500;600&display=swap" rel="stylesheet" />
</head>
<body class="min-h-screen bg-zinc-950 text-white flex items-center justify-center font-sans antialiased">
    <div class="text-center px-6">
        <img src="{{ asset('images/beefit_v2_nobg.png') }}" alt="BeeFit" class="h-16 mx-auto mb-10 opacity-80">
        <div class="text-7xl font-black text-yellow-400 mb-4">@yield('code')</div>
        <h1 class="text-2xl font-bold text-white mb-3">@yield('title')</h1>
        <p class="text-zinc-400 max-w-sm mx-auto mb-10">@yield('message')</p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            @auth
                <a href="{{ route('dashboard') }}"
                   class="px-6 py-3 rounded-xl bg-yellow-400 hover:bg-yellow-300 text-black font-semibold transition">
                    {{ __('app.back_to_dashboard') }}
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="px-6 py-3 rounded-xl bg-yellow-400 hover:bg-yellow-300 text-black font-semibold transition">
                    {{ __('app.login') }}
                </a>
            @endauth
            <a href="javascript:history.back()"
               class="px-6 py-3 rounded-xl border border-zinc-700 hover:bg-zinc-800 text-zinc-300 font-medium transition">
                {{ __('app.go_back') }}
            </a>
        </div>
    </div>
</body>
</html>