<section>
    <header>
        <h2 class="text-lg font-medium text-white">
            {{ __('app.profile_information') }}
        </h2>
        <p class="mt-1 text-sm text-gray-400">
            {{ __('app.update_your_account_profile_information_and_email_address') }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6"
          enctype="multipart/form-data">
        @csrf
        @method('patch')

        {{-- Avatar --}}
        <div class="flex items-center gap-5">
            <div class="w-16 h-16 rounded-full bg-zinc-700 overflow-hidden flex items-center justify-center shrink-0">
                @if($user->avatar_path)
                    <img src="{{ asset('storage/' . $user->avatar_path) }}"
                         alt="{{ $user->name }}"
                         class="w-full h-full object-cover">
                @else
                    <span class="text-2xl font-semibold text-zinc-400">
                        {{ $user->initials() }}
                    </span>
                @endif
            </div>
            <div>
                <label class="cursor-pointer inline-flex items-center gap-2 text-sm text-blue-400 hover:text-blue-300 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    {{ __('app.upload_photo') }}
                    <input type="file" name="avatar" accept="image/*" class="hidden"
                           onchange="previewAvatar(this)">
                </label>
                @if($user->avatar_path)
                    <p class="text-xs text-zinc-500 mt-1">{{ __('app.upload_photo_hint') }}</p>
                @endif
                <x-input-error :messages="$errors->get('avatar')" class="mt-1" />
            </div>
        </div>

        <script>
        function previewAvatar(input) {
            if (!input.files || !input.files[0]) return;
            const reader = new FileReader();
            reader.onload = e => {
                const img = input.closest('form').querySelector('img');
                const initials = input.closest('form').querySelector('.text-2xl');
                if (img) img.src = e.target.result;
                if (initials) initials.style.display = 'none';
            };
            reader.readAsDataURL(input.files[0]);
        }
        </script>

        {{-- Name --}}
        <div>
            <x-input-label for="name" :value="__('app.name')" />
            <x-text-input id="name" name="name" type="text"
                class="mt-1 block w-full text-gray-900"
                :value="old('name', $user->name)"
                required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        {{-- Email --}}
        <div>
            <x-input-label for="email" :value="__('app.email')" />
            <x-text-input id="email" name="email" type="email"
                class="mt-1 block w-full text-gray-900"
                :value="old('email', $user->email)"
                required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-400">
                        {{ __('app.your_email_address_is_unverified') }}
                        <button form="send-verification"
                            class="underline text-sm text-blue-400 hover:text-blue-300 rounded-md focus:outline-none">
                            {{ __('app.click_here_to_re_send_the_verification_email') }}
                        </button>
                    </p>
                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-500">
                            {{ __('app.a_new_verification_link_has_been_sent_to_your_email_address') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <x-input-label for="locale" :value="__('app.language')" />
            <select id="locale" name="locale" class="mt-1 block w-full rounded border-gray-300 bg-white text-gray-900 shadow-sm">
                <option value="pt" {{ old('locale', $user->locale) === 'pt' ? 'selected' : '' }}>{{ __('app.portuguese') }}</option>
                <option value="en" {{ old('locale', $user->locale) === 'en' ? 'selected' : '' }}>{{ __('app.english') }}</option>
                <option value="es" {{ old('locale', $user->locale) === 'es' ? 'selected' : '' }}>{{ __('app.spanish') }}</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('locale')" />
        </div>

        {{-- Divider --}}
        <div class="border-t border-zinc-700 pt-4">
            <p class="text-sm text-zinc-400 mb-4">{{ __('app.physical_metrics') }}</p>

            {{-- Gender --}}
            <div class="mb-4">
                <x-input-label :value="__('app.gender')" />
                <div class="flex gap-3 mt-2">
                    @foreach(['male' => __('app.male'), 'female' => __('app.female'), 'other' => __('app.other')] as $val => $label)
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="gender" value="{{ $val }}"
                                   {{ old('gender', $user->gender) === $val ? 'checked' : '' }}
                                   class="peer hidden">
                            <div class="py-2 text-center rounded-xl border text-sm transition
                                        border-zinc-600 text-zinc-400
                                        peer-checked:bg-blue-600 peer-checked:border-blue-600 peer-checked:text-white
                                        hover:border-blue-400 hover:text-white">
                                {{ $label }}
                            </div>
                        </label>
                    @endforeach
                </div>
                <x-input-error :messages="$errors->get('gender')" class="mt-1" />
            </div>

            {{-- Birthdate --}}
            <div class="mb-4">
                <x-input-label for="birthdate" :value="__('app.birthdate')" />
                <x-text-input id="birthdate" name="birthdate" type="date"
                    class="mt-1 block w-full text-gray-900"
                    :value="old('birthdate', $user->birthdate?->format('Y-m-d'))" />
                <x-input-error :messages="$errors->get('birthdate')" class="mt-1" />
            </div>

            {{-- Height & Weight --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="height_cm" :value="__('app.height_cm')" />
                    <x-text-input id="height_cm" name="height_cm" type="number"
                        class="mt-1 block w-full text-gray-900"
                        :value="old('height_cm', $user->height_cm)"
                        placeholder="175" min="50" max="300" />
                    <x-input-error :messages="$errors->get('height_cm')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="weight_kg" :value="__('app.weight_kg')" />
                    <x-text-input id="weight_kg" name="weight_kg" type="number"
                        class="mt-1 block w-full text-gray-900"
                        :value="old('weight_kg', $user->weight_kg)"
                        placeholder="70.0" min="20" max="500" step="0.1" />
                    <x-input-error :messages="$errors->get('weight_kg')" class="mt-1" />
                </div>
            </div>
        </div>

        {{-- Privacy --}}
        <div class="pt-4 border-t border-zinc-700">
            <label class="flex items-center gap-3 cursor-pointer select-none">
                <input type="hidden" name="is_private" value="0">
                <input type="checkbox" name="is_private" value="1"
                    {{ old('is_private', $user->is_private ?? false) ? 'checked' : '' }}
                    class="w-4 h-4 rounded accent-purple-500">
                <div>
                    <span class="text-sm font-medium text-white">🔒 {{ __('app.private_account') }}</span>
                    <p class="text-xs text-zinc-500 mt-0.5">{{ __('app.private_account_hint') }}</p>
                </div>
            </label>
        </div>

        {{-- Save --}}
        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('app.save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
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