<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PostController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = Auth::id(); // Get current user ID once

        $postsQuery = Post::with('user:id,name,email')
            ->withCount('likes') // 👈 adds `likes_count` attribute
            ->where('is_published', true) // Use boolean cast to filter published posts
            ->where(function ($query) {
                // only show posts that are published in the past or have no published date
                $query->where('published_at', '<=', now())
                      ->orWhereNull('published_at');
            });

        // If user is authenticated, check which posts they've liked
        if ($userId) {
            // Use join/subquery approach to determine if current user liked each post
            $postsQuery->addSelect([
                'liked_by_current_user' => Like::selectRaw('count(*) > 0')
                    ->whereColumn('post_id', 'posts.id')
                    ->byUser($userId)
            ]);
        } else {
            // If not authenticated, none of the posts are liked by current user
            $postsQuery->selectRaw('*, false as liked_by_current_user');
        }

        $posts = $postsQuery->latest()
            ->paginate(3)
            ->withQueryString();

        // If user is not authenticated, ensure the attribute is properly set
        if (!$userId) {
            $posts->getCollection()->transform(function ($post) {
                $post->liked_by_current_user = false;
                return $post;
            });
        }

        return Inertia::render('posts/Index', [
            'posts' => $posts,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        $user = Auth::user();
        assert($user instanceof User);

        $postData = $request->validated();
        $postData['published_at'] = now();
        $postData['is_published'] = true;

        $user->posts()->create($postData);

        return to_route('posts.index')->with('success', 'Post created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        //
    }

    /**
     * Delete the given post after authorizing the action.
     *
     * This method checks if the authenticated user is authorized to delete
     * the post via the PostPolicy, permanently removes it from storage,
     * and redirects back to the posts index with a success message.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Post $post): RedirectResponse
    {
        $this->authorize('delete', $post);
        $post->delete();
        return to_route('posts.index');
    }
}
