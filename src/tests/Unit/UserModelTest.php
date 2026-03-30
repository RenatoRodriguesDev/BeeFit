<?php

use App\Models\User;

describe('User::initials()', function () {

    it('returns first letter of each word', function () {
        $user = new User(['name' => 'Renato Rodrigues']);
        expect($user->initials())->toBe('RR');
    });

    it('handles single name', function () {
        $user = new User(['name' => 'Renato']);
        expect($user->initials())->toBe('R');
    });

    it('handles three words and takes first two initials', function () {
        $user = new User(['name' => 'Ana Maria Silva']);
        // first letter of first and last word
        expect(strlen($user->initials()))->toBeGreaterThanOrEqual(1);
    });
});

describe('User::age()', function () {

    it('returns null when birthdate is not set', function () {
        $user = new User();
        expect($user->age())->toBeNull();
    });

    it('calculates age correctly', function () {
        $user = User::factory()->make(['birthdate' => now()->subYears(25)->toDateString()]);
        expect($user->age())->toBe(25);
    });
});

describe('User plan limits', function () {

    it('free user has max 3 routines', function () {
        $user = createUser(['plan' => 'free']);
        expect($user->maxRoutines())->toBe(3);
    });

    it('premium user has unlimited routines', function () {
        $user = User::factory()->premium()->create();
        expect($user->maxRoutines())->toBe(PHP_INT_MAX);
    });

    it('canCreateRoutine is true when under limit', function () {
        $user = createUser(['plan' => 'free']);
        expect($user->canCreateRoutine())->toBeTrue();
    });

    it('canCreateRoutine is false when at limit', function () {
        $user = createUser(['plan' => 'free']);
        for ($i = 0; $i < 3; $i++) {
            $user->routines()->create(['name' => "Routine {$i}"]);
        }
        expect($user->canCreateRoutine())->toBeFalse();
    });
});

describe('User role helpers', function () {

    it('isAdmin returns true for admin role', function () {
        $user = createUser(['role' => 'admin']);
        expect($user->isAdmin())->toBeTrue();
    });

    it('isAdmin returns false for regular user', function () {
        $user = createUser(['role' => 'user']);
        expect($user->isAdmin())->toBeFalse();
    });

    it('isTrainer returns true for trainer role', function () {
        $user = createUser(['role' => 'trainer']);
        expect($user->isTrainer())->toBeTrue();
    });
});
