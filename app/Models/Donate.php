<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Donate extends Model
{
    protected $connection = 'sqlsrv';

    protected $table = 'donates';

    protected $primaryKey = 'id';

    protected $fillable = [
        'method',
        'transaction_id',
        'status',
        'amount',
        'value',
        'desc',
        'jid',
        'ip',
    ];

    public static function DonateLog(array $data)
    {
        return self::create([
            'method' => $data['method'] ?? 'unknown',
            'transaction_id' => $data['transaction_id'] ?? Str::uuid(),
            'status' => $data['status'] ?? 'true',
            'amount' => $data['amount'] ?? 0,
            'value' => $data['value'] ?? 0,
            'desc' => $data['desc'] ?? '',
            'jid' => $data['jid'] ?? 0,
            'ip' => $data['ip'] ?? request()->ip(),
        ]);
    }

    public static function getDonateSum()
    {
        return Cache::remember('donate_sum', 60, function () {
            return self::where('amount', '>', 0)->sum('amount');
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'jid', 'jid');
    }
}
