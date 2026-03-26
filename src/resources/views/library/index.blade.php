@extends('layouts.app')

@section('content')

    <div class="flex flex-col lg:flex-row lg:h-full">

        {{-- Painel Central --}}
        <div class="flex-1">
            @livewire('exercise-viewer')
        </div>

        {{-- Painel Lateral --}}
        <div class="lg:w-96 lg:h-full lg:shrink-0">
            @livewire('library-panel')
        </div>

    </div>
@endsection