<?php

namespace App\Models;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Like extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'post_id'];

    public $timestamps = true;

    /**
     * Get the user that owns the like.
     *
     * @return BelongsTo<User, self>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the post that owns the like.
     *
     * @return BelongsTo<Post, self>
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Scope a query to only include likes by a given user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include likes for a given post.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $postId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForPost($query, $postId)
    {
        return $query->where('post_id', $postId);
    }
}
