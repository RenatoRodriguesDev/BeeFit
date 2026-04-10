@extends('layouts.public')
@section('page_title', __('app.terms_page_title'))
@section('meta_description', __('app.terms_meta_desc'))

@section('content')
<div class="max-w-3xl mx-auto px-6 py-16">
    <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-800 mb-8 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        {{ __('app.go_back') }}
    </a>

    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ __('app.terms_heading') }}</h1>
    <p class="text-sm text-gray-400 mb-10">{{ __('app.legal_last_updated') }}: {{ date("d/m/Y") }}</p>

    <div class="prose prose-gray max-w-none space-y-8 text-gray-600">

        <section>
            <h2 class="text-xl font-semibold text-gray-900 mb-3">{{ __('app.terms_s1_title') }}</h2>
            <p>{{ __('app.terms_s1_body') }}</p>
        </section>

        <section>
            <h2 class="text-xl font-semibold text-gray-900 mb-3">{{ __('app.terms_s2_title') }}</h2>
            <p>{{ __('app.terms_s2_body') }}</p>
        </section>

        <section>
            <h2 class="text-xl font-semibold text-gray-900 mb-3">{{ __('app.terms_s3_title') }}</h2>
            <ul class="list-disc pl-5 space-y-1">
                <li>{{ __('app.terms_s3_li1') }}</li>
                <li>{{ __('app.terms_s3_li2') }}</li>
                <li>{{ __('app.terms_s3_li3') }}</li>
                <li>{{ __('app.terms_s3_li4') }}</li>
            </ul>
        </section>

        <section>
            <h2 class="text-xl font-semibold text-gray-900 mb-3">{{ __('app.terms_s4_title') }}</h2>
            <ul class="list-disc pl-5 space-y-1">
                <li>{{ __('app.terms_s4_li1') }}</li>
                <li>{{ __('app.terms_s4_li2') }}</li>
                <li>{{ __('app.terms_s4_li3') }}</li>
                <li>{{ __('app.terms_s4_li4') }}</li>
            </ul>
        </section>

        <section>
            <h2 class="text-xl font-semibold text-gray-900 mb-3">{{ __('app.terms_s5_title') }}</h2>
            <p>{{ __('app.terms_s5_body') }}</p>
        </section>

        <section>
            <h2 class="text-xl font-semibold text-gray-900 mb-3">{{ __('app.terms_s6_title') }}</h2>
            <p>{{ __('app.terms_s6_body') }}</p>
        </section>

        <section>
            <h2 class="text-xl font-semibold text-gray-900 mb-3">{{ __('app.terms_s7_title') }}</h2>
            <p>{{ __('app.terms_s7_body') }}</p>
        </section>

        <section>
            <h2 class="text-xl font-semibold text-gray-900 mb-3">{{ __('app.terms_s8_title') }}</h2>
            <p>{{ __('app.terms_s8_body') }} <a href="mailto:{{ config('mail.from.address') }}" class="text-yellow-600 hover:underline">{{ config('mail.from.address') }}</a></p>
        </section>

    </div>
</div>
@endsection
