<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            return;
        }

        try {
            Config::set('settings', Setting::pluck('value', 'key')->toArray());
            Config::set('app.name', config('settings.site_title', 'iSRO CMS v2'));
            Config::set('app.url', config('settings.site_url', 'http://localhost'));

            date_default_timezone_set(config('settings.timezone', config('app.timezone')));
            View::getFinder()->prependLocation(resource_path("themes/".config('settings.theme', 'default').'/views'));

        } catch (\Exception $e) {
            // DB not ready, ignore
        }
    }
}
