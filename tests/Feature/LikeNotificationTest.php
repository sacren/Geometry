<?php

use App\Models\Post;
use App\Models\User;
use App\Notifications\PostLikedNotification;
use Illuminate\Support\Facades\Notification;

it('sends notification when a post is liked by another user', function () {
    // Create users and post
    $postOwner = User::factory()->create();
    $liker = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $postOwner->id]);

    // Fake notifications
    Notification::fake();

    // Act as the liker user
    $this->actingAs($liker);

    // Call the LikeController store method directly
    $controller = new \App\Http\Controllers\LikeController();
    $request = \Illuminate\Http\Request::create('/posts/' . $post->id . '/likes', 'POST');

    $controller->store($post, $request);

    // Assert that the notification was sent to the post owner
    Notification::assertSentTo(
        $postOwner,
        PostLikedNotification::class,
        function ($notification) use ($post, $liker) {
            return $notification->post->id === $post->id &&
                   $notification->liker->id === $liker->id;
        }
    );
});

it('cannot like your own post - authorization prevents it', function () {
    // Create user and their post
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    // Act as the same user who owns the post
    $this->actingAs($user);

    // Call the LikeController store method directly
    $controller = new \App\Http\Controllers\LikeController();
    $request = \Illuminate\Http\Request::create('/posts/' . $post->id . '/likes', 'POST');

    // Expect an authorization exception
    $this->expectException(\Illuminate\Auth\Access\AuthorizationException::class);
    $this->expectExceptionMessage('You cannot like your own post.');

    $controller->store($post, $request);
});
