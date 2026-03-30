<?php

use App\Models\Follow;
use Livewire\Livewire;
use App\Livewire\Social\UserProfile;

describe('Follow system', function () {

    it('following a public user creates an accepted follow', function () {
        $user   = createUser();
        $target = createUser();

        $this->actingAs($user);

        Livewire::test(UserProfile::class, ['username' => $target->username])
            ->call('follow');

        $follow = Follow::where('follower_id', $user->id)
            ->where('following_id', $target->id)
            ->first();

        expect($follow)->not->toBeNull()
            ->and($follow->status)->toBe('accepted');
    });

    it('following a private user creates a pending follow', function () {
        $user   = createUser();
        $target = createUser(['is_private' => true]);

        $this->actingAs($user);

        Livewire::test(UserProfile::class, ['username' => $target->username])
            ->call('follow');

        $follow = Follow::where('follower_id', $user->id)
            ->where('following_id', $target->id)
            ->first();

        expect($follow)->not->toBeNull()
            ->and($follow->status)->toBe('pending');
    });

    it('cannot follow the same user twice', function () {
        $user   = createUser();
        $target = createUser();
        follow($user, $target);

        $this->actingAs($user);

        Livewire::test(UserProfile::class, ['username' => $target->username])
            ->call('follow');

        expect(Follow::where('follower_id', $user->id)->where('following_id', $target->id)->count())->toBe(1);
    });

    it('can unfollow a user', function () {
        $user   = createUser();
        $target = createUser();
        follow($user, $target);

        $this->actingAs($user);

        Livewire::test(UserProfile::class, ['username' => $target->username])
            ->call('unfollow');

        expect(Follow::where('follower_id', $user->id)->where('following_id', $target->id)->exists())->toBeFalse();
    });

    it('isFollowing returns true for accepted follows', function () {
        $user   = createUser();
        $target = createUser();
        follow($user, $target, 'accepted');

        expect($user->isFollowing($target))->toBeTrue();
    });

    it('isFollowing returns false for pending follows', function () {
        $user   = createUser();
        $target = createUser();
        follow($user, $target, 'pending');

        expect($user->isFollowing($target))->toBeFalse();
    });

    it('isPendingFollow returns true for pending follows', function () {
        $user   = createUser();
        $target = createUser();
        follow($user, $target, 'pending');

        expect($user->isPendingFollow($target))->toBeTrue();
    });

    it('followingIds returns only accepted follow ids', function () {
        $user    = createUser();
        $accepted = createUser();
        $pending  = createUser();

        follow($user, $accepted, 'accepted');
        follow($user, $pending, 'pending');

        $ids = $user->followingIds();

        expect($ids)->toContain($accepted->id)
            ->and($ids)->not->toContain($pending->id);
    });

    it('owner can accept a follow request', function () {
        $requester = createUser();
        $owner     = createUser(['is_private' => true]);
        $followRow = follow($requester, $owner, 'pending');

        $this->actingAs($owner);

        Livewire::test(UserProfile::class, ['username' => $owner->username])
            ->call('acceptFollow', $followRow->id);

        expect($followRow->fresh()->status)->toBe('accepted');
    });

    it('owner can reject a follow request', function () {
        $requester = createUser();
        $owner     = createUser(['is_private' => true]);
        $followRow = follow($requester, $owner, 'pending');

        $this->actingAs($owner);

        Livewire::test(UserProfile::class, ['username' => $owner->username])
            ->call('rejectFollow', $followRow->id);

        expect(Follow::find($followRow->id))->toBeNull();
    });

    it('owner can remove an accepted follower', function () {
        $follower = createUser();
        $owner    = createUser();
        $followRow = follow($follower, $owner, 'accepted');

        $this->actingAs($owner);

        Livewire::test(UserProfile::class, ['username' => $owner->username])
            ->call('confirmRemoveFollower', $followRow->id)
            ->call('removeFollower');

        expect(Follow::find($followRow->id))->toBeNull();
    });
});
