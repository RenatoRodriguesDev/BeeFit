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
            <input type="file" wire:model="photo" accept="image/*"
                class="block w-full text-sm text-zinc-400
                       file:mr-4 file:py-2 file:px-4
                       file:rounded-xl file:border-0
                       file:text-sm file:font-medium
                       file:bg-zinc-700 file:text-white
                       hover:file:bg-zinc-600 cursor-pointer">

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
