<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'author_id',
        'title',
        'slug',
        'image',
        'category',
        'content',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($post) {
            if ( !$post->author_id ) {
                $post->author_id = Auth::id();
            }
        });
    }

    protected static function booted()
    {
        static::created(fn ($news) => self::clearCache($news));
        static::updated(fn ($news) => self::clearCache($news));
        static::deleted(fn ($news) => self::clearCache($news));
    }

    public static function getPosts()
    {
        return Cache::rememberForever("news", function () {
            return self::where('active', '=', 1)->where('published_at', '<=', now())->orderBy('created_at', 'DESC')->get();
        });
    }

    public static function getPost($slug)
    {
        return Cache::rememberForever("news_view_{$slug}", function () use ($slug) {
            return self::where('slug', $slug)->first();
        });
    }

    public function author()
    {
        return $this->belongsTo(User::class);
    }

    protected static function clearCache(self $news): void
    {
        Cache::forget('news');
        Cache::forget("news_view_{$news->slug}");
    }
}
