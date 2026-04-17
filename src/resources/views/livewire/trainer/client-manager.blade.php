<div>
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">{{ __('app.trainer_clients') }}</h1>
            <p class="text-sm text-zinc-400 mt-0.5">{{ __('app.trainer_clients_subtitle') }}</p>
        </div>
        <button wire:click="$set('showInviteModal', true)"
                class="flex items-center gap-2 bg-yellow-500 hover:bg-yellow-400 text-zinc-900 font-semibold text-sm px-4 py-2 rounded-xl transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            {{ __('app.trainer_invite_client') }}
        </button>
    </div>

    {{-- Tabs / Nav --}}
    <div class="flex gap-3 mb-6">
        <a href="{{ route('trainer.clients') }}"
           class="px-4 py-2 rounded-xl text-sm font-medium bg-zinc-800 text-white">
            {{ __('app.trainer_clients') }}
        </a>
        <a href="{{ route('trainer.plans') }}"
           class="px-4 py-2 rounded-xl text-sm font-medium text-zinc-400 hover:text-white hover:bg-zinc-800/60 transition">
            {{ __('app.trainer_plans') }}
        </a>
    </div>

    {{-- Search --}}
    <div class="mb-4">
        <input wire:model.live.debounce.300ms="search"
               type="text"
               placeholder="{{ __('app.search_clients') }}"
               class="w-full bg-zinc-800 border border-zinc-700 rounded-xl px-4 py-2.5 text-sm text-white placeholder-zinc-500 focus:outline-none focus:border-yellow-500/60">
    </div>

    {{-- Client list --}}
    @if($clients->isEmpty())
        <div class="text-center py-16 text-zinc-500">
            <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <p class="text-sm">{{ __('app.trainer_no_clients') }}</p>
            <button wire:click="$set('showInviteModal', true)"
                    class="mt-4 text-yellow-500 hover:text-yellow-400 text-sm font-medium transition">
                {{ __('app.trainer_invite_first_client') }}
            </button>
        </div>
    @else
        <div class="space-y-3">
            @foreach($clients as $tc)
                <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-4 flex items-center gap-4">
                    {{-- Avatar --}}
                    <div class="shrink-0">
                        @if($tc->client->avatarUrl())
                            <img src="{{ $tc->client->avatarUrl() }}" alt=""
                                 class="w-10 h-10 rounded-full object-cover">
                        @else
                            <div class="w-10 h-10 rounded-full bg-zinc-700 flex items-center justify-center text-sm font-bold text-zinc-300">
                                {{ $tc->client->initials() }}
                            </div>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-white truncate">{{ $tc->client->name }}</p>
                        <p class="text-xs text-zinc-500">@if($tc->client->username){{ '@' . $tc->client->username }}@endif</p>
                    </div>

                    {{-- Status badge --}}
                    <span @class([
                        'px-2.5 py-1 rounded-full text-xs font-medium',
                        'bg-green-500/15 text-green-400'   => $tc->status === 'active',
                        'bg-yellow-500/15 text-yellow-400' => $tc->status === 'invited',
                        'bg-zinc-700/50 text-zinc-400'     => $tc->status === 'suspended',
                        'bg-red-500/15 text-red-400'       => $tc->status === 'rejected',
                    ])>
                        {{ __('app.trainer_status_' . $tc->status) }}
                    </span>

                    {{-- Actions --}}
                    <div class="flex items-center gap-2 shrink-0">
                        @if($tc->status === 'active')
                            <button wire:click="suspendClient({{ $tc->client_id }})"
                                    class="text-xs text-zinc-400 hover:text-yellow-400 transition px-2 py-1 rounded-lg hover:bg-zinc-800">
                                {{ __('app.trainer_suspend') }}
                            </button>
                        @elseif($tc->status === 'suspended')
                            <button wire:click="reactivateClient({{ $tc->client_id }})"
                                    class="text-xs text-zinc-400 hover:text-green-400 transition px-2 py-1 rounded-lg hover:bg-zinc-800">
                                {{ __('app.trainer_reactivate') }}
                            </button>
                        @endif
                        <button wire:click="confirmRemove({{ $tc->client_id }})"
                                class="text-xs text-zinc-500 hover:text-red-400 transition px-2 py-1 rounded-lg hover:bg-zinc-800">
                            {{ __('app.remove') }}
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Invite Modal --}}
    @if($showInviteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm" wire:click.self="$set('showInviteModal', false)">
            <div class="bg-zinc-900 border border-zinc-700 rounded-2xl p-6 w-full max-w-md mx-4">
                <h2 class="text-lg font-bold text-white mb-1">{{ __('app.trainer_invite_client') }}</h2>
                <p class="text-sm text-zinc-400 mb-5">{{ __('app.trainer_invite_desc') }}</p>

                <div>
                    <label class="block text-xs font-medium text-zinc-400 mb-1.5">{{ __('app.username_or_email') }}</label>
                    <input wire:model="inviteUsername"
                           wire:keydown.enter="inviteClient"
                           type="text"
                           placeholder="{{ __('app.trainer_invite_placeholder') }}"
                           class="w-full bg-zinc-800 border border-zinc-700 rounded-xl px-4 py-2.5 text-sm text-white placeholder-zinc-500 focus:outline-none focus:border-yellow-500/60">
                    @error('inviteUsername')
                        <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-3 mt-5">
                    <button wire:click="$set('showInviteModal', false)"
                            class="flex-1 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-sm font-medium py-2.5 rounded-xl transition">
                        {{ __('app.cancel') }}
                    </button>
                    <button wire:click="inviteClient"
                            class="flex-1 bg-yellow-500 hover:bg-yellow-400 text-zinc-900 text-sm font-semibold py-2.5 rounded-xl transition">
                        {{ __('app.trainer_send_invite') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Remove confirm modal --}}
    @if($showRemoveModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm" wire:click.self="$set('showRemoveModal', false)">
            <div class="bg-zinc-900 border border-zinc-700 rounded-2xl p-6 w-full max-w-sm mx-4">
                <h2 class="text-lg font-bold text-white mb-2">{{ __('app.trainer_remove_client_title') }}</h2>
                <p class="text-sm text-zinc-400 mb-5">{{ __('app.trainer_remove_client_desc') }}</p>
                <div class="flex gap-3">
                    <button wire:click="$set('showRemoveModal', false)"
                            class="flex-1 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-sm font-medium py-2.5 rounded-xl transition">
                        {{ __('app.cancel') }}
                    </button>
                    <button wire:click="removeClient"
                            class="flex-1 bg-red-600 hover:bg-red-500 text-white text-sm font-semibold py-2.5 rounded-xl transition">
                        {{ __('app.remove') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
