<div>

    <button wire:click="$set('showModal', true)"
        class="w-full flex items-center justify-center gap-2 py-3 rounded-xl bg-white hover:bg-zinc-200 text-black font-semibold text-sm transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        {{ __('app.new_routine') }}
    </button>

    @if($showModal)
        <div class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-end sm:items-center justify-center z-[9999] p-4">
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 w-full max-w-sm space-y-4">

                <h2 class="text-lg font-semibold text-white">{{ __('app.new_routine') }}</h2>

                {{-- Emoji + nome --}}
                <div class="flex gap-3 items-start">
                    <div class="shrink-0">
                        <button type="button" wire:click="$toggle('showEmojiPicker')"
                            class="w-12 h-12 rounded-xl bg-zinc-800 hover:bg-zinc-700 border border-zinc-700 flex items-center justify-center text-2xl transition"
                            title="{{ __('app.choose_emoji') }}">
                            {{ $emoji }}
                        </button>
                    </div>
                    <div class="flex-1">
                        <input wire:model="name"
                            type="text"
                            class="w-full bg-zinc-800 border border-zinc-700 rounded-xl px-4 py-3 text-sm placeholder-zinc-500 focus:outline-none focus:border-zinc-500 transition"
                            placeholder="Push Day, Leg Day…"
                            autofocus>
                        @error('name')
                            <p class="text-xs text-red-400 mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Emoji picker grid --}}
                @if($showEmojiPicker)
                    <div class="grid grid-cols-9 gap-1 p-3 bg-zinc-800 rounded-xl border border-zinc-700">
                        @foreach(['💪','🏋️','🏃','🚴','🤸','🏊','⚽','🏀','🎯','🔥','⚡','🏆','💯','🧘','🥊','🏈','🤼','🧗','🎽','🥇','🦾','🏇','🛹','🏒','🎾','⛷️','🏄','🤾','🥋','🧲','🫀','🦵','🦶','🤲','👟','🩹','🌊','⛰️','🎪'] as $e)
                            <button type="button" wire:click="selectEmoji('{{ $e }}')"
                                class="text-xl w-8 h-8 flex items-center justify-center rounded-lg hover:bg-zinc-600 transition {{ $emoji === $e ? 'bg-zinc-600 ring-1 ring-zinc-400' : '' }}">
                                {{ $e }}
                            </button>
                        @endforeach
                    </div>
                @endif

                <div class="flex gap-3">
                    <button wire:click="$set('showModal', false)"
                        class="flex-1 py-2.5 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-sm transition">
                        {{ __('app.cancel') }}
                    </button>
                    <button wire:click="createRoutine"
                        class="flex-1 py-2.5 rounded-xl bg-white hover:bg-zinc-200 text-black text-sm font-semibold transition">
                        {{ __('app.create') }}
                    </button>
                </div>

            </div>
        </div>
    @endif

</div>
