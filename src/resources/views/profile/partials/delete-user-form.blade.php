<section x-data="{ showModal: {{ $errors->userDeletion->isNotEmpty() ? 'true' : 'false' }} }" class="space-y-4">

    <div>
        <h2 class="text-base font-semibold text-white">{{ __('app.delete_account') }}</h2>
        <p class="text-sm text-zinc-500 mt-0.5">{{ __('app.once_your_account_is_deleted_all_of_its_resources_and_data_will_be_permanently_deleted_before_deleting_your_account_please_download_any_data_or_information_that_you_wish_to_retain') }}</p>
    </div>

    <button type="button" @click="showModal = true"
        class="px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-500 text-sm font-semibold text-white transition">
        {{ __('app.delete_account') }}
    </button>

    <div x-show="showModal"
         x-transition:enter="transition duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-end sm:items-center justify-center z-50 p-4"
         style="display: none">

        <form method="post" action="{{ route('profile.destroy') }}"
              class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 w-full max-w-sm space-y-4">
            @csrf
            @method('delete')

            <div>
                <h2 class="text-lg font-semibold text-white">{{ __('app.are_you_sure_you_want_to_delete_your_account') }}</h2>
                <p class="text-sm text-zinc-400 mt-1">{{ __('app.once_your_account_is_deleted_all_of_its_resources_and_data_will_be_permanently_deleted_please_enter_your_password_to_confirm_you_would_like_to_permanently_delete_your_account') }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-300 mb-1">{{ __('app.password') }}</label>
                <input id="password" name="password" type="password"
                    placeholder="{{ __('app.password') }}"
                    class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-zinc-500 transition">
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-1.5" />
            </div>

            <div class="flex gap-3">
                <button type="button" @click="showModal = false"
                    class="flex-1 py-2.5 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-sm transition">
                    {{ __('app.cancel') }}
                </button>
                <button type="submit"
                    class="flex-1 py-2.5 rounded-xl bg-red-600 hover:bg-red-500 text-sm font-semibold transition">
                    {{ __('app.delete_account') }}
                </button>
            </div>

        </form>
    </div>

</section>
