<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PostPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Allow all authenticated users with verified email to view posts list
        // Assuming that having a verified email indicates an active account
        return $user->hasVerifiedEmail();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Post $post): bool
    {
        // Post owners can always view their own posts
        if ($user->id === $post->user_id) {
            return true;
        }

        // Published posts are publicly visible
        if ($post->published ?? false) {
            return true;
        }

        // Admins and moderators can view any posts (if role system exists)
        if (method_exists($user, 'hasRole')) {
            return $user->hasRole(['admin', 'moderator']);
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasVerifiedEmail();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Post $post): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Post $post): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Post $post): bool
    {
        return false;
    }

    /**
     * Determine whether the user can like the post.
     */
    public function like(User $user, Post $post): Response
    {
        // Prevent users from liking their own posts
        if ($post->user_id === $user->id) {
            return Response::deny('You cannot like your own post.');
        }

        return Response::allow();
    }
}
