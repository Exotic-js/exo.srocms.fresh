<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
