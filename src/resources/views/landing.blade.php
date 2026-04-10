@extends('layouts.public')

@section('page_title', __('app.landing_page_title'))
@section('meta_description', __('app.landing_meta_description'))
@section('og_title', __('app.landing_og_title'))
@section('og_description', __('app.landing_og_description'))

@section('content')
<main>
    {{-- HERO --}}
    <section class="text-center py-20 px-6">
        <img src="{{ asset('images/beefit_v2_nobg.png') }}" alt="BeeFit logo" class="h-24 mx-auto mb-8">
        <h1 class="text-4xl sm:text-5xl font-bold text-gray-900 leading-tight mb-4">
            {!! __('app.landing_hero_headline') !!}
        </h1>
        <p class="text-lg text-gray-500 max-w-xl mx-auto mb-8">
            {{ __('app.landing_hero_sub') }}
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('register') }}"
               class="px-8 py-3.5 rounded-xl bg-yellow-400 hover:bg-yellow-300 text-black font-bold text-base transition">
                {{ __('app.landing_start_free') }}
            </a>
            <a href="{{ route('login') }}"
               class="px-8 py-3.5 rounded-xl border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium text-base transition">
                {{ __('app.landing_enter_account') }}
            </a>
        </div>
    </section>

    {{-- FEATURES --}}
    <section class="py-16 px-6 bg-gray-50" aria-labelledby="features-heading">
        <h2 id="features-heading" class="text-2xl font-bold text-center text-gray-900 mb-12">
            {{ __('app.landing_features_heading') }}
        </h2>
        @php
        $features = [
            ['icon' => '🏋️', 'title' => __('app.landing_feature_workout_title'),   'desc' => __('app.landing_feature_workout_desc')],
            ['icon' => '📈', 'title' => __('app.landing_feature_pr_title'),        'desc' => __('app.landing_feature_pr_desc')],
            ['icon' => '📋', 'title' => __('app.landing_feature_routines_title'),  'desc' => __('app.landing_feature_routines_desc')],
            ['icon' => '🏆', 'title' => __('app.landing_feature_xp_title'),        'desc' => __('app.landing_feature_xp_desc')],
            ['icon' => '📊', 'title' => __('app.landing_feature_stats_title'),     'desc' => __('app.landing_feature_stats_desc')],
            ['icon' => '👥', 'title' => __('app.landing_feature_community_title'), 'desc' => __('app.landing_feature_community_desc')],
        ];
        @endphp
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8 max-w-5xl mx-auto">
            @foreach($features as $feature)
                <article class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="text-3xl mb-3">{{ $feature['icon'] }}</div>
                    <h3 class="font-semibold text-gray-900 mb-1">{{ $feature['title'] }}</h3>
                    <p class="text-sm text-gray-500">{{ $feature['desc'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    {{-- PLANS --}}
    <section class="py-16 px-6" aria-labelledby="plans-heading">
        <h2 id="plans-heading" class="text-2xl font-bold text-center text-gray-900 mb-4">
            {{ __('app.landing_plans_heading') }}
        </h2>
        <p class="text-center text-gray-500 mb-10">{{ __('app.landing_plans_sub') }}</p>
        <div class="grid sm:grid-cols-2 gap-6 max-w-2xl mx-auto">
            <div class="border border-gray-200 rounded-2xl p-6 text-center">
                <div class="font-bold text-xl mb-1">Free</div>
                <div class="text-3xl font-bold mb-4">&euro;0<span class="text-base font-normal text-gray-400">{{ __('app.landing_per_month') }}</span></div>
                <ul class="text-sm text-gray-500 space-y-2 mb-6 text-left">
                    <li>✓ {{ __('app.landing_free_f1') }}</li>
                    <li>✓ {{ __('app.landing_free_f2') }}</li>
                    <li>✓ {{ __('app.landing_free_f3') }}</li>
                    <li>✓ {{ __('app.landing_free_f4') }}</li>
                </ul>
                <a href="{{ route('register') }}" class="block w-full py-2.5 rounded-xl border border-gray-300 text-sm font-medium hover:bg-gray-50 transition">
                    {{ __('app.landing_start_free') }}
                </a>
            </div>
            <div class="border-2 border-yellow-400 rounded-2xl p-6 text-center relative">
                <span class="absolute -top-3 left-1/2 -translate-x-1/2 bg-yellow-400 text-black text-xs font-bold px-3 py-1 rounded-full">{{ __('app.landing_popular') }}</span>
                <div class="font-bold text-xl mb-1">Premium</div>
                <div class="text-3xl font-bold mb-4">&euro;4.99<span class="text-base font-normal text-gray-400">{{ __('app.landing_per_month') }}</span></div>
                <ul class="text-sm text-gray-500 space-y-2 mb-6 text-left">
                    <li>✓ {{ __('app.landing_premium_f1') }}</li>
                    <li>✓ {{ __('app.landing_premium_f2') }}</li>
                    <li>✓ {{ __('app.landing_premium_f3') }}</li>
                    <li>✓ {{ __('app.landing_premium_f4') }}</li>
                </ul>
                <a href="{{ route('register') }}" class="block w-full py-2.5 rounded-xl bg-yellow-400 hover:bg-yellow-300 text-black text-sm font-bold transition">
                    {{ __('app.landing_try_premium') }}
                </a>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="py-16 px-6 bg-gray-900 text-white text-center">
        <h2 class="text-2xl font-bold mb-3">{{ __('app.landing_cta_heading') }}</h2>
        <p class="text-gray-400 mb-6">{{ __('app.landing_cta_sub') }}</p>
        <a href="{{ route('register') }}"
           class="inline-block px-10 py-3.5 rounded-xl bg-yellow-400 hover:bg-yellow-300 text-black font-bold transition">
            {{ __('app.landing_cta_btn') }}
        </a>
    </section>

@php $_appUrl = config('app.url'); @endphp
{{-- JSON-LD Structured Data --}}
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@graph": [
        {
            "@@type": "WebSite",
            "name": "BeeFit",
            "url": "{{ $_appUrl }}",
            "description": "App de fitness para registar treinos, acompanhar recordes pessoais e evoluir dia após dia.",
            "potentialAction": {
                "@@type": "SearchAction",
                "target": "{{ $_appUrl }}/library?search={search_term_string}",
                "query-input": "required name=search_term_string"
            }
        },
        {
            "@@type": "SoftwareApplication",
            "name": "BeeFit",
            "applicationCategory": "HealthApplication",
            "operatingSystem": "Web",
            "offers": [
                {"@@type": "Offer", "name": "Free", "price": "0", "priceCurrency": "EUR"},
                {"@@type": "Offer", "name": "Premium", "price": "4.99", "priceCurrency": "EUR", "billingIncrement": "P1M"}
            ]
        },
        {
            "@@type": "FAQPage",
            "mainEntity": [
                {
                    "@@type": "Question",
                    "name": "O BeeFit é gratuito?",
                    "acceptedAnswer": {"@@type": "Answer", "text": "Sim, o BeeFit tem um plano gratuito com até 3 rotinas e registo ilimitado de treinos."}
                },
                {
                    "@@type": "Question",
                    "name": "Como registo um treino no BeeFit?",
                    "acceptedAnswer": {"@@type": "Answer", "text": "Cria uma rotina com os teus exercícios, inicia uma sessão de treino e regista as séries, repetições e pesos em tempo real."}
                },
                {
                    "@@type": "Question",
                    "name": "O BeeFit calcula automaticamente os recordes pessoais?",
                    "acceptedAnswer": {"@@type": "Answer", "text": "Sim, o BeeFit calcula automaticamente os teus recordes pessoais (peso máximo, repetições máximas e 1RM estimado) após cada treino."}
                }
            ]
        }
    ]
}
</script>
</main>

<footer class="py-8 px-6 border-t border-gray-100 text-center text-sm text-gray-400">
    <p>© {{ date('Y') }} BeeFit. {{ __('app.landing_footer_rights') }}</p>
    <nav class="mt-2 space-x-4">
        <a href="{{ route('login') }}" class="hover:text-gray-600 transition">{{ __('app.login') }}</a>
        <a href="{{ route('register') }}" class="hover:text-gray-600 transition">{{ __('app.register') }}</a>
        <a href="{{ route('privacy') }}" class="hover:text-gray-600 transition">{{ __('app.privacy_policy') }}</a>
        <a href="{{ route('terms') }}" class="hover:text-gray-600 transition">{{ __('app.terms_of_service') }}</a>
    </nav>
</footer>
@endsection
