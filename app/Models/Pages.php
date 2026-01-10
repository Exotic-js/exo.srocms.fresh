<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Pages extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
    ];

    protected $casts = [];

    protected static function booted()
    {
        static::created(fn ($page) => self::clearCache($page));
        static::updated(fn ($page) => self::clearCache($page));
        static::deleted(fn ($page) => self::clearCache($page));
    }

    public static function getPageNames()
    {
        return Cache::rememberForever("pages_names", function () {
            return self::select('title', 'slug')->get();
        });
    }

    public static function getPage($slug)
    {
        return Cache::rememberForever("pages_view_{$slug}", function () use ($slug) {
            return self::where('slug', $slug)->first();
        });
    }

    protected static function clearCache(self $page): void
    {
        Cache::forget('pages_names');
        Cache::forget("pages_view_{$page->slug}");
    }
}
