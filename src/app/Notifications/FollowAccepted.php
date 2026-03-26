<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Notifications\Notification;

class FollowAccepted extends Notification
{
    public function __construct(public User $acceptedBy) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'         => 'follow_accepted',
            'user_id'      => $this->acceptedBy->id,
            'user_name'    => $this->acceptedBy->name,
            'user_avatar'  => $this->acceptedBy->avatar_path,
            'user_initials'=> $this->acceptedBy->initials(),
        ];
    }
}
