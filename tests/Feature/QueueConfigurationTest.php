<?php

use App\Models\Post;
use App\Models\User;
use App\Notifications\PostLikedNotification;
use Illuminate\Support\Facades\Notification;

it('validates PostLikedNotification is properly configured for queued delivery', function () {
    // Create users and post
    $postOwner = User::factory()->create();
    $liker = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $postOwner->id]);

    // Create the notification instance
    $notification = new PostLikedNotification($post, $liker);

    // Verify the notification implements ShouldQueue for asynchronous processing
    expect($notification)->toBeInstanceOf(Illuminate\Contracts\Queue\ShouldQueue::class);

    // Fake the notification system to prevent actual email sending during test
    Notification::fake();

    // Send the notification to verify it can be dispatched
    $postOwner->notify($notification);

    // Verify that the notification was properly sent through the notification system
    Notification::assertSentTo($postOwner, PostLikedNotification::class);
});
