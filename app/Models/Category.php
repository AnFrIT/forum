<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_ar',
        'description',
        'description_ar',
        'slug',
        'order',
        'parent_id',
        'is_active',
        'is_private',
        'is_readonly',
        'requires_approval',
        'icon',
        'color',
        'image',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_private' => 'boolean',
        'is_readonly' => 'boolean',
        'requires_approval' => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('order');
    }

    public function topics()
    {
        return $this->hasMany(Topic::class);
    }

    public function posts()
    {
        return $this->hasManyThrough(Post::class, Topic::class);
    }

    public function moderators()
    {
        return $this->belongsToMany(User::class, 'category_moderator')
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeMain($query)
    {
        return $query->whereNull('parent_id');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getTopicsCountAttribute()
    {
        return $this->topics()->count();
    }

    public function getPostsCountAttribute()
    {
        return Post::whereHas('topic', function($query) {
            $query->where('category_id', $this->id);
        })->count();
    }

    public function getLastPostAttribute()
    {
        $topic = $this->topics()
            ->with(['lastPostUser', 'posts' => function ($query) {
                $query->latest()->limit(1);
            }])
            ->latest('last_post_at')
            ->first();

        if ($topic && $topic->posts->isNotEmpty()) {
            return $topic->posts->first();
        }

        return null;
    }

    public function getAllChildrenIds()
    {
        $ids = [$this->id];

        foreach ($this->children as $child) {
            $ids = array_merge($ids, $child->getAllChildrenIds());
        }

        return $ids;
    }

    /**
     * Получить популярные категории
     * 
     * @param int $limit Количество категорий для получения
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getPopular($limit = 6)
    {
        return self::where('is_active', true)
            ->withCount(['topics', 'posts'])
            ->orderByDesc('topics_count')
            ->orderByDesc('posts_count')
            ->limit($limit)
            ->get();
    }
}
