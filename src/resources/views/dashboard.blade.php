@extends('layouts.app')

@section('content')

<h1 class="text-3xl font-bold mb-8">{{ __('app.routines') }}</h1>

<div class="grid grid-cols-3 gap-8">

    {{-- Lista de Rotinas --}}
    <div class="col-span-2 space-y-6">

        @if($routine)

            <div class="bg-zinc-900 p-6 rounded-2xl hover:bg-zinc-800 transition cursor-pointer">
                <h2 class="text-xl font-semibold">
                    {{ $routine->name }}
                </h2>

                <p class="text-zinc-400 mt-2">
                    {{ $exerciseCount }} exercises • {{ number_format($volume) }} kg volume
                </p>
            </div>

        @else

            <div class="bg-zinc-900 p-6 rounded-2xl text-zinc-400">
                {{ __('app.no_routines') }}
            </div>

        @endif

    </div>

    {{-- Lateral Action Card --}}
    <div class="bg-zinc-900 p-6 rounded-2xl space-y-4 h-fit">

        <a href="#"
           class="block w-full bg-white text-black text-center py-3 rounded-xl font-semibold hover:opacity-90">
            {{ __('app.new_routine') }}
        </a>

        <a href="{{ route('library.index', app()->getLocale()) }}"
           class="block w-full bg-zinc-800 text-center py-3 rounded-xl hover:bg-zinc-700">
            {{ __('app.browse_exercises') }}
        </a>

    </div>

</div>

@endsection