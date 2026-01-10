<?php

namespace App\Models\SRO\Shard;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CharCos extends Model
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
    protected $table = 'dbo._CharCos';

    /**
     * The table primary Key
     *
     * @var string
     */
    protected $primaryKey = 'ID';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'OwnerCharID',
        'RefCharID',
        'HP',
        'MP',
        'KeeperNPC',
        'State',
        'CharName',
        'Lvl',
        'ExpOffset',
        'HGP',
        'PetOption',
        'RentEndTime'
    ];

    public static function getPet($iPetId)
    {
        return Cache::remember("character_info_pet_{$iPetId}", config('global.cache.character_info', 86400), function () use ($iPetId) {
            return self::select(
                '_CharCOS.*',
                'TimedJob.*',
                'TimedJob.Data3 as inventorysize',
                'TimedJob.TimeToKeep as inventorykeep',
                'Items.*',
                'Common.*',
                'ObjItem.*'
            )
            ->leftJoin('_TimedJobForPet as TimedJob', static function ($join) {
                $join->on('TimedJob.CharID', '_CharCOS.ID');
                $join->where('TimedJob.Category', '=', 5);
                $join->where('TimedJob.JobID', '=', 22926);
            })
            ->join('_Items as Items', 'Items.Serial64', 'TimedJob.Serial64')
            ->join('_RefObjCommon as Common', 'Items.RefItemId', 'Common.ID')
            ->join('_RefObjItem as ObjItem', 'Common.Link', 'ObjItem.ID')
            ->where('_CharCOS.ID', '=', $iPetId)
            ->first();
        });
    }
}
