<section>

    <div class="mb-6">
        <h2 class="text-base font-semibold text-white">{{ __('app.profile_information') }}</h2>
        <p class="text-sm text-zinc-500 mt-0.5">{{ __('app.update_your_account_profile_information_and_email_address') }}</p>
    </div>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-5"
          enctype="multipart/form-data">
        @csrf
        @method('patch')

        {{-- Avatar --}}
        <div class="flex items-center gap-5">
            <div class="avatar-container w-16 h-16 rounded-full bg-zinc-800 overflow-hidden flex items-center justify-center shrink-0">
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
                <label class="cursor-pointer inline-flex items-center gap-2 text-sm text-violet-400 hover:text-violet-300 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    {{ __('app.upload_photo') }}
                    <input type="file" name="avatar" accept="image/*" class="hidden"
                           onchange="previewAvatar(this)">
                </label>
                @if($user->avatar_path)
                    <p class="text-xs text-zinc-600 mt-1">{{ __('app.upload_photo_hint') }}</p>
                @endif
                <x-input-error :messages="$errors->get('avatar')" class="mt-1" />
            </div>
        </div>

        <script>
        function previewAvatar(input) {
            if (!input.files || !input.files[0]) return;
            const reader = new FileReader();
            reader.onload = e => {
                const container = input.closest('form').querySelector('.avatar-container');
                let img = container.querySelector('img');
                const initials = container.querySelector('span');
                if (!img) {
                    img = document.createElement('img');
                    img.className = 'w-full h-full object-cover';
                    container.appendChild(img);
                }
                img.src = e.target.result;
                if (initials) initials.style.display = 'none';
            };
            reader.readAsDataURL(input.files[0]);
        }
        </script>

        {{-- Name --}}
        <div>
            <label for="name" class="block text-sm font-medium text-zinc-300 mb-1">{{ __('app.name') }}</label>
            <input id="name" name="name" type="text"
                value="{{ old('name', $user->name) }}"
                required autofocus autocomplete="name"
                class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-zinc-500 transition">
            <x-input-error class="mt-1.5" :messages="$errors->get('name')" />
        </div>

        {{-- Username --}}
        <div>
            <label for="username" class="block text-sm font-medium text-zinc-300 mb-1">{{ __('app.username') }}</label>
            <input id="username" name="username" type="text"
                value="{{ old('username', $user->username) }}"
                required autocomplete="username"
                class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-zinc-500 transition">
            <x-input-error class="mt-1.5" :messages="$errors->get('username')" />
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-medium text-zinc-300 mb-1">{{ __('app.email') }}</label>
            <input id="email" name="email" type="email"
                value="{{ old('email', $user->email) }}"
                required autocomplete="email"
                class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-zinc-500 transition">
            <x-input-error class="mt-1.5" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2">
                    <p class="text-sm text-zinc-400">
                        {{ __('app.your_email_address_is_unverified') }}
                        <button form="send-verification"
                            class="underline text-sm text-violet-400 hover:text-violet-300 rounded-md focus:outline-none">
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

        {{-- Language --}}
        <div>
            <label for="locale" class="block text-sm font-medium text-zinc-300 mb-1">{{ __('app.language') }}</label>
            <select id="locale" name="locale"
                class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-zinc-500 transition">
                <option value="pt" {{ old('locale', $user->locale) === 'pt' ? 'selected' : '' }}>{{ __('app.portuguese') }}</option>
                <option value="en" {{ old('locale', $user->locale) === 'en' ? 'selected' : '' }}>{{ __('app.english') }}</option>
                <option value="es" {{ old('locale', $user->locale) === 'es' ? 'selected' : '' }}>{{ __('app.spanish') }}</option>
            </select>
            <x-input-error class="mt-1.5" :messages="$errors->get('locale')" />
        </div>

        {{-- Physical Metrics --}}
        <div class="border-t border-zinc-800 pt-5">
            <p class="text-sm font-medium text-zinc-400 mb-4">{{ __('app.physical_metrics') }}</p>

            {{-- Gender --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-zinc-300 mb-2">{{ __('app.gender') }}</label>
                <div class="flex gap-3">
                    @foreach(['male' => __('app.male'), 'female' => __('app.female'), 'other' => __('app.other')] as $val => $label)
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="gender" value="{{ $val }}"
                                   {{ old('gender', $user->gender) === $val ? 'checked' : '' }}
                                   class="peer hidden">
                            <div class="py-2 text-center rounded-xl border text-sm transition
                                        border-zinc-700 text-zinc-400
                                        peer-checked:bg-violet-600 peer-checked:border-violet-600 peer-checked:text-white
                                        hover:border-zinc-500 hover:text-white">
                                {{ $label }}
                            </div>
                        </label>
                    @endforeach
                </div>
                <x-input-error :messages="$errors->get('gender')" class="mt-1.5" />
            </div>

            {{-- Birthdate --}}
            <div class="mb-4">
                <label for="birthdate" class="block text-sm font-medium text-zinc-300 mb-1">{{ __('app.birthdate') }}</label>
                <input id="birthdate" name="birthdate" type="date"
                    value="{{ old('birthdate', $user->birthdate?->format('Y-m-d')) }}"
                    class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-zinc-500 transition">
                <x-input-error :messages="$errors->get('birthdate')" class="mt-1.5" />
            </div>

            {{-- Height & Weight --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="height_cm" class="block text-sm font-medium text-zinc-300 mb-1">{{ __('app.height_cm') }}</label>
                    <input id="height_cm" name="height_cm" type="number"
                        value="{{ old('height_cm', $user->height_cm) }}"
                        placeholder="175" min="50" max="300"
                        class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-zinc-500 transition">
                    <x-input-error :messages="$errors->get('height_cm')" class="mt-1.5" />
                </div>
                <div>
                    <label for="weight_kg" class="block text-sm font-medium text-zinc-300 mb-1">{{ __('app.weight_kg') }}</label>
                    <input id="weight_kg" name="weight_kg" type="number"
                        value="{{ old('weight_kg', $user->weight_kg) }}"
                        placeholder="70.0" min="20" max="500" step="0.1"
                        class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-zinc-500 transition">
                    <x-input-error :messages="$errors->get('weight_kg')" class="mt-1.5" />
                </div>
            </div>
        </div>

        {{-- Privacy --}}
        <div class="border-t border-zinc-800 pt-5">
            <label class="flex items-center gap-3 cursor-pointer select-none">
                <input type="hidden" name="is_private" value="0">
                <input type="checkbox" name="is_private" value="1"
                    {{ old('is_private', $user->is_private ?? false) ? 'checked' : '' }}
                    class="w-4 h-4 rounded accent-violet-500">
                <div>
                    <span class="text-sm font-medium text-white">🔒 {{ __('app.private_account') }}</span>
                    <p class="text-xs text-zinc-500 mt-0.5">{{ __('app.private_account_hint') }}</p>
                </div>
            </label>
        </div>

        {{-- Save --}}
        <div class="flex items-center gap-4 pt-1">
            <button type="submit"
                class="px-5 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-500 text-sm font-semibold text-white transition">
                {{ __('app.save') }}
            </button>

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
