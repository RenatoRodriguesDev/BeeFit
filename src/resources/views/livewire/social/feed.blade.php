<div class="max-w-[520px] mx-auto space-y-4">

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
                                    <img src="{{ asset('storage/' . $found->avatar_path) }}" class="w-full h-full object-cover">
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
                            <button wire:click="unfollow({{ $found->id }})"
                                class="text-xs bg-zinc-700 hover:bg-red-600/50 text-zinc-300 px-3 py-1 rounded-lg transition shrink-0">
                                {{ __('app.following') }}
                            </button>
                        @elseif($isPending)
                            <button wire:click="unfollow({{ $found->id }})"
                                class="text-xs bg-zinc-700 hover:bg-red-600/50 text-zinc-400 px-3 py-1 rounded-lg transition shrink-0">
                                ⏳ {{ __('app.pending') }}
                            </button>
                        @else
                            <button wire:click="follow({{ $found->id }})"
                                class="text-xs bg-white text-black hover:bg-zinc-200 px-3 py-1 rounded-lg transition shrink-0 font-medium">
                                {{ __('app.follow') }}
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
                <a href="{{ route('social.profile', $post->user->id) }}"
                    class="w-9 h-9 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center font-bold overflow-hidden shrink-0">
                    @if($post->user->avatar_path)
                        <img src="{{ asset('storage/' . $post->user->avatar_path) }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-xs">{{ $post->user->initials() }}</span>
                    @endif
                </a>
                <div class="flex-1 min-w-0">
                    <a href="{{ route('social.profile', $post->user->id) }}"
                        class="font-semibold text-white text-sm hover:underline leading-none">{{ $post->user->name }}</a>
                    <p class="text-[11px] text-zinc-500 mt-0.5">{{ $post->created_at->diffForHumans() }}</p>
                </div>
                @if($post->user_id === auth()->id())
                    <button wire:click="confirmDeletePost({{ $post->id }})"
                        class="text-zinc-600 hover:text-red-400 text-xs px-2 py-1 transition shrink-0">
                        🗑
                    </button>
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
                <button wire:click="toggleLike({{ $post->id }})"
                    class="text-xl transition {{ $post->isLikedBy(auth()->user()) ? 'text-red-400' : 'text-zinc-500 hover:text-red-400' }}">
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
                    <span class="text-xs">{{ $post->comments->count() }}</span>
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
                                    <img src="{{ asset('storage/' . $comment['user']['avatar_path']) }}" class="w-full h-full object-cover">
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
                                    <button wire:click="toggleCommentLike({{ $comment['id'] }})"
                                        class="text-[10px] transition {{ $comment['liked'] ? 'text-red-400' : 'text-zinc-600 hover:text-red-400' }}">
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
                                <img src="{{ asset('storage/' . auth()->user()->avatar_path) }}" class="w-full h-full object-cover">
                            @else
                                {{ auth()->user()->initials() }}
                            @endif
                        </div>
                        <div class="flex-1 flex gap-2">
                            <input wire:model="newComment" wire:keydown.enter="addComment" type="text"
                                placeholder="{{ __('app.write_comment') }}"
                                class="flex-1 bg-zinc-800 text-white text-xs rounded-xl px-3 py-1.5 outline-none focus:ring-1 focus:ring-zinc-600 placeholder-zinc-700 min-w-0">
                            <button wire:click="addComment"
                                class="shrink-0 bg-zinc-700 hover:bg-zinc-600 text-white text-xs px-3 py-1.5 rounded-xl transition">
                                {{ __('app.send') }}
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
                                <span>{{ __('app.set') }}</span><span>{{ __('app.weight') }}</span><span>{{ __('app.reps') }}</span>
                            </div>
                            @foreach($ex['sets'] as $set)
                                <div class="grid grid-cols-3 bg-zinc-800 rounded-lg px-2 py-1.5 text-sm mb-1">
                                    <span class="text-zinc-400">{{ $set['number'] }}</span>
                                    <span>{{ $set['weight'] }} kg</span>
                                    <span>{{ $set['reps'] }} reps</span>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
