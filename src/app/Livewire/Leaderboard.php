<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;

class Leaderboard extends Component
{
    public string $tab = 'global';

    public function render()
    {
        $user = auth()->user();

        if ($this->tab === 'friends') {
            $friendIds = array_merge([$user->id], $user->followingIds());
            $users = User::whereIn('id', $friendIds)
                ->orderByDesc('xp')
                ->limit(50)
                ->get();
        } else {
            $users = User::orderByDesc('xp')
                ->limit(50)
                ->get();
        }

        $myRank = User::where('xp', '>', $user->xp ?? 0)->count() + 1;

        return view('livewire.leaderboard', [
            'users'  => $users,
            'myRank' => $myRank,
        ])->title(__('app.leaderboard'));
    }
}
