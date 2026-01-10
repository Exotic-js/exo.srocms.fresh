<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    public $timestamps = false;

    public $incrementing = false;

    protected $primaryKey = 'key';

    protected $keyType = 'string';

    protected $fillable = [
        'key',
        'value'
    ];

    public static function get($key, $default = null)
    {
        return Cache::rememberForever("setting_{$key}", function () use ($key, $default) {
            return optional(self::where('key', $key)->first())->value ?? $default;
        });
    }

    public static function set($key, $value)
    {
        $setting = self::updateOrCreate(['key' => $key], ['value' => $value]);

        Cache::forget("setting_{$key}");
        Cache::forget("settings_all");

        return $setting;
    }

    public static function cached()
    {
        return cache()->rememberForever('settings_all', function () {
            return self::pluck('value', 'key');
        });
    }
}
