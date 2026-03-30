<?php

use App\Models\Post;
use App\Models\PostComment;
use App\Models\PostLike;
use Livewire\Livewire;
use App\Livewire\Social\SocialFeed;
use App\Livewire\Social\UserProfile;

describe('Social feed', function () {

    it('shows posts from followed users', function () {
        $user   = createUser();
        $friend = createUser();
        follow($user, $friend);

        Post::create(['user_id' => $friend->id, 'emoji' => '💪', 'description' => 'Friend post']);
        $this->actingAs($user);

        Livewire::test(SocialFeed::class)
            ->assertSee('Friend post');
    });

    it('does not show own posts in the feed', function () {
        $user = createUser();
        Post::create(['user_id' => $user->id, 'emoji' => '💪', 'description' => 'My own post']);
        $this->actingAs($user);

        Livewire::test(SocialFeed::class)
            ->assertDontSee('My own post');
    });

    it('does not show posts from non-followed users', function () {
        $user    = createUser();
        $stranger = createUser();
        Post::create(['user_id' => $stranger->id, 'emoji' => '💪', 'description' => 'Stranger post']);
        $this->actingAs($user);

        Livewire::test(SocialFeed::class)
            ->assertDontSee('Stranger post');
    });
});

describe('Post likes', function () {

    it('user can like a post', function () {
        $user   = createUser();
        $author = createUser();
        follow($user, $author);
        $post = Post::create(['user_id' => $author->id, 'emoji' => '💪', 'description' => 'test']);
        $this->actingAs($user);

        Livewire::test(SocialFeed::class)
            ->call('toggleLike', $post->id);

        expect(PostLike::where('user_id', $user->id)->where('post_id', $post->id)->exists())->toBeTrue();
    });

    it('user can unlike a post', function () {
        $user   = createUser();
        $author = createUser();
        follow($user, $author);
        $post = Post::create(['user_id' => $author->id, 'emoji' => '💪', 'description' => 'test']);
        PostLike::create(['user_id' => $user->id, 'post_id' => $post->id]);
        $this->actingAs($user);

        Livewire::test(SocialFeed::class)
            ->call('toggleLike', $post->id);

        expect(PostLike::where('user_id', $user->id)->where('post_id', $post->id)->exists())->toBeFalse();
    });
});

describe('Post comments', function () {

    it('user can comment on a post', function () {
        $user   = createUser();
        $author = createUser();
        follow($user, $author);
        $post = Post::create(['user_id' => $author->id, 'emoji' => '💪', 'description' => 'test']);
        $this->actingAs($user);

        Livewire::test(SocialFeed::class)
            ->set('expandedPostId', $post->id)
            ->set('newComment', 'Great workout!')
            ->call('addComment');

        expect(PostComment::where('user_id', $user->id)->where('body', 'Great workout!')->exists())->toBeTrue();
    });

    it('user can delete their own comment', function () {
        $user   = createUser();
        $author = createUser();
        follow($user, $author);
        $post    = Post::create(['user_id' => $author->id, 'emoji' => '💪', 'description' => 'test']);
        $comment = PostComment::create(['post_id' => $post->id, 'user_id' => $user->id, 'body' => 'My comment']);
        $this->actingAs($user);

        Livewire::test(SocialFeed::class)
            ->call('deleteComment', $comment->id);

        expect(PostComment::find($comment->id))->toBeNull();
    });

    it('user cannot delete another users comment', function () {
        $user    = createUser();
        $author  = createUser();
        $other   = createUser();
        follow($user, $author);
        $post    = Post::create(['user_id' => $author->id, 'emoji' => '💪', 'description' => 'test']);
        $comment = PostComment::create(['post_id' => $post->id, 'user_id' => $other->id, 'body' => 'Their comment']);
        $this->actingAs($user);

        Livewire::test(SocialFeed::class)
            ->call('deleteComment', $comment->id);

        expect(PostComment::find($comment->id))->not->toBeNull();
    });
});

describe('User profile visibility', function () {

    it('public profile is visible to everyone', function () {
        $viewer = createUser();
        $target = createUser();
        $this->actingAs($viewer);

        Livewire::test(UserProfile::class, ['username' => $target->username])
            ->assertStatus(200);
    });

    it('private profile hides posts from non-followers', function () {
        $viewer = createUser();
        $target = createUser(['is_private' => true]);
        Post::create(['user_id' => $target->id, 'emoji' => '💪', 'description' => 'Private post']);
        $this->actingAs($viewer);

        Livewire::test(UserProfile::class, ['username' => $target->username])
            ->assertDontSee('Private post');
    });

    it('private profile shows posts to accepted followers', function () {
        $viewer = createUser();
        $target = createUser(['is_private' => true]);
        follow($viewer, $target, 'accepted');
        Post::create(['user_id' => $target->id, 'emoji' => '💪', 'description' => 'Follower post']);
        $this->actingAs($viewer);

        Livewire::test(UserProfile::class, ['username' => $target->username])
            ->assertSee('Follower post');
    });

    it('own private profile always shows posts', function () {
        $user = createUser(['is_private' => true]);
        Post::create(['user_id' => $user->id, 'emoji' => '💪', 'description' => 'Own private post']);
        $this->actingAs($user);

        Livewire::test(UserProfile::class)
            ->assertSee('Own private post');
    });
});
