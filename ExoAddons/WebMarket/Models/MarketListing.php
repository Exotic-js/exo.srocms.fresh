<?php

namespace ExoAddons\WebMarket\Models;

use Illuminate\Database\Eloquent\Model;

class MarketListing extends Model
{
    protected $table = 'exo_market_listings';

    protected $fillable = [
        'account_id',
        'char_id',
        'char_name',
        'sro_item_id',
        'ref_obj_id',
        'plus_opt',
        'item_data_json',
        'price',
        'currency',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'item_data_json' => 'array',
        'expires_at' => 'datetime',
        'price' => 'integer',
        'plus_opt' => 'integer',
    ];
}
