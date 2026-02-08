<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Notifications\PostLikedNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    /**
     * Record a like by the authenticated user on the given post.
     *
     * If the user has already liked the post, this action is idempotent
     * due to the unique constraint on (user_id, post_id) in the likes table.
     *
     * @param  Post  $post  The post to be liked
     * @return JsonResponse|RedirectResponse
     */
    public function store(Post $post, Request $request)
    {
        $this->authorize('like', $post);

        $user = Auth::user();

        // Check if like already exists
        $existingLike = $post->likes()->where('user_id', $user->id)->first();

        if (!$existingLike) {
            $post->likes()->create(['user_id' => $user->id]);
            $isLiked = true;
            $newCount = $post->likes()->count(); // Recalculate the count after adding the like

            // Send notification to post owner if the liker is not the post owner
            if ($post->user_id !== $user->id) {
                $post->user->notify(new PostLikedNotification($post, $user));
            }
        } else {
            // Like already exists, return current state
            $isLiked = true;
            $newCount = $post->likes()->count(); // Return current count
        }

        if ($request->expectsJson()) {
            return response()->json([
                'liked' => $isLiked,
                'likes_count' => $newCount,
                'message' => 'Post liked successfully'
            ], 200);
        }

        return back();
    }

    /**
     * Remove the authenticated user's like from the given post.
     *
     * If the user hasn't liked the post, this operation has no effect.
     *
     * @param  Post  $post  The post from which to remove the like
     * @return JsonResponse|RedirectResponse
     */
    public function destroy(Post $post, Request $request)
    {
        $this->authorize('like', $post);

        $user = Auth::user();
        $wasLiked = $post->likes()->where('user_id', $user->id)->exists();

        if ($wasLiked) {
            $post->likes()->where('user_id', $user->id)->delete();
            $newCount = $post->likes()->count(); // Recalculate the count after removing the like
        } else {
            $newCount = $post->likes()->count(); // Return current count
        }

        if ($request->expectsJson()) {
            return response()->json([
                'liked' => false,
                'likes_count' => $newCount,
                'message' => 'Like removed successfully'
            ], 200);
        }

        return back();
    }
}
