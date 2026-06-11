Route::get('/debug-filter', function () {
    if (!app()->isLocal() && !config('app.debug')) {
        abort(403);
    }

    $results = [];

    // Check _ShardManagerCommands in vanguard
    try {
        $rows = DB::connection('vanguard')
            ->select("SELECT TOP 5 ID, Type, CharID, Status=ISNULL(CAST(Status AS VARCHAR),'?') FROM _ShardManagerCommands ORDER BY ID DESC");
        $results['vanguard._ShardManagerCommands'] = $rows ?: '(empty)';
    } catch (\Throwable $e) {
        $results['vanguard._ShardManagerCommands'] = 'ERROR: ' . $e->getMessage();
    }

    // Check _ShardManagerCommands in shard
    try {
        $rows = DB::connection('shard')
            ->select("SELECT TOP 5 ID, Type, CharID FROM _ShardManagerCommands ORDER BY ID DESC");
        $results['shard._ShardManagerCommands'] = $rows ?: '(empty)';
    } catch (\Throwable $e) {
        $results['shard._ShardManagerCommands'] = 'ERROR: ' . $e->getMessage();
    }

    // Check if _ShardManagerAddItem exists in vanguard
    try {
        $p = DB::connection('vanguard')
            ->select("SELECT ROUTINE_NAME FROM INFORMATION_SCHEMA.ROUTINES WHERE ROUTINE_NAME='_ShardManagerAddItem'");
        $results['_ShardManagerAddItem_in_vanguard'] = $p ? 'EXISTS ✓' : 'NOT FOUND ✗';
    } catch (\Throwable $e) {
        $results['_ShardManagerAddItem_in_vanguard'] = 'ERROR: ' . $e->getMessage();
    }

    // Check if _ShardManagerAddItem exists in shard
    try {
        $p = DB::connection('shard')
            ->select("SELECT ROUTINE_NAME FROM INFORMATION_SCHEMA.ROUTINES WHERE ROUTINE_NAME='_ShardManagerAddItem'");
        $results['_ShardManagerAddItem_in_shard'] = $p ? 'EXISTS ✓' : 'NOT FOUND ✗';
    } catch (\Throwable $e) {
        $results['_ShardManagerAddItem_in_shard'] = 'ERROR: ' . $e->getMessage();
    }

    // Check _ActiveServerUser in shard
    try {
        $p = DB::connection('shard')
            ->select("SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='_ActiveServerUser'");
        $results['_ActiveServerUser_in_shard'] = $p ? 'EXISTS ✓' : 'NOT FOUND ✗';
    } catch (\Throwable $e) {
        $results['_ActiveServerUser_in_shard'] = 'ERROR: ' . $e->getMessage();
    }

    // Check _ActiveServerUser in account
    try {
        $p = DB::connection('account')
            ->select("SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='_ActiveServerUser'");
        $results['_ActiveServerUser_in_account'] = $p ? 'EXISTS ✓' : 'NOT FOUND ✗';
    } catch (\Throwable $e) {
        $results['_ActiveServerUser_in_account'] = 'ERROR: ' . $e->getMessage();
    }

    return response()->json($results, 200, [], JSON_PRETTY_PRINT);
});
