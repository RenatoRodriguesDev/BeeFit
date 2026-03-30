<?php

use App\Models\Follow;
use App\Models\Post;
use App\Models\PostComment;
use App\Notifications\FollowAccepted;
use App\Notifications\FollowRequested;
use App\Notifications\PostCommented;
use App\Notifications\PostLiked;
use Illuminate\Support\Facades\Notification;

describe('FollowRequested notification', function () {

    it('uses database and broadcast channels', function () {
        $requester = createUser();
        $target    = createUser();
        $follow    = Follow::create(['follower_id' => $requester->id, 'following_id' => $target->id, 'status' => 'pending']);

        $notification = new FollowRequested($requester, $follow);

        expect($notification->via($target))->toBe(['database', 'broadcast']);
    });

    it('toArray contains required keys', function () {
        $requester = createUser();
        $target    = createUser();
        $follow    = Follow::create(['follower_id' => $requester->id, 'following_id' => $target->id, 'status' => 'pending']);

        $data = (new FollowRequested($requester, $follow))->toArray($target);

        expect($data)->toHaveKeys(['type', 'follow_id', 'user_id', 'user_name', 'user_username', 'user_avatar', 'user_initials'])
            ->and($data['type'])->toBe('follow_requested')
            ->and($data['user_id'])->toBe($requester->id)
            ->and($data['user_username'])->toBe($requester->username);
    });
});

describe('FollowAccepted notification', function () {

    it('uses database and broadcast channels', function () {
        $acceptor  = createUser();
        $requester = createUser();

        $notification = new FollowAccepted($acceptor);
        expect($notification->via($requester))->toBe(['database', 'broadcast']);
    });

    it('toArray contains required keys', function () {
        $acceptor  = createUser();
        $requester = createUser();

        $data = (new FollowAccepted($acceptor))->toArray($requester);

        expect($data)->toHaveKeys(['type', 'user_id', 'user_name', 'user_username', 'user_avatar', 'user_initials'])
            ->and($data['type'])->toBe('follow_accepted')
            ->and($data['user_id'])->toBe($acceptor->id);
    });
});

describe('PostLiked notification', function () {

    it('uses database and broadcast channels', function () {
        $liker  = createUser();
        $author = createUser();
        $post   = Post::create(['user_id' => $author->id, 'emoji' => '💪', 'description' => 'test']);

        $notification = new PostLiked($liker, $post);
        expect($notification->via($author))->toBe(['database', 'broadcast']);
    });

    it('toArray contains required keys', function () {
        $liker  = createUser();
        $author = createUser();
        $post   = Post::create(['user_id' => $author->id, 'emoji' => '💪', 'description' => 'test']);

        $data = (new PostLiked($liker, $post))->toArray($author);

        expect($data)->toHaveKeys(['type', 'post_id', 'user_id', 'user_name', 'user_username'])
            ->and($data['type'])->toBe('post_liked')
            ->and($data['post_id'])->toBe($post->id)
            ->and($data['user_username'])->toBe($liker->username);
    });
});

describe('PostCommented notification', function () {

    it('uses database and broadcast channels', function () {
        $commenter = createUser();
        $author    = createUser();
        $post      = Post::create(['user_id' => $author->id, 'emoji' => '💪', 'description' => 'test']);
        $comment   = PostComment::create(['post_id' => $post->id, 'user_id' => $commenter->id, 'body' => 'Great!']);

        $notification = new PostCommented($commenter, $post, $comment);
        expect($notification->via($author))->toBe(['database', 'broadcast']);
    });

    it('toArray contains required keys with preview', function () {
        $commenter = createUser();
        $author    = createUser();
        $post      = Post::create(['user_id' => $author->id, 'emoji' => '💪', 'description' => 'test']);
        $comment   = PostComment::create(['post_id' => $post->id, 'user_id' => $commenter->id, 'body' => 'Amazing workout!']);

        $data = (new PostCommented($commenter, $post, $comment))->toArray($author);

        expect($data)->toHaveKeys(['type', 'post_id', 'comment_id', 'user_id', 'user_username', 'preview'])
            ->and($data['type'])->toBe('post_commented')
            ->and($data['preview'])->toBe('Amazing workout!');
    });

    it('truncates preview to 60 characters', function () {
        $commenter = createUser();
        $author    = createUser();
        $post      = Post::create(['user_id' => $author->id, 'emoji' => '💪', 'description' => 'test']);
        $longBody  = str_repeat('a', 100);
        $comment   = PostComment::create(['post_id' => $post->id, 'user_id' => $commenter->id, 'body' => $longBody]);

        $data = (new PostCommented($commenter, $post, $comment))->toArray($author);

        expect(mb_strlen($data['preview']))->toBe(60);
    });
});
