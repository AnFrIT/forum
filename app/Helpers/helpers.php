<?php

use Carbon\Carbon;

if (! function_exists('setting')) {
    /**
     * Get setting value
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    function setting($key, $default = null)
    {
        return \App\Models\Setting::get($key, $default);
    }
}

if (! function_exists('format_date')) {
    /**
     * Format date
     *
     * @param  Carbon|string  $date
     * @param  string  $format
     * @return string
     */
    function format_date($date, $format = 'd.m.Y H:i')
    {
        if (! $date instanceof Carbon) {
            $date = Carbon::parse($date);
        }

        return $date->format($format);
    }
}

if (! function_exists('format_date_human')) {
    /**
     * Format date for humans
     *
     * @param  Carbon|string  $date
     * @return string
     */
    function format_date_human($date)
    {
        if (! $date instanceof Carbon) {
            $date = Carbon::parse($date);
        }

        Carbon::setLocale('ru');

        return $date->diffForHumans();
    }
}

if (! function_exists('breadcrumbs')) {
    /**
     * Generate breadcrumbs
     *
     * @param  array  $items
     * @return string
     */
    function breadcrumbs($items = [])
    {
        if (empty($items)) {
            return '';
        }

        $html = '<nav aria-label="breadcrumb"><ol class="breadcrumb">';

        foreach ($items as $i => $item) {
            if (isset($item['url']) && $item['url'] && isset($item['title']) && $item['title']) {
                $html .= '<li class="breadcrumb-item"><a href="'.$item['url'].'">'.e($item['title']).'</a></li>';
            } elseif (isset($item['title']) && $item['title']) {
                $html .= '<li class="breadcrumb-item active">'.e($item['title']).'</li>';
            }
        }

        $html .= '</ol></nav>';

        return $html;
    }
}

if (! function_exists('excerpt')) {
    /**
     * Get excerpt from text
     *
     * @param  string  $text
     * @param  int  $length
     * @param  string  $suffix
     * @return string
     */
    function excerpt($text, $length = 200, $suffix = '...')
    {
        $text = strip_tags($text);
        $text = trim($text);

        if (mb_strlen($text) <= $length) {
            return $text;
        }

        return mb_substr($text, 0, $length).$suffix;
    }
}

if (! function_exists('parse_bbcode')) {
    /**
     * Parse BBCode
     *
     * @param  string  $text
     * @return string
     */
    function parse_bbcode($text)
    {
        $text = e($text);

        // Bold
        $text = preg_replace('/\[b\](.*?)\[\/b\]/s', '<strong>$1</strong>', $text);

        // Italic
        $text = preg_replace('/\[i\](.*?)\[\/i\]/s', '<em>$1</em>', $text);

        // Underline
        $text = preg_replace('/\[u\](.*?)\[\/u\]/s', '<u>$1</u>', $text);

        // Quote
        $text = preg_replace('/\[quote\](.*?)\[\/quote\]/s', '<blockquote class="blockquote">$1</blockquote>', $text);

        // Code
        $text = preg_replace('/\[code\](.*?)\[\/code\]/s', '<pre><code>$1</code></pre>', $text);

        // URL
        $text = preg_replace('/\[url=(.*?)\](.*?)\[\/url\]/s', '<a href="$1" target="_blank" rel="noopener">$2</a>', $text);
        $text = preg_replace('/\[url\](.*?)\[\/url\]/s', '<a href="$1" target="_blank" rel="noopener">$1</a>', $text);

        // Image
        $text = preg_replace('/\[img\](.*?)\[\/img\]/s', '<img src="$1" class="img-fluid" alt="Image">', $text);

        // Line breaks
        $text = nl2br($text);

        return $text;
    }
}

if (! function_exists('active_route')) {
    /**
     * Check if route is active
     *
     * @param  string  $route
     * @param  string  $class
     * @return string
     */
    function active_route($route, $class = 'active')
    {
        return request()->routeIs($route) ? $class : '';
    }
}

if (! function_exists('highlightSearchTerms')) {
    /**
     * Highlight search terms in a text string
     *
     * @param  string  $text
     * @param  string  $query
     * @return string
     */
    function highlightSearchTerms($text, $query)
    {
        if (empty($query) || empty($text)) {
            return $text;
        }
        
        // Convert query to array of terms
        $terms = preg_split('/\s+/', trim($query));
        
        // Escape the terms for regex
        $terms = array_map(function($term) {
            return preg_quote($term, '/');
        }, $terms);
        
        // Replace each term with highlighted version
        foreach ($terms as $term) {
            $text = preg_replace('/(' . $term . ')/iu', '<span class="bg-yellow-200">$1</span>', $text);
        }
        
        return $text;
    }
}
