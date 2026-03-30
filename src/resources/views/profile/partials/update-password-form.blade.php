<section>

    <div class="mb-6">
        <h2 class="text-base font-semibold text-white">{{ __('app.update_password') }}</h2>
        <p class="text-sm text-zinc-500 mt-0.5">{{ __('app.ensure_your_account_is_using_a_long_random_password_to_stay_secure') }}</p>
    </div>

    <form method="post" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="block text-sm font-medium text-zinc-300 mb-1">
                {{ __('app.current_password') }}
            </label>
            <input id="update_password_current_password" name="current_password" type="password"
                autocomplete="current-password"
                class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-zinc-500 transition">
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1.5" />
        </div>

        <div>
            <label for="update_password_password" class="block text-sm font-medium text-zinc-300 mb-1">
                {{ __('app.new_password') }}
            </label>
            <input id="update_password_password" name="password" type="password"
                autocomplete="new-password"
                class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-zinc-500 transition">
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1.5" />
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-sm font-medium text-zinc-300 mb-1">
                {{ __('app.confirm_password') }}
            </label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                autocomplete="new-password"
                class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-zinc-500 transition">
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1.5" />
        </div>

        <div class="flex items-center gap-4 pt-1">
            <button type="submit"
                class="px-5 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-500 text-sm font-semibold text-white transition">
                {{ __('app.save') }}
            </button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }"
                   x-show="show"
                   x-transition
                   x-init="setTimeout(() => show = false, 2000)"
                   class="text-sm text-green-500">
                    {{ __('app.saved') }}
                </p>
            @endif
        </div>
    </form>

</section>
