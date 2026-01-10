<?php

namespace App\Models\SRO\Shard;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class TimedJob extends Model
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
    protected $table = 'dbo._TimedJob';

    public static function getCharBuffInfo($CharID)
    {
        return Cache::remember("char_buff_{$CharID}", config('global.cache.character_info', 86400), function () use ($CharID) {
            return self::select(
                    '_TimedJob.JobID',
                    '_TimedJob.Category',
                    '_TimedJob.Serial64',
                    '_RefSkill.UI_IconFile',
                    '_RefSkill.UI_SkillName'
                )
                ->join('_RefSkill', '_RefSkill.ID', '=', '_TimedJob.JobID')
                ->where('_TimedJob.CharID', $CharID)
                ->where('_RefSkill.UI_IconFile', '!=', 'xxx')
                ->orderByDesc('_TimedJob.Category')
                ->get()
                ->map(function ($row) {
                    $iconPath = str_replace('\\', '/', trim($row->UI_IconFile));
                    $iconPath = preg_replace('/\.ddj$/i', '', $iconPath);
                    $iconPath = strtolower($iconPath . '.png');

                    $row->UI_IconFile_PNG = $iconPath;

                    return $row;
                });
        });
    }
}
