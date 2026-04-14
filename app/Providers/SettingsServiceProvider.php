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
            $settings = Setting::pluck('value', 'key')->toArray();

            $this->applyAppSettings($settings);
            $this->applyMailSettings($settings);
            $this->applyCaptchaSettings($settings);
            $this->applyVoteSettings($settings);

        } catch (\Exception $e) {
            // DB not ready (e.g. during migrations), silently skip
        }
    }

    /**
     * Apply general app settings.
     */
    private function applyAppSettings(array $settings): void
    {
        Config::set('settings', $settings);

        Config::set('app.name', $settings['site_title'] ?? config('app.name'));
        Config::set('app.url',  $settings['site_url']   ?? config('app.url'));

        if (!empty($settings['timezone'])) {
            date_default_timezone_set($settings['timezone']);
        }

        if (!empty($settings['theme'])) {
            $themePath = resource_path('themes/' . $settings['theme'] . '/views');
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

        Config::set('vote', $vote);
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
        Config::set('mail.mailers.smtp.encryption',$nullable($mail['MAIL_SCHEME']   ?? null));
        Config::set('mail.mailers.smtp.username',  $nullable($mail['MAIL_USERNAME'] ?? null));
        Config::set('mail.mailers.smtp.password',  $nullable($mail['MAIL_PASSWORD'] ?? null));
        Config::set('mail.from.address',           $mail['MAIL_FROM_ADDRESS'] ?? config('mail.from.address'));
        Config::set('mail.from.name',              $mail['MAIL_FROM_NAME']    ?? config('app.name'));
    }
}
