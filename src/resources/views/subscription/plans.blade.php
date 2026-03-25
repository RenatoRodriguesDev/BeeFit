@extends('layouts.app')

@section('content')

<h1 class="text-3xl font-bold mb-2">{{ __('app.choose_plan') }}</h1>
<p class="text-zinc-400 mb-8">{{ __('app.choose_plan_subtitle') }}</p>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl">

    {{-- Free --}}
    <div class="bg-zinc-800 rounded-2xl p-6 flex flex-col border border-zinc-700">
        <div class="mb-4">
            <h2 class="text-xl font-semibold">Free</h2>
            <div class="text-3xl font-bold mt-2">€0<span class="text-sm font-normal text-zinc-400">/mês</span></div>
        </div>
        <ul class="space-y-2 text-sm text-zinc-300 flex-1 mb-6">
            <li class="flex gap-2"><span class="text-green-400">✓</span> Até 3 rotinas</li>
            <li class="flex gap-2"><span class="text-green-400">✓</span> Acesso à biblioteca de exercícios</li>
            <li class="flex gap-2"><span class="text-green-400">✓</span> Registo de treinos</li>
            <li class="flex gap-2"><span class="text-zinc-600">✗</span> Estatísticas avançadas</li>
            <li class="flex gap-2"><span class="text-zinc-600">✗</span> Tema personalizado</li>
        </ul>
        @if($user->plan === 'free')
            <div class="py-3 text-center text-sm bg-zinc-700 rounded-xl text-zinc-400">
                {{ __('app.current_plan') }}
            </div>
        @endif
    </div>

    {{-- Premium --}}
    <div class="bg-zinc-800 rounded-2xl p-6 flex flex-col border-2 border-yellow-500/60 relative">
        <div class="absolute -top-3 left-1/2 -translate-x-1/2">
            <span class="bg-yellow-500 text-black text-xs font-semibold px-3 py-1 rounded-full">
                {{ __('app.most_popular') }}
            </span>
        </div>
        <div class="mb-4">
            <h2 class="text-xl font-semibold">Premium</h2>
            <div class="text-3xl font-bold mt-2 text-yellow-400">€4.99<span class="text-sm font-normal text-zinc-400">/mês</span></div>
        </div>
        <ul class="space-y-2 text-sm text-zinc-300 flex-1 mb-6">
            <li class="flex gap-2"><span class="text-green-400">✓</span> Rotinas ilimitadas</li>
            <li class="flex gap-2"><span class="text-green-400">✓</span> Estatísticas avançadas</li>
            <li class="flex gap-2"><span class="text-green-400">✓</span> Recordes pessoais</li>
            <li class="flex gap-2"><span class="text-green-400">✓</span> Tema personalizado (cores)</li>
            <li class="flex gap-2"><span class="text-green-400">✓</span> Prioridade no suporte</li>
        </ul>
        @if($user->plan === 'premium')
            <div class="py-3 text-center text-sm bg-yellow-500/20 rounded-xl text-yellow-300">
                {{ __('app.current_plan') }}
            </div>
        @else
            <form action="{{ route('subscription.checkout') }}" method="POST">
                @csrf
                <input type="hidden" name="plan" value="premium">
                <button class="w-full py-3 bg-yellow-500 hover:bg-yellow-400 text-black font-semibold rounded-xl transition">
                    {{ __('app.subscribe') }}
                </button>
            </form>
        @endif
    </div>

    {{-- Trainer --}}
    <div class="bg-zinc-800 rounded-2xl p-6 flex flex-col border border-zinc-700">
        <div class="mb-4">
            <h2 class="text-xl font-semibold">Trainer</h2>
            <div class="text-3xl font-bold mt-2">€9.99<span class="text-sm font-normal text-zinc-400">/mês</span></div>
        </div>
        <ul class="space-y-2 text-sm text-zinc-300 flex-1 mb-6">
            <li class="flex gap-2"><span class="text-green-400">✓</span> Tudo do Premium</li>
            <li class="flex gap-2"><span class="text-green-400">✓</span> Gerir múltiplos clientes</li>
            <li class="flex gap-2"><span class="text-green-400">✓</span> Criar rotinas para clientes</li>
            <li class="flex gap-2"><span class="text-green-400">✓</span> Dashboard de treinador</li>
            <li class="flex gap-2"><span class="text-green-400">✓</span> Exportar relatórios PDF</li>
        </ul>
        @if($user->plan === 'trainer')
            <div class="py-3 text-center text-sm bg-zinc-700 rounded-xl text-zinc-400">
                {{ __('app.current_plan') }}
            </div>
        @else
            <form action="{{ route('subscription.checkout') }}" method="POST">
                @csrf
                <input type="hidden" name="plan" value="trainer">
                <button class="w-full py-3 border border-zinc-500 hover:border-white font-semibold rounded-xl transition">
                    {{ __('app.subscribe') }}
                </button>
            </form>
        @endif
    </div>

</div>

@if($user->hasActiveSubscription())
    <div class="mt-8">
        <a href="{{ route('subscription.portal') }}"
           class="text-sm text-zinc-400 hover:text-white underline transition">
            {{ __('app.manage_subscription') }}
        </a>
    </div>
@endif

@endsection