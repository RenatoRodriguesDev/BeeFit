<?php

namespace App\Notifications;

use App\Models\Post;
use App\Models\User;
use Illuminate\Notifications\Notification;

class PostLiked extends Notification
{
    public function __construct(public User $liker, public Post $post) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'         => 'post_liked',
            'post_id'      => $this->post->id,
            'user_id'      => $this->liker->id,
            'user_name'    => $this->liker->name,
            'user_avatar'  => $this->liker->avatar_path,
            'user_initials'=> $this->liker->initials(),
        ];
    }
}
