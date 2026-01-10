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

    ];

    public static function getInventory($CharID, $max = 12, $min = 0, $not = 8)
    {
        return Cache::remember("character_info_inventory_{$CharID}_{$max}_{$min}", config('global.cache.character_info', 86400), static function () use ($CharID, $max, $min, $not) {
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
