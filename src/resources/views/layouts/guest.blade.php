<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BeeFit &mdash; Fitness Tracker</title>
    <meta name="description" content="BeeFit &mdash; Regista treinos, acompanha recordes pessoais e evolui dia apos dia.">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="BeeFit">
    <meta property="og:title" content="BeeFit &mdash; Fitness Tracker">
    <meta property="og:url" content="{{ url()->current() }}">
    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.bunny.net/css?family=figtree:400;500;600&display=swap" rel="stylesheet" />
</head>

<body class="h-screen overflow-hidden bg-white font-sans antialiased text-gray-900">

    <div class="h-screen grid lg:grid-cols-2">

        <!-- LEFT -->
        <div class="h-full overflow-y-auto flex flex-col justify-center px-8 py-6 bg-white">
            <div class="w-full max-w-md mx-auto">
                <div class="mb-5">
                    <x-application-logo class="w-8 h-8 text-black" />
                </div>
                {{ $slot }}
            </div>
        </div>

        <!-- RIGHT -->
        <div class="hidden lg:block relative h-full">
            <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-blue-500 rounded-full blur-3xl opacity-20 -translate-y-1/2"></div>
            <img src="{{ asset('images/mockup.png') }}" class="absolute inset-0 w-full h-full object-cover" alt="App preview">
        </div>

    </div>

</body>
</html>