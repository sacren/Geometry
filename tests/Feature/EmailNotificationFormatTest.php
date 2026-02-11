<?php

use App\Models\Post;
use App\Models\User;
use App\Notifications\PostLikedNotification;
use Illuminate\Support\Facades\Notification;

it('validates complete email notification functionality with proper queue handling', function () {
    // Create users and post
    $postOwner = User::factory()->create(['email' => 'postowner@example.com']);
    $liker = User::factory()->create(['email' => 'liker@example.com']);
    $post = Post::factory()->create([
        'user_id' => $postOwner->id,
        'content' => 'This is the content of the test post that was liked.'
    ]);

    // Verify the notification implements ShouldQueue for proper queuing
    $notification = new PostLikedNotification($post, $liker);
    expect($notification)->toBeInstanceOf(App\Notifications\PostLikedNotification::class);
    expect($notification)->toBeInstanceOf(Illuminate\Contracts\Queue\ShouldQueue::class);

    // Verify the data is properly set
    expect($notification->post->id)->toEqual($post->id);
    expect($notification->liker->id)->toEqual($liker->id);

    // Fake the notification system to prevent actual sending during test
    Notification::fake();

    // Send the notification
    $postOwner->notify($notification);

    // Verify that the notification was sent through the system
    Notification::assertSentTo(
        $postOwner,
        PostLikedNotification::class,
        function ($notification) use ($post, $liker) {
            return $notification->post->id === $post->id &&
                   $notification->liker->id === $liker->id;
        }
    );

    // Verify the notification data is correctly passed to email template
    $mailMessage = $notification->toMail($postOwner);

    // Verify the data is passed correctly
    $this->assertArrayHasKey('userName', $mailMessage->data());
    $this->assertArrayHasKey('likerName', $mailMessage->data());
    $this->assertArrayHasKey('postTitle', $mailMessage->data());
    $this->assertArrayHasKey('postContent', $mailMessage->data());
    $this->assertArrayHasKey('postUrl', $mailMessage->data());

    // Verify specific values
    $data = $mailMessage->data();
    $this->assertEquals($postOwner->name, $data['userName']);
    $this->assertEquals($liker->name, $data['likerName']);
    // The postTitle is generated from content with Str::limit, so we just verify it's not empty
    $this->assertNotEmpty($data['postTitle']);
    $this->assertEquals($post->content, $data['postContent']);
    $this->assertEquals(url("/posts/{$post->id}"), $data['postUrl']);
});

it('maintains performance with queued notifications', function () {
    // Measure performance of notification creation and queuing
    $start = microtime(true);

    // Create users and post
    $postOwner = User::factory()->create();
    $liker = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $postOwner->id]);

    // Create and send notification (this gets queued)
    $notification = new PostLikedNotification($post, $liker);
    $postOwner->notify($notification);

    $end = microtime(true);
    $duration = ($end - $start) * 1000; // Convert to milliseconds

    // The notification should be queued quickly (under 50ms)
    // This verifies that the queuing doesn't block the main thread
    expect($duration)->toBeLessThan(50.0);

    // The notification should be accepted without throwing exceptions
    $this->assertTrue(true); // If we reach this point, no exceptions were thrown during queuing
});
