<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class LikeController extends Controller
{
    use AuthorizesRequests;

    /**
     * Record a like by the authenticated user on the given post.
     *
     * If the user has already liked the post, this action is idempotent
     * due to the unique constraint on (user_id, post_id) in the likes table.
     *
     * @param  Post  $post  The post to be liked
     * @return RedirectResponse Redirects back to the previous page
     */
    public function store(Post $post): RedirectResponse
    {
        // 🔒 Prevent liking own post
        $this->authorize('like', $post);

        $post->likes()->firstOrCreate(['user_id' => Auth::id()]);
        return back();
    }

    /**
     * Remove the authenticated user's like from the given post.
     *
     * If the user hasn't liked the post, this operation has no effect.
     *
     * @param  Post  $post  The post from which to remove the like
     * @return RedirectResponse Redirects back to the previous page
     */
    public function destroy(Post $post): RedirectResponse
    {
        // 🔒 Prevent liking own post
        $this->authorize('like', $post);

        $post->likes()->where('user_id', Auth::id())->delete();
        return back();
    }
}
