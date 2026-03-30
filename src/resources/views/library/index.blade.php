@extends('layouts.app')

@section('content')

    {{-- Mobile: painel + viewer empilhados --}}
    {{-- Desktop: sidebar fixa à esquerda + viewer à direita --}}
    <div class="flex flex-col lg:flex-row lg:gap-0" style="min-height: calc(100vh - 4rem)">

        {{-- Sidebar (lista + filtros) --}}
        <div class="lg:w-80 lg:shrink-0 lg:sticky lg:top-0 lg:h-screen lg:overflow-hidden border-b lg:border-b-0 lg:border-r border-zinc-800">
            @livewire('library-panel')
        </div>

        {{-- Viewer --}}
        <div class="flex-1 lg:overflow-y-auto">
            @livewire('exercise-viewer')
        </div>

    </div>
@endsection