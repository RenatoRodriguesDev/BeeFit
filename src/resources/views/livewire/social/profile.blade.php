<div class="max-w-[520px] mx-auto space-y-4">

    {{-- Profile header --}}
    <div class="bg-zinc-900 rounded-2xl p-4 pt-5">
        <div class="flex items-center gap-4">

            {{-- Avatar --}}
            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center font-bold text-xl overflow-hidden shrink-0">
                @if($profileUser->avatar_path)
                    <img src="{{ asset('storage/' . $profileUser->avatar_path) }}" class="w-full h-full object-cover">
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
                                        <img src="{{ asset('storage/' . $u['avatar_path']) }}" class="w-full h-full object-cover">
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
        <div class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-end sm:items-center justify-center p-0 sm:p-4"
            wire:click.self="closePost">
            <div class="bg-zinc-900 rounded-t-2xl sm:rounded-2xl w-full sm:max-w-md max-h-[90vh] flex flex-col overflow-hidden">

                {{-- Photo --}}
                @if($activePost['photo'])
                    <img src="{{ $activePost['photo'] }}" class="w-full max-h-64 object-cover shrink-0">
                @endif

                <div class="flex-1 overflow-y-auto">
                    <div class="p-4 space-y-3">

                        {{-- Workout badge --}}
                        @if($activePost['workout'])
                            <button wire:click="loadWorkoutDetail({{ $activePost['workout_id'] }})"
                                class="w-full flex items-center gap-2 bg-zinc-800 hover:bg-zinc-700 rounded-lg px-3 py-2 transition text-left">
                                <span>{{ $activePost['emoji'] }}</span>
                                <div class="flex-1 min-w-0">
                                    <span class="text-zinc-300 text-xs font-medium block truncate">{{ $activePost['workout'] }}</span>
                                    <span class="text-zinc-600 text-[10px]">{{ __('app.tap_to_see_detail') }}</span>
                                </div>
                                <span class="text-zinc-600 text-xs">›</span>
                            </button>
                        @endif

                        {{-- Description --}}
                        @if($activePost['description'])
                            <p class="text-sm text-zinc-200 leading-relaxed">{{ $activePost['description'] }}</p>
                        @endif

                        {{-- Actions row --}}
                        <div class="flex items-center gap-4 pt-1 border-t border-zinc-800">
                            <button wire:click="togglePostLike"
                                class="text-xl transition {{ $activePost['liked'] ? 'text-red-400' : 'text-zinc-500 hover:text-red-400' }}">
                                {{ $activePost['liked'] ? '❤️' : '🤍' }}
                            </button>
                            @if($activePost['likes'] > 0)
                                <button wire:click="loadPostLikers"
                                    class="text-xs text-zinc-400 hover:text-white transition">
                                    {{ $activePost['likes'] }} {{ __('app.likes') }}
                                </button>
                            @else
                                <span class="text-xs text-zinc-700">0 {{ __('app.likes') }}</span>
                            @endif
                            <span class="ml-auto text-[11px] text-zinc-600">{{ $activePost['date'] }}</span>
                        </div>

                        {{-- Comments --}}
                        <div class="space-y-3">
                            @foreach($modalComments as $comment)
                                <div class="flex gap-2" wire:key="mc-{{ $comment['id'] }}">
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

                            @if(empty($modalComments))
                                <p class="text-xs text-zinc-700 text-center py-1">{{ __('app.no_comments_yet') }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Add comment + actions --}}
                <div class="border-t border-zinc-800 p-3 flex gap-2 shrink-0">
                    <div class="w-6 h-6 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-[9px] font-bold overflow-hidden shrink-0 mt-1">
                        @if(auth()->user()->avatar_path)
                            <img src="{{ asset('storage/' . auth()->user()->avatar_path) }}" class="w-full h-full object-cover">
                        @else
                            {{ auth()->user()->initials() }}
                        @endif
                    </div>
                    <input wire:model="newComment" wire:keydown.enter="addComment" type="text"
                        placeholder="{{ __('app.write_comment') }}"
                        class="flex-1 bg-zinc-800 text-white text-xs rounded-xl px-3 py-1.5 outline-none focus:ring-1 focus:ring-zinc-600 placeholder-zinc-700 min-w-0">
                    <button wire:click="addComment"
                        class="shrink-0 bg-zinc-700 hover:bg-zinc-600 text-white text-xs px-3 py-1.5 rounded-xl transition">
                        {{ __('app.send') }}
                    </button>
                    @if($activePost['is_own'])
                        <button wire:click="confirmDeletePost({{ $activePost['id'] }})"
                            class="text-zinc-600 hover:text-red-400 text-sm px-2 shrink-0 transition">
                            🗑
                        </button>
                    @endif
                    <button wire:click="closePost" class="text-zinc-500 hover:text-white text-sm px-2">✕</button>
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