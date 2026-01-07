<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Referral extends Model
{
    protected $fillable = [
        'code',
        'name',
        'ip',
        'fingerprint',
        'jid',
        'invited_jid',
        'points',
    ];

    public static function getReferralLogs(int $perPage = 20)
    {
        $logs = self::select('jid', DB::raw('SUM(points) as total_points'))
            ->groupBy('jid')
            ->orderByDesc('total_points')
            ->paginate($perPage);

        $jids = $logs->pluck('jid')->all();

        $latestReferrals = self::whereIn('jid', $jids)
            ->latest('created_at')
            ->get()
            ->groupBy('jid');

        $logs->getCollection()->transform(function ($ref) use ($latestReferrals) {
            $referral = $latestReferrals[$ref->jid]->first();

            return (object)[
                'jid' => $ref->jid,
                'total_points' => $ref->total_points,
                'code' => $referral->code,
                'ip' => $referral->ip,
                'name' => optional($referral->creator)->username,
            ];
        });

        return $logs;
    }

    public static function createReferral($user, ?string $fingerprint = null): self
    {
        $invite = $user->invitesCreated()->first();
        if ($invite) {
            return $invite;
        }

        do {
            $code = strtoupper(Str::random(8));
        } while (self::where('code', $code)->exists());

        return $user->invitesCreated()->create([
            'code' => $code,
            'name' => $user->username,
            'jid' => $user->jid,
            'ip' => request()->ip(),
            'fingerprint' => $fingerprint,
        ]);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'jid', 'jid');
    }

    public function invitedUser()
    {
        return $this->belongsTo(User::class, 'invited_jid', 'jid');
    }
}
