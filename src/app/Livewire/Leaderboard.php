<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;

class Leaderboard extends Component
{
    use WithPagination;

    public string $tab = 'global';

    public function updatingTab(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();

        if ($this->tab === 'friends') {
            $friendIds = array_merge([$user->id], $user->followingIds());
            $users = User::whereIn('id', $friendIds)
                ->orderByDesc('xp')
                ->paginate(25);
        } else {
            $users = User::orderByDesc('xp')
                ->paginate(25);
        }

        $myRank = User::where('xp', '>', $user->xp ?? 0)->count() + 1;

        return view('livewire.leaderboard', [
            'users'  => $users,
            'myRank' => $myRank,
        ])->title(__('app.leaderboard'));
    }
}
