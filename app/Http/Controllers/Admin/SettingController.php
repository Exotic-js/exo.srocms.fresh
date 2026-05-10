<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SettingController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        return view('admin.settings.index', $this->viewContext());
    }

    public function general(): \Illuminate\View\View
    {
        return view('admin.settings.general', $this->viewContext());
    }

    public function widgets(): \Illuminate\View\View
    {
        $context = $this->viewContext();
        $widgets = $context['widgets'];

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

        $context['discord']       = $widgets['discord']        ?? ['enabled' => false, 'server_id' => '', 'channel_id' => '', 'theme' => 'dark'];
        $context['eventSchedule'] = $widgets['event_schedule'] ?? ['enabled' => false, 'names' => [], 'custom' => []];
        $context['fortressWar']   = $widgets['fortress_war']   ?? ['enabled' => false, 'names' => []];
        $context['serverInfo']    = $widgets['server_info']    ?? ['enabled' => false, 'data' => []];
        $context['customWidgets'] = $widgets['custom']         ?? [];

        return view('admin.settings.widgets', $context);
    }

    public function donate(): \Illuminate\View\View
    {
        $context = $this->viewContext();
        $context['gateways'] = config('donate', []);

        return view('admin.settings.donate', $context);
    }

    public function ranking(): \Illuminate\View\View
    {
        return view('admin.settings.ranking', $this->viewContext());
    }

    public function update(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()?->role?->is_admin, 403);

        $donateKeys = array_keys(config('donate', []));
        $widgetKeys = array_merge(
            array_keys(config('widgets', [])),
            ['event_schedule', 'fortress_war', 'server_info', 'custom', 'discord']
        );
        $jsonKeys = ['donate', 'widgets', 'ranking', 'history', 'referral', 'tickets', 'sliders', 'footer', 'mail', 'captcha', 'vote', 'cache'];

        // Load existing blobs so partial saves don't wipe other sub-keys
        $donate  = $this->getJsonSetting('donate',  config('donate',  []));
        $widgets = $this->getJsonSetting('widgets', config('widgets', []));
        $history = $this->getJsonSetting('history', config('global.history', []));

        $toSave = [];

        foreach ($request->except('_token') as $key => $value) {
            if (in_array($key, $donateKeys, true)) {
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

            if ($key === 'history') {
                $decoded = json_decode($value, true);
                if (is_array($decoded)) {
                    $history = $decoded;
                }
                continue;
            }

            // Scalar fields (General tab direct name= attributes) and other JSON blobs
            $toSave[$key] = is_array($value) ? json_encode($value) : $value;
        }

        $toSave['donate']  = json_encode($donate);
        $toSave['widgets'] = json_encode($widgets);
        $toSave['history'] = json_encode($history);

        Setting::saveMany($toSave);
        Setting::flushCache();

        return back()->with('success', __('Settings updated successfully.'));
    }

    public function clearCache(): RedirectResponse
    {
        abort_unless(auth()->user()?->role?->is_admin, 403);

        Artisan::call('optimize:clear');
        Setting::flushCache();

        return back()->with('success', __('All caches cleared successfully.'));
    }

    /*
    |--------------------------------------------------------------------------
    | View Context
    |--------------------------------------------------------------------------
    */

    private function viewContext(): array
    {
        $data = Setting::cached()->toArray();

        return [
            'settings'  => $this->mergeScalarSettings($data, config('global.general', [])),
            'themes'    => $this->loadThemes(),
            'languages' => config('global.languages', []),
            'timezones' => \DateTimeZone::listIdentifiers(),
            'appUrl'    => config('app.url'),
            'appName'   => config('app.name'),

            'referral' => $this->mergeJsonSetting($data, 'referral', config('global.referral', [])),
            'tickets'  => $this->mergeJsonSetting($data, 'tickets',  config('global.tickets',  [])),
            'sliders'  => $this->mergeJsonSetting($data, 'sliders',  config('global.sliders',  [])),
            'footer'   => $this->mergeJsonSetting($data, 'footer',   config('global.footer',   [])),
            'mail'     => $this->mergeJsonSetting($data, 'mail',     []),
            'captcha'  => $this->mergeJsonSetting($data, 'captcha',  config('captcha',          [])),
            'vote'     => $this->mergeJsonSetting($data, 'vote',     config('vote',             [])),
            'widgets'  => $this->mergeJsonSetting($data, 'widgets',  config('widgets',          [])),
            'ranking'  => $this->mergeJsonSetting($data, 'ranking',  config('ranking',          [])),
            'history'  => $this->mergeJsonSetting($data, 'history',  config('global.history',   [])),
            'cache'    => $this->mergeJsonSetting($data, 'cache',    config('global.cache',     [])),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Merge scalar settings stored as individual DB rows (General tab plain name= fields).
     */
    private function mergeScalarSettings(array $data, array $defaults): array
    {
        foreach (array_keys($defaults) as $key) {
            if (array_key_exists($key, $data)) {
                $defaults[$key] = $data[$key];
            }
        }

        return $defaults;
    }

    /**
     * Decode a JSON blob from the cached settings array.
     *
     * If the key has never been saved to the DB we fall back to $defaults.
     * Once a value exists in the DB we trust it completely — no merging —
     * so that rows deleted by the user are not re-injected from config defaults.
     */
    private function mergeJsonSetting(array $data, string $key, array $defaults = []): array
    {
        $raw     = $data[$key] ?? null;
        $decoded = is_string($raw) ? json_decode($raw, true) : $raw;

        return is_array($decoded) ? $decoded : $defaults;
    }

    /**
     * Load a JSON blob directly from the DB/cache.
     */
    private function getJsonSetting(string $key, array $default = []): array
    {
        $value   = Setting::get($key, json_encode($default));
        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : $default;
    }

    private function loadThemes(): array
    {
        $path = resource_path('themes');

        if (! is_dir($path)) {
            return [];
        }

        return collect(scandir($path))
            ->reject(fn ($item) => in_array($item, ['.', '..']) || ! is_dir($path . '/' . $item))
            ->values()
            ->all();
    }
}
