<?php

namespace Database\Seeders;

use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some users first
        $users = User::factory(5)->create();

        // Create posts with some published and some unpublished
        $posts = Post::factory(20)
            ->sequence(
                fn ($sequence) => [
                    'user_id' => $users->random()->id,
                    'is_published' => $sequence->index < 15, // 15 published, 5 unpublished
                    'published_at' => $sequence->index < 15 ? now()->subDays(rand(1, 30)) : null,
                    'content' => fake()->realText(200, 2),
                ]
            )
            ->create();

        // Create some likes randomly
        foreach ($posts as $post) {
            // Randomly decide if this post gets likes (about 60% chance)
            if (fake()->boolean(60)) {
                // Create between 0 and 5 likes for each post
                $numLikes = rand(0, 5);

                // Select random users to like this post, ensuring no user likes their own post
                $otherUsers = $users->where('id', '!=', $post->user_id);

                if ($otherUsers->count() > 0) {
                    $selectedUsers = $otherUsers->random(min($numLikes, $otherUsers->count()));

                    foreach ($selectedUsers as $user) {
                        Like::factory()->create([
                            'user_id' => $user->id,
                            'post_id' => $post->id,
                        ]);
                    }
                }
            }
        }
    }
}
