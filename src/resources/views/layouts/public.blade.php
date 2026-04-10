<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('page_title', 'BeeFit — Fitness Tracker')</title>

    <meta name="description" content="@yield('meta_description', 'BeeFit é a app de fitness para registares treinos, acompanhares recordes pessoais e evoluíres dia após dia.')">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">

    <meta property="og:type"        content="website">
    <meta property="og:site_name"   content="BeeFit">
    <meta property="og:title"       content="@yield('og_title', 'BeeFit — Fitness Tracker')">
    <meta property="og:description" content="@yield('og_description', 'App de fitness para registar treinos e acompanhar recordes pessoais.')">
    <meta property="og:image"       content="@yield('og_image', asset('images/beefit_v2_nobg.png'))">
    <meta property="og:url"         content="{{ url()->current() }}">

    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="@yield('og_title', 'BeeFit — Fitness Tracker')">
    <meta name="twitter:description" content="@yield('og_description', 'App de fitness para registar treinos e acompanhar recordes pessoais.')">
    <meta name="twitter:image"       content="@yield('og_image', asset('images/beefit_v2_nobg.png'))">

    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.bunny.net/css?family=figtree:400;500;600&display=swap" rel="stylesheet">
</head>
<body class="bg-white text-gray-900 font-sans antialiased">
    @yield('content')
</body>
</html>
