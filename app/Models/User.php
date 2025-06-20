<?php

namespace App\Models;

use App\Models\Post;
use App\Models\Report;
use App\Models\Topic;
use App\Models\Activity;
use App\Models\Conversation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'preferred_language',
        'is_admin',
        'is_moderator',
        'is_banned',
        'banned_at',
        'ban_reason',
        'ban_expires_at',
        'bio',
        'avatar',
        'country',
        'website',
        'last_activity_at',
        'last_ip',
        'posts_count',
        'topics_count',
        'reputation',
        'signature',
        'timezone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'last_ip',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
        'is_admin' => 'boolean',
        'is_moderator' => 'boolean',
        'is_banned' => 'boolean',
        'banned_at' => 'datetime',
        'ban_expires_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'posts_count' => 'integer',
        'topics_count' => 'integer',
        'reputation' => 'integer',
    ];

    /**
     * ИСПРАВЛЕНО: Добавлены отсутствующие связи
     */
    
    /**
     * Связь с темами (topics), которые создал пользователь
     */
    public function topics()
    {
        return $this->hasMany(Topic::class)->orderBy('created_at', 'desc');
    }

    /**
     * Связь с сообщениями (posts), которые написал пользователь
     */
    public function posts()
    {
        return $this->hasMany(Post::class)->orderBy('created_at', 'desc');
    }

    /**
     * Связь с отправленными личными сообщениями
     */
    public function sentMessages()
    {
        return $this->hasMany(PrivateMessage::class, 'sender_id');
    }

    /**
     * Связь с полученными личными сообщениями
     */
    public function receivedMessages()
    {
        return $this->hasMany(PrivateMessage::class, 'recipient_id');
    }

    /**
     * Связь с жалобами, поданными пользователем
     */
    public function reports()
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }

    /**
     * Связь с вложениями пользователя
     */
    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    /**
     * Категории, где пользователь является модератором
     */
    public function moderatedCategories()
    {
        return $this->belongsToMany(Category::class, 'category_moderator')
            ->withTimestamps();
    }

    /**
     * Role checking methods
     */
    public function isAdmin(): bool
    {
        return $this->is_admin ?? false;
    }

    public function isModerator(): bool
    {
        return $this->is_moderator || $this->is_admin;
    }

    public function canModerate(): bool
    {
        return $this->isModerator() && ! $this->isBanned();
    }

    public function isBanned(): bool
    {
        if (! $this->is_banned) {
            return false;
        }

        // Check if temporary ban has expired
        if ($this->ban_expires_at && $this->ban_expires_at->isPast()) {
            $this->update([
                'is_banned' => false,
                'banned_at' => null,
                'ban_reason' => null,
                'ban_expires_at' => null,
            ]);

            return false;
        }

        return true;
    }

    public function ban(?string $reason = null, ?\Carbon\Carbon $expiresAt = null): void
    {
        $this->update([
            'is_banned' => true,
            'banned_at' => now(),
            'ban_reason' => $reason,
            'ban_expires_at' => $expiresAt,
        ]);
    }

    public function unban(): void
    {
        $this->update([
            'is_banned' => false,
            'banned_at' => null,
            'ban_reason' => null,
            'ban_expires_at' => null,
        ]);
    }

    /**
     * Activity tracking
     */
    public function updateLastActivity(): void
    {
        try {
            $this->update([
                'last_activity_at' => now(),
                'last_ip' => request()->ip(),
            ]);
            
            // Дублируем обновление через запрос для гарантии
            DB::table('users')
                ->where('id', $this->id)
                ->update([
                    'last_activity_at' => now(),
                    'last_ip' => request()->ip(),
                ]);
        } catch (\Exception $e) {
            \Log::error('Failed to update last activity: ' . $e->getMessage());
        }
    }

    public function isOnline(): bool
    {
        return $this->last_activity_at &&
               $this->last_activity_at->diffInMinutes(now()) <= 15;
    }

    public function getOnlineStatusAttribute(): string
    {
        if ($this->isOnline()) {
            return 'online';
        }

        if ($this->last_activity_at && $this->last_activity_at->diffInHours(now()) <= 24) {
            return 'away';
        }

        return 'offline';
    }

    /**
     * Avatar handling
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            // Check if the avatar is a base64 encoded image
            if (strpos($this->avatar, 'data:image') === 0) {
                return $this->avatar;
            }
            
            // Check if the avatar is a full URL
            if (filter_var($this->avatar, FILTER_VALIDATE_URL)) {
                return $this->avatar;
            }
            
            // Legacy support - Check if the file exists in the public directory
            if (file_exists(public_path($this->avatar))) {
                return url($this->avatar);
            }
            
            // Legacy support - check storage path
            if (strpos($this->avatar, 'storage/') === 0) {
                return url($this->avatar);
            }
            
            // Legacy support - old storage format
            $storageAvatarPath = 'storage/' . $this->avatar;
            if (file_exists(public_path($storageAvatarPath))) {
                return url($storageAvatarPath);
            }
        }

        // Generate Gravatar URL as fallback
        $hash = md5(strtolower(trim($this->email)));
        return "https://www.gravatar.com/avatar/{$hash}?d=identicon&s=150";
    }

    /**
     * Language preferences
     */
    public function getPreferredLanguageAttribute($value): string
    {
        return $value ?: config('app.locale', 'ru');
    }

    public function setPreferredLanguage(string $language): void
    {
        $supportedLanguages = ['ru', 'en', 'ar'];

        if (in_array($language, $supportedLanguages)) {
            $this->update(['preferred_language' => $language]);
        }
    }

    /**
     * Statistics
     */
    public function incrementPostsCount(): void
    {
        $this->increment('posts_count');
    }

    public function decrementPostsCount(): void
    {
        $this->decrement('posts_count');
    }

    public function incrementTopicsCount(): void
    {
        $this->increment('topics_count');
    }

    public function decrementTopicsCount(): void
    {
        $this->decrement('topics_count');
    }

    public function addReputation(int $points = 1): void
    {
        $this->increment('reputation', $points);
    }

    public function removeReputation(int $points = 1): void
    {
        $this->decrement('reputation', $points);
    }

    /**
     * Scopes
     */
    public function scopeAdmins($query)
    {
        return $query->where('is_admin', true);
    }

    public function scopeModerators($query)
    {
        return $query->where('is_moderator', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_banned', false);
    }

    public function scopeBanned($query)
    {
        return $query->where('is_banned', true);
    }

    public function scopeOnline($query)
    {
        return $query->where('last_activity_at', '>=', now()->subMinutes(15));
    }

    public function scopeByLanguage($query, $language)
    {
        return $query->where('preferred_language', $language);
    }

    /**
     * Display name with role badges
     */
    public function getDisplayNameAttribute(): string
    {
        $name = $this->name;

        if ($this->is_admin) {
            $name .= ' [ADMIN]';
        } elseif ($this->is_moderator) {
            $name .= ' [MOD]';
        }

        return $name;
    }

    /**
     * Get user role for display
     */
    public function getRoleNameAttribute(): string
    {
        if ($this->is_admin) {
            return __('main.admin');
        }

        if ($this->is_moderator) {
            return __('main.moderator');
        }

        if ($this->is_banned) {
            return __('main.banned');
        }

        return __('main.user');
    }

    /**
     * Format join date
     */
    public function getJoinDateAttribute(): string
    {
        return $this->created_at->format('M Y');
    }

    /**
     * Check if user can access admin panel
     */
    public function canAccessAdminPanel(): bool
    {
        return ($this->is_admin || $this->hasRole('admin')) && ! $this->is_banned;
    }

    /**
     * Check if the user has a given ability.
     */
    public function can($abilities, $arguments = [])
    {
        // Если пользователь админ и не забанен - разрешаем все
        if ($this->is_admin && ! $this->is_banned) {
            return true;
        }

        // Иначе используем стандартную проверку Laravel
        return parent::can($abilities, $arguments);
    }

    /**
     * Методы для совместимости с проверками ролей
     */
    public function hasRole($role)
    {
        if ($role === 'admin') {
            return $this->is_admin;
        }
        if ($role === 'moderator') {
            return $this->is_moderator;
        }

        return false;
    }

    public function assignRole($role)
    {
        // Пустой метод для совместимости
        return $this;
    }

    public function roles()
    {
        // Возвращаем пустую коллекцию для совместимости
        return collect([]);
    }

    public function syncRoles($roles)
    {
        // Пустой метод для совместимости
        return $this;
    }

    public function removeRole($role)
    {
        // Пустой метод для совместимости
        return $this;
    }

    public function isTrusted(): bool
    {
        // Users with high reputation or moderators/admins are trusted
        return $this->reputation >= 50 || $this->isModerator();
    }

    public function hasRecentViolations(): bool
    {
        // Check if user has reports resolved against them in the last 30 days
        return $this->receivedReports()
            ->where('status', 'resolved')
            ->where('updated_at', '>=', now()->subDays(30))
            ->exists();
    }

    public function receivedReports()
    {
        return Report::whereHasMorph('reportable', [Topic::class, Post::class], function ($query) {
            $query->where('user_id', $this->id);
        });
    }

    /**
     * Get all conversations where the user is a participant
     */
    public function conversations()
    {
        return Conversation::where(function($query) {
            $query->where('user_one_id', $this->id)
                ->orWhere('user_two_id', $this->id);
        });
    }

    /**
     * Get active conversations (not deleted by this user)
     */
    public function activeConversations()
    {
        return $this->conversations()->where(function($query) {
            $query->where(function($q) {
                $q->where('user_one_id', $this->id)
                    ->where('user_one_deleted', false);
            })->orWhere(function($q) {
                $q->where('user_two_id', $this->id)
                    ->where('user_two_deleted', false);
            });
        });
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            // Record user registration activity only if the activities table exists
            try {
                if (Schema::hasTable('activities')) {
                    Activity::recordUserRegistration($user);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to record user registration activity: ' . $e->getMessage());
            }
        });
    }
}