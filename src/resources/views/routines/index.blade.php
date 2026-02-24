@extends('layouts.app')

@section('content')

<h1 class="text-3xl font-bold mb-8">{{ __('app.routines') }}</h1>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    {{-- Lista --}}
    <div class="lg:col-span-2 space-y-6">

        @forelse($routines as $routine)

            <div class="bg-zinc-900 p-5 md:p-6 rounded-2xl hover:bg-zinc-800 transition cursor-pointer">

                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold">
                        {{ $routine->name }}
                    </h2>

                    <div class="text-zinc-500 text-sm">
                        {{ $routine->exercises_count }} exercises
                    </div>
                </div>

            </div>

        @empty

            <div class="bg-zinc-900 p-6 rounded-2xl text-zinc-400">
                {{ __('app.no_routines') }}
            </div>

        @endforelse

    </div>

    {{-- Action Card --}}
    <div class="bg-zinc-900 p-6 rounded-2xl space-y-4 h-fit">

        <a href="#"
           class="block w-full bg-white text-black text-center py-3 rounded-xl font-semibold hover:opacity-90">
            {{ __('app.new_routine') }}
        </a>

        <button class="block w-full bg-zinc-800 py-3 rounded-xl hover:bg-zinc-700">
            {{ __('app.new_folder') }}
        </button>

    </div>

</div>

@endsection