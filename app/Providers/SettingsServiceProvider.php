<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
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
            $settings = Setting::cached()->toArray();

            $this->applyAppSettings($settings);
            $this->applyMailSettings($settings);
            $this->applyCaptchaSettings($settings);
            $this->applyVoteSettings($settings);
            $this->applyJsonSettings($settings, 'donate', 'donate');
            $this->applyJsonSettings($settings, 'widgets', 'widgets');
            $this->applyJsonSettings($settings, 'ranking', 'ranking');
            $this->applyJsonSettings($settings, 'referral', 'global.referral');
            $this->applyJsonSettings($settings, 'tickets', 'global.tickets');
            $this->applyJsonSettings($settings, 'sliders', 'global.sliders');
            $this->applyJsonSettings($settings, 'footer', 'global.footer');

        } catch (\Exception $e) {
            // DB not ready (e.g. during migrations), silently skip
        }
    }

    /**
     * Apply general app settings.
     */
    private function applyAppSettings(array $settings): void
    {
        $mergedSettings = array_replace_recursive(config('settings', []), $settings);
        Config::set('settings', $mergedSettings);

        Config::set('app.name', $mergedSettings['site_title'] ?? config('app.name'));
        Config::set('app.url', $mergedSettings['site_url']   ?? config('app.url'));

        // Apply donate settings to override config defaults
        $donateConfig = config('donate', []);
        if (!empty($settings['donate']) && is_array($donateConfig)) {
            foreach ($donateConfig as $gateway => $gatewayConfig) {
                if (isset($gatewayConfig['fields']) && is_array($gatewayConfig['fields'])) {
                    foreach ($gatewayConfig['fields'] as $fieldKey => $fieldConfig) {
                        $currentValue = $settings['donate'][$gateway][$fieldKey] ?? null;
                        $configValue = $gatewayConfig['fields'][$fieldKey]['placeholder'] ?? null;
                        Config::set("donate.{$gateway}.{$fieldKey}", $currentValue ?? $configValue);
                    }
                }
            }
        }

        if (!empty($mergedSettings['timezone'])) {
            date_default_timezone_set($mergedSettings['timezone']);
        }

        if (!empty($mergedSettings['theme'])) {
            $themePath = resource_path('themes/' . $mergedSettings['theme'] . '/views');
            if (is_dir($themePath)) {
                $this->app['view']->getFinder()->prependLocation($themePath);
            }
        }
    }

    /**
     * Apply vote config — makes config('vote.*') available at runtime.
     */
    private function applyVoteSettings(array $settings): void
    {
        if (empty($settings['vote'])) {
            return;
        }

        $vote = json_decode($settings['vote'], true);

        if (!is_array($vote) || empty($vote)) {
            return;
        }

        Config::set('vote', array_replace_recursive(config('vote', []), $vote));
    }

    /**
     * Apply captcha settings from the stored JSON config.
     */
    private function applyCaptchaSettings(array $settings): void
    {
        if (empty($settings['captcha'])) {
            return;
        }

        $captcha = json_decode($settings['captcha'], true);

        if (!is_array($captcha) || empty($captcha)) {
            return;
        }

        Config::set('captcha.enabled',          $captcha['enabled']             ?? config('captcha.enabled'));
        Config::set('captcha.secret',           $captcha['secret']              ?? config('captcha.secret'));
        Config::set('captcha.sitekey',          $captcha['sitekey']             ?? config('captcha.sitekey'));
        Config::set('captcha.options.timeout',  $captcha['options']['timeout']  ?? 30);
    }

    /**
     * Apply mail settings from the stored JSON config.
     */
    private function applyMailSettings(array $settings): void
    {
        if (empty($settings['mail'])) {
            return;
        }

        $mail = json_decode($settings['mail'], true);

        if (!is_array($mail) || empty($mail)) {
            return;
        }

        $nullable = fn($v) => (!isset($v) || $v === 'null' || $v === '') ? null : $v;

        Config::set('mail.default',               $mail['MAIL_MAILER']       ?? config('mail.default'));
        Config::set('mail.mailers.smtp.host',      $mail['MAIL_HOST']         ?? config('mail.mailers.smtp.host'));
        Config::set('mail.mailers.smtp.port',      $mail['MAIL_PORT']         ?? config('mail.mailers.smtp.port'));
        Config::set('mail.mailers.smtp.encryption',$nullable($mail['MAIL_SCHEME']   ?? config('mail.mailers.smtp.encryption')));
        Config::set('mail.mailers.smtp.username',  $nullable($mail['MAIL_USERNAME'] ?? config('mail.mailers.smtp.username')));
        Config::set('mail.mailers.smtp.password',  $nullable($mail['MAIL_PASSWORD'] ?? config('mail.mailers.smtp.password')));
        Config::set('mail.from.address',           $mail['MAIL_FROM_ADDRESS'] ?? config('mail.from.address'));
        Config::set('mail.from.name',              $mail['MAIL_FROM_NAME']    ?? config('mail.from.name'));
    }

    private function applyJsonSettings(array $settings, string $settingKey, string $configKey): void
    {
        if (empty($settings[$settingKey])) {
            return;
        }

        $decoded = json_decode($settings[$settingKey], true);
        if (is_array($decoded)) {
            Config::set($configKey, array_replace_recursive(config($configKey, []), $decoded));
        }
    }
}
