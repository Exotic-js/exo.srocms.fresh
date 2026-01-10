<?php

namespace App\Models\SRO\Shard;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Schedule extends Model
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
    protected $table = 'dbo._Schedule';

    /**
     * The table primary Key
     *
     * @var string
     */
    protected $primaryKey = 'ScheduleIdx';

    public static function getSchedules($Idx = [])
    {
        return Cache::remember("event_schedule_{$Idx[0]}", config('global.cache.event_schedule', 604800), function () use ($Idx) {
            return self::whereIn("ScheduleDefineIdx", $Idx)->get();
        });
    }
}
