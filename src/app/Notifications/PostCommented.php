<?php

namespace App\Notifications;

use App\Models\Post;
use App\Models\PostComment;
use App\Models\User;
use Illuminate\Notifications\Notification;

class PostCommented extends Notification
{
    public function __construct(public User $commenter, public Post $post, public PostComment $comment) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'         => 'post_commented',
            'post_id'      => $this->post->id,
            'comment_id'   => $this->comment->id,
            'user_id'      => $this->commenter->id,
            'user_name'    => $this->commenter->name,
            'user_avatar'  => $this->commenter->avatar_path,
            'user_initials'=> $this->commenter->initials(),
            'preview'      => mb_substr($this->comment->body, 0, 60),
        ];
    }
}
