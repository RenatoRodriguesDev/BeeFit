<?php

use App\Livewire\Leaderboard;
use Livewire\Livewire;

describe('Leaderboard', function () {

    it('requires authentication', function () {
        $this->get('/leaderboard')->assertRedirect(route('login'));
    });

    it('renders the global tab by default', function () {
        $user = createUser();
        $this->actingAs($user);

        Livewire::test(Leaderboard::class)
            ->assertSet('tab', 'global');
    });

    it('shows all users sorted by XP descending', function () {
        $low  = createUser(['xp' => 100]);
        $high = createUser(['xp' => 9999]);
        $this->actingAs($low);

        $component = Livewire::test(Leaderboard::class);
        $users     = $component->viewData('users');

        expect($users->first()->xp)->toBeGreaterThanOrEqual($users->last()->xp);
    });

    it('can switch to friends tab', function () {
        $user = createUser();
        $this->actingAs($user);

        Livewire::test(Leaderboard::class)
            ->set('tab', 'friends')
            ->assertSet('tab', 'friends');
    });

    it('friends tab includes the current user', function () {
        $user   = createUser(['xp' => 500]);
        $friend = createUser(['xp' => 200]);
        follow($user, $friend);
        $this->actingAs($user);

        $component = Livewire::test(Leaderboard::class)->set('tab', 'friends');
        $ids = $component->viewData('users')->pluck('id');

        expect($ids)->toContain($user->id);
    });

    it('friends tab includes followed users', function () {
        $user   = createUser(['xp' => 500]);
        $friend = createUser(['xp' => 200]);
        follow($user, $friend);
        $this->actingAs($user);

        $component = Livewire::test(Leaderboard::class)->set('tab', 'friends');
        $ids = $component->viewData('users')->pluck('id');

        expect($ids)->toContain($friend->id);
    });

    it('friends tab does not include non-followed users', function () {
        $user    = createUser();
        $other   = createUser(['xp' => 9999]);
        $this->actingAs($user);

        $component = Livewire::test(Leaderboard::class)->set('tab', 'friends');
        $ids = $component->viewData('users')->pluck('id');

        expect($ids)->not->toContain($other->id);
    });

    it('calculates rank correctly', function () {
        createUser(['xp' => 9999]); // higher rank
        $user = createUser(['xp' => 100]);
        $this->actingAs($user);

        $component = Livewire::test(Leaderboard::class);
        $myRank    = $component->viewData('myRank');

        expect($myRank)->toBeGreaterThan(1);
    });

    it('rank is 1 for the top user', function () {
        $user = createUser(['xp' => 999999]);
        $this->actingAs($user);

        $component = Livewire::test(Leaderboard::class);
        expect($component->viewData('myRank'))->toBe(1);
    });
});
