<div class="max-w-[520px] mx-auto space-y-4">

    {{-- Profile header --}}
    <div class="bg-zinc-900 rounded-2xl p-4 pt-5">
        <div class="flex items-center gap-4">

            {{-- Avatar --}}
            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center font-bold text-xl overflow-hidden shrink-0">
                @if($profileUser->avatar_path)
                    <img src="{{ avatar_url($profileUser->avatar_path) }}" class="w-full h-full object-cover">
                @else
                    {{ $profileUser->initials() }}
                @endif
            </div>

            {{-- Stats --}}
            <div class="flex-1 min-w-0 space-y-1.5">
                <div class="flex items-center gap-2">
                    <h1 class="text-base font-bold text-white truncate">{{ $profileUser->name }}</h1>
                    @if($profileUser->is_private)
                        <span class="text-zinc-500 text-sm" title="{{ __('app.private_account') }}">🔒</span>
                    @endif
                </div>

                <div class="flex gap-4 text-xs text-zinc-400">
                    <span><strong class="text-white">{{ $posts->total() }}</strong> {{ __('app.posts') }}</span>
                    <button wire:click="loadFollowers" class="hover:text-white transition">
                        <strong class="text-white">{{ $followerCount }}</strong> {{ __('app.followers') }}
                    </button>
                    <button wire:click="loadFollowing" class="hover:text-white transition">
                        <strong class="text-white">{{ $followingCount }}</strong> {{ __('app.following') }}
                    </button>
                </div>

                {{-- Action button --}}
                @if(! $isOwn)
                    @if($isFollowing)
                        <button wire:click="unfollow"
                            class="bg-zinc-700 hover:bg-red-600/50 text-zinc-300 text-xs px-3 py-1.5 rounded-lg transition">
                            {{ __('app.following') }}
                        </button>
                    @elseif($isPending)
                        <button wire:click="unfollow"
                            class="bg-zinc-700 hover:bg-red-600/50 text-zinc-400 text-xs px-3 py-1.5 rounded-lg transition">
                            ⏳ {{ __('app.pending') }}
                        </button>
                    @else
                        <button wire:click="follow"
                            class="bg-white hover:bg-zinc-200 text-black text-xs px-3 py-1.5 rounded-lg transition font-medium">
                            {{ __('app.follow') }}
                        </button>
                    @endif
                @else
                    <a href="{{ route('social.create-post') }}"
                        class="bg-zinc-700 hover:bg-zinc-600 text-white text-xs px-3 py-1.5 rounded-lg transition inline-block">
                        ➕ {{ __('app.new_post') }}
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- XP / Level card --}}
    <div class="bg-zinc-900 rounded-2xl p-4 space-y-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-lg font-black bg-gradient-to-r {{ $profileUser->levelBadgeColor() }} bg-clip-text text-transparent">
                    {{ __('app.level') }} {{ $profileUser->level() }}
                </span>
                <span class="text-xs text-zinc-400">· {{ $profileUser->levelTitle() }}</span>
            </div>
            <span class="text-sm font-bold text-yellow-400">{{ number_format($profileUser->xp ?? 0) }} XP</span>
        </div>
        @if($isOwn)
            <div class="space-y-1">
                <div class="w-full bg-zinc-700 rounded-full h-2">
                    <div class="h-2 rounded-full bg-gradient-to-r from-yellow-400 to-amber-500"
                         style="width: {{ $profileUser->xpProgressPercent() }}%"></div>
                </div>
                <div class="text-xs text-zinc-500 text-right">{{ $profileUser->xpProgress() }} / {{ $profileUser->xpNeeded() }} XP {{ __('app.level') }} {{ $profileUser->level() + 1 }}</div>
            </div>
        @endif

        {{-- Achievements --}}
        @php
            $achievements = $profileUser->achievements()->orderByPivot('unlocked_at', 'desc')->get();
            $totalAchievements = \App\Models\Achievement::count();
        @endphp
        @if($achievements->isNotEmpty())
            <div x-data="{ ach: null }">
                <div class="flex items-center gap-2 mb-2">
                    <p class="text-xs font-semibold text-zinc-500 uppercase tracking-wider">{{ __('app.my_achievements') }}</p>
                    <span class="text-xs text-zinc-600">{{ $achievements->count() }}/{{ $totalAchievements }}</span>
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach($achievements as $ach)
                        <button type="button"
                                @click="ach = { icon: '{{ $ach->icon }}', name: {{ Js::from($ach->name) }}, description: {{ Js::from($ach->description) }}, xp: {{ (int) $ach->xp_reward }}, unlocked_at: {{ Js::from($ach->pivot->unlocked_at ? \Carbon\Carbon::parse($ach->pivot->unlocked_at)->format('d M Y') : '') }} }"
                                class="w-10 h-10 rounded-xl bg-zinc-800 hover:bg-zinc-700 flex items-center justify-center text-xl transition cursor-pointer"
                                title="{{ $ach->name }}">
                            {{ $ach->icon }}
                        </button>
                    @endforeach
                </div>

                {{-- Achievement detail modal --}}
                <template x-teleport="body">
                    <div x-show="ach" x-cloak
                         class="fixed inset-0 z-[80] flex items-center justify-center bg-black/70 backdrop-blur-sm p-4"
                         @click.self="ach = null" @keydown.escape.window="ach = null">
                        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 w-full max-w-xs shadow-2xl text-center">
                            <div class="text-5xl mb-3" x-text="ach?.icon"></div>
                            <h3 class="text-lg font-bold text-white mb-1" x-text="ach?.name"></h3>
                            <p class="text-sm text-zinc-400 leading-relaxed mb-4" x-text="ach?.description"></p>
                            <div class="flex items-center justify-center gap-4 text-xs text-zinc-500 border-t border-zinc-800 pt-3">
                                <span class="font-semibold text-yellow-400" x-text="'+' + ach?.xp + ' XP'"></span>
                                <span x-text="ach?.unlocked_at"></span>
                            </div>
                            <button @click="ach = null"
                                class="mt-4 w-full bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-sm font-medium py-2.5 rounded-xl transition">
                                {{ __('app.close') }}
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        @endif
    </div>

    {{-- Follow requests (own private profile) --}}
    @if($isOwn && count($followRequests) > 0)
        <div class="bg-zinc-900 rounded-2xl p-4 space-y-2">
            <h2 class="text-xs font-semibold text-zinc-400 uppercase tracking-widest">{{ __('app.follow_requests') }} ({{ count($followRequests) }})</h2>
            @foreach($followRequests as $req)
                <div class="flex items-center gap-3">
                    <a href="{{ route('social.profile', $req['user_username']) }}"
                        class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-xs font-bold overflow-hidden shrink-0">
                        @if($req['user_avatar'])
                            <img src="{{ asset('storage/' . $req['user_avatar']) }}" class="w-full h-full object-cover">
                        @else
                            {{ $req['user_initials'] }}
                        @endif
                    </a>
                    <a href="{{ route('social.profile', $req['user_username']) }}"
                        class="flex-1 text-sm text-white hover:underline truncate">{{ $req['user_name'] }}</a>
                    <button wire:click="acceptFollow({{ $req['id'] }})"
                        class="text-xs bg-white text-black font-medium px-3 py-1 rounded-lg transition hover:bg-zinc-200 shrink-0">
                        {{ __('app.accept') }}
                    </button>
                    <button wire:click="rejectFollow({{ $req['id'] }})"
                        class="text-xs bg-zinc-800 text-zinc-400 px-3 py-1 rounded-lg transition hover:bg-zinc-700 shrink-0">
                        {{ __('app.decline') }}
                    </button>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Posts grid OR private lock --}}
    @if(! $canSeePosts)
        <div class="text-center py-16 text-zinc-500 bg-zinc-900 rounded-2xl">
            <div class="text-4xl mb-3">🔒</div>
            <p class="font-medium text-zinc-400">{{ __('app.private_account') }}</p>
            <p class="text-sm mt-1">{{ __('app.follow_to_see_posts') }}</p>
        </div>
    @elseif($posts->count())
        <div class="grid grid-cols-3 gap-0.5">
            @foreach($posts as $post)
                <button type="button"
                    wire:click="openPost({{ $post->id }})"
                    wire:key="post-thumb-{{ $post->id }}"
                    class="aspect-square bg-zinc-900 relative overflow-hidden group w-full">
                    @if($post->photo_path)
                        <img src="{{ asset('storage/' . $post->photo_path) }}"
                            class="w-full h-full object-cover group-hover:opacity-75 transition">
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center gap-1 p-2 bg-zinc-800">
                            <span class="text-2xl">{{ $post->emoji ?? '💪' }}</span>
                            @if($post->description)
                                <p class="text-[10px] text-zinc-400 text-center line-clamp-3 leading-tight">{{ $post->description }}</p>
                            @endif
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-sm font-semibold pointer-events-none">
                        ❤️ {{ $post->likes->count() }}
                    </div>
                </button>
            @endforeach
        </div>

        @if($posts->hasPages())
            <div>{{ $posts->links() }}</div>
        @endif
    @else
        <div class="text-center py-16 text-zinc-500">
            <div class="text-5xl mb-4">📸</div>
            <p class="text-zinc-400">{{ __('app.no_posts_yet') }}</p>
        </div>
    @endif

    {{-- ── Modals ── --}}

    {{-- Followers / Following modal --}}
    @if(!is_null($followersList) || !is_null($followingList))
        @php $list = $followersList ?? $followingList; $title = !is_null($followersList) ? __('app.followers') : __('app.following'); @endphp
        <div class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-end sm:items-center justify-center p-0 sm:p-4"
            wire:click.self="closeFollowersModal">
            <div class="bg-zinc-900 rounded-t-2xl sm:rounded-2xl w-full sm:max-w-sm max-h-[60vh] flex flex-col">
                <div class="flex items-center justify-between px-5 pt-5 pb-3 border-b border-zinc-800 shrink-0">
                    <h3 class="font-semibold text-white text-sm">{{ $title }}</h3>
                    <button wire:click="closeFollowersModal" class="text-zinc-400 hover:text-white text-xl leading-none">✕</button>
                </div>
                <div class="overflow-y-auto p-4 space-y-3">
                    @forelse($list as $u)
                        <div class="flex items-center gap-3 p-2 rounded-xl hover:bg-zinc-800 transition">
                            <a href="{{ route('social.profile', $u['username']) }}" class="flex items-center gap-3 flex-1 min-w-0">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-xs font-bold overflow-hidden shrink-0">
                                    @if($u['avatar_path'])
                                        <img src="{{ avatar_url($u['avatar_path']) }}" class="w-full h-full object-cover">
                                    @else
                                        {{ $u['initials'] }}
                                    @endif
                                </div>
                                <span class="text-sm text-white truncate">{{ $u['name'] }}</span>
                            </a>
                            @if($isOwn && !is_null($followersList) && isset($u['follow_id']))
                                <button wire:click="confirmRemoveFollower({{ $u['follow_id'] }})"
                                    class="text-xs text-zinc-500 hover:text-red-400 transition px-2 py-1 rounded-lg hover:bg-zinc-700 shrink-0">
                                    {{ __('app.remove') }}
                                </button>
                            @endif
                        </div>
                    @empty
                        <p class="text-xs text-zinc-500 text-center py-4">-</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    {{-- Post detail modal --}}
    @if($activePost)
        <div class="fixed inset-0 bg-black/85 backdrop-blur-sm z-50 flex items-end md:items-center justify-center p-0 md:p-4"
             wire:click.self="closePost">

            {{-- Close button (outside, top-right, desktop only) --}}
            <button wire:click="closePost"
                class="hidden md:flex fixed top-4 right-4 z-[60] w-10 h-10 items-center justify-center rounded-full bg-zinc-800/80 text-zinc-300 hover:text-white hover:bg-zinc-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <div class="bg-zinc-900 rounded-t-3xl md:rounded-2xl w-full md:max-w-4xl md:max-h-[90vh] flex flex-col md:flex-row overflow-hidden shadow-2xl">

                {{-- LEFT: Image --}}
                @if($activePost['photo'])
                    <div class="md:w-[55%] md:shrink-0 bg-black flex items-center justify-center md:rounded-l-2xl overflow-hidden">
                        <img src="{{ $activePost['photo'] }}"
                             class="w-full max-h-[40vh] md:max-h-full md:h-full object-cover md:object-contain">
                    </div>
                @else
                    <div class="md:w-[55%] md:shrink-0 bg-zinc-950 flex items-center justify-center text-6xl md:rounded-l-2xl min-h-[200px] md:min-h-0">
                        {{ $activePost['emoji'] ?? '💪' }}
                    </div>
                @endif

                {{-- RIGHT: Details --}}
                <div class="flex flex-col flex-1 min-h-0 overflow-hidden">

                    {{-- Header: user + actions --}}
                    <div class="shrink-0 flex items-center gap-3 px-4 py-3 border-b border-zinc-800">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-xs font-bold overflow-hidden shrink-0">
                            @if($profileUser->avatar_path)
                                <img src="{{ avatar_url($profileUser->avatar_path) }}" class="w-full h-full object-cover">
                            @else
                                {{ $profileUser->initials() }}
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-white truncate">{{ $profileUser->name }}</p>
                            @if($profileUser->username)
                                <p class="text-xs text-zinc-500">{{ '@' . $profileUser->username }}</p>
                            @endif
                        </div>
                        {{-- Owner actions --}}
                        @if($activePost['is_own'])
                            <button wire:click="openEditPost"
                                class="p-2 rounded-lg text-zinc-500 hover:text-white hover:bg-zinc-800 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <button wire:click="confirmDeletePost({{ $activePost['id'] }})"
                                class="p-2 rounded-lg text-zinc-500 hover:text-red-400 hover:bg-zinc-800 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        @endif
                        {{-- Mobile close --}}
                        <button wire:click="closePost"
                            class="md:hidden p-2 rounded-lg text-zinc-500 hover:text-white hover:bg-zinc-800 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Scrollable content --}}
                    <div class="flex-1 overflow-y-auto p-4 space-y-4">

                        {{-- Description + workout badge --}}
                        @if($activePost['description'] || $activePost['workout'])
                            <div class="space-y-2">
                                @if($activePost['description'])
                                    <div class="flex gap-2.5">
                                        <div class="w-7 h-7 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-[10px] font-bold overflow-hidden shrink-0">
                                            @if($profileUser->avatar_path)
                                                <img src="{{ avatar_url($profileUser->avatar_path) }}" class="w-full h-full object-cover">
                                            @else
                                                {{ $profileUser->initials() }}
                                            @endif
                                        </div>
                                        <div>
                                            <span class="text-sm font-semibold text-white">{{ $profileUser->name }}</span>
                                            <span class="text-sm text-zinc-300 ml-1.5">{{ $activePost['description'] }}</span>
                                        </div>
                                    </div>
                                @endif

                                @if($activePost['workout'])
                                    <button wire:click="loadWorkoutDetail({{ $activePost['workout_id'] }})"
                                        class="w-full flex items-center gap-2 bg-zinc-800 hover:bg-zinc-700 rounded-xl px-3 py-2.5 transition text-left">
                                        <span class="text-lg">{{ $activePost['emoji'] }}</span>
                                        <div class="flex-1 min-w-0">
                                            <span class="text-zinc-200 text-xs font-semibold block truncate">{{ $activePost['workout'] }}</span>
                                            <span class="text-zinc-600 text-[10px]">{{ __('app.tap_to_see_detail') }}</span>
                                        </div>
                                        <svg class="w-4 h-4 text-zinc-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        @endif

                        {{-- Comments --}}
                        <div class="space-y-3">
                            @foreach($modalComments as $comment)
                                <div class="flex gap-2.5" wire:key="mc-{{ $comment['id'] }}">
                                    <a href="{{ route('social.profile', $comment['user']['username']) }}"
                                        class="w-7 h-7 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-[10px] font-bold overflow-hidden shrink-0 mt-0.5">
                                        @if($comment['user']['avatar_path'])
                                            <img src="{{ avatar_url($comment['user']['avatar_path']) }}" class="w-full h-full object-cover">
                                        @else
                                            {{ $comment['user']['initials'] }}
                                        @endif
                                    </a>
                                    <div class="flex-1 min-w-0">
                                        <div>
                                            <a href="{{ route('social.profile', $comment['user']['username']) }}"
                                                class="text-sm font-semibold text-white hover:underline">{{ $comment['user']['name'] }}</a>
                                            <span class="text-sm text-zinc-300 ml-1.5 break-words">{{ $comment['body'] }}</span>
                                        </div>
                                        <div class="flex items-center gap-3 mt-1">
                                            <span class="text-[11px] text-zinc-600">{{ $comment['created_at'] }}</span>
                                            <button wire:click="toggleCommentLike({{ $comment['id'] }})"
                                                class="text-[11px] font-semibold transition {{ $comment['liked'] ? 'text-red-400' : 'text-zinc-500 hover:text-zinc-300' }}">
                                                {{ __('app.like') }}
                                            </button>
                                            @if($comment['likes'] > 0)
                                                <button wire:click="loadCommentLikers({{ $comment['id'] }})"
                                                    class="text-[11px] text-zinc-500 hover:text-white transition">
                                                    {{ $comment['likes'] }} {{ __('app.likes') }}
                                                </button>
                                            @endif
                                            @if($comment['user']['id'] === auth()->id())
                                                <button wire:click="deleteComment({{ $comment['id'] }})"
                                                    class="text-[11px] text-zinc-700 hover:text-red-400 transition">
                                                    {{ __('app.delete') }}
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            @if(empty($modalComments))
                                <p class="text-xs text-zinc-700 text-center py-4">{{ __('app.no_comments_yet') }}</p>
                            @endif
                        </div>
                    </div>

                    {{-- Bottom: likes + comment input --}}
                    <div class="shrink-0 border-t border-zinc-800">
                        {{-- Like row --}}
                        <div class="flex items-center gap-3 px-4 pt-3 pb-2">
                            <button wire:click="togglePostLike"
                                class="transition {{ $activePost['liked'] ? 'text-red-400' : 'text-zinc-400 hover:text-red-400' }}">
                                <svg class="w-6 h-6" fill="{{ $activePost['liked'] ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                            </button>
                            @if($activePost['likes'] > 0)
                                <button wire:click="loadPostLikers" class="text-sm font-semibold text-white hover:underline">
                                    {{ $activePost['likes'] }} {{ __('app.likes') }}
                                </button>
                            @else
                                <span class="text-sm text-zinc-600">0 {{ __('app.likes') }}</span>
                            @endif
                            <span class="ml-auto text-xs text-zinc-600">{{ $activePost['date'] }}</span>
                        </div>

                        {{-- Comment input --}}
                        <div class="flex items-center gap-2 px-4 pb-4">
                            <div class="w-7 h-7 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-[10px] font-bold overflow-hidden shrink-0">
                                @if(auth()->user()->avatar_path)
                                    <img src="{{ avatar_url(auth()->user()->avatar_path) }}" class="w-full h-full object-cover">
                                @else
                                    {{ auth()->user()->initials() }}
                                @endif
                            </div>
                            <input wire:model="newComment" wire:keydown.enter="addComment" type="text"
                                placeholder="{{ __('app.write_comment') }}"
                                class="flex-1 bg-transparent text-sm text-white outline-none placeholder-zinc-600 min-w-0 border-b border-zinc-700 focus:border-zinc-400 pb-1 transition">
                            <button wire:click="addComment"
                                class="text-sm font-semibold text-blue-400 hover:text-blue-300 transition shrink-0">
                                {{ __('app.send') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Post likers sub-modal --}}
        @include('livewire.social.partials.likers-modal', [
            'likers'  => $postLikers,
            'onClose' => 'closeSubModal',
            'title'   => __('app.liked_by'),
        ])

        {{-- Comment likers sub-modal --}}
        @include('livewire.social.partials.likers-modal', [
            'likers'  => $commentLikers,
            'onClose' => 'closeSubModal',
            'title'   => __('app.liked_by'),
        ])

        {{-- Workout detail sub-modal --}}
        @if($activeWorkout)
            <div class="fixed inset-0 bg-black/80 backdrop-blur-sm z-[60] flex items-end sm:items-center justify-center p-0 sm:p-4"
                wire:click.self="closeWorkoutDetail">
                <div class="bg-zinc-900 rounded-t-2xl sm:rounded-2xl w-full sm:max-w-md max-h-[80vh] overflow-y-auto">
                    <div class="sticky top-0 bg-zinc-900 flex items-center justify-between px-5 pt-5 pb-3 border-b border-zinc-800">
                        <div>
                            <h3 class="font-bold text-white">{{ $activeWorkout['name'] }}</h3>
                            <p class="text-xs text-zinc-500">{{ $activeWorkout['date'] }}</p>
                        </div>
                        <button wire:click="closeWorkoutDetail" class="text-zinc-400 hover:text-white text-xl leading-none">✕</button>
                    </div>
                    <div class="p-5 space-y-5">
                        @foreach($activeWorkout['exercises'] as $ex)
                            <div>
                                <p class="font-semibold text-white text-sm mb-2">{{ $ex['name'] }}</p>
                                <div class="grid grid-cols-3 text-xs text-zinc-500 px-2 mb-1">
                                    @if($ex['is_cardio'])
                                        <span>{{ __('app.set') }}</span><span>{{ __('app.duration') }}</span><span>{{ __('app.distance') }}</span>
                                    @else
                                        <span>{{ __('app.set') }}</span><span>{{ __('app.weight') }}</span><span>{{ __('app.reps') }}</span>
                                    @endif
                                </div>
                                @foreach($ex['sets'] as $set)
                                    <div class="grid grid-cols-3 bg-zinc-800 rounded-lg px-2 py-1.5 text-sm mb-1">
                                        <span class="text-zinc-400">{{ $set['number'] }}</span>
                                        @if($ex['is_cardio'])
                                            <span>{{ $set['duration_seconds'] ? sprintf('%d:%02d', intdiv($set['duration_seconds'], 60), $set['duration_seconds'] % 60) : '—' }}</span>
                                            <span>{{ $set['distance_meters'] ? number_format($set['distance_meters'] / 1000, 2) . ' km' : '—' }}</span>
                                        @else
                                            <span>{{ $set['weight'] }} kg</span>
                                            <span>{{ $set['reps'] }} reps</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    @endif

    {{-- Delete post confirmation --}}
    @if($showDeletePostModal)
        <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-[70]">
            <div class="bg-zinc-900 p-6 rounded-2xl w-80 space-y-5">
                <div>
                    <h2 class="text-base font-semibold text-white">{{ __('app.confirm_delete') }}</h2>
                    <p class="text-zinc-400 text-sm mt-1">{{ __('app.confirm_delete_post') }}</p>
                </div>
                <div class="flex justify-end gap-3">
                    <button wire:click="closeDeletePostModal"
                        class="px-4 py-2 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-sm transition">
                        {{ __('app.cancel') }}
                    </button>
                    <button wire:click="deletePost"
                        class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-500 text-sm transition">
                        {{ __('app.delete') }}
                    </button>
                </div>
            </div>
        </div>
    @endif


    {{-- Edit post modal --}}
    @if($showEditPostModal)
        <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-[70]">
            <div class="bg-zinc-900 p-6 rounded-2xl w-96 space-y-4">
                <h2 class="text-base font-semibold text-white">{{ __('app.edit_post') }}</h2>

                {{-- Photo: shows new preview when selected, otherwise shows existing --}}
                <div>
                    <label class="text-xs text-zinc-400 mb-1 block">{{ __('app.upload_photo') }}</label>
                    @if($editPhoto)
                        <img src="{{ $editPhoto->temporaryUrl() }}" class="w-full rounded-xl object-cover max-h-48 mb-2">
                    @elseif($activePost && $activePost['photo'])
                        <img src="{{ $activePost['photo'] }}" class="w-full rounded-xl object-cover max-h-48 mb-2">
                    @endif
                    <input type="file" wire:model="editPhoto" accept="image/*"
                        class="w-full text-sm text-zinc-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-zinc-700 file:text-white hover:file:bg-zinc-600 cursor-pointer">
                    <div wire:loading wire:target="editPhoto" class="text-xs text-zinc-500 mt-1">{{ __('app.uploading') }}...</div>
                </div>

                {{-- Description --}}
                <div>
                    <label class="text-xs text-zinc-400 mb-1 block">{{ __('app.description') }}</label>
                    <textarea wire:model="editDescription" rows="3"
                        class="w-full bg-zinc-800 text-white text-sm rounded-xl px-3 py-2 outline-none focus:ring-1 focus:ring-zinc-600 placeholder-zinc-600 resize-none"
                        placeholder="{{ __('app.post_description_placeholder') }}"></textarea>
                </div>

                {{-- Emoji --}}
                <div>
                    <label class="text-xs text-zinc-400 mb-1 block">{{ __('app.emoji') }}</label>
                    <input wire:model="editEmoji" type="text"
                        class="w-20 bg-zinc-800 text-white text-xl text-center rounded-xl px-3 py-2 outline-none focus:ring-1 focus:ring-zinc-600">
                </div>

                <div class="flex justify-end gap-3 pt-1">
                    <button wire:click="closeEditPost"
                        class="px-4 py-2 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-sm transition">
                        {{ __('app.cancel') }}
                    </button>
                    <button wire:click="saveEditPost" wire:loading.attr="disabled" wire:target="saveEditPost,editPhoto"
                        class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-sm transition disabled:opacity-50">
                        {{ __('app.save') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Remove follower confirmation --}}
    @if($followerToRemove)
        <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-[70]">
            <div class="bg-zinc-900 p-6 rounded-2xl w-80 space-y-5">
                <div>
                    <h2 class="text-base font-semibold text-white">{{ __("app.confirm_remove_follower") }}</h2>
                    <p class="text-zinc-400 text-sm mt-1">{{ __("app.confirm_remove_follower_message") }}</p>
                </div>
                <div class="flex justify-end gap-3">
                    <button wire:click="cancelRemoveFollower"
                        class="px-4 py-2 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-sm transition">
                        {{ __("app.cancel") }}
                    </button>
                    <button wire:click="removeFollower"
                        class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-500 text-sm transition">
                        {{ __("app.remove") }}
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>