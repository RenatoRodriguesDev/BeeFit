<?php

use App\Models\User;

describe('User::level()', function () {

    it('starts at level 1 with 0 XP', function () {
        $user = new User(['xp' => 0]);
        expect($user->level())->toBe(1);
    });

    it('returns level 2 at 200 XP', function () {
        $user = new User(['xp' => 200]);
        expect($user->level())->toBe(2);
    });

    it('returns level 2 just before threshold for level 3', function () {
        $user = new User(['xp' => 499]);
        expect($user->level())->toBe(2);
    });

    it('returns level 3 at 500 XP', function () {
        $user = new User(['xp' => 500]);
        expect($user->level())->toBe(3);
    });

    it('returns level 10 at 8200 XP', function () {
        $user = new User(['xp' => 8200]);
        expect($user->level())->toBe(10);
    });

    it('returns level 50 at 320000 XP', function () {
        $user = new User(['xp' => 320000]);
        expect($user->level())->toBe(50);
    });

    it('increases level beyond 50 with extra XP', function () {
        $user = new User(['xp' => 330000]); // 320000 + 10000
        expect($user->level())->toBe(51);
    });

    it('handles null XP as 0', function () {
        $user = new User(['xp' => null]);
        expect($user->level())->toBe(1);
    });
});

describe('User::xpProgress() and xpNeeded()', function () {

    it('xpProgress is XP above current level threshold', function () {
        $user = new User(['xp' => 350]); // level 2 starts at 200
        expect($user->xpProgress())->toBe(150);
    });

    it('xpNeeded is the gap between current and next level threshold', function () {
        $user = new User(['xp' => 350]); // level 2: 200→500 = 300 needed
        expect($user->xpNeeded())->toBe(300);
    });

    it('xpProgressPercent returns 0 at level start', function () {
        $user = new User(['xp' => 200]); // exactly level 2
        expect($user->xpProgressPercent())->toBe(0);
    });

    it('xpProgressPercent returns 50 at midpoint', function () {
        $user = new User(['xp' => 350]); // 150/300 = 50%
        expect($user->xpProgressPercent())->toBe(50);
    });

    it('xpProgressPercent never exceeds 100', function () {
        $user = new User(['xp' => 499]); // just below level 3
        expect($user->xpProgressPercent())->toBeLessThanOrEqual(100);
    });
});

describe('User::levelTitle()', function () {

    it('returns beginner for level 1-4', function () {
        $user = new User(['xp' => 0]);
        expect($user->levelTitle())->toBeString()->not->toBeEmpty();
    });

    it('returns a different title at level 10', function () {
        $userLow  = new User(['xp' => 0]);
        $userHigh = new User(['xp' => 8200]);
        expect($userLow->levelTitle())->not->toBe($userHigh->levelTitle());
    });
});

describe('User::levelBadgeColor()', function () {

    it('returns a tailwind gradient string', function () {
        $user = new User(['xp' => 0]);
        expect($user->levelBadgeColor())->toContain('from-')->toContain('to-');
    });

    it('returns different colors at different tier levels', function () {
        $beginner = new User(['xp' => 0]);
        $legend   = new User(['xp' => 320000]);
        expect($beginner->levelBadgeColor())->not->toBe($legend->levelBadgeColor());
    });
});

describe('User::addXp()', function () {

    it('increments xp by the given amount', function () {
        $user = createUser(['xp' => 100]);
        $user->addXp(50);
        expect($user->fresh()->xp)->toBe(150);
    });

    it('refreshes model after incrementing', function () {
        $user = createUser(['xp' => 0]);
        $user->addXp(200);
        expect($user->xp)->toBe(200);
    });
});

describe('User::hasAchievement()', function () {

    it('returns false when achievement not unlocked', function () {
        $user = createUser();
        expect($user->hasAchievement('first_workout'))->toBeFalse();
    });

    it('returns true after achievement is attached', function () {
        $user        = createUser();
        $achievement = \App\Models\Achievement::create(['key' => 'first_workout', 'icon' => '🏋️', 'xp_reward' => 100]);
        $user->achievements()->attach($achievement->id, ['unlocked_at' => now()]);
        expect($user->hasAchievement('first_workout'))->toBeTrue();
    });
});
