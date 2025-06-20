<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            Cache::forget('settings');
        });

        static::deleted(function () {
            Cache::forget('settings');
        });
    }

    public static function get($key, $default = null)
    {
        $settings = Cache::remember('settings', 3600, function () {
            return self::all()->pluck('value', 'key');
        });

        $value = $settings->get($key, $default);

        // Cast value based on type
        $setting = self::where('key', $key)->first();
        if ($setting) {
            switch ($setting->type) {
                case 'boolean':
                    return filter_var($value, FILTER_VALIDATE_BOOLEAN);
                case 'integer':
                    return (int) $value;
                case 'float':
                    return (float) $value;
                case 'json':
                    return json_decode($value, true);
            }
        }

        return $value;
    }

    public static function set($key, $value)
    {
        $setting = self::firstOrNew(['key' => $key]);

        if (is_array($value)) {
            $value = json_encode($value);
            $setting->type = 'json';
        } elseif (is_bool($value)) {
            $value = $value ? 'true' : 'false';
            $setting->type = 'boolean';
        } elseif (is_int($value)) {
            $setting->type = 'integer';
        } elseif (is_float($value)) {
            $setting->type = 'float';
        } else {
            $setting->type = 'string';
        }

        $setting->value = $value;
        $setting->save();

        return $setting;
    }

    public static function getByGroup($group)
    {
        return self::where('group', $group)->get();
    }
}
