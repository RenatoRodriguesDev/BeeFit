@extends('layouts.app')

@section('content')

    <div class="flex flex-col lg:flex-row h-full">

        {{-- ========================= --}}
        {{-- Painel Central --}}
        {{-- ========================= --}}
        <div class="flex-1">
            @livewire('exercise-viewer')
        </div>

        {{-- Painel Direita --}}
        {{-- ========================= --}}
        {{-- Desktop Sidebar (>= lg) --}}
        {{-- ========================= --}}
        <div class="block w-96">
            @livewire('library-panel')
        </div>

    </div>
@endsection