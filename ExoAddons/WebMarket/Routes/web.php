<?php

use ExoAddons\WebMarket\Controllers\WebMarketController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::middleware('web')->group(function () {
    // Public/Logged-in Player routes
    Route::prefix('market')->name('market.')->group(function () {
        Route::get('/', [WebMarketController::class, 'index'])->name('index');
        
        // Logged-in players only
        Route::middleware('auth')->group(function () {
            Route::get('/sell', [WebMarketController::class, 'sell'])->name('sell');
            Route::get('/sell/inventory/{charId}', [WebMarketController::class, 'getInventory'])->name('inventory');
            Route::post('/sell/list', [WebMarketController::class, 'list'])->name('list');
            
            Route::post('/buy/{id}', [WebMarketController::class, 'buy'])->name('buy');
            Route::post('/cancel/{id}', [WebMarketController::class, 'cancel'])->name('cancel');
            
            Route::get('/claims', [WebMarketController::class, 'claims'])->name('claims');
            Route::post('/claims/{id}/claim', [WebMarketController::class, 'claim'])->name('claims.claim');
        });

        // Temporary debug routes — remove after diagnosis!
        Route::get('/debug-filter', function () {
            if (!config('app.debug')) abort(403);
            $r = [];

            // Check APH_ChangedSilk full column structure
            try {
                $cols = DB::connection('vanguard')->select(
                    "SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT
                     FROM [GB_JoymaxPortal].INFORMATION_SCHEMA.COLUMNS
                     WHERE TABLE_NAME='APH_ChangedSilk'
                     ORDER BY ORDINAL_POSITION"
                );
                $r['APH_ChangedSilk_columns'] = $cols;
            } catch (\Throwable $e) { $r['APH_ChangedSilk_columns'] = $e->getMessage(); }

            // Show last 5 rows of APH_ChangedSilk for the logged-in user
            try {
                $cmsJid  = auth()->user()?->jid;
                $gameJid = DB::connection('account')->table('dbo.TB_User')->where('PortalJID', $cmsJid)->value('JID');
                $rows = DB::connection('vanguard')->select(
                    "SELECT TOP 5 * FROM [GB_JoymaxPortal].[dbo].[APH_ChangedSilk]
                     WHERE JID=(SELECT PortalJID FROM [SILKROAD_R_ACCOUNT].[dbo].[TB_User] WHERE JID=?)
                     ORDER BY CSID DESC",
                    [$gameJid]
                );
                $r['my_silk_rows'] = $rows;
            } catch (\Throwable $e) { $r['my_silk_rows'] = $e->getMessage(); }

            foreach (['vanguard', 'shard'] as $conn) {
                foreach (['_ShardManagerAddItem','_ShardManagerRemoveItemBySlot','_ShardManagerAddGold'] as $obj) {
                    try {
                        $found = DB::connection($conn)->select("SELECT 1 AS f FROM INFORMATION_SCHEMA.ROUTINES WHERE ROUTINE_NAME=?", [$obj]);
                        $r["{$conn}.{$obj}"] = $found ? 'EXISTS ✓' : 'NOT FOUND ✗';
                    } catch (\Throwable $e) { $r["{$conn}.{$obj}"] = $e->getMessage(); }
                }
            }
            return response()->json($r, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        });

        Route::get('/debug-jid', function () {
            if (!config('app.debug')) abort(403);
            $user   = auth()->user();
            $cmsJid = $user?->jid;
            $r      = ['cms_user_jid' => $cmsJid, 'cms_user_email' => $user?->email];

            // Show full TB_User record for this PortalJID
            try {
                $tbUser = DB::connection('account')
                    ->table('dbo.TB_User')
                    ->where('PortalJID', $cmsJid)
                    ->first();
                $r['TB_User_by_PortalJID'] = $tbUser ?? 'NOT FOUND';
            } catch (\Throwable $e) { $r['TB_User_by_PortalJID'] = $e->getMessage(); }

            // Also try finding by JID directly (maybe CMS jid IS the game JID)
            try {
                $tbUser2 = DB::connection('account')
                    ->table('dbo.TB_User')
                    ->where('JID', $cmsJid)
                    ->first();
                $r['TB_User_by_JID'] = $tbUser2 ?? 'NOT FOUND';
            } catch (\Throwable $e) { $r['TB_User_by_JID'] = $e->getMessage(); }

            // List all characters for this account (both JID and PortalJID lookup)
            try {
                $gameJid = DB::connection('account')->table('dbo.TB_User')->where('PortalJID', $cmsJid)->value('JID');
                $r['resolved_game_jid'] = $gameJid;

                $chars = DB::connection('shard')
                    ->table('dbo._User as u')
                    ->join('dbo._Char as c', 'c.CharID', '=', 'u.CharID')
                    ->where('u.UserJID', $gameJid)
                    ->select('u.UserJID', 'u.CharID', 'c.CharName16', 'c.CurLevel')
                    ->get();
                $r['characters_by_gameJid'] = $chars;
            } catch (\Throwable $e) { $r['characters_by_gameJid'] = $e->getMessage(); }

            // Check silk balance
            try {
                $gameJid = DB::connection('account')->table('dbo.TB_User')->where('PortalJID', $cmsJid)->value('JID');
                $silk = DB::connection('vanguard')->selectOne(
                    "SELECT ISNULL(SUM(acs.RemainedSilk), 0) AS silk_amount
                     FROM [GB_JoymaxPortal].[dbo].[APH_ChangedSilk] acs
                     WHERE acs.JID = (SELECT PortalJID FROM [SILKROAD_R_ACCOUNT].[dbo].[TB_User] WHERE JID = ?)
                     AND acs.AvailableStatus = 'Y' AND acs.SilkType = 3",
                    [$gameJid]
                );
                $r['silk_balance_SilkType3'] = $silk?->silk_amount ?? 0;

                // Also check what ALL silk types show
                $silkAll = DB::connection('vanguard')->select(
                    "SELECT SilkType, SUM(RemainedSilk) as Total
                     FROM [GB_JoymaxPortal].[dbo].[APH_ChangedSilk] acs
                     WHERE acs.JID = (SELECT PortalJID FROM [SILKROAD_R_ACCOUNT].[dbo].[TB_User] WHERE JID = ?)
                     AND acs.AvailableStatus = 'Y'
                     GROUP BY SilkType",
                    [$gameJid]
                );
                $r['silk_by_type'] = $silkAll;
            } catch (\Throwable $e) { $r['silk_balance'] = $e->getMessage(); }

            return response()->json($r, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        });
    });
});
