<?php

namespace App\Notifications;

use App\Models\Follow;
use App\Models\User;
use Illuminate\Notifications\Notification;

class FollowRequested extends Notification
{
    public function __construct(public User $requester, public Follow $follow) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'         => 'follow_requested',
            'follow_id'    => $this->follow->id,
            'user_id'      => $this->requester->id,
            'user_name'    => $this->requester->name,
            'user_username'=> $this->requester->username,
            'user_avatar'  => $this->requester->avatar_path,
            'user_initials'=> $this->requester->initials(),
        ];
    }
}
