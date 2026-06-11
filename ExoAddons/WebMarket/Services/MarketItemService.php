<?php

namespace ExoAddons\WebMarket\Services;

use ExoAddons\WebMarket\Models\MarketListing;
use ExoAddons\WebMarket\Models\MarketClaim;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * MarketItemService
 *
 * Handles all market operations using the Vanguard Filter for live delivery.
 *
 * Currencies supported: Gold, Silk
 * Gold  → _ShardManagerAddGold  (via Filter, works online/offline)
 * Silk  → _SharedUpdateSilkAmount (via Filter, works online/offline)
 * Items → _ShardManagerAddItem / _ShardManagerRemoveItemBySlot (via Filter)
 */
class MarketItemService
{
    public function __construct(protected VanguardFilterService $filter) {}

    // =========================================================================
    // JID RESOLUTION
    // =========================================================================

    /**
     * Resolve the Game JID (TB_User.JID) from the CMS user JID.
     *
     * For iSRO: User.jid = PortalJID → TB_User.PortalJID → TB_User.JID (game JID)
     * For vSRO: User.jid = TB_User.JID directly (no lookup needed)
     *
     * The game tables (_User, _ActiveServerUser) use TB_User.JID as UserJID.
     */
    public function resolveGameJid(int $cmsJid): ?int
    {
        $version = config('global.server.version', 'iSRO');

        if ($version === 'vSRO') {
            // vSRO: jid IS the game JID
            return $cmsJid;
        }

        // iSRO: jid is PortalJID → look up TB_User.JID
        $gameJid = DB::connection('account')
            ->table('dbo.TB_User')
            ->where('PortalJID', $cmsJid)
            ->value('JID');

        return $gameJid ? (int) $gameJid : null;
    }

    // =========================================================================
    // CHARACTER HELPERS
    // =========================================================================

    public function getUserCharacters(int $userJid)
    {
        $gameJid = $this->resolveGameJid($userJid);
        if (!$gameJid) {
            return collect();
        }

        return DB::connection('shard')
            ->table('dbo._User')
            ->join('dbo._Char', 'dbo._Char.CharID', '=', 'dbo._User.CharID')
            ->where('dbo._User.UserJID', $gameJid)
            ->where('dbo._Char.Deleted', 0)
            ->select('dbo._Char.CharID', 'dbo._Char.CharName16', 'dbo._Char.CurLevel', 'dbo._Char.RefObjID', 'dbo._Char.RemainGold')
            ->get();
    }

    /**
     * Check if account is currently in-game.
     * _ActiveServerUser lives in the SHARD database, not account.
     */
    public function isAccountOnline(int $userJid): bool
    {
        $gameJid = $this->resolveGameJid($userJid);
        if (!$gameJid) {
            return false;
        }

        try {
            return DB::connection('shard')
                ->table('dbo._ActiveServerUser')
                ->where('UserJID', $gameJid)
                ->exists();
        } catch (\Throwable $e) {
            Log::warning("[WebMarket] Online check failed, defaulting offline: " . $e->getMessage());
            return false;
        }
    }

    public function isCharacterOnline(int $charId): bool
    {
        try {
            $latestStatus = DB::connection('log')
                ->table('dbo._LogEventChar')
                ->where('CharID', $charId)
                ->whereIn('EventID', [4, 6])
                ->orderByDesc('EventTime')
                ->value('EventID');

            return (int) $latestStatus === 4;
        } catch (\Throwable $e) {
            Log::warning("[WebMarket] Character online check failed, defaulting offline: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get character inventory from the DB.
     * Accurate when offline; used for item selection before listing.
     *
     * Bypasses InventoryService::convertItemList() entirely — that method
     * does array access on internal stdClass objects which causes fatal errors.
     * We build the display object directly from DB columns instead.
     */
    public function getRealtimeInventory(int $charId): array
    {
        $dbAccount = DB::connection('account')->getDatabaseName();

        $rawItems = DB::connection('shard')
            ->table('dbo._Inventory as inv')
            ->join('dbo._Items as i', 'i.ID64', '=', 'inv.ItemID')
            ->join('dbo._RefObjCommon as r', 'r.ID', '=', 'i.RefItemID')
            ->leftJoin('dbo._RefObjItem as ri', 'ri.ID', '=', 'r.Link')
            ->leftJoin(DB::raw("{$dbAccount}.dbo._Rigid_ItemNameDesc as nameDesc"), 'nameDesc.StrID', '=', 'r.NameStrID128')
            ->where('inv.CharID', $charId)
            ->where('inv.Slot', '>=', 13)
            ->where('inv.ItemID', '>', 0)
            ->select('inv.Slot', 'inv.ItemID as ID64', 'i.*', 'r.*', 'ri.*', 'nameDesc.ENG as ItemName')
            ->get();

        $processed = [];

        foreach ($rawItems as $raw) {
            // Build icon path from AssocFileIcon128
            $iconPath = str_replace('\\', '/', trim($raw->AssocFileIcon128 ?? ''));
            $iconPath = preg_replace('/\.ddj$/i', '', $iconPath);
            $iconPath = strtolower($iconPath . '.png');

            // Determine if Sox item
            $codeName = $raw->CodeName128 ?? '';
            $isSox    = str_contains($codeName, '_RARE') || str_contains($codeName, '_UNIQUE');
            $soxType  = 'Normal';
            if (str_contains($codeName, '_RARE'))   $soxType = 'Seal of Star';
            if (str_contains($codeName, '_UNIQUE'))  $soxType = 'Seal of Moon';
            if (str_contains($codeName, '_LEGEND'))  $soxType = 'Seal of Sun';

            // Parse blue (magic) options from MagParam columns
            $blueInfo = $this->parseMagParams($raw);

            $processed[] = (object) [
                'ID64'       => $raw->ID64,
                'Slot'       => $raw->Slot,
                'ItemName'   => $raw->ItemName ?? $raw->CodeName128 ?? 'Unknown Item',
                'ImgPath'    => $iconPath,
                'OptLevel'   => (int) ($raw->OptLevel ?? 0),
                'SoxType'    => $soxType,
                'Degree'     => $raw->Degree ?? null,
                'ReqLevel1'  => $raw->ReqLevel1 ?? null,
                'CodeName128'=> $codeName,
                'TypeID1'    => $raw->TypeID1 ?? null,
                'TypeID2'    => $raw->TypeID2 ?? null,
                'BlueInfo'   => $blueInfo,
                'WhiteInfo'  => (object) [
                    'PAtack'    => $raw->PAttackMin_L ?? null,
                    'MAtack'    => $raw->MAttackMin_L ?? null,
                    'PDefance'  => $raw->PD_L ?? null,
                    'MDefance'  => $raw->MD_L ?? null,
                    'Durability'=> $raw->Dur_L ?? null,
                    'Critical'  => $raw->CHR_L ?? null,
                ],
            ];
        }

        return $processed;
    }

    /**
     * Parse MagParam columns into human-readable blue option labels.
     */
    private function parseMagParams(object $raw): array
    {
        $result = [];
        $count  = (int) ($raw->MagParamNum ?? 0);

        for ($i = 1; $i <= min($count, 12); $i++) {
            $val = $raw->{"MagParam{$i}"} ?? null;
            if (!$val || $val == 0) continue;
            $result[] = ['code' => "Opt_{$i}", 'name' => "Magic Option {$i}", 'value' => $val];
        }

        return $result;
    }


    // =========================================================================
    // LIST ITEM — Take item via Filter
    // =========================================================================

    /**
     * List an item for sale.
     * Requires offline to read DB inventory accurately.
     * Item removal is sent to the Filter → safe even if they log in afterwards.
     */
    public function listItem(int $charId, int $slot, int $price, string $currency, int $userJid): MarketListing
    {
        $this->validateCurrency($currency);

        // Must be offline — DB inventory is only authoritative when offline
        // Verify character ownership (using game JID for iSRO compatibility)
        $gameJid = $this->resolveGameJid($userJid);
        $char = DB::connection('shard')->table('dbo._User')
            ->where('UserJID', $gameJid)->where('CharID', $charId)->first();
        if (!$char) {
            throw new \RuntimeException("Character not found or access denied.");
        }

        // Read item from inventory slot — comprehensive join to get ALL data needed by the view
        // (_Items alone lacks CodeName128, AssocFileIcon128, stats, etc.)
        $dbAccount = DB::connection('account')->getDatabaseName();

        $fullItemRow = (array) DB::connection('shard')
            ->table('dbo._Inventory as inv')
            ->join('dbo._Items as i', 'i.ID64', '=', 'inv.ItemID')
            ->join('dbo._RefObjCommon as r', 'r.ID', '=', 'i.RefItemID')
            ->leftJoin('dbo._RefObjItem as ri', 'ri.ID', '=', 'r.Link')
            ->leftJoin(DB::raw("{$dbAccount}.dbo._Rigid_ItemNameDesc as nameDesc"), 'nameDesc.StrID', '=', 'r.NameStrID128')
            ->where('inv.CharID', $charId)
            ->where('inv.Slot', $slot)
            ->where('inv.ItemID', '>', 0)
            ->select('inv.Slot', 'inv.ItemID', 'i.*', 'r.*', 'ri.*', 'nameDesc.ENG as ItemName')
            ->first();

        if (empty($fullItemRow) || empty($fullItemRow['ItemID'])) {
            throw new \RuntimeException("Item not found in character inventory slot.");
        }

        // Use the slot and ItemID from the full row
        $inventorySlot  = $fullItemRow['Slot'];
        $inventoryItemId = $fullItemRow['ItemID'];
        $inventoryRefItemId = $fullItemRow['RefItemID'] ?? $fullItemRow['ID'] ?? 0;
        $inventoryOptLevel  = $fullItemRow['OptLevel'] ?? 0;

        $charName  = DB::connection('shard')->table('dbo._Char')->where('CharID', $charId)->value('CharName16');
        $expiresAt = now()->addDays(config('webmarket.listing_duration_days', 7));
        $isCharOnline = $this->isCharacterOnline($charId);

        return DB::transaction(function () use ($userJid, $charId, $charName, $inventorySlot, $inventoryItemId, $inventoryRefItemId, $inventoryOptLevel, $fullItemRow, $price, $currency, $expiresAt, $isCharOnline) {
            $listing = MarketListing::create([
                'account_id'     => $userJid,
                'char_id'        => $charId,
                'char_name'      => $charName,
                'sro_item_id'    => $inventoryItemId,
                'ref_obj_id'     => $inventoryRefItemId,
                'plus_opt'       => $inventoryOptLevel,
                'item_data_json' => $fullItemRow,
                'price'          => $price,
                'currency'       => $currency,
                'status'         => 'active',
                'expires_at'     => $expiresAt,
            ]);

            $this->removeListedItem($charId, $inventorySlot, $inventoryItemId, $isCharOnline);

            return $listing;
        });
    }

    // =========================================================================
    // BUY ITEM — Deduct currency live, create claims
    // =========================================================================

    /**
     * Purchase a listing.
     *
     * Gold:  Buyer deducted via _ShardManagerAddGold (live, no offline needed)
     *        Seller gets a gold_proceeds claim → delivered via Filter when claimed
     * Silk:  Buyer deducted via _SharedUpdateSilkAmount (live, no offline needed)
     *        Seller credited immediately via _SharedUpdateSilkAmount
     *
     * No offline requirement for either currency.
     */
    public function buyItem(int $listingId, int $buyerUserJid, ?int $buyerCharId = null): MarketClaim
    {
        // Quick pre-check (no lock yet — fast read)
        $exists = MarketListing::where('id', $listingId)->where('status', 'active')->exists();
        if (!$exists) {
            throw new \RuntimeException("Listing is not active or already sold.");
        }

        $price    = 0;
        $currency = '';

        return DB::transaction(function () use ($listingId, $buyerUserJid, $buyerCharId, &$price, &$currency) {

            // Re-fetch inside transaction WITH row lock to prevent double purchase
            $listing = MarketListing::where('id', $listingId)
                ->where('status', 'active')
                ->lockForUpdate()
                ->first();

            if (!$listing) {
                throw new \RuntimeException("Listing is not active or already sold.");
            }
            if ($listing->account_id === $buyerUserJid) {
                throw new \RuntimeException("You cannot buy your own items.");
            }

            $price     = $listing->price;
            $tax       = (int) floor($price * (config('webmarket.tax', 5) / 100));
            $sellerAmt = $price - $tax;
            $currency  = $listing->currency;


            if ($currency === 'gold') {
                // Gold — both buyer and seller handled via Filter
                if (!$buyerCharId) {
                    throw new \RuntimeException("You must select a character to deduct Gold from.");
                }

                // Verify character belongs to buyer (using game JID)
                $buyerGameJid = $this->resolveGameJid($buyerUserJid);
                $char = DB::connection('shard')->table('dbo._Char')
                    ->join('dbo._User', 'dbo._User.CharID', '=', 'dbo._Char.CharID')
                    ->where('dbo._User.UserJID', $buyerGameJid)
                    ->where('dbo._Char.CharID', $buyerCharId)
                    ->select('dbo._Char.RemainGold', 'dbo._Char.CharName16')
                    ->first();

                if (!$char) {
                    throw new \RuntimeException("Selected character not found on your account.");
                }

                // Balance check against DB (rough validation — Filter applies live)
                $currentGold = $this->filter->getCharGold($buyerCharId);
                if ($currentGold < $price) {
                    throw new \RuntimeException(
                        "Not enough Gold. Your character has " . number_format($currentGold) . " Gold, need " . number_format($price) . "."
                    );
                }

                // Deduct from buyer via Filter (live — works online or offline)
                $this->filter->addGold($buyerCharId, -$price);

                // Seller gets a gold_proceeds claim (they pick char when claiming)
                MarketClaim::create([
                    'account_id'     => $listing->account_id,
                    'listing_id'     => $listing->id,
                    'ref_obj_id'     => 0,
                    'plus_opt'       => 0,
                    'item_data_json' => ['gold_amount' => $sellerAmt],
                    'type'           => 'gold_proceeds',
                    'status'         => 'pending',
                ]);

            } elseif ($currency === 'silk') {
                $buyerGameJid  = $this->resolveGameJid($buyerUserJid);
                $sellerGameJid = $this->resolveGameJid($listing->account_id);

                if (!$buyerGameJid) {
                    throw new \RuntimeException("Could not resolve buyer game account. Please contact support.");
                }

                // Read current usable market Silk balance.
                $buyerSilk = $this->filter->getSilkAmount($buyerGameJid);

                if ($buyerSilk < $price) {
                    throw new \RuntimeException(
                        "Not enough Silk. You have " . number_format($buyerSilk) . " Silk, need " . number_format($price) . "."
                    );
                }

                Log::info("[WebMarket] SILK PURCHASE", [
                    'listing_id'      => $listing->id,
                    'price'           => $price,
                    'tax'             => $price - $sellerAmt,
                    'buyerGameJid'    => $buyerGameJid,
                    'buyerSilkBefore' => $buyerSilk,
                    'buyerSilkAfter'  => $buyerSilk - $price,
                    'sellerGameJid'   => $sellerGameJid,
                    'sellerReceives'  => $sellerAmt,
                ]);

                // Deduct from buyer with a negative delta.
                $this->filter->deductSilk($buyerGameJid, $price);

                // Credit seller immediately with a positive delta.
                if ($sellerGameJid) {
                    $this->filter->creditSilk($sellerGameJid, $sellerAmt);
                }

            } else {
                throw new \RuntimeException("Invalid currency. Only Gold and Silk are supported.");
            }

            // Mark listing sold
            $listing->update(['status' => 'sold']);

            // Create item claim for buyer (delivered via Filter in claimItem)
            return MarketClaim::create([
                'account_id'     => $buyerUserJid,
                'listing_id'     => $listing->id,
                'ref_obj_id'     => $listing->ref_obj_id,
                'plus_opt'       => $listing->plus_opt,
                'item_data_json' => $listing->item_data_json,
                'type'           => 'purchase',
                'status'         => 'pending',
            ]);
        });
    }

    // =========================================================================
    // CANCEL LISTING
    // =========================================================================

    public function cancelListing(int $listingId, int $userJid): void
    {
        $listing = MarketListing::where('id', $listingId)
            ->where('account_id', $userJid)
            ->where('status', 'active')
            ->first();

        if (!$listing) {
            throw new \RuntimeException("Listing not found or cannot be cancelled.");
        }

        DB::transaction(function () use ($listing) {
            $listing->update(['status' => 'cancelled']);

            MarketClaim::create([
                'account_id'     => $listing->account_id,
                'listing_id'     => $listing->id,
                'ref_obj_id'     => $listing->ref_obj_id,
                'plus_opt'       => $listing->plus_opt,
                'item_data_json' => $listing->item_data_json,
                'type'           => 'return',
                'status'         => 'pending',
            ]);
        });
    }

    // =========================================================================
    // CLAIM — Deliver via Filter (NO offline required for items or gold!)
    // =========================================================================

    /**
     * Claim a pending item or gold proceeds.
     *
     * Items     → _ShardManagerAddItem  (online or offline ✅)
     * Gold      → _ShardManagerAddGold  (online or offline ✅)
     * No offline requirement for any claim type!
     */
    public function claimItem(int $claimId, int $charId, int $userJid): void
    {
        $claim = MarketClaim::where('id', $claimId)
            ->where('account_id', $userJid)
            ->where('status', 'pending')
            ->first();

        if (!$claim) {
            throw new \RuntimeException("Claim not found or already processed.");
        }

        // Verify character belongs to user (using game JID for iSRO compatibility)
        $gameJid    = $this->resolveGameJid($userJid);
        $charExists = DB::connection('shard')->table('dbo._User')
            ->where('UserJID', $gameJid)->where('CharID', $charId)->exists();

        if (!$charExists) {
            throw new \RuntimeException("Selected character does not belong to your account.");
        }

        DB::transaction(function () use ($claim, $charId, $gameJid) {

            if ($claim->type === 'gold_proceeds') {
                // Gold proceeds → add to character via Filter (no offline needed!)
                $goldAmount = (int) ($claim->item_data_json['gold_amount'] ?? 0);
                $this->filter->addGold($charId, $goldAmount);

            } else {
                // Item (purchase or return) → deliver via Filter
                $itemData = $claim->item_data_json;
                $codeName = $itemData['CodeName128'] ?? null;

                if (!$codeName) {
                    throw new \RuntimeException("Could not resolve item CodeName. Please contact support.");
                }

                if (config('webmarket.claim_delivery', 'chest_box') === 'chest_box') {
                    $this->filter->addItemToChestBox(
                        gameJid:  $gameJid,
                        charId:   $charId,
                        codeName: $codeName,
                        count:    1,
                        referer:  "webmarket_claim_{$claim->id}"
                    );
                } else {
                    $this->filter->addItem(
                        charId:         $charId,
                        codeName:       $codeName,
                        count:          1,
                        plus:           (int) ($claim->plus_opt ?? 0),
                        storageType:    (int) config('webmarket.claim_storage_type', 2),
                        randomVariance: false
                    );
                }

                // Log which char received it so we can trace
                $charName = DB::connection('shard')
                    ->table('dbo._Char')->where('CharID', $charId)->value('CharName16');
                Log::info("[WebMarket] Item claimed", [
                    'claim_id'  => $claim->id,
                    'char_id'   => $charId,
                    'char_name' => $charName,
                    'item'      => $codeName,
                    'delivery'  => config('webmarket.claim_delivery', 'chest_box'),
                ]);
            }

            $claim->update([
                'status'          => 'claimed',
                'claimed_char_id' => $charId,
                'claimed_at'      => now(),
            ]);
        });
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    private function validateCurrency(string $currency): void
    {
        if (!in_array($currency, ['gold', 'silk'])) {
            throw new \RuntimeException("Invalid currency. Accepted: Gold, Silk.");
        }
    }

    private function removeListedItem(int $charId, int $slot, int $itemId, bool $isCharOnline): void
    {
        if ($isCharOnline) {
            $this->filter->removeItemBySlot($charId, $slot);
            Log::info('[WebMarket] Listed item removed via Vanguard filter', [
                'char_id' => $charId,
                'slot' => $slot,
                'item_id' => $itemId,
            ]);
            return;
        }

        $updated = DB::connection('shard')
            ->table('dbo._Inventory')
            ->where('CharID', $charId)
            ->where('Slot', $slot)
            ->where('ItemID', $itemId)
            ->update(['ItemID' => 0]);

        if ($updated !== 1) {
            throw new \RuntimeException('Could not remove the item from the offline character inventory. Please refresh and try again.');
        }

        Log::info('[WebMarket] Listed item removed directly from offline inventory', [
            'char_id' => $charId,
            'slot' => $slot,
            'item_id' => $itemId,
        ]);
    }
}
