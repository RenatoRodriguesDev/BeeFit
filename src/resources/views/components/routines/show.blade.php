@extends('layouts.app')

@section('content')

    <h1 class="text-3xl font-bold mb-8">
        {{ $routine->name }}
    </h1>

    <div class="space-y-6">

        @forelse($routine->routineExercises as $routineExercise)

            <div class="bg-zinc-900 p-6 rounded-2xl space-y-4">

                <h2 class="text-xl font-semibold">
                    {{ $routineExercise->exercise->translate()->name }}
                </h2>

                <div class="space-y-2">

                    @foreach($routineExercise->sets as $set)

                        <div class="flex items-center gap-4 bg-zinc-800 p-3 rounded-xl">

                            <span class="w-8 text-zinc-400">
                                {{ $set->set_number }}
                            </span>

                            <input type="number" value="{{ $set->weight }}" class="bg-zinc-700 p-2 rounded w-24" placeholder="kg">

                            <input type="number" value="{{ $set->reps }}" class="bg-zinc-700 p-2 rounded w-24" placeholder="reps">

                        </div>

                    @endforeach

                </div>

            </div>

        @empty

            <div class="text-zinc-400">
                No exercises yet.
            </div>

        @endforelse

    </div>

@endsection