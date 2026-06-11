<?php

namespace ExoAddons\Dashboard;

use ExoAddons\Dashboard\Services\ExoAddonRegistry;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class DashboardServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/Config/dashboard.php', 'exodash');

        // Bind ExoAddonRegistry as a singleton — available everywhere as app(ExoAddonRegistry::class)
        $this->app->singleton(ExoAddonRegistry::class, fn() => new ExoAddonRegistry());
    }

    public function boot(): void
    {
        if (!config('exodash.enabled', true)) {
            return;
        }

        $this->loadMigrationsFrom(__DIR__ . '/Migrations');
        $this->loadRoutesFrom(__DIR__ . '/Routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/Views', 'exodash');

        $this->publishes([
            __DIR__ . '/Assets' => public_path('ExoAddons/Dashboard'),
        ], 'exoaddons-dashboard-assets');

        $this->ensureAssetsPublished();

        // Apply stored configs from DB
        $this->applyStoredConfigs();

        // Dynamically boot all enabled addons
        $this->bootAddons();
    }

    /**
     * Load all enabled addons from the exo_addons DB table dynamically.
     */
    protected function bootAddons(): void
    {
        /** @var ExoAddonRegistry $registry */
        $registry = $this->app->make(ExoAddonRegistry::class);
        $registry->bootEnabledAddons();
    }

    protected function ensureAssetsPublished(): void
    {
        $targetDir = public_path('ExoAddons/Dashboard/css');
        if (!is_dir($targetDir)) {
            $sourceDir = __DIR__ . '/Assets/css';
            if (is_dir($sourceDir)) {
                mkdir($targetDir, 0755, true);
                foreach (glob($sourceDir . '/*.css') as $file) {
                    copy($file, $targetDir . '/' . basename($file));
                }
            }
        }
    }

    protected function applyStoredConfigs(): void
    {
        try {
            $configs = DB::table('exo_addon_configs')->get();
            foreach ($configs as $cfg) {
                $val = $cfg->config_value;

                // Cast JSON arrays/objects
                if (is_string($val) && (str_starts_with($val, '[') || str_starts_with($val, '{'))) {
                    $decoded = json_decode($val, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $val = $decoded;
                    }
                }
                // Cast boolean strings
                elseif ($val === '1' || $val === '0') {
                    $val = (bool) $val;
                }
                // Cast numeric strings
                elseif (is_numeric($val)) {
                    $val = $val + 0; // int or float
                }

                config([$cfg->config_key => $val]);
            }
        } catch (\Exception) {
            // Table may not exist yet on first boot
        }
    }
}
