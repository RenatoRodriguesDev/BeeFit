<div class="max-w-5xl mx-auto lg:grid lg:grid-cols-[1fr_280px] lg:gap-6 lg:items-start">

{{-- Feed column --}}
<div class="space-y-4">

    {{-- Header --}}
    <div class="flex items-center justify-between pt-2">
        <h1 class="text-xl font-bold text-white">{{ __('app.social_feed') }}</h1>
        <a href="{{ route('social.profile') }}"
            class="text-sm text-zinc-400 hover:text-white transition">
            👤 {{ __('app.my_profile') }}
        </a>
    </div>

    {{-- Find people --}}
    <div class="bg-zinc-900 rounded-2xl p-4 space-y-3">
        <input type="text" wire:model.live.debounce.300ms="search"
            placeholder="{{ __('app.search_users') }}"
            class="w-full bg-zinc-800 text-white rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-1 focus:ring-zinc-600 placeholder-zinc-600">

        @if(count($searchResults))
            <div class="space-y-1">
                @foreach($searchResults as $found)
                    @php
                        $me = auth()->user();
                        $isFollowing = $me->isFollowing($found);
                        $isPending   = ! $isFollowing && $me->isPendingFollow($found);
                    @endphp
                    <div class="flex items-center gap-3 p-2 rounded-xl hover:bg-zinc-800 transition">
                        <a href="{{ route('social.profile', $found->username) }}" class="flex items-center gap-2 flex-1 min-w-0">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-xs font-bold overflow-hidden shrink-0">
                                @if($found->avatar_path)
                                    <img src="{{ avatar_url($found->avatar_path) }}" class="w-full h-full object-cover">
                                @else
                                    {{ $found->initials() }}
                                @endif
                            </div>
                            <div class="flex items-center gap-1 min-w-0">
                                <span class="text-sm text-white truncate">{{ $found->name }}</span>
                                @if($found->is_private)<span class="text-zinc-600 text-xs">🔒</span>@endif
                            </div>
                        </a>
                        @if($isFollowing)
                            <button wire:click="unfollow({{ $found->id }})" wire:loading.attr="disabled" wire:target="unfollow({{ $found->id }})"
                                class="text-xs bg-zinc-700 hover:bg-red-600/50 text-zinc-300 px-3 py-1 rounded-lg transition shrink-0 disabled:opacity-50">
                                <span wire:loading.remove wire:target="unfollow({{ $found->id }})">{{ __('app.following') }}</span>
                                <span wire:loading wire:target="unfollow({{ $found->id }})">...</span>
                            </button>
                        @elseif($isPending)
                            <button wire:click="unfollow({{ $found->id }})" wire:loading.attr="disabled" wire:target="unfollow({{ $found->id }})"
                                class="text-xs bg-zinc-700 hover:bg-red-600/50 text-zinc-400 px-3 py-1 rounded-lg transition shrink-0 disabled:opacity-50">
                                <span wire:loading.remove wire:target="unfollow({{ $found->id }})">⏳ {{ __('app.pending') }}</span>
                                <span wire:loading wire:target="unfollow({{ $found->id }})">...</span>
                            </button>
                        @else
                            <button wire:click="follow({{ $found->id }})" wire:loading.attr="disabled" wire:target="follow({{ $found->id }})"
                                class="text-xs bg-white text-black hover:bg-zinc-200 px-3 py-1 rounded-lg transition shrink-0 font-medium disabled:opacity-50">
                                <span wire:loading.remove wire:target="follow({{ $found->id }})">{{ __('app.follow') }}</span>
                                <span wire:loading wire:target="follow({{ $found->id }})">...</span>
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Posts --}}
    @forelse($posts as $post)
        <div class="bg-zinc-900 rounded-2xl overflow-hidden" wire:key="post-{{ $post->id }}">

            {{-- Header --}}
            <div class="flex items-center gap-3 p-3">
                <a href="{{ route('social.profile', $post->user->username) }}"
                    class="w-9 h-9 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center font-bold overflow-hidden shrink-0">
                    @if($post->user->avatar_path)
                        <img src="{{ avatar_url($post->user->avatar_path) }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-xs">{{ $post->user->initials() }}</span>
                    @endif
                </a>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <a href="{{ route('social.profile', $post->user->username) }}"
                            class="font-semibold text-white text-sm hover:underline leading-none">{{ $post->user->name }}</a>
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-md bg-gradient-to-r {{ $post->user->levelBadgeColor() }} text-white">
                            Lv.{{ $post->user->level() }}
                        </span>
                    </div>
                    <p class="text-[11px] text-zinc-500 mt-0.5">{{ $post->created_at->diffForHumans() }}</p>
                </div>
                @if($post->user_id === auth()->id())
                    <div x-data="{ open: false }" class="relative shrink-0">
                        <button @click="open = !open" @click.away="open = false"
                            class="w-8 h-8 flex items-center justify-center rounded-full text-zinc-400 hover:text-white hover:bg-zinc-800 transition">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <circle cx="5" cy="12" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="19" cy="12" r="1.5"/>
                            </svg>
                        </button>
                        <div x-show="open" x-transition
                            class="absolute right-0 top-9 bg-zinc-800 border border-zinc-700 rounded-xl shadow-xl w-36 py-1 z-20"
                            style="display:none">
                            <button @click="open = false" wire:click="openEditPost({{ $post->id }})"
                                class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-zinc-300 hover:bg-zinc-700 hover:text-white transition text-left">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                {{ __('app.edit') }}
                            </button>
                            <button @click="open = false" wire:click="confirmDeletePost({{ $post->id }})"
                                class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-red-400 hover:bg-red-500/10 hover:text-red-300 transition text-left">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                {{ __('app.delete') }}
                            </button>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Workout badge --}}
            @if($post->workout)
                <button wire:click="loadWorkoutDetail({{ $post->workout->id }})"
                    class="mx-3 mb-2 w-[calc(100%-1.5rem)] bg-zinc-800 hover:bg-zinc-700 rounded-xl px-3 py-2 flex items-center gap-2 text-sm transition text-left">
                    <span class="text-base leading-none">{{ $post->emoji ?? '💪' }}</span>
                    <div class="flex-1 min-w-0">
                        <span class="text-zinc-300 text-xs truncate block font-medium">{{ $post->workout->routine->name ?? __('app.workout') }}</span>
                        <span class="text-zinc-600 text-[10px]">{{ __('app.tap_to_see_detail') }}</span>
                    </div>
                    <span class="text-zinc-600 text-xs">›</span>
                </button>
            @endif

            {{-- Photo --}}
            @if($post->photo_path)
                <img src="{{ asset('storage/' . $post->photo_path) }}" class="w-full object-cover" style="max-height:480px" alt="">
            @endif

            {{-- Description --}}
            @if($post->description)
                <p class="px-3 pt-2 text-sm text-zinc-200 leading-relaxed">{{ $post->description }}</p>
            @endif

            {{-- Actions --}}
            <div class="px-3 py-2.5 flex items-center gap-4 border-b border-zinc-800/60">
                <button wire:click="toggleLike({{ $post->id }})" wire:loading.attr="disabled" wire:target="toggleLike({{ $post->id }})"
                    class="text-xl transition disabled:opacity-50 {{ $post->isLikedBy(auth()->user()) ? 'text-red-400' : 'text-zinc-500 hover:text-red-400' }}">
                    {{ $post->isLikedBy(auth()->user()) ? '❤️' : '🤍' }}
                </button>

                @if($post->likes->count() > 0)
                    <button wire:click="loadPostLikers({{ $post->id }})"
                        class="text-xs text-zinc-400 hover:text-white transition">
                        {{ $post->likes->count() }} {{ __('app.likes') }}
                    </button>
                @else
                    <span class="text-xs text-zinc-700">0 {{ __('app.likes') }}</span>
                @endif

                <button wire:click="toggleComments({{ $post->id }})"
                    class="ml-auto flex items-center gap-1 text-sm transition {{ $expandedPostId === $post->id ? 'text-white' : 'text-zinc-500 hover:text-white' }}">
                    <span>💬</span>
                    <span class="text-xs">{{ $post->comments_count }}</span>
                </button>
            </div>

            {{-- Comments --}}
            @if($expandedPostId === $post->id)
                <div class="px-3 py-3 space-y-3 bg-zinc-950/40">
                    @foreach($comments as $comment)
                        <div class="flex gap-2" wire:key="c-{{ $comment['id'] }}">
                            <a href="{{ route('social.profile', $comment['user']['username']) }}"
                                class="w-6 h-6 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-[9px] font-bold overflow-hidden shrink-0 mt-0.5">
                                @if($comment['user']['avatar_path'])
                                    <img src="{{ avatar_url($comment['user']['avatar_path']) }}" class="w-full h-full object-cover">
                                @else
                                    {{ $comment['user']['initials'] }}
                                @endif
                            </a>
                            <div class="flex-1 min-w-0">
                                <div class="bg-zinc-800 rounded-xl px-2.5 py-1.5">
                                    <a href="{{ route('social.profile', $comment['user']['username']) }}"
                                        class="text-[11px] font-semibold text-white hover:underline">{{ $comment['user']['name'] }}</a>
                                    <p class="text-xs text-zinc-300 mt-0.5 break-words">{{ $comment['body'] }}</p>
                                </div>
                                <div class="flex items-center gap-3 mt-1 pl-1">
                                    <span class="text-[10px] text-zinc-600">{{ $comment['created_at'] }}</span>
                                    <button wire:click="toggleCommentLike({{ $comment['id'] }})" wire:loading.attr="disabled" wire:target="toggleCommentLike({{ $comment['id'] }})"
                                        class="text-[10px] transition disabled:opacity-50 {{ $comment['liked'] ? 'text-red-400' : 'text-zinc-600 hover:text-red-400' }}">
                                        {{ $comment['liked'] ? '❤️' : '🤍' }} {{ __('app.like') }}
                                    </button>
                                    @if($comment['likes'] > 0)
                                        <button wire:click="loadCommentLikers({{ $comment['id'] }})"
                                            class="text-[10px] text-zinc-600 hover:text-white transition">
                                            {{ $comment['likes'] }} {{ __('app.likes') }}
                                        </button>
                                    @endif
                                    @if($comment['user']['id'] === auth()->id())
                                        <button wire:click="deleteComment({{ $comment['id'] }})"
                                            class="text-[10px] text-zinc-700 hover:text-red-400 transition ml-auto">
                                            {{ __('app.delete') }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach

                    @if(empty($comments))
                        <p class="text-xs text-zinc-700 text-center py-1">{{ __('app.no_comments_yet') }}</p>
                    @endif

                    <div class="flex gap-2 pt-1">
                        <div class="w-6 h-6 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-[9px] font-bold overflow-hidden shrink-0 mt-1.5">
                            @if(auth()->user()->avatar_path)
                                <img src="{{ avatar_url(auth()->user()->avatar_path) }}" class="w-full h-full object-cover">
                            @else
                                {{ auth()->user()->initials() }}
                            @endif
                        </div>
                        <div class="flex-1 flex gap-2">
                            <input wire:model="newComment" wire:keydown.enter="addComment" type="text"
                                placeholder="{{ __('app.write_comment') }}"
                                class="flex-1 bg-zinc-800 text-white text-xs rounded-xl px-3 py-1.5 outline-none focus:ring-1 focus:ring-zinc-600 placeholder-zinc-700 min-w-0">
                            <button wire:click="addComment" wire:loading.attr="disabled" wire:target="addComment"
                                class="shrink-0 bg-zinc-700 hover:bg-zinc-600 text-white text-xs px-3 py-1.5 rounded-xl transition disabled:opacity-50">
                                <span wire:loading.remove wire:target="addComment">{{ __('app.send') }}</span>
                                <span wire:loading wire:target="addComment">...</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @empty
        <div class="text-center py-16 text-zinc-500">
            <div class="text-4xl mb-3">🏋️</div>
            <p class="font-medium text-zinc-400">{{ __('app.no_posts_yet') }}</p>
            <p class="text-sm mt-1">{{ __('app.follow_to_see_feed') }}</p>
        </div>
    @endforelse

    @if($posts->hasPages())
        <div class="pb-4">{{ $posts->links() }}</div>
    @endif

    {{-- ── Modals ── --}}

    @include('livewire.social.partials.likers-modal', [
        'likers'   => $postLikers,
        'onClose'  => 'closePostLikers',
        'title'    => __('app.liked_by'),
    ])

    @include('livewire.social.partials.likers-modal', [
        'likers'   => $commentLikers,
        'onClose'  => 'closeCommentLikers',
        'title'    => __('app.liked_by'),
    ])

    {{-- Edit post modal --}}
    @if($showEditPostModal)
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-end sm:items-center justify-center z-50 p-4">
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl w-full max-w-sm overflow-hidden">

                <div class="flex items-center justify-between px-5 py-4 border-b border-zinc-800">
                    <h2 class="text-sm font-semibold text-white">{{ __('app.edit_post') }}</h2>
                    <button wire:click="closeEditPost" class="text-zinc-500 hover:text-white transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-5 space-y-4">
                    {{-- Emoji --}}
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">{{ $editEmoji }}</span>
                        <span class="text-xs text-zinc-500">{{ __('app.emoji') }}</span>
                    </div>

                    {{-- Description --}}
                    <div>
                        <textarea wire:model="editDescription" rows="4"
                            maxlength="500"
                            placeholder="{{ __('app.whats_on_your_mind') }}"
                            class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-zinc-500 transition resize-none"></textarea>
                        <p class="text-xs text-zinc-600 text-right mt-1">{{ strlen($editDescription) }}/500</p>
                        <x-input-error :messages="$errors->get('editDescription')" class="mt-1" />
                    </div>
                </div>

                <div class="flex gap-3 px-5 py-4 border-t border-zinc-800">
                    <button wire:click="closeEditPost"
                        class="flex-1 py-2.5 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-sm transition">
                        {{ __('app.cancel') }}
                    </button>
                    <button wire:click="saveEditPost" wire:loading.attr="disabled" wire:target="saveEditPost"
                        class="flex-1 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-500 text-sm font-semibold transition disabled:opacity-60">
                        <span wire:loading.remove wire:target="saveEditPost">{{ __('app.save') }}</span>
                        <span wire:loading wire:target="saveEditPost">...</span>
                    </button>
                </div>

            </div>
        </div>
    @endif

    {{-- Delete post confirmation --}}
    @if($showDeletePostModal)
        <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50">
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

    {{-- Workout detail --}}
    @if($activeWorkout)
        <div class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-end sm:items-center justify-center p-0 sm:p-4"
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

</div>{{-- end feed column --}}

{{-- Suggestions sidebar (desktop only) --}}
@if($suggestions->isNotEmpty())
<div class="hidden lg:block space-y-3 sticky top-6">
    <h2 class="text-xs font-semibold text-zinc-500 uppercase tracking-wider px-1">{{ __('app.suggested_for_you') }}</h2>
    <div class="bg-zinc-900 rounded-2xl p-3 space-y-3">
        @foreach($suggestions as $suggested)
            @php
                $me = auth()->user();
                $isFollowing = $me->isFollowing($suggested);
                $isPending   = !$isFollowing && $me->isPendingFollow($suggested);
            @endphp
            <div class="flex items-center gap-2">
                <a href="{{ route('social.profile', $suggested->username) }}" class="flex items-center gap-2 flex-1 min-w-0">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-[10px] font-bold overflow-hidden shrink-0">
                        @if($suggested->avatar_path)
                            <img src="{{ avatar_url($suggested->avatar_path) }}" class="w-full h-full object-cover">
                        @else
                            {{ $suggested->initials() }}
                        @endif
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold text-white truncate">{{ $suggested->name }}</p>
                        <p class="text-[10px] text-zinc-500 truncate">{{ '@' . $suggested->username }}</p>
                    </div>
                </a>
                @if($isFollowing)
                    <button wire:click="unfollow({{ $suggested->id }})" wire:loading.attr="disabled" wire:loading.class="opacity-50" wire:target="unfollow({{ $suggested->id }}),follow({{ $suggested->id }})"
                        class="text-[10px] bg-zinc-700 hover:bg-red-600/50 text-zinc-300 px-2 py-1 rounded-lg transition shrink-0">
                        {{ __('app.following') }}
                    </button>
                @elseif($isPending)
                    <button wire:click="unfollow({{ $suggested->id }})" wire:loading.attr="disabled" wire:loading.class="opacity-50" wire:target="unfollow({{ $suggested->id }}),follow({{ $suggested->id }})"
                        class="text-[10px] bg-zinc-700 text-zinc-400 px-2 py-1 rounded-lg transition shrink-0">
                        ⏳
                    </button>
                @else
                    <button wire:click="follow({{ $suggested->id }})" wire:loading.attr="disabled" wire:loading.class="opacity-50" wire:target="follow({{ $suggested->id }}),unfollow({{ $suggested->id }})"
                        class="text-[10px] bg-white text-black hover:bg-zinc-200 px-2 py-1 rounded-lg transition shrink-0 font-semibold">
                        {{ __('app.follow') }}
                    </button>
                @endif
            </div>
        @endforeach
    </div>
</div>
@endif

</div>{{-- end outer grid --}}
