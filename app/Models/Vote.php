<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class Vote extends Model
{
    protected $fillable = [
        'jid',
        'site',
        'ip',
        'fingerprint',
        'expire',
    ];

    protected $casts = [
        'expire' => 'datetime',
    ];

    public static function getVotes($request, ?string $fingerprint): Collection
    {
        $voteSites = collect(config('vote'));
        $ip = $request->ip();

        if (!$ip || !$fingerprint) {
            return $voteSites->map(fn($site) => (object) [
                ...$site,
                'expire' => null,
            ]);
        }

        $logs = self::whereIn('site', $voteSites->pluck('route'))
            ->where(function ($q) use ($ip, $fingerprint) {
                $q->where('ip', $ip)
                    ->orWhere('fingerprint', $fingerprint);
            })
            ->get()
            ->keyBy('site');

        return $voteSites->map(function ($site) use ($logs) {
            $log = $logs->get($site['route']);

            return (object) [
                ...$site,
                'expire' => $log?->expire,
            ];
        });
    }

    public static function activeVote(string $site, string $ip, string $fingerprint): ?self
    {
        return self::where('site', $site)
            ->where(function ($q) use ($ip, $fingerprint) {
                $q->where('ip', $ip)
                    ->orWhere('fingerprint', $fingerprint);
            })
            ->where('expire', '>', now())
            ->first();
    }

    public static function getVotesCount()
    {
        return Cache::remember('votes_count', 60, function () {
            return self::whereNotNull('expire')->count();
        });
    }
}
