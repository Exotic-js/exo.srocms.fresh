<?php

namespace App\Models\SRO\Account;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ItemNameDesc extends Model
{
    /**
     * The Database connection name for the model.
     *
     * @var string
     */
    protected $connection = 'account';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    //protected $table = 'dbo._Rigid_ItemNameDesc';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'Service',
        'ID',
        'StrID',
        'KOR',
        'UNK0',
        'UNK1',
        'UNK2',
        'UNK3',
        'VNM',
        'ENG',
        'UNK4',
        'UNK5',
        'UNK6',
        'TUR',
        'ARA',
        'ESP',
        'GER'
    ];

    public function getTable()
    {
        if (config('global.server.version') === 'vSRO') {
            return 'dbo._ItemNameDesc';
        }

        return 'dbo._Rigid_ItemNameDesc';
    }

    public static function getItemName($CodeName128): string
    {
        return Cache::remember("character_info_ItemNameDesc_{$CodeName128}", config('global.cache.character_info', 86400), static function () use ($CodeName128) {
            if (config('global.server.version') === 'vSRO') {
                return self::select('RealName')->where('NameStrID', $CodeName128)->first()->RealName ?? $CodeName128;
            }

            return self::select('ENG')->where('StrID', $CodeName128)->first()->ENG ?? $CodeName128;
        });
    }

    public static function getItemNames(array $ids): array
    {
        $ids = array_unique(array_filter($ids));
        if (empty($ids)) {
            return [];
        }

        return cache()->remember('item_names:' . md5(implode('|', $ids)), config('global.cache.character_info', 86400), function () use ($ids) {
            if (config('global.server.version') === 'vSRO') {
                return self::whereIn('NameStrID', $ids)->pluck('RealName', 'NameStrID')->toArray();
            }

            return self::whereIn('StrID', $ids)->pluck('ENG', 'StrID')->toArray();
        });
    }
}
