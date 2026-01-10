<?php

namespace App\Models\SRO\Shard;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiegeFortress extends Model
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
    protected $table = 'dbo._SiegeFortress';

    /**
     * The table primary Key
     *
     * @var string
     */
    protected $primaryKey = 'FortressID';

    /**
     * The attributes format for dates.
     *
     * @var array
     */
    protected $dates = [
        'CreatedDungeonTime'
    ];

    protected $dateFormat = 'Y-m-d H:i:s';

    public static function getFortressWar()
    {
        return Cache::remember('fortress_war', config('global.cache.fortress_war', 604800), function () {
            return self::select([
                "FortressID",
                "GuildID",
                "TaxRatio",
                "_Guild.Name"
            ])
            ->join("_Guild", "_SiegeFortress.GuildID", "=", "_Guild.ID")
            ->get();
        });
    }

    public function getGuildName()
    {
        return $this->hasOne(Guild::class, 'ID', 'GuildID')->where('ID', '!=', 0);
    }
}
