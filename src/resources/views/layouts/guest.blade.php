<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href="https://fonts.bunny.net/css?family=figtree:400;500;600&display=swap" rel="stylesheet" />
</head>

<body class="min-h-screen bg-gray-100 text-gray-900 font-sans antialiased">

    <div class="min-h-screen grid lg:grid-cols-2">

        <!-- LEFT SIDE -->
        <div class="flex items-center justify-center p-8 bg-white">

            <div class="w-full max-w-md">

                <div class="mb-10">
                    <x-application-logo class="w-10 h-10 text-black mb-6" />
                </div>

                {{ $slot }}

            </div>

        </div>

        <!-- RIGHT SIDE -->
        <div class="hidden lg:block relative h-screen">

            <!-- Optional background accent -->
            <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-blue-500
            rounded-full blur-3xl opacity-20 -translate-y-1/2">
            </div>

            <!-- Image -->
            <img src="{{ asset('images/mockup.png') }}" class="absolute inset-0 w-full h-full object-cover"
                alt="App preview">
        </div>

    </div>

</body>

</html>