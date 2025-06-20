<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'data',
        'description',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    /**
     * Get the user that owns the activity
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include activities of a specific type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include activities for a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Create a new user registration activity
     */
    public static function recordUserRegistration(User $user)
    {
        return self::create([
            'user_id' => $user->id,
            'type' => 'user_registered',
            'description' => 'Новый пользователь зарегистрировался',
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    /**
     * Create a new topic creation activity
     */
    public static function recordTopicCreation(Topic $topic)
    {
        return self::create([
            'user_id' => $topic->user_id,
            'type' => 'topic_created',
            'description' => 'Новая тема создана',
            'data' => [
                'title' => $topic->title,
                'category_id' => $topic->category_id,
                'topic_id' => $topic->id,
            ],
        ]);
    }

    /**
     * Create a new post creation activity
     */
    public static function recordPostCreation(Post $post)
    {
        return self::create([
            'user_id' => $post->user_id,
            'type' => 'post_created',
            'description' => 'Новый ответ в теме',
            'data' => [
                'topic_id' => $post->topic_id,
                'post_id' => $post->id,
            ],
        ]);
    }
} 