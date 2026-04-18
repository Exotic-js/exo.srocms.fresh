<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SettingController extends Controller
{
    public function index()
    {
        return view('admin.settings.index', $this->settingsViewContext());
    }

    public function general()
    {
        return view('admin.settings.general', $this->settingsViewContext());
    }

    public function widgets()
    {
        $context = $this->settingsViewContext();
        $context['limitWidgets'] = [
            ['id' => 'globals_history', 'label' => 'Globals History'],
            ['id' => 'unique_history',  'label' => 'Unique History'],
            ['id' => 'top_player',      'label' => 'Top Player'],
            ['id' => 'top_guild',       'label' => 'Top Guild'],
            ['id' => 'sox_plus',        'label' => 'SoX Plus'],
            ['id' => 'sox_drop',        'label' => 'SoX Drop'],
            ['id' => 'pvp_kills',       'label' => 'PvP Kills'],
            ['id' => 'job_kills',       'label' => 'Job Kills'],
        ];

        $widgets = $context['widgets'];

        $context['discord'] = $widgets['discord'] ?? ['enabled' => false, 'server_id' => '', 'channel_id' => '', 'theme' => 'dark'];
        $context['eventSchedule'] = [
            'enabled' => $widgets['event_schedule']['enabled'] ?? false,
            'names' => $widgets['event_schedule']['names'] ?? [],
            'custom' => $widgets['event_schedule']['custom'] ?? [],
        ];
        $context['fortressWar'] = [
            'enabled' => $widgets['fortress_war']['enabled'] ?? false,
            'names' => $widgets['fortress_war']['names'] ?? [],
        ];
        $context['serverInfo'] = [
            'enabled' => $widgets['server_info']['enabled'] ?? false,
            'data' => $widgets['server_info']['data'] ?? [],
        ];
        $context['customWidgets'] = $widgets['custom'] ?? [];

        return view('admin.settings.widgets', $context);
    }

    public function donate()
    {
        $context = $this->settingsViewContext();
        $context['gateways'] = $context['donate'];

        return view('admin.settings.donate', $context);
    }

    public function ranking()
    {
        return view('admin.settings.ranking', $this->settingsViewContext());
    }

    public function update(Request $request)
    {
        abort_unless(auth()->user()?->role->is_admin, 403);

        $donateGateways = array_keys(config('donate', []));
        $widgetKeys = array_keys(config('widgets', []));

        $donate = $this->getJsonSetting('donate', config('donate', []));
        $widgets = $this->getJsonSetting('widgets', config('widgets', []));

        $toSave = [];
        foreach ($request->except('_token') as $key => $value) {
            if (in_array($key, $donateGateways, true)) {
                $decoded = json_decode($value, true);
                if (is_array($decoded)) {
                    $donate[$key] = $decoded;
                }
                continue;
            }

            if (in_array($key, $widgetKeys, true)) {
                $decoded = json_decode($value, true);
                if (is_array($decoded)) {
                    $widgets[$key] = $decoded;
                }
                continue;
            }

            $toSave[$key] = is_array($value) ? json_encode($value) : $value;
        }

        $toSave['donate'] = json_encode($donate);
        $toSave['widgets'] = json_encode($widgets);

        foreach ($toSave as $key => $value) {
            Setting::set($key, $value);
        }

        cache()->forget('settings');
        cache()->forget('settings_all');

        return back()->with('success', 'Settings updated!');
    }

    public function clearCache()
    {
        abort_unless(auth()->user()?->role->is_admin, 403);

        Artisan::call('optimize:clear');
        cache()->forget('settings');
        cache()->forget('settings_all');

        return back()->with('success', 'All caches have been cleared!');
    }

    private function settingsViewContext(): array
    {
        $data = Setting::cached()->toArray();

        return [
            'data' => $data,
            'settings' => $this->mergeScalarSettings($data, config('settings', [])),
            'themes' => $this->loadThemes(),
            'languages' => config('global.languages', []),
            'timezones' =>
                method_exists('\DateTimeZone', 'listIdentifiers')
                    ? \DateTimeZone::listIdentifiers()
                    : [],
            'appUrl' => config('app.url'),
            'appName' => config('app.name'),
            'referral' => $this->mergeJsonSetting($data, 'referral', config('global.referral', [])),
            'tickets' => $this->mergeJsonSetting($data, 'tickets', config('global.tickets', [])),
            'sliders' => $this->mergeJsonSetting($data, 'sliders', config('global.sliders', [])),
            'footer' => $this->mergeJsonSetting($data, 'footer', config('global.footer', [])),
            'mail' => $this->mergeJsonSetting($data, 'mail', []),
            'captcha' => $this->mergeJsonSetting($data, 'captcha', [
                'secret' => config('captcha.secret'),
                'sitekey' => config('captcha.sitekey'),
                'options' => [
                    'timeout' => config('captcha.options.timeout'),
                ],
            ]),
            'vote' => $this->mergeJsonSetting($data, 'vote', config('vote', [])),
            'donate' => $this->mergeJsonSetting($data, 'donate', config('donate', [])),
            'widgets' => $this->mergeJsonSetting($data, 'widgets', config('widgets', [])),
            'ranking' => $this->mergeJsonSetting($data, 'ranking', config('ranking', [])),
            'cache' => $this->mergeJsonSetting($data, 'cache', config('global.cache', [])),
        ];
    }

    private function mergeScalarSettings(array $data, array $defaults): array
    {
        foreach ($defaults as $key => $value) {
            if (array_key_exists($key, $data)) {
                $defaults[$key] = $data[$key];
            }
        }

        return $this->normalizeBooleans($defaults, [
            'disable_register',
            'register_confirm',
            'duplicate_email',
            'agree_terms',
        ]);
    }

    private function mergeJsonSetting(array $data, string $key, array $defaults = []): array
    {
        $value = $data[$key] ?? null;
        if (!is_array($value)) {
            $value = json_decode($value ?? '', true);
        }

        if (!is_array($value)) {
            $value = [];
        }

        return array_replace_recursive($defaults, $value);
    }

    private function normalizeBooleans(array $settings, array $keys): array
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $settings)) {
                $settings[$key] = filter_var($settings[$key], FILTER_VALIDATE_BOOLEAN);
            }
        }

        return $settings;
    }

    private function getJsonSetting(string $key, array $default = []): array
    {
        $value = Setting::get($key, json_encode($default));
        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : $default;
    }

    private function loadThemes(): array
    {
        $themePath = resource_path('themes');
        if (!is_dir($themePath)) {
            return [];
        }

        $themes = [];
        foreach (scandir($themePath) as $dir) {
            if ($dir === '.' || $dir === '..') {
                continue;
            }

            if (is_dir($themePath . '/' . $dir)) {
                $themes[] = $dir;
            }
        }

        return $themes;
    }
}
