<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'amount',
        'type',
        'valid_date',
        'jid',
        'status'
    ];

    protected $casts = [
        'valid_date' => 'datetime',
    ];

    public static function getUserVoucher(int $jid)
    {
        return Cache::remember("vouchers_user_{$jid}", config('global.cache.account_info', 600), function () use ($jid) {
            return self::where('jid', $jid)->get();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'jid', 'jid');
    }
}
