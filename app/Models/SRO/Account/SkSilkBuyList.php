<?php

namespace App\Models\SRO\Account;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class SkSilkBuyList extends Model
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
    protected $table = 'dbo.SK_SilkBuyList';

    /**
     * The table primary Key
     *
     * @var string JID
     */
    protected $primaryKey = 'BuyNo';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'BuyNo',
        'UserJID',
        'Silk_Type',
        'Silk_Reason',
        'Silk_Offset',
        'Silk_Remain',
        'ID',
        'BuyQuantity',
        'OrderNumber',
        'PGCompany',
        'PayMethod',
        'PGUniqueNo',
        'AuthNumber',
        'AuthDate',
        'SubJID',
        'srID',
        'SlipPaper',
        'MngID',
        'IP',
        'RegDate'
    ];

    public static function getSilkHistory($jid, $paginate = 10, $page = 1): LengthAwarePaginator
    {
        $data = Cache::remember("account_info_vsro_donate_history_{$jid}_{$paginate}_{$page}", config('global.cache.account_info', 600), function () use ($paginate, $page, $jid) {
            return self::select(
                'SK_SilkBuyList.BuyNo',
                'SK_SilkBuyList.OrderNumber',
                'SK_SilkBuyList.Silk_Offset',
                'SK_SilkBuyList.Silk_Remain',
                'SK_SilkBuyList.Silk_Type',
                'SK_SilkBuyList.RegDate'
            )
                ->where('SK_SilkBuyList.UserJID', $jid)
                ->orderBy('SK_SilkBuyList.RegDate', 'desc')
                ->get();
        });

        return new LengthAwarePaginator(
            $data->forPage($page, $paginate)->values(),
            $data->count(),
            $paginate,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );
    }
}
