<div class="max-w-lg mx-auto">

    <div class="bg-zinc-900 rounded-2xl p-6 space-y-5">

        <h2 class="text-xl font-bold text-white">{{ __('app.new_post') }}</h2>

        {{-- Workout badge with emoji picker --}}
        @if($workout)
            <div class="bg-zinc-800 rounded-xl px-4 py-3 space-y-3">
                <div class="flex items-center gap-3">

                    {{-- Emoji button --}}
                    <button type="button" wire:click="$toggle('showEmojiPicker')"
                        class="text-2xl w-10 h-10 flex items-center justify-center bg-zinc-700 hover:bg-zinc-600 rounded-xl transition shrink-0"
                        title="{{ __('app.choose_emoji') }}">
                        {{ $emoji }}
                    </button>

                    <div>
                        <p class="text-zinc-300 font-medium text-sm">{{ $workout->routine->name ?? __('app.workout') }}</p>
                        <p class="text-zinc-500 text-xs">{{ $workout->created_at->format('d M Y') }}</p>
                    </div>
                </div>

                {{-- Emoji picker grid --}}
                @if($showEmojiPicker)
                    <div class="grid grid-cols-9 gap-1 pt-2 border-t border-zinc-700">
                        @foreach(['💪','🏋️','🏃','🚴','🤸','🏊','⚽','🏀','🎯','🔥','⚡','🏆','💯','🧘','🥊','🏈','🤼','🧗','🎽','🥇','🦾','🏇','🛹','🏒','🎾','⛷️','🏄','🤾','🥋'] as $e)
                            <button type="button" wire:click="selectEmoji('{{ $e }}')"
                                class="text-xl w-8 h-8 flex items-center justify-center rounded-lg hover:bg-zinc-600 transition {{ $emoji === $e ? 'bg-zinc-600 ring-1 ring-zinc-400' : '' }}">
                                {{ $e }}
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        {{-- Description --}}
        <div>
            <textarea wire:model="description" rows="4"
                maxlength="500"
                placeholder="{{ __('app.post_description_placeholder') }}"
                class="w-full bg-zinc-800 text-white rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-zinc-600 placeholder-zinc-500 resize-none"></textarea>
            <p class="text-right text-xs text-zinc-600 mt-1">{{ strlen($description) }}/500</p>
        </div>

        {{-- Photo upload --}}
        <div>
            <label class="block text-sm text-zinc-400 mb-2">{{ __('app.upload_photo') }}</label>
            <div class="flex gap-2">
                {{-- Gallery --}}
                <label class="flex-1 flex items-center justify-center gap-2 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-sm font-medium py-2.5 px-4 rounded-xl cursor-pointer transition">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ __('app.photo_from_gallery') }}
                    <input type="file" wire:model="photo" accept="image/*" class="hidden">
                </label>

                {{-- Camera --}}
                <label class="flex-1 flex items-center justify-center gap-2 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-sm font-medium py-2.5 px-4 rounded-xl cursor-pointer transition">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    {{ __('app.photo_from_camera') }}
                    <input type="file" wire:model="photo" accept="image/*" capture="environment" class="hidden">
                </label>
            </div>

            @if($photo)
                <div class="mt-3 rounded-xl overflow-hidden">
                    <img src="{{ $photo->temporaryUrl() }}" class="w-full max-h-64 object-cover">
                </div>
            @endif

            @error('photo')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Actions --}}
        <div class="flex gap-3 pt-2">
            <a href="{{ url()->previous() }}"
                class="flex-1 text-center py-2.5 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-sm transition">
                {{ __('app.cancel') }}
            </a>
            <button wire:click="publish" wire:loading.attr="disabled"
                class="flex-1 py-2.5 rounded-xl bg-white text-black font-semibold text-sm hover:bg-zinc-200 transition disabled:opacity-50">
                <span wire:loading.remove wire:target="publish">{{ __('app.publish') }}</span>
                <span wire:loading wire:target="publish">...</span>
            </button>
        </div>
    </div>
</div>
