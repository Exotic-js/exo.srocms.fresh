<?php

namespace App\Models\SRO\Shard;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Inventory extends Model
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
    protected $table = 'dbo._Inventory';

    /**
     * The table primary Key
     *
     * @var string
     */
    protected $primaryKey = 'CharID';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'CharID',
        'slot',
        'ItemID'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        // RefObjCommon
        'CashItem' => 'integer',
        'Bionic' => 'integer',
        'TypeID1' => 'integer',
        'TypeID2' => 'integer',
        'TypeID3' => 'integer',
        'TypeID4' => 'integer',
        'DecayTime' => 'integer',
        'Country' => 'integer',
        'Rarity' => 'integer',
        'CanTrade' => 'integer',
        'CanSell' => 'integer',
        'CanBuy' => 'integer',
        'CanBorrow' => 'integer',
        'CanDrop' => 'integer',
        'CanPick' => 'integer',
        'CanRepair' => 'integer',
        'CanUse' => 'integer',
        'CanThrow' => 'integer',
        'Price' => 'integer',
        'CostRepair' => 'integer',
        'CostRevive' => 'integer',
        'CostBorrow' => 'integer',
        'KeepingFee' => 'integer',
        'SellPrice' => 'integer',
        'ReqLevelType1' => 'integer',
        'ReqLevel1' => 'integer',
        'ReqLevelType2' => 'integer',
        'ReqLevel2' => 'integer',
        'ReqLevelType3' => 'integer',
        'ReqLevel3' => 'integer',
        'ReqLevelType4' => 'integer',
        'ReqLevel4' => 'integer',
        'MaxContain' => 'integer',

        // RefObjItem
        'MaxStack' => 'integer',
        'ReqGender' => 'integer',
        'ReqStr' => 'integer',
        'ReqInt' => 'integer',
        'ItemClass' => 'integer',
        'MaxMagicOptOption' => 'integer',

        // Items
        'OptLevel' => 'integer',
        'Variance' => 'integer',
        'MagParamNum' => 'integer',
        'MagParam1' => 'integer',
        'MagParam2' => 'integer',
        'MagParam3' => 'integer',
        'MagParam4' => 'integer',
        'MagParam5' => 'integer',
        'MagParam6' => 'integer',
        'MagParam7' => 'integer',
        'MagParam8' => 'integer',
        'MagParam9' => 'integer',
        'MagParam10' => 'integer',
        'MagParam11' => 'integer',
        'MagParam12' => 'integer',
    ];

    public static function getInventory($CharID, $max = 12, $min = 0, $not = 8)
    {
        return Cache::remember("character_info_inventory_{$CharID}_{$max}_{$min}", now()->addMinutes(config('global.cache.character_info', 1440)), static function () use ($CharID, $max, $min, $not) {
            return self::join('_Items', '_Items.ID64', '_Inventory.ItemID')
            ->join('_RefObjCommon', '_Items.RefItemId', '_RefObjCommon.ID')
            ->join('_RefObjItem', '_RefObjCommon.Link', '_RefObjItem.ID')
            ->leftJoin('_BindingOptionWithItem', static function ($join) {
                $join->on('_BindingOptionWithItem.nItemDBID', '_Items.ID64');
                $join->where('_BindingOptionWithItem.bOptType', '=', '2');
            })
            ->where('CharID', '=', $CharID)
            ->where('Slot', '<=', $max)
            ->where('Slot', '>=', $min)
            ->where('Slot', '!=', $not)
            ->where('ItemID', '!=', 0)
            ->get();
        });
    }

    public static function getInventorySlot($CharID, $slot)
    {
        return self::where('CharID', '=', $CharID)
            ->where('Slot', '=', $slot)
            ->where('ItemID', '>', '0')
            ->first();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function getChar()
    {
        return $this->hasMany(Char::class, 'CharID');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function getItem()
    {
        return $this->belongsTo(Items::class, 'ItemID', 'ID64');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function getSerial64()
    {
        return $this->belongsto(Items::class, 'ItemID', 'ID64');
    }
}
