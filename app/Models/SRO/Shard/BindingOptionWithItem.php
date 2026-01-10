<?php

namespace App\Models\SRO\Shard;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class BindingOptionWithItem extends Model
{
    use HasFactory;

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
    protected $table = 'dbo._BindingOptionWithItem';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nItemDBID'
    ];

    public static function getBindingOption($ItemDBID)
    {
        return Cache::remember("character_info_binding_{$ItemDBID}", config('global.cache.character_info', 86400), static function () use ($ItemDBID) {
            return self::where('nItemDBID', $ItemDBID)->get()->toArray();
        });
    }
}
