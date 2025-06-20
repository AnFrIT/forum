<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Activity;
use Illuminate\Support\Facades\Schema;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'topic_id',
        'user_id',
        'is_approved',
        'is_edited',
        'edited_at',
        'edited_by',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'is_edited' => 'boolean',
        'edited_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($post) {
            // Only increment posts_count for replies, not for the initial topic post
            if ($post->topic->posts()->count() > 1) {
                $post->topic->increment('posts_count');
            }
            $post->topic->updateLastPost($post->user_id);
            $post->user->incrementPostsCount();
            
            // Record activity only if the activities table exists
            try {
                if (Schema::hasTable('activities')) {
                    Activity::recordPostCreation($post);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to record post creation activity: ' . $e->getMessage());
            }
        });

        static::deleted(function ($post) {
            // Only decrement posts_count if it's not the initial topic post
            if ($post->topic->posts()->count() > 0) {
                $post->topic->decrement('posts_count');
            }
        });
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'edited_by');
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeVisible($query)
    {
        if (auth()->check() && auth()->user()->isModerator()) {
            return $query;
        }

        return $query->approved();
    }

    public function canEdit()
    {
        if (! auth()->check()) {
            return false;
        }

        // Can edit within 30 minutes of creation
        if (auth()->id() === $this->user_id && $this->created_at->addMinutes(30)->isFuture()) {
            return true;
        }

        return auth()->user()->can('edit any posts');
    }

    public function canDelete()
    {
        if (! auth()->check()) {
            return false;
        }

        return auth()->id() === $this->user_id || auth()->user()->can('delete any posts');
    }

    public function markAsEdited($editorId = null)
    {
        $this->update([
            'is_edited' => true,
            'edited_at' => now(),
            'edited_by' => $editorId ?: auth()->id(),
        ]);
    }
}
