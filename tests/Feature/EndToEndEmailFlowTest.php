<?php

use App\Models\Post;
use App\Models\User;
use App\Notifications\PostLikedNotification;
use Illuminate\Support\Facades\Notification;

it('verifies complete flow: notification is properly configured for queuing', function () {
    // Create users and post
    $postOwner = User::factory()->create(['email' => 'postowner@example.com']);
    $liker = User::factory()->create(['email' => 'liker@example.com']);
    $post = Post::factory()->create(['user_id' => $postOwner->id, 'content' => 'Test post content']);

    // Fake the notification system
    Notification::fake();

    // Verify the notification implements ShouldQueue
    $notification = new PostLikedNotification($post, $liker);
    expect($notification)->toBeInstanceOf(Illuminate\Contracts\Queue\ShouldQueue::class);

    // Send the notification
    $postOwner->notify($notification);

    // Assert that the notification was sent to the user
    Notification::assertSentTo(
        $postOwner,
        PostLikedNotification::class
    );
});

it('verifies controller triggers notification when like is created', function () {
    // Create users and post
    $postOwner = User::factory()->create();
    $liker = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $postOwner->id]);

    // Fake the notification system to verify notification is sent
    Notification::fake();

    // Act as the liker and like the post
    $this->actingAs($liker);

    $response = $this->post("/posts/{$post->id}/likes");

    // Verify the like was created successfully
    $response->assertStatus(302); // Redirect after successful like

    // Verify the like exists in the database
    $this->assertDatabaseHas('likes', [
        'post_id' => $post->id,
        'user_id' => $liker->id,
    ]);

    // Verify that the notification was sent to the post owner
    Notification::assertSentTo(
        $postOwner,
        PostLikedNotification::class,
        function ($notification) use ($post, $liker) {
            return $notification->post->id === $post->id && $notification->liker->id === $liker->id;
        }
    );
});
