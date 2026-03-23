@extends('layouts.app')

@section('content')

<h1 class="text-3xl font-bold mb-8">{{ __('app.statistics') }}</h1>

@livewire('statistics')

@endsection