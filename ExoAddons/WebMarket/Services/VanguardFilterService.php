<?php

namespace ExoAddons\WebMarket\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Bridges the Web Market with the Vanguard game server Filter.
 *
 * Vanguard item and gold actions are queued through stored procedures.
 * Silk actions use _SharedUpdateSilkAmount, which expects a delta:
 * deduct 300 => pass -300, credit 285 => pass +285.
 */
class VanguardFilterService
{
    public function addItem(
        int $charId,
        string $codeName,
        int $count = 1,
        int $plus = 0,
        int $storageType = 1,
        bool $randomVariance = false
    ): void {
        DB::connection('vanguard')->statement(
            'EXEC dbo._ShardManagerAddItem @CharID=?, @StorageType=?, @CodeName128=?, @Count=?, @Plus=?, @RandomVariance=?',
            [$charId, $storageType, $codeName, $count, $plus, $randomVariance ? 1 : 0]
        );

        Log::info("[VanguardFilter] AddItem CharID={$charId} Item={$codeName} +{$plus} StorageType={$storageType}", [
            'command' => $this->latestCommandFor(1, $charId, $codeName),
        ]);
    }

    public function addItemToChestBox(
        int $gameJid,
        int $charId,
        string $codeName,
        int $count = 1,
        string $referer = 'web_market'
    ): void {
        DB::connection('vanguard')->statement(
            'EXEC dbo._SharedAddItemChestBox @ItemCode=?, @JID=?, @CharID=?, @Count=?, @Referer=?',
            [$codeName, $gameJid, $charId, $count, $referer]
        );

        Log::info("[VanguardFilter] AddItemChestBox GameJID={$gameJid} CharID={$charId} Item={$codeName} Count={$count}", [
            'referer' => $referer,
        ]);
    }

    public function removeItemBySlot(int $charId, int $slotId): void
    {
        DB::connection('vanguard')->statement(
            'EXEC dbo._ShardManagerRemoveItemBySlot @CharID=?, @SlotID=?',
            [$charId, $slotId]
        );

        Log::info("[VanguardFilter] RemoveItemBySlot CharID={$charId} Slot={$slotId}");
    }

    public function addGold(int $charId, int $amount): void
    {
        DB::connection('vanguard')->statement(
            'EXEC dbo._ShardManagerAddGold @CharID=?, @GoldAmount=?',
            [$charId, $amount]
        );

        $action = $amount >= 0 ? "+{$amount}" : "{$amount}";
        Log::info("[VanguardFilter] AddGold CharID={$charId} Amount={$action}");
    }

    public function getCharGold(int $charId): int
    {
        return (int) (DB::connection('shard')
            ->table('dbo._Char')
            ->where('CharID', $charId)
            ->value('RemainGold') ?? 0);
    }

    public function getSilkAmount(int $gameJid): int
    {
        $silkType = (int) config('webmarket.silk_type', 3);

        $result = DB::connection('vanguard')->selectOne(
            "SELECT ISNULL(SUM(acs.RemainedSilk), 0) AS silk_amount
             FROM [GB_JoymaxPortal].[dbo].[APH_ChangedSilk] acs
             WHERE acs.JID = (
                 SELECT PortalJID FROM [SILKROAD_R_ACCOUNT].[dbo].[TB_User] WHERE JID = ?
             )
             AND acs.AvailableStatus = 'Y'
             AND acs.SilkType = ?",
            [$gameJid, $silkType]
        );

        return (int) ($result->silk_amount ?? 0);
    }

    public function deductSilk(int $gameJid, int $amount): void
    {
        $amount = abs($amount);
        $currentBalance = $this->getSilkAmount($gameJid);
        $newBalance = max(0, $currentBalance - $amount);

        DB::connection('vanguard')->statement(
            'EXEC dbo._SharedUpdateSilkAmount @JID=?, @SilkAmount=?',
            [$gameJid, -$amount]
        );

        Log::info("[VanguardFilter] DeductSilk GameJID={$gameJid} -{$amount} | {$currentBalance} -> {$newBalance}");
    }

    public function creditSilk(int $gameJid, int $amount): void
    {
        $amount = abs($amount);
        $currentBalance = $this->getSilkAmount($gameJid);
        $newBalance = $currentBalance + $amount;

        DB::connection('vanguard')->statement(
            'EXEC dbo._SharedUpdateSilkAmount @JID=?, @SilkAmount=?',
            [$gameJid, $amount]
        );

        Log::info("[VanguardFilter] CreditSilk GameJID={$gameJid} +{$amount} | {$currentBalance} -> {$newBalance}");
    }

    public function isConnected(): bool
    {
        try {
            DB::connection('vanguard')->select('SELECT 1 AS ok');
            return true;
        } catch (\Throwable $e) {
            Log::warning('[VanguardFilter] Connection check failed: ' . $e->getMessage());
            return false;
        }
    }

    protected function latestCommandFor(int $type, int $charId, ?string $codeName = null): ?array
    {
        try {
            $query = DB::connection('vanguard')
                ->table('_ShardManagerCommands')
                ->where('Type', $type)
                ->where('Data1', $charId);

            if ($codeName !== null) {
                $query->where('Data3', $codeName);
            }

            $row = $query->orderByDesc('ID')->first();
            return $row ? (array) $row : null;
        } catch (\Throwable $e) {
            Log::warning('[VanguardFilter] Could not read latest command: ' . $e->getMessage());
            return null;
        }
    }
}
