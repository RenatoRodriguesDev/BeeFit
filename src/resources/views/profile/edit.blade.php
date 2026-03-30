@extends('layouts.app')

@section('content')

<div class="max-w-2xl mx-auto space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-white">{{ __('app.profile') }}</h1>
        <p class="text-sm text-zinc-500 mt-1">{{ __('app.update_your_account_profile_information_and_email_address') }}</p>
    </div>

    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6">
        @include('profile.partials.update-profile-information-form')
    </div>

    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6">
        @include('profile.partials.update-password-form')
    </div>

    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6">
        @include('profile.partials.delete-user-form')
    </div>

</div>

@endsection
