<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class VoteLog extends Model
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

    public static function getVoteStatus($request, ?string $fingerprint, ?array $sites = null): Collection
    {
        $voteSites = collect(config('vote'));

        if ($sites) {
            $voteSites = $voteSites->only($sites);
        }

        $ip = $request->ip();

        if (!$ip || !$fingerprint) {
            return $voteSites->map(fn($site) => (object) [
                ...$site,
                'canVote' => false,
                'expire' => null,
            ]);
        }

        $activeLogs = self::whereIn('site', $voteSites->pluck('route'))
            ->where(function ($q) use ($ip, $fingerprint) {
                $q->where('ip', $ip)
                    ->orWhere('fingerprint', $fingerprint);
            })
            ->where('expire', '>', now())
            ->pluck('expire', 'site'); // [site => expire]

        return $voteSites->map(fn($site) => (object) [
            ...$site,
            'canVote' => !isset($activeLogs[$site['route']]),
            'expire' => $activeLogs[$site['route']] ?? null,
        ]);
    }
}
