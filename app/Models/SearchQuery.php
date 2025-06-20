<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchQuery extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'term',
        'user_id',
        'count',
        'last_searched_at',
        'filters',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'filters' => 'array',
        'last_searched_at' => 'datetime',
    ];

    /**
     * Get the user that performed the search.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Log a search query.
     *
     * @param string $term
     * @param int|null $userId
     * @param array $filters
     * @return SearchQuery|null
     */
    public static function log($term, $userId = null, $filters = [])
    {
        try {
            $term = trim(strtolower($term));
            
            if (empty($term)) {
                return null;
            }

            $query = self::firstOrNew([
                'term' => $term,
            ]);

            $query->count = ($query->count ?? 0) + 1;
            $query->user_id = $userId;
            $query->last_searched_at = now();
            $query->filters = $filters;
            $query->save();

            return $query;
        } catch (\Exception $e) {
            // В случае ошибки просто возвращаем null
            return null;
        }
    }

    /**
     * Get popular search queries.
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getPopular($limit = 10)
    {
        try {
            return self::orderBy('count', 'desc')
                ->limit($limit)
                ->get();
        } catch (\Exception $e) {
            // Возвращаем пустую коллекцию в случае ошибки (например, если таблица не существует)
            return collect();
        }
    }
} 