<?php

/**
 * Global helper functions for AL-INSAF Forum
 */

if (! function_exists('active_route')) {
    /**
     * Check if current route matches pattern and return active CSS class
     */
    function active_route($pattern, $activeClass = 'active')
    {
        if (request()->routeIs($pattern)) {
            return $activeClass;
        }
        return '';
    }
}

if (! function_exists('setting')) {
    /**
     * Get setting value from config or database
     */
    function setting($key, $default = null)
    {
        // Для основных настроек форума
        switch ($key) {
            case 'forum_name':
                return config('app.name', 'AL-INSAF');
            case 'forum_description':
                return config('settings.forum_description', 'Форум сообщества AL-INSAF');
            case 'posts_per_page':
                return config('settings.posts_per_page', 10);
            case 'topics_per_page':
                return config('settings.topics_per_page', 20);
            default:
                return config("settings.{$key}", $default);
        }
    }
}

if (! function_exists('format_number')) {
    /**
     * Format number with K, M abbreviations
     */
    function format_number($number)
    {
        if ($number >= 1000000) {
            return number_format($number / 1000000, 1).'M';
        } elseif ($number >= 1000) {
            return number_format($number / 1000, 1).'K';
        }

        return number_format($number);
    }
}

if (! function_exists('time_ago')) {
    /**
     * Get time ago string
     */
    function time_ago($datetime)
    {
        return \Carbon\Carbon::parse($datetime)->diffForHumans();
    }
}

if (! function_exists('avatar_url')) {
    /**
     * Get user avatar URL
     */
    function avatar_url($user, $size = 150)
    {
        if ($user && $user->avatar) {
            return Storage::url($user->avatar);
        }

        $email = $user ? $user->email : 'anonymous@example.com';
        $hash = md5(strtolower(trim($email)));

        return "https://www.gravatar.com/avatar/{$hash}?d=identicon&s={$size}";
    }
}

if (! function_exists('highlight_search_terms')) {
    /**
     * Highlight search terms in text
     */
    function highlight_search_terms($text, $query)
    {
        if (! $query) {
            return $text;
        }

        $terms = explode(' ', $query);
        foreach ($terms as $term) {
            if (strlen($term) > 2) {
                $text = preg_replace('/('.preg_quote($term, '/').')/iu', '<mark class="bg-yellow-200 px-1 rounded">$1</mark>', $text);
            }
        }

        return $text;
    }
}

if (! function_exists('truncate_html')) {
    /**
     * Truncate HTML while preserving tags
     */
    function truncate_html($text, $length = 100)
    {
        return Str::limit(strip_tags($text), $length);
    }
}
