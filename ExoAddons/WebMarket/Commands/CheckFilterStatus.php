<?php

namespace ExoAddons\WebMarket\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckFilterStatus extends Command
{
    protected $signature   = 'market:filter-check';
    protected $description = 'Check Vanguard Filter queue and procedure status';

    public function handle(): void
    {
        $this->info('=== Checking _ShardManagerCommands in VANGUARD DB ===');
        try {
            $rows = DB::connection('vanguard')
                ->select("SELECT TOP 10 * FROM _ShardManagerCommands ORDER BY ID DESC");
            if (empty($rows)) {
                $this->warn('  Table is empty or not found via this connection.');
            } else {
                foreach ($rows as $r) {
                    $this->line("  ID={$r->ID} Type={$r->Type} CharID={$r->CharID} Status=" . ($r->Status ?? '?'));
                }
            }
        } catch (\Throwable $e) {
            $this->error('  Vanguard: ' . $e->getMessage());
        }

        $this->info('=== Checking _ShardManagerCommands in SHARD DB ===');
        try {
            $rows = DB::connection('shard')
                ->select("SELECT TOP 10 * FROM _ShardManagerCommands ORDER BY ID DESC");
            if (empty($rows)) {
                $this->warn('  Table is empty or not found via this connection.');
            } else {
                foreach ($rows as $r) {
                    $this->line("  ID={$r->ID} Type={$r->Type} CharID={$r->CharID}");
                }
            }
        } catch (\Throwable $e) {
            $this->error('  Shard: ' . $e->getMessage());
        }

        $this->info('=== Checking _ShardManagerAddItem procedure exists ===');
        try {
            $p = DB::connection('vanguard')
                ->select("SELECT ROUTINE_NAME FROM INFORMATION_SCHEMA.ROUTINES WHERE ROUTINE_NAME='_ShardManagerAddItem'");
            $this->line($p ? '  EXISTS in Vanguard DB ✓' : '  NOT FOUND in Vanguard DB ✗');
        } catch (\Throwable $e) {
            $this->error('  ' . $e->getMessage());
        }

        $this->info('=== Checking _ShardManagerRemoveItemBySlot exists ===');
        try {
            $p = DB::connection('vanguard')
                ->select("SELECT ROUTINE_NAME FROM INFORMATION_SCHEMA.ROUTINES WHERE ROUTINE_NAME='_ShardManagerRemoveItemBySlot'");
            $this->line($p ? '  EXISTS in Vanguard DB ✓' : '  NOT FOUND in Vanguard DB ✗');
        } catch (\Throwable $e) {
            $this->error('  ' . $e->getMessage());
        }

        $this->info('=== Checking _ActiveServerUser table location ===');
        foreach (['vanguard', 'shard', 'account'] as $conn) {
            try {
                $exists = DB::connection($conn)
                    ->select("SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='_ActiveServerUser'");
                $this->line("  {$conn}: " . ($exists ? 'EXISTS ✓' : 'NOT FOUND ✗'));
            } catch (\Throwable $e) {
                $this->error("  {$conn}: " . $e->getMessage());
            }
        }
    }
}
