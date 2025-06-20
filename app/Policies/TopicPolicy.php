<?php

namespace App\Policies;

use App\Models\Topic;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TopicPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the given user can view the topic.
     */
    public function view(User $user, Topic $topic)
    {
        return true; // Everyone can view topics
    }

    /**
     * Determine if the given user can edit the topic.
     */
    public function edit(User $user, Topic $topic)
    {
        // Allow if user is admin
        if ($user->isAdmin()) {
            return true;
        }
        
        // Allow if user is moderator
        if ($user->isModerator()) {
            return true;
        }
        
        // Allow if user is the author of the topic
        return $user->id === $topic->user_id;
    }

    /**
     * Determine if the given user can delete the topic.
     */
    public function delete(User $user, Topic $topic)
    {
        // Allow if user is admin
        if ($user->isAdmin()) {
            return true;
        }
        
        // Allow if user is moderator
        if ($user->isModerator()) {
            return true;
        }
        
        // Allow if user is the author of the topic
        return $user->id === $topic->user_id;
    }

    /**
     * Determine if the given user can create a topic.
     */
    public function create(User $user)
    {
        return !$user->isBanned();
    }
} 