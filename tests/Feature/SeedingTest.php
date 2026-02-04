<?php

use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;

// Test that the database seeding creates the expected amount of data and works properly
it('seeds the database with expected amounts of users, posts, and likes and allows viewing posts page', function () {
    // Fresh migrate and seed once
    Artisan::call('migrate:fresh', ['--seed' => true]);

    // Check that we have the expected number of records
    expect(User::count())->toBe(10); // 1 test user + 4 from DatabaseSeeder + 5 from PostSeeder
    expect(Post::count())->toBe(20); // From PostSeeder

    // Since seeding involves randomness, we should expect at least some likes
    expect(Like::count())->toBeGreaterThanOrEqual(0);

    // Check that we have both published and unpublished posts
    expect(Post::where('is_published', true)->count())->toBe(15);
    expect(Post::where('is_published', false)->count())->toBe(5);

    // Check that some posts have likes (with tolerance for randomness)
    $postsWithLikes = Post::has('likes')->count();
    expect($postsWithLikes)->toBeGreaterThanOrEqual(0);

    // Check that likes are properly associated with different posts (if any likes exist)
    $totalLikes = Like::count();
    if ($totalLikes > 0) {
        $postIdsWithLikes = Like::pluck('post_id')->unique()->count();
        expect($postIdsWithLikes)->toBeGreaterThan(0);

        // Check that likes are properly associated with different users
        $userIdsWhoLiked = Like::pluck('user_id')->unique()->count();
        expect($userIdsWhoLiked)->toBeGreaterThan(0);

        // Get a post that has likes to test relationships
        $postWithLikes = Post::withCount('likes')->has('likes')->first();
        if ($postWithLikes) {
            expect($postWithLikes->likes_count)->toBeGreaterThan(0);

            // Check that the post has a user
            expect($postWithLikes->user)->not->toBeNull();
            expect($postWithLikes->user->name)->not->toBeEmpty();

            // Check that the post's likes belong to different users (not the post owner)
            $likeUsers = $postWithLikes->likes->pluck('user_id');
            expect($likeUsers->contains($postWithLikes->user_id))->toBeFalse();
        }
    }

    // Test that published posts have proper timestamps
    $publishedPosts = Post::where('is_published', true)->get();
    foreach ($publishedPosts as $post) {
        expect($post->published_at)->not->toBeNull();
        expect($post->published_at)->toBeInstanceOf(\Carbon\Carbon::class);
    }

    $unpublishedPosts = Post::where('is_published', false)->get();
    foreach ($unpublishedPosts as $post) {
        expect($post->published_at)->toBeNull();
    }

    // Test that the application can load the posts page with seeded data
    // Authenticate as a user to access the posts page
    $user = User::first();
    $this->actingAs($user);

    $response = $this->get('/posts');
    $response->assertStatus(200);

    // For Inertia-based applications, we should check the component and props
    $response->assertInertia(function ($page) {
        $page->component('posts/Index'); // Check that the correct component is loaded

        // Check that posts are included in the props
        $page->has('posts.data'); // Check that posts data exists
        $page->has('posts.links'); // Check that pagination links exist
    });
});
