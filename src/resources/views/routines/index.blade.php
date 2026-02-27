@extends('layouts.app')

@section('content')

<h1 class="text-3xl font-bold mb-8">
    {{ __('app.routines') }}
</h1>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <div class="lg:col-span-2">
        @livewire('routine-list')
    </div>

    <div class="bg-zinc-600 p-6 rounded-2xl h-fit">
        @livewire('routine-manager')

        <a href="{{ route('library.index', app()->getLocale()) }}"
           class="block w-full bg-zinc-800 text-center py-3 rounded-xl hover:bg-zinc-700 mt-4">
            {{ __('app.browse_exercises') }}
        </a>
    </div>

</div>

@endsection