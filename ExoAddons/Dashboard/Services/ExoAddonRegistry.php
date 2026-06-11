<?php

namespace ExoAddons\Dashboard\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * ExoAddonRegistry
 *
 * The brain of the ExoAddons system.
 * Scans ExoAddons/ folders, manages install/uninstall/toggle,
 * resolves dependencies, checks health, and caches the addon list.
 */
class ExoAddonRegistry
{
    /** Cache key for the addon list */
    protected const CACHE_KEY = 'exo.addons.registry';
    protected const CACHE_TTL = 300; // 5 minutes

    /** Base path where addon folders live */
    protected string $addonsPath;

    public function __construct()
    {
        $this->addonsPath = base_path('ExoAddons');
    }

    /* ================================================================
       PUBLIC API
       ================================================================ */

    /**
     * Scan all addon folders + merge with DB records.
     * Returns array keyed by slug with full state info.
     * Results are cached for CACHE_TTL seconds.
     *
     * @return array<string, array>
     */
    public function scanAll(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return $this->buildAddonList();
        });
    }

    /**
     * Force a fresh scan (clears cache first).
     */
    public function freshScan(): array
    {
        Cache::forget(self::CACHE_KEY);
        return $this->scanAll();
    }

    /**
     * Install an addon from a discovered folder.
     *
     * Flow:
     *  1. Validate manifest
     *  2. Check dependencies
     *  3. Security check (namespace must be ExoAddons\)
     *  4. DB::transaction → insert exo_addons record
     *  5. Run migrations via Artisan
     *  6. Publish assets
     *  7. Clear cache
     *
     * @throws \RuntimeException on validation or dependency failure
     * @throws \Throwable on migration failure (transaction rolled back)
     */
    public function install(string $slug): array
    {
        $manifest = $this->readManifest($slug);

        if (!$manifest) {
            throw new \RuntimeException("addon.json not found for addon: {$slug}");
        }

        // Already installed?
        if (DB::table('exo_addons')->where('slug', $slug)->exists()) {
            throw new \RuntimeException("Addon '{$slug}' is already installed.");
        }

        // Security: provider must be under ExoAddons\ namespace
        $provider = $manifest['provider'] ?? '';
        if (!str_starts_with($provider, 'ExoAddons\\')) {
            throw new \RuntimeException("Security: provider must be under ExoAddons\\ namespace.");
        }

        // Dependency check
        $missing = $this->checkDependencies($manifest['requires'] ?? []);
        if (!empty($missing)) {
            throw new \RuntimeException("Missing dependencies: " . implode(', ', $missing));
        }

        // Transactional install
        DB::transaction(function () use ($slug, $manifest) {
            DB::table('exo_addons')->insert([
                'slug'         => $slug,
                'name'         => $manifest['name'],
                'version'      => $manifest['version'] ?? '1.0.0',
                'provider'     => $manifest['provider'],
                'enabled'      => true,
                'installed_at' => now(),
            ]);

            // Run migrations
            $migrationsPath = "ExoAddons/{$this->slugToFolder($slug)}/Migrations";
            if (is_dir(base_path($migrationsPath))) {
                $exitCode = Artisan::call('migrate', [
                    '--path'  => $migrationsPath,
                    '--force' => true,
                ]);
                if ($exitCode !== 0) {
                    throw new \RuntimeException("Migration failed for addon: {$slug}");
                }
            }

            // Publish assets
            $this->publishAssets($manifest['provider']);
        });

        Log::info("[ExoAddons] Installed addon: {$slug}");
        Cache::forget(self::CACHE_KEY);

        return $this->buildAddonEntry($slug, $manifest);
    }

    /**
     * Toggle addon enabled/disabled.
     */
    public function toggle(string $slug): bool
    {
        $addon = DB::table('exo_addons')->where('slug', $slug)->first();
        if (!$addon) {
            throw new \RuntimeException("Addon '{$slug}' not found.");
        }

        $newState = !$addon->enabled;
        DB::table('exo_addons')->where('slug', $slug)->update(['enabled' => $newState]);
        Cache::forget(self::CACHE_KEY);

        Log::info("[ExoAddons] Addon '{$slug}' " . ($newState ? 'enabled' : 'disabled'));
        return $newState;
    }

    /**
     * Uninstall an addon.
     *
     * $hard = false → disable only (safe)
     * $hard = true  → rollback migrations + delete DB record + delete configs
     */
    public function uninstall(string $slug, bool $hard = false): void
    {
        $addon = DB::table('exo_addons')->where('slug', $slug)->first();

        if (!$addon) {
            throw new \RuntimeException("Addon '{$slug}' is not installed in the database.");
        }

        if (!$hard) {
            // Safe uninstall = just disable
            DB::table('exo_addons')->where('slug', $slug)->update(['enabled' => false]);
            Cache::forget(self::CACHE_KEY);
            return;
        }

        // Hard uninstall
        DB::transaction(function () use ($slug, $addon) {
            // Rollback migrations (reset all for this addon)
            $migrationsPath = "ExoAddons/{$this->slugToFolder($slug)}/Migrations";
            if (is_dir(base_path($migrationsPath))) {
                try {
                    Artisan::call('migrate:reset', [
                        '--path'  => $migrationsPath,
                        '--force' => true,
                    ]);
                } catch (\Throwable $e) {
                    Log::warning("[ExoAddons] Migration rollback warning for '{$slug}': " . $e->getMessage());
                }
            }

            // Delete DB records
            DB::table('exo_addon_configs')->where('addon_name', $slug)->delete();
            DB::table('exo_addons')->where('slug', $slug)->delete();
        });

        Cache::forget(self::CACHE_KEY);
        Log::info("[ExoAddons] Hard uninstalled addon: {$slug}");
    }

    /**
     * Update an addon to the version in addon.json.
     */
    public function update(string $slug): void
    {
        $manifest = $this->readManifest($slug);
        if (!$manifest) {
            throw new \RuntimeException("addon.json not found for: {$slug}");
        }

        DB::transaction(function () use ($slug, $manifest) {
            // Run new migrations
            $migrationsPath = "ExoAddons/{$this->slugToFolder($slug)}/Migrations";
            if (is_dir(base_path($migrationsPath))) {
                Artisan::call('migrate', [
                    '--path'  => $migrationsPath,
                    '--force' => true,
                ]);
            }

            DB::table('exo_addons')->where('slug', $slug)->update([
                'version' => $manifest['version'] ?? '1.0.0',
            ]);

            $this->publishAssets($manifest['provider']);
        });

        Cache::forget(self::CACHE_KEY);
        Log::info("[ExoAddons] Updated addon: {$slug} to v" . ($manifest['version'] ?? '?'));
    }

    /**
     * Load all enabled + valid addons into the Laravel container.
     * Called from DashboardServiceProvider::boot().
     */
    public function bootEnabledAddons(): void
    {
        try {
            $installed = DB::table('exo_addons')->where('enabled', true)->get();
        } catch (\Throwable) {
            return; // Table may not exist yet (first boot before migration)
        }

        // Topological sort by dependencies
        $sorted = $this->sortByDependencies($installed->toArray());

        foreach ($sorted as $addon) {
            $provider = $addon->provider;

            if (!class_exists($provider)) {
                Log::warning("[ExoAddons] Broken addon '{$addon->slug}': class '{$provider}' not found.");
                continue;
            }

            try {
                app()->register($provider);
            } catch (\Throwable $e) {
                Log::error("[ExoAddons] Failed to register '{$addon->slug}': " . $e->getMessage());
            }
        }
    }

    /* ================================================================
       PRIVATE HELPERS
       ================================================================ */

    /**
     * Build the full addon list: discovered folders merged with DB state.
     */
    protected function buildAddonList(): array
    {
        $addons = [];

        // Index DB records by slug
        $dbRecords = [];
        try {
            foreach (DB::table('exo_addons')->get() as $row) {
                $dbRecords[$row->slug] = $row;
            }
        } catch (\Throwable) {
            // DB table doesn't exist yet — that's fine
        }

        // Scan ExoAddons/ folders
        foreach (glob($this->addonsPath . '/*/addon.json') as $manifestPath) {
            $manifest = json_decode(file_get_contents($manifestPath), true);
            if (!isset($manifest['slug'])) continue;

            $slug  = $manifest['slug'];
            $entry = $this->buildAddonEntry($slug, $manifest, $dbRecords[$slug] ?? null);
            $addons[$slug] = $entry;

            unset($dbRecords[$slug]); // Remove from DB-only list
        }

        // Whatever's left in $dbRecords is in DB but folder is GONE → broken
        foreach ($dbRecords as $slug => $row) {
            $addons[$slug] = [
                'slug'        => $slug,
                'name'        => $row->name,
                'version'     => $row->version,
                'description' => '(Addon folder missing)',
                'provider'    => $row->provider,
                'requires'    => [],
                'state'       => 'broken',
                'enabled'     => $row->enabled,
                'installed'   => true,
                'installed_at'=> $row->installed_at,
                'health'      => false,
                'error'       => 'Addon folder or addon.json is missing.',
            ];
        }

        return $addons;
    }

    /**
     * Build a single addon entry with full state resolution.
     */
    protected function buildAddonEntry(string $slug, array $manifest, ?object $dbRow = null): array
    {
        $installed = $dbRow !== null;
        $provider  = $manifest['provider'] ?? '';
        $dbVersion = $dbRow?->version ?? null;
        $jsonVersion = $manifest['version'] ?? '1.0.0';

        // Resolve state
        if (!$installed) {
            $state = 'discovered';
        } elseif (!class_exists($provider)) {
            $state = 'broken';
        } elseif (!($dbRow?->enabled)) {
            $state = 'disabled';
        } elseif ($dbVersion && version_compare($jsonVersion, $dbVersion, '>')) {
            $state = 'update_available';
        } else {
            $state = 'installed';
        }

        // Health check
        $health = null;
        if ($installed && $state === 'installed' && ($manifest['health_check'] ?? false)) {
            try {
                $health = method_exists($provider, 'health') ? $provider::health() : true;
            } catch (\Throwable) {
                $health = false;
            }
        }

        return [
            'slug'          => $slug,
            'name'          => $manifest['name'] ?? $slug,
            'version'       => $jsonVersion,
            'db_version'    => $dbVersion,
            'description'   => $manifest['description'] ?? '',
            'author'        => $manifest['author'] ?? 'Unknown',
            'provider'      => $provider,
            'requires'      => $manifest['requires'] ?? [],
            'settings_view' => $manifest['settings_view'] ?? null,
            'state'         => $state,
            'enabled'       => (bool) ($dbRow?->enabled ?? false),
            'installed'     => $installed,
            'installed_at'  => $dbRow?->installed_at ?? null,
            'health'        => $health,
        ];
    }

    /**
     * Read and parse the addon.json for a given slug.
     * Looks for ExoAddons/{FolderName}/addon.json.
     */
    protected function readManifest(string $slug): ?array
    {
        // Try to find the folder (slug may differ from folder name)
        foreach (glob($this->addonsPath . '/*/addon.json') as $path) {
            $manifest = json_decode(file_get_contents($path), true);
            if (($manifest['slug'] ?? '') === $slug) {
                return $manifest;
            }
        }
        return null;
    }

    /**
     * Map slug → folder name. Falls back to ucfirst(slug).
     */
    protected function slugToFolder(string $slug): string
    {
        foreach (glob($this->addonsPath . '/*/addon.json') as $path) {
            $manifest = json_decode(file_get_contents($path), true);
            if (($manifest['slug'] ?? '') === $slug) {
                return basename(dirname($path));
            }
        }
        return ucfirst($slug);
    }

    /**
     * Check if all required addons are installed and enabled.
     * Returns array of missing slug names.
     */
    protected function checkDependencies(array $requires): array
    {
        if (empty($requires)) return [];

        $installed = DB::table('exo_addons')
            ->whereIn('slug', $requires)
            ->where('enabled', true)
            ->pluck('slug')
            ->toArray();

        return array_values(array_diff($requires, $installed));
    }

    /**
     * Topological sort — load dependencies before dependents.
     */
    protected function sortByDependencies(array $addons): array
    {
        if (empty($addons)) return $addons;

        // Build a slug-indexed map from the DB records
        $map = [];
        foreach ($addons as $row) {
            $manifest = $this->readManifest($row->slug);
            $map[$row->slug] = [
                'row'      => $row,
                'requires' => $manifest['requires'] ?? [],
            ];
        }

        $sorted  = [];
        $visited = [];

        $visit = function (string $slug) use (&$visit, &$map, &$sorted, &$visited) {
            if (isset($visited[$slug])) return;
            $visited[$slug] = true;
            foreach ($map[$slug]['requires'] ?? [] as $dep) {
                if (isset($map[$dep])) $visit($dep);
            }
            if (isset($map[$slug])) {
                $sorted[] = $map[$slug]['row'];
            }
        };

        foreach (array_keys($map) as $slug) {
            $visit($slug);
        }

        return $sorted;
    }

    /**
     * Publish assets for a provider (runs vendor:publish).
     */
    protected function publishAssets(string $provider): void
    {
        try {
            Artisan::call('vendor:publish', [
                '--provider' => $provider,
                '--tag'      => 'exoaddons-' . strtolower(class_basename($provider)) . '-assets',
                '--force'    => true,
            ]);
        } catch (\Throwable $e) {
            Log::info("[ExoAddons] No assets to publish for {$provider}: " . $e->getMessage());
        }
    }
}
