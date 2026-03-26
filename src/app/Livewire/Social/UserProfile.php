<?php

namespace App\Livewire\Social;

use App\Models\CommentLike;
use App\Models\Follow;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\PostLike;
use App\Models\User;
use App\Models\Workout;
use App\Notifications\FollowAccepted;
use App\Notifications\FollowRequested;
use App\Notifications\PostCommented;
use App\Notifications\PostLiked;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class UserProfile extends Component
{
    use WithPagination;

    public User $profileUser;

    // Post detail modal
    public ?array $activePost = null;
    public array $modalComments = [];
    public string $newComment = '';
    public ?array $postLikers = null;
    public ?array $commentLikers = null;

    // Workout detail modal (from post)
    public ?array $activeWorkout = null;

    // Delete post confirmation
    public bool $showDeletePostModal = false;
    public ?int $postToDelete = null;

    // Followers modal
    public ?array $followersList = null;
    public ?array $followingList = null;

    public function mount(?int $userId = null): void
    {
        $this->profileUser = $userId
            ? User::findOrFail($userId)
            : auth()->user();
    }

    // ─── Follow ────────────────────────────────────────────────────

    public function follow(): void
    {
        $me = Auth::user();
        if ($me->id === $this->profileUser->id) return;

        $existing = Follow::where('follower_id', $me->id)
            ->where('following_id', $this->profileUser->id)
            ->first();
        if ($existing) return;

        $status = $this->profileUser->is_private ? 'pending' : 'accepted';

        $follow = Follow::create([
            'follower_id'  => $me->id,
            'following_id' => $this->profileUser->id,
            'status'       => $status,
        ]);

        if ($status === 'pending') {
            $this->profileUser->notify(new FollowRequested($me, $follow));
        }
    }

    public function unfollow(): void
    {
        Follow::where('follower_id', Auth::id())
            ->where('following_id', $this->profileUser->id)
            ->delete();
    }

    public function acceptFollow(int $followId): void
    {
        $follow = Follow::where('id', $followId)
            ->where('following_id', Auth::id())
            ->where('status', 'pending')
            ->first();

        if (! $follow) return;

        $follow->update(['status' => 'accepted']);
        $follow->follower->notify(new FollowAccepted(Auth::user()));
    }

    public function rejectFollow(int $followId): void
    {
        Follow::where('id', $followId)
            ->where('following_id', Auth::id())
            ->delete();
    }

    // ─── Followers / Following modals ──────────────────────────────

    public function loadFollowers(): void
    {
        $this->followersList = Follow::with('follower')
            ->where('following_id', $this->profileUser->id)
            ->get()
            ->map(fn($f) => [
                'id'          => $f->follower->id,
                'name'        => $f->follower->name,
                'avatar_path' => $f->follower->avatar_path,
                'initials'    => $f->follower->initials(),
            ])->toArray();

        $this->followingList = null;
    }

    public function loadFollowing(): void
    {
        $this->followingList = Follow::with('following')
            ->where('follower_id', $this->profileUser->id)
            ->get()
            ->map(fn($f) => [
                'id'          => $f->following->id,
                'name'        => $f->following->name,
                'avatar_path' => $f->following->avatar_path,
                'initials'    => $f->following->initials(),
            ])->toArray();

        $this->followersList = null;
    }

    public function closeFollowersModal(): void
    {
        $this->followersList = null;
        $this->followingList = null;
    }

    // ─── Post detail modal ─────────────────────────────────────────

    public function openPost(int $postId): void
    {
        $post = Post::with(['workout.routine', 'likes.user'])->findOrFail($postId);

        $this->activePost = [
            'id'          => $post->id,
            'description' => $post->description,
            'photo'       => $post->photo_path ? asset('storage/' . $post->photo_path) : null,
            'emoji'       => $post->emoji ?? '💪',
            'workout'     => $post->workout?->routine?->name ?? null,
            'workout_id'  => $post->workout_id,
            'likes'       => $post->likes->count(),
            'liked'       => $post->isLikedBy(auth()->user()),
            'date'        => $post->created_at->format('d M Y'),
            'is_own'      => $post->user_id === Auth::id(),
        ];

        $this->newComment = '';
        $this->postLikers = null;
        $this->commentLikers = null;
        $this->refreshModalComments($postId);
    }

    public function closePost(): void
    {
        $this->activePost = null;
        $this->modalComments = [];
        $this->newComment = '';
        $this->postLikers = null;
        $this->commentLikers = null;
    }

    public function togglePostLike(): void
    {
        if (! $this->activePost) return;

        $me     = Auth::user();
        $postId = $this->activePost['id'];
        $existing = PostLike::where('post_id', $postId)->where('user_id', $me->id)->first();

        if ($existing) {
            $existing->delete();
            $this->activePost['liked'] = false;
            $this->activePost['likes'] -= 1;
        } else {
            PostLike::create(['post_id' => $postId, 'user_id' => $me->id]);
            $this->activePost['liked'] = true;
            $this->activePost['likes'] += 1;

            $post = Post::with('user')->find($postId);
            if ($post && $post->user_id !== $me->id) {
                $post->user->notify(new PostLiked($me, $post));
            }
        }
    }

    public function loadPostLikers(): void
    {
        if (!$this->activePost) return;

        $this->postLikers = PostLike::with('user')
            ->where('post_id', $this->activePost['id'])
            ->get()
            ->map(fn($l) => [
                'id'          => $l->user->id,
                'name'        => $l->user->name,
                'avatar_path' => $l->user->avatar_path,
                'initials'    => $l->user->initials(),
            ])->toArray();

        $this->commentLikers = null;
    }

    public function addComment(): void
    {
        if (! $this->activePost || trim($this->newComment) === '') return;

        $me    = Auth::user();
        $postId = $this->activePost['id'];

        $comment = PostComment::create([
            'post_id' => $postId,
            'user_id' => $me->id,
            'body'    => trim($this->newComment),
        ]);

        $post = Post::with('user')->find($postId);
        if ($post && $post->user_id !== $me->id) {
            $post->user->notify(new PostCommented($me, $post, $comment));
        }

        $this->newComment = '';
        $this->refreshModalComments($postId);
    }

    public function toggleCommentLike(int $commentId): void
    {
        $userId = auth()->id();
        $existing = CommentLike::where('comment_id', $commentId)->where('user_id', $userId)->first();

        if ($existing) {
            $existing->delete();
        } else {
            CommentLike::create(['comment_id' => $commentId, 'user_id' => $userId]);
        }

        if ($this->activePost) {
            $this->refreshModalComments($this->activePost['id']);
        }
    }

    public function loadCommentLikers(int $commentId): void
    {
        $this->commentLikers = CommentLike::with('user')
            ->where('comment_id', $commentId)
            ->get()
            ->map(fn($l) => [
                'id'          => $l->user->id,
                'name'        => $l->user->name,
                'avatar_path' => $l->user->avatar_path,
                'initials'    => $l->user->initials(),
            ])->toArray();

        $this->postLikers = null;
    }

    public function closeSubModal(): void
    {
        $this->postLikers = null;
        $this->commentLikers = null;
    }

    public function confirmDeletePost(int $postId): void
    {
        $this->closePost();
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
            if ($this->activePost) {
                $this->refreshModalComments($this->activePost['id']);
            }
        }
    }

    // ─── Workout detail (from post) ────────────────────────────────

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

    private function refreshModalComments(int $postId): void
    {
        $me = auth()->user();

        $this->modalComments = PostComment::with(['user', 'likes'])
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
                    'name'        => $c->user->name,
                    'avatar_path' => $c->user->avatar_path,
                    'initials'    => $c->user->initials(),
                ],
            ])->toArray();
    }

    // ─── Render ────────────────────────────────────────────────────

    public function render()
    {
        $me      = Auth::user();
        $isOwn   = $me->id === $this->profileUser->id;

        $isFollowing = ! $isOwn ? $me->isFollowing($this->profileUser) : null;
        $isPending   = ! $isOwn ? $me->isPendingFollow($this->profileUser) : null;

        $followerCount  = $this->profileUser->followers()->where('status', 'accepted')->count();
        $followingCount = $this->profileUser->following()->where('status', 'accepted')->count();

        // Follow requests (only visible on own private profile)
        $followRequests = [];
        if ($isOwn && $this->profileUser->is_private) {
            $followRequests = Follow::where('following_id', $this->profileUser->id)
                ->where('status', 'pending')
                ->with('follower')
                ->latest('id')
                ->get()
                ->map(fn($f) => [
                    'id'           => $f->id,
                    'user_id'      => $f->follower->id,
                    'user_name'    => $f->follower->name,
                    'user_avatar'  => $f->follower->avatar_path,
                    'user_initials'=> $f->follower->initials(),
                ])->toArray();
        }

        // Privacy: only show posts grid if public OR own profile OR accepted follower
        $canSeePosts = ! $this->profileUser->is_private
            || $isOwn
            || $isFollowing;

        $posts = $canSeePosts
            ? Post::with(['workout', 'likes', 'comments'])
                ->where('user_id', $this->profileUser->id)
                ->latest()
                ->paginate(12)
            : new \Illuminate\Pagination\LengthAwarePaginator([], 0, 12);

        return view('livewire.social.profile', compact(
            'posts', 'isFollowing', 'isPending', 'followerCount', 'followingCount',
            'followRequests', 'canSeePosts', 'isOwn'
        ));
    }
}
