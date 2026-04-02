<?php

namespace App\Livewire\Social;

use App\Models\CommentLike;
use App\Models\Follow;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\PostLike;
use App\Models\User;
use App\Models\Workout;
use App\Notifications\FollowRequested;
use App\Notifications\PostCommented;
use App\Notifications\PostLiked;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;
use Livewire\WithPagination;

class SocialFeed extends Component
{
    use WithPagination;

    public string $search = '';

    // Workout detail modal
    public ?array $activeWorkout = null;

    // Comments
    public ?int $expandedPostId = null;
    public array $comments = [];
    public string $newComment = '';

    // Likers modals
    public ?array $postLikers = null;
    public ?array $commentLikers = null;

    // Delete post confirmation
    public bool $showDeletePostModal = false;
    public ?int $postToDelete = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    // ─── Follow ────────────────────────────────────────────────────

    public function follow(int $userId): void
    {
        $me = Auth::user();
        if (RateLimiter::tooManyAttempts('follow:' . $me->id, 20)) {
            $this->dispatch('toast', message: __('app.too_many_requests'), type: 'error');
            return;
        }
        RateLimiter::hit('follow:' . $me->id, 3600);

        if ($me->id === $userId) return;

        $target = User::find($userId);
        if (! $target) return;

        $existing = Follow::where('follower_id', $me->id)->where('following_id', $userId)->first();
        if ($existing) return;

        $status = $target->is_private ? 'pending' : 'accepted';

        $follow = Follow::create([
            'follower_id'  => $me->id,
            'following_id' => $userId,
            'status'       => $status,
        ]);

        if ($status === 'pending') {
            $target->notify(new FollowRequested($me, $follow));
        }
    }

    public function unfollow(int $userId): void
    {
        Follow::where('follower_id', Auth::id())
            ->where('following_id', $userId)
            ->delete();
    }

    // ─── Post likes ────────────────────────────────────────────────

    public function toggleLike(int $postId): void
    {
        $me = Auth::user();
        if (RateLimiter::tooManyAttempts('like:' . $me->id, 60)) {
            return;
        }
        RateLimiter::hit('like:' . $me->id, 60);

        $existing = PostLike::where('post_id', $postId)->where('user_id', $me->id)->first();

        if ($existing) {
            $existing->delete();
        } else {
            PostLike::create(['post_id' => $postId, 'user_id' => $me->id]);

            $post = Post::with('user')->find($postId);
            if ($post && $post->user_id !== $me->id) {
                $post->user->notify(new PostLiked($me, $post));
            }
        }
    }

    public function loadPostLikers(int $postId): void
    {
        $this->postLikers = PostLike::with('user')
            ->where('post_id', $postId)
            ->get()
            ->map(fn($l) => [
                'id'          => $l->user->id,
                'username'    => $l->user->username,
                'name'        => $l->user->name,
                'avatar_path' => $l->user->avatar_path,
                'initials'    => $l->user->initials(),
            ])->toArray();

        $this->commentLikers = null;
    }

    public function closePostLikers(): void
    {
        $this->postLikers = null;
    }

    // ─── Comments ──────────────────────────────────────────────────

    public function toggleComments(int $postId): void
    {
        if ($this->expandedPostId === $postId) {
            $this->expandedPostId = null;
            $this->comments = [];
            $this->newComment = '';
        } else {
            $this->expandedPostId = $postId;
            $this->newComment = '';
            $this->refreshComments($postId);
        }
    }

    public function addComment(): void
    {
        $body = trim($this->newComment);
        if ($body === '' || ! $this->expandedPostId) return;

        $me = Auth::user();
        if (RateLimiter::tooManyAttempts('comment:' . $me->id, 30)) {
            $this->dispatch('toast', message: __('app.too_many_requests'), type: 'error');
            return;
        }
        RateLimiter::hit('comment:' . $me->id, 3600);

        $comment = PostComment::create([
            'post_id' => $this->expandedPostId,
            'user_id' => $me->id,
            'body'    => $body,
        ]);

        $post = Post::with('user')->find($this->expandedPostId);
        if ($post && $post->user_id !== $me->id) {
            $post->user->notify(new PostCommented($me, $post, $comment));
        }

        $this->newComment = '';
        $this->refreshComments($this->expandedPostId);
    }

    public function toggleCommentLike(int $commentId): void
    {
        $userId = auth()->id();
        if (RateLimiter::tooManyAttempts('like:' . $userId, 60)) {
            return;
        }
        RateLimiter::hit('like:' . $userId, 60);

        $existing = CommentLike::where('comment_id', $commentId)->where('user_id', $userId)->first();

        if ($existing) {
            $existing->delete();
        } else {
            CommentLike::create(['comment_id' => $commentId, 'user_id' => $userId]);
        }

        if ($this->expandedPostId) {
            $this->refreshComments($this->expandedPostId);
        }
    }

    public function loadCommentLikers(int $commentId): void
    {
        $this->commentLikers = CommentLike::with('user')
            ->where('comment_id', $commentId)
            ->get()
            ->map(fn($l) => [
                'id'          => $l->user->id,
                'username'    => $l->user->username,
                'name'        => $l->user->name,
                'avatar_path' => $l->user->avatar_path,
                'initials'    => $l->user->initials(),
            ])->toArray();

        $this->postLikers = null;
    }

    public function closeCommentLikers(): void
    {
        $this->commentLikers = null;
    }

    public function confirmDeletePost(int $postId): void
    {
        $this->postToDelete = $postId;
        $this->showDeletePostModal = true;
    }

    public function deletePost(): void
    {
        $post = Post::find($this->postToDelete);
        if ($post && $post->user_id === Auth::id()) {
            $post->delete();
        }
        $this->showDeletePostModal = false;
        $this->postToDelete = null;
    }

    public function closeDeletePostModal(): void
    {
        $this->showDeletePostModal = false;
        $this->postToDelete = null;
    }

    public function deleteComment(int $commentId): void
    {
        $comment = PostComment::find($commentId);
        if ($comment && $comment->user_id === Auth::id()) {
            $comment->delete();
            if ($this->expandedPostId) {
                $this->refreshComments($this->expandedPostId);
            }
        }
    }

    private function refreshComments(int $postId): void
    {
        $me = auth()->user();

        $this->comments = PostComment::with(['user', 'likes'])
            ->where('post_id', $postId)
            ->oldest()
            ->get()
            ->map(fn($c) => [
                'id'         => $c->id,
                'body'       => $c->body,
                'likes'      => $c->likes->count(),
                'liked'      => $c->isLikedBy($me),
                'created_at' => $c->created_at->diffForHumans(),
                'user' => [
                    'id'          => $c->user->id,
                    'username'    => $c->user->username,
                    'name'        => $c->user->name,
                    'avatar_path' => $c->user->avatar_path,
                    'initials'    => $c->user->initials(),
                ],
            ])->toArray();
    }

    // ─── Workout detail ────────────────────────────────────────────

    public function loadWorkoutDetail(int $workoutId): void
    {
        $workout = Workout::with('exercises.exercise', 'exercises.sets', 'routine')
            ->findOrFail($workoutId);

        $this->activeWorkout = [
            'name'      => $workout->routine?->name ?? __('app.workout'),
            'date'      => $workout->finished_at?->format('d M Y') ?? $workout->created_at->format('d M Y'),
            'exercises' => $workout->exercises->map(fn($we) => [
                'name' => $we->exercise->translate()->name,
                'sets' => $we->sets->map(fn($s) => [
                    'number' => $s->set_number,
                    'weight' => $s->weight,
                    'reps'   => $s->reps,
                ])->toArray(),
            ])->toArray(),
        ];
    }

    public function closeWorkoutDetail(): void
    {
        $this->activeWorkout = null;
    }

    // ─── Render ────────────────────────────────────────────────────

    public function render()
    {
        $user = auth()->user();
        $followingIds = $user->followingIds();

        $posts = Post::with(['user', 'workout', 'likes.user', 'comments'])
            ->whereIn('user_id', $followingIds)
            ->latest()
            ->paginate(10);

        $searchResults = [];
        if (strlen($this->search) >= 2) {
            $searchResults = User::where('id', '!=', $user->id)
                ->where('name', 'like', "%{$this->search}%")
                ->limit(8)
                ->get();
        }

        // Suggestions: users followed by people the current user follows
        $suggestions = collect();
        if (!empty($followingIds)) {
            $suggestedIds = DB::table('follows')
                ->where('status', 'accepted')
                ->whereIn('follower_id', $followingIds)
                ->whereNotIn('following_id', array_merge($followingIds, [$user->id]))
                ->select('following_id', DB::raw('count(*) as score'))
                ->groupBy('following_id')
                ->orderByDesc('score')
                ->limit(5)
                ->pluck('following_id');

            $suggestions = User::whereIn('id', $suggestedIds)
                ->get()
                ->sortBy(fn($u) => array_search($u->id, $suggestedIds->toArray()))
                ->values();
        }

        return view('livewire.social.feed', compact('posts', 'searchResults', 'suggestions'));
    }
}
