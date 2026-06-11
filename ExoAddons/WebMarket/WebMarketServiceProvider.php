<?php

namespace ExoAddons\WebMarket;

use ExoAddons\WebMarket\Commands\CheckFilterStatus;
use ExoAddons\WebMarket\Services\MarketItemService;
use ExoAddons\WebMarket\Services\VanguardFilterService;
use Illuminate\Support\ServiceProvider;

class WebMarketServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/Config/webmarket.php', 'webmarket');

        $this->app->singleton(VanguardFilterService::class, fn () => new VanguardFilterService());

        $this->app->singleton(MarketItemService::class, function ($app) {
            return new MarketItemService($app->make(VanguardFilterService::class));
        });
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/Migrations');
        $this->loadRoutesFrom(__DIR__ . '/Routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/Views', 'web-market');

        $this->publishes([
            __DIR__ . '/Assets' => public_path('ExoAddons/WebMarket'),
        ], 'exoaddons-webmarket-assets');

        $this->ensureAssetsPublished();

        if ($this->app->runningInConsole()) {
            $this->commands([CheckFilterStatus::class]);
        }
    }

    protected function ensureAssetsPublished(): void
    {
        $targetDir = public_path('ExoAddons/WebMarket/css');
        if (!is_dir($targetDir) && is_dir(__DIR__ . '/Assets/css')) {
            mkdir($targetDir, 0755, true);
            foreach (glob(__DIR__ . '/Assets/css/*.css') as $file) {
                copy($file, $targetDir . '/' . basename($file));
            }
        }
    }

    /** Health check — called by ExoAddonRegistry */
    public static function health(): bool
    {
        try {
            \Illuminate\Support\Facades\DB::table('exo_market_listings')->count();
            return true;
        } catch (\Throwable) {
            return false;
        }
    }
}
