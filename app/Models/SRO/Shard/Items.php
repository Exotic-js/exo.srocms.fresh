<?php

namespace App\Models\SRO\Shard;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class Items extends Model
{
    /**
     * The Database connection name for the model.
     *
     * @var string
     */
    protected $connection = 'shard';

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
    protected $table = 'dbo._Items';

    /**
     * The table primary Key
     *
     * @var string
     */
    protected $primaryKey = 'ID64';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ID64'
    ];

    /**
     * @var array
     */
    protected $casts = [

    ];

    public static function getItemNameBySerial($serials): array
    {
        return Cache::remember("globals_history_serial_{$serials[0]}", config('global.cache.character_info', 86400), static function () use ($serials) {
            return self::select(
                '_Items.Serial64',
                '_Items.OptLevel',
                '_Rigid_ItemNameDesc.ENG as ItemName',
                DB::raw("REPLACE(REPLACE(_RefObjCommon.AssocFileIcon128, '\\', '/'), '.ddj', '') as IconPath")
            )
            ->join('_RefObjCommon', '_Items.RefItemID', '=', '_RefObjCommon.ID')
            ->leftJoin(DB::raw(DB::connection('account')->getDatabaseName().'.dbo._Rigid_ItemNameDesc'), '_Rigid_ItemNameDesc.StrID', '=', '_RefObjCommon.NameStrID128')
            ->whereIn('Serial64', $serials)
            ->get()
            ->keyBy('Serial64')
            ->toArray();
        });
    }
}
