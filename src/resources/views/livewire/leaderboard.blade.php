<div class="min-h-screen bg-zinc-950 text-white">
    <div class="max-w-2xl mx-auto px-4 py-6 space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">{{ __('app.leaderboard') }}</h1>
            <div class="text-sm text-zinc-500">{{ __('app.rank') }} #{{ $myRank }}</div>
        </div>

        {{-- Tabs --}}
        <div class="flex gap-2 bg-zinc-900 rounded-xl p-1">
            <button wire:click="$set('tab', 'global')"
                class="flex-1 py-2 rounded-lg text-sm font-medium transition
                    {{ $tab === 'global' ? 'bg-zinc-700 text-white' : 'text-zinc-400 hover:text-white' }}">
                {{ __('app.global') }}
            </button>
            <button wire:click="$set('tab', 'friends')"
                class="flex-1 py-2 rounded-lg text-sm font-medium transition
                    {{ $tab === 'friends' ? 'bg-zinc-700 text-white' : 'text-zinc-400 hover:text-white' }}">
                {{ __('app.friends') }}
            </button>
        </div>

        {{-- Leaderboard list --}}
        <div class="space-y-2">
            @foreach($users as $index => $player)
                @php
                    $rank = $index + 1;
                    $isMe = $player->id === auth()->id();
                    $medal = match($rank) { 1 => '🥇', 2 => '🥈', 3 => '🥉', default => null };
                @endphp
                <div class="flex items-center gap-4 px-4 py-3 rounded-xl
                    {{ $isMe ? 'bg-yellow-500/10 border border-yellow-500/30' : 'bg-zinc-900' }}">

                    {{-- Rank --}}
                    <div class="w-8 text-center text-sm font-bold
                        {{ $rank <= 3 ? 'text-lg' : 'text-zinc-500' }}">
                        {{ $medal ?? $rank }}
                    </div>

                    {{-- Avatar --}}
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br {{ $player->levelBadgeColor() }}
                                flex items-center justify-center text-sm font-bold text-white shrink-0 overflow-hidden">
                        @if($player->avatar_path)
                            <img src="{{ $player->avatarUrl() }}" alt="" class="w-full h-full object-cover">
                        @else
                            {{ $player->initials() }}
                        @endif
                    </div>

                    {{-- Name & title --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-sm truncate {{ $isMe ? 'text-yellow-400' : '' }}">
                                {{ $player->name }}
                                @if($isMe) <span class="text-xs font-normal text-zinc-400">({{ __('app.you') ?? 'you' }})</span> @endif
                            </span>
                        </div>
                        <div class="text-xs text-zinc-500">
                            {{ $player->levelTitle() }} · {{ __('app.level') }} {{ $player->level() }}
                        </div>
                    </div>

                    {{-- XP --}}
                    <div class="text-right shrink-0">
                        <div class="text-sm font-bold text-yellow-400">{{ number_format($player->xp ?? 0) }}</div>
                        <div class="text-xs text-zinc-500">XP</div>
                    </div>
                </div>
            @endforeach

            @if($users->isEmpty())
                <div class="text-center py-12 text-zinc-500">
                    <div class="text-4xl mb-3">🏆</div>
                    <p>{{ $tab === 'friends' ? __('app.follow_people_to_see_friends') : '' }}</p>
                </div>
            @endif
        </div>

    </div>
</div>
