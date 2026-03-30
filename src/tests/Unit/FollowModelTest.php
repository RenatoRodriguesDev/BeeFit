<?php

use App\Models\Follow;

describe('Follow model', function () {

    it('isPending returns true when status is pending', function () {
        $follow = new Follow(['status' => 'pending']);
        expect($follow->isPending())->toBeTrue();
    });

    it('isPending returns false when status is accepted', function () {
        $follow = new Follow(['status' => 'accepted']);
        expect($follow->isPending())->toBeFalse();
    });

    it('isAccepted returns true when status is accepted', function () {
        $follow = new Follow(['status' => 'accepted']);
        expect($follow->isAccepted())->toBeTrue();
    });

    it('isAccepted returns false when status is pending', function () {
        $follow = new Follow(['status' => 'pending']);
        expect($follow->isAccepted())->toBeFalse();
    });
});
