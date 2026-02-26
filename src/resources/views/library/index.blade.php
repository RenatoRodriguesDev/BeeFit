@extends('layouts.app')

@section('content')

<div class="flex h-full">

    {{-- Painel Central --}}
    <div class="flex-1">
        @livewire('exercise-viewer')
    </div>

    {{-- Painel Direita --}}
    <div class="hidden lg:block w-96">
        @livewire('library-panel')
    </div>

</div>

@endsection