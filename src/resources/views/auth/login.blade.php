<x-guest-layout>

    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold tracking-tight">
            {{ __('app.login') }}
        </h2>

        <p class="text-gray-400 text-sm mt-2">
            {{ __('app.login_continue') }}
        </p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email -->
        <div>
            <label class="block text-sm font-medium mb-2 text-gray-300">
                {{ __('app.email') }}
            </label>

            <input type="email" name="email" value="{{ old('email') }}" required autofocus class="w-full p-3 rounded-xl bg-[#0f172a] border border-gray-700 bg-white
                   focus:border-blue-500 focus:ring-2 focus:ring-blue-500 outline-none
                   transition" />

            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <label class="block text-sm font-medium mb-2 text-gray-300">
                {{ __('app.password') }}
            </label>

            <input type="password" name="password" required autocomplete="current-password" class="w-full p-3 rounded-xl bg-[#0f172a] border border-gray-700 bg-white
                   focus:border-blue-500 focus:ring-2 focus:ring-blue-500 outline-none
                   transition" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember -->
        <div class="flex items-center justify-between text-sm">

            <label class="flex items-center text-gray-400">
                <input type="checkbox" name="remember"
                    class="rounded border-gray-600 text-blue-600 focus:ring-blue-500">

                <span class="ml-2">{{ __('app.remember_me') }}</span>
            </label>

            @if(Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-blue-500 hover:text-blue-400 transition">
                    {{ __('app.forgot_password') }}
                </a>
            @endif
        </div>

        <!-- Button -->
        <button class="w-full py-3 rounded-xl bg-beeyellow hover:bg-black hover:text-white
                   font-semibold transition active:scale-[0.98]">
            {{ __('app.login') }}
        </button>
        @if (Route::has('register'))
            <div class="text-center text-sm text-gray-400 mt-6">
                {{ __('app.no_account') }}
                <a href="{{ route('register') }}" class="text-blue-500 hover:text-blue-400 font-medium transition">
                    {{ __('app.create_account') }}
                </a>
            </div>
        @endif

    </form>

</x-guest-layout>