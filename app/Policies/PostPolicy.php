<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the given user can edit the post.
     */
    public function edit(User $user, Post $post)
    {
        // Allow if user is admin
        if ($user->isAdmin()) {
            return true;
        }
        
        // Allow if user is moderator
        if ($user->isModerator()) {
            return true;
        }
        
        // Allow if user is the author of the post
        return $user->id === $post->user_id;
    }

    /**
     * Determine if the given user can delete the post.
     */
    public function delete(User $user, Post $post)
    {
        // Allow if user is admin
        if ($user->isAdmin()) {
            return true;
        }
        
        // Allow if user is moderator
        if ($user->isModerator()) {
            return true;
        }
        
        // Allow if user is the author of the post
        return $user->id === $post->user_id;
    }
} 