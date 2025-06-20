<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Activity;
use Illuminate\Support\Facades\Schema;

class Topic extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'category_id',
        'user_id',
        'views_count',
        'posts_count',
        'is_pinned',
        'is_locked',
        'is_approved',
        'last_post_at',
        'last_post_user_id',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_locked' => 'boolean',
        'is_approved' => 'boolean',
        'last_post_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($topic) {
            if (! $topic->slug) {
                $topic->slug = Str::slug($topic->title);

                // Ensure unique slug
                $count = 1;
                $originalSlug = $topic->slug;
                while (static::where('slug', $topic->slug)->exists()) {
                    $topic->slug = $originalSlug.'-'.$count++;
                }
            }

            $topic->last_post_at = now();
            $topic->last_post_user_id = $topic->user_id;
        });

        static::created(function ($topic) {
            $topic->user->incrementTopicsCount();
            
            // Record activity only if the activities table exists
            try {
                if (Schema::hasTable('activities')) {
                    Activity::recordTopicCreation($topic);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to record topic creation activity: ' . $e->getMessage());
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function lastPostUser()
    {
        return $this->belongsTo(User::class, 'last_post_user_id');
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

    public function scopeOrdered($query)
    {
        return $query->orderBy('is_pinned', 'desc')
            ->orderBy('last_post_at', 'desc');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function updateLastPost($userId = null)
    {
        $this->update([
            'last_post_at' => now(),
            'last_post_user_id' => $userId ?: auth()->id(),
        ]);
    }

    public function canEdit()
    {
        if (! auth()->check()) {
            return false;
        }

        return auth()->id() === $this->user_id || auth()->user()->can('edit any topics');
    }

    public function canDelete()
    {
        if (! auth()->check()) {
            return false;
        }

        return auth()->id() === $this->user_id || auth()->user()->can('delete any topics');
    }
}
