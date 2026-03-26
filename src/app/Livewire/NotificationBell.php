<?php

namespace App\Livewire;

use App\Models\Follow;
use App\Notifications\FollowAccepted;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class NotificationBell extends Component
{
    public int $userId;

    public function mount(): void
    {
        $this->userId = Auth::id();
    }

    #[On('echo-private:App.Models.User.{userId},Illuminate\Notifications\Events\BroadcastNotificationCreated')]
    public function notificationReceived(): void
    {
        // triggers re-render to refresh count and list
    }

    public function acceptFollow(int $followId, string $notifId): void
    {
        $follow = Follow::find($followId);
        if ($follow && (int) $follow->following_id === Auth::id()) {
            $follow->update(['status' => 'accepted']);
            $follow->follower->notify(new FollowAccepted(Auth::user()));
        }
        Auth::user()->notifications()->where('id', $notifId)->delete();
        $this->dispatch('toast', message: __('app.friend_request_accepted'), type: 'success');
    }

    public function rejectFollow(int $followId, string $notifId): void
    {
        $follow = Follow::find($followId);
        if ($follow && (int) $follow->following_id === Auth::id()) {
            $follow->delete();
        }
        Auth::user()->notifications()->where('id', $notifId)->delete();
    }

    public function markAllRead(): void
    {
        Auth::user()->unreadNotifications->markAsRead();
    }

    public function markRead(string $id): void
    {
        Auth::user()->notifications()->where('id', $id)->update(['read_at' => now()]);
    }

    public function render()
    {
        $notifications = Auth::user()
            ->notifications()
            ->latest()
            ->limit(20)
            ->get();

        $unreadCount = $notifications->whereNull('read_at')->count();

        return view('livewire.notification-bell', compact('notifications', 'unreadCount'));
    }
}
