<?php

namespace App\Notifications;

use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PostLikedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Post $post,
        public User $liker
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * This method must accept the $notifiable parameter as part of the interface contract,
     * but it's common not to use it directly in the implementation. Many notifications
     * use the same channels regardless of who receives them.
     */
    public function via(object $_notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Your post was liked!")
            ->view('emails.post-liked', [
                'userName' => $notifiable->name,
                'likerName' => $this->liker->name,
                'postTitle' => \Illuminate\Support\Str::limit(strip_tags($this->post->content), 50, '...') ?: 'Untitled Post',
                'postContent' => $this->post->content,
                'postUrl' => url("/posts/{$this->post->id}")
            ]);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'post_id' => $this->post->id,
            'recipient_id' => $notifiable->id,  // Using the notifiable parameter
            'liker_id' => $this->liker->id,
            'liker_name' => $this->liker->name,
            'post_content' => $this->post->content,
        ];
    }
}
