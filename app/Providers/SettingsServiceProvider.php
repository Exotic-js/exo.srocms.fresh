<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            return;
        }

        try {
            $settings = Setting::cached()->toArray();

            $this->applyGeneralSettings($settings);
            $this->applyMailSettings($settings);
            $this->applyCaptchaSettings($settings);
            $this->applyVoteSettings($settings);
            $this->applyJsonConfig($settings, 'donate',  'donate');
            $this->applyJsonConfig($settings, 'widgets', 'widgets');
            $this->applyJsonConfig($settings, 'ranking', 'ranking');
            $this->applyJsonConfig($settings, 'referral', 'global.referral');
            $this->applyJsonConfig($settings, 'tickets',  'global.tickets');
            $this->applyJsonConfig($settings, 'sliders',  'global.sliders');
            $this->applyJsonConfig($settings, 'footer',   'global.footer');
            $this->applyJsonConfig($settings, 'cache',    'global.cache');
        } catch (\Throwable) {
            // Database not ready (e.g. during migrations) — silently skip.
        }
    }

    /*
    |--------------------------------------------------------------------------
    | General / Scalar Settings
    |--------------------------------------------------------------------------
    */

    private function applyGeneralSettings(array $settings): void
    {
        // Scalar fields are stored as individual DB rows (site_title, site_url, etc.)
        // Merge them on top of the config defaults.
        $general = config('global.general', []);

        foreach (array_keys($general) as $key) {
            if (array_key_exists($key, $settings)) {
                $general[$key] = $settings[$key];
            }
        }

        Config::set('settings', $general);
        Config::set('global.general', $general);
        Config::set('app.name', $general['site_title'] ?? config('app.name'));
        Config::set('app.url',  $general['site_url']   ?? config('app.url'));

        if (! empty($general['timezone'])) {
            date_default_timezone_set($general['timezone']);
        }

        if (! empty($general['theme'])) {
            $themePath = resource_path('themes/' . $general['theme'] . '/views');
            if (is_dir($themePath)) {
                $this->app['view']->getFinder()->prependLocation($themePath);
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Mail
    |--------------------------------------------------------------------------
    */

    private function applyMailSettings(array $settings): void
    {
        $mail = $this->decodeJson($settings['mail'] ?? null);

        if (empty($mail)) {
            return;
        }

        $nullable = static fn ($v) => ($v === '' || $v === 'null' || $v === null) ? null : $v;

        Config::set('mail.default',                $mail['MAIL_MAILER']       ?? config('mail.default'));
        Config::set('mail.mailers.smtp.host',      $mail['MAIL_HOST']         ?? config('mail.mailers.smtp.host'));
        Config::set('mail.mailers.smtp.port',      $mail['MAIL_PORT']         ?? config('mail.mailers.smtp.port'));
        Config::set('mail.mailers.smtp.encryption',$nullable($mail['MAIL_SCHEME']   ?? config('mail.mailers.smtp.encryption')));
        Config::set('mail.mailers.smtp.username',  $nullable($mail['MAIL_USERNAME'] ?? config('mail.mailers.smtp.username')));
        Config::set('mail.mailers.smtp.password',  $nullable($mail['MAIL_PASSWORD'] ?? config('mail.mailers.smtp.password')));
        Config::set('mail.from.address',           $mail['MAIL_FROM_ADDRESS'] ?? config('mail.from.address'));
        Config::set('mail.from.name',              $mail['MAIL_FROM_NAME']    ?? config('mail.from.name'));
    }

    /*
    |--------------------------------------------------------------------------
    | Captcha
    |--------------------------------------------------------------------------
    */

    private function applyCaptchaSettings(array $settings): void
    {
        $captcha = $this->decodeJson($settings['captcha'] ?? null);

        if (empty($captcha)) {
            return;
        }

        Config::set('captcha.enabled',         $captcha['enabled']            ?? config('captcha.enabled'));
        Config::set('captcha.secret',          $captcha['secret']             ?? config('captcha.secret'));
        Config::set('captcha.sitekey',         $captcha['sitekey']            ?? config('captcha.sitekey'));
        Config::set('captcha.options.timeout', $captcha['options']['timeout'] ?? 30);
    }

    /*
    |--------------------------------------------------------------------------
    | Vote
    |--------------------------------------------------------------------------
    */

    private function applyVoteSettings(array $settings): void
    {
        $vote = $this->decodeJson($settings['vote'] ?? null);

        if (empty($vote)) {
            return;
        }

        Config::set('vote', $vote);
    }

    /*
    |--------------------------------------------------------------------------
    | Generic JSON → Config
    |--------------------------------------------------------------------------
    */

    private function applyJsonConfig(array $settings, string $settingKey, string $configKey): void
    {
        $decoded = $this->decodeJson($settings[$settingKey] ?? null);

        // Trust the saved value completely — do not merge back config defaults.
        // Merging would re-inject items the user intentionally deleted.
        if (! empty($decoded)) {
            Config::set($configKey, $decoded);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    private function decodeJson(mixed $value): array
    {
        if (empty($value)) {
            return [];
        }

        $decoded = is_string($value) ? json_decode($value, true) : $value;

        return is_array($decoded) ? $decoded : [];
    }
}
