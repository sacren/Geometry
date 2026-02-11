<?php

use App\Models\Post;
use App\Models\User;
use App\Notifications\PostLikedNotification;
use Illuminate\Support\Facades\Notification;

it('directly tests if notification is properly queued', function () {
    // Create users and post
    $postOwner = User::factory()->create();
    $liker = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $postOwner->id]);

    // Fake the notification system
    Notification::fake();

    // Create and send notification directly
    $notification = new PostLikedNotification($post, $liker);

    // Check if notification implements ShouldQueue
    expect($notification)->toBeInstanceOf(Illuminate\Contracts\Queue\ShouldQueue::class);

    // Send notification
    $postOwner->notify($notification);

    // Assert that the notification was sent to the user
    Notification::assertSentTo(
        $postOwner,
        PostLikedNotification::class
    );
});
