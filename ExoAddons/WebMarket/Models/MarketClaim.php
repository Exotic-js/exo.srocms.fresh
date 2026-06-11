<?php

namespace ExoAddons\WebMarket\Models;

use Illuminate\Database\Eloquent\Model;

class MarketClaim extends Model
{
    protected $table = 'exo_market_claims';

    protected $fillable = [
        'account_id',
        'listing_id',
        'ref_obj_id',
        'plus_opt',
        'item_data_json',
        'type',
        'status',
        'claimed_char_id',
        'claimed_at',
    ];

    protected $casts = [
        'item_data_json' => 'array',
        'claimed_at'     => 'datetime',
        'plus_opt'       => 'integer',
    ];

    public function listing()
    {
        return $this->belongsTo(MarketListing::class, 'listing_id');
    }

    /**
     * Build a display-ready object from the raw item_data_json.
     * Bypasses InventoryService entirely to avoid stdClass/array access issues.
     * Returns null for gold_proceeds claims.
     */
    public function getParsedItemAttribute(): ?object
    {
        if ($this->type === 'gold_proceeds') {
            return null;
        }

        $raw = $this->item_data_json;
        if (empty($raw) || !is_array($raw)) {
            return null;
        }

        $d = (object) $raw;

        // Build icon path from AssocFileIcon128 (same logic as index.blade.php)
        $iconPath = str_replace('\\', '/', trim($d->AssocFileIcon128 ?? ''));
        $iconPath = preg_replace('/\.ddj$/i', '', $iconPath);
        $iconPath = strtolower($iconPath . '.png');

        $displayName = $d->ItemName ?? $d->CodeName128 ?? $d->NameStrID128 ?? null;

        // Return null if truly no useful data (old listing stored only _Items columns)
        if (!$displayName && empty($d->CodeName128)) {
            return null;
        }

        return (object) [
            'ItemName' => $displayName ?? 'Unknown Item',
            'ImgPath'  => $iconPath,
            'OptLevel' => (int) ($d->OptLevel ?? 0),
            'SoxType'  => $d->SoxType ?? null,
            'Degree'   => $d->Degree ?? null,
            'ReqLevel1' => $d->ReqLevel1 ?? null,
            'CodeName128' => $d->CodeName128 ?? null,
        ];
    }
}
