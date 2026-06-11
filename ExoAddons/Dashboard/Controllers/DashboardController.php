<?php

namespace ExoAddons\Dashboard\Controllers;

use App\Http\Controllers\Controller;
use ExoAddons\Dashboard\Services\ExoAddonRegistry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class DashboardController extends Controller
{
    public function __construct(protected ExoAddonRegistry $registry) {}

    /* ================================================================
       AUTH
       ================================================================ */

    public function login()
    {
        if ($this->isAdmin()) {
            return redirect()->route('exodash.addons');
        }
        return view('exodash::login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt(['username' => $credentials['username'], 'password' => $credentials['password']])) {
            if (!$this->isAdmin()) {
                Auth::logout();
                return back()->withErrors(['username' => 'Access denied. Admin role required.']);
            }
            $request->session()->regenerate();
            return redirect()->route('exodash.addons');
        }

        // Fallback: legacy MD5 password (sro-cms)
        $user = \App\Models\User::where('username', $credentials['username'])
                                ->where('password', md5($credentials['password']))
                                ->first();
        if ($user) {
            Auth::login($user, false);
            if (!$this->isAdmin()) {
                Auth::logout();
                return back()->withErrors(['username' => 'Access denied. Admin role required.']);
            }
            $request->session()->regenerate();
            return redirect()->route('exodash.addons');
        }

        return back()->withErrors(['username' => 'Invalid username or password.']);
    }

    /* ================================================================
       ADDON LIST
       ================================================================ */

    public function addons()
    {
        $addons = $this->registry->scanAll();
        return view('exodash::addons.index', compact('addons'));
    }

    /* ================================================================
       ADDON MANAGE (settings page)
       ================================================================ */

    public function manage(string $slug)
    {
        $addons = $this->registry->scanAll();

        if (!isset($addons[$slug])) {
            abort(404, 'Addon not found.');
        }

        $addon = $addons[$slug];

        // Load DB config values for this addon
        $configs = DB::table('exo_addon_configs')
            ->where('addon_name', $slug)
            ->pluck('config_value', 'config_key')
            ->toArray();

        // Use addon's own settings view if defined, else fall back to generic
        $view = $addon['settings_view'] ?? "exodash::addons.generic";

        return view($view, compact('addon', 'configs'));
    }

    /* ================================================================
       SAVE CONFIG
       ================================================================ */

    public function saveConfig(Request $request, string $slug)
    {
        $addons = $this->registry->scanAll();
        if (!isset($addons[$slug])) abort(404);

        foreach ($request->except(['_token', '_method']) as $key => $value) {
            DB::table('exo_addon_configs')->updateOrInsert(
                ['addon_name' => $slug, 'config_key' => $key],
                ['config_value' => $value, 'updated_at' => now()]
            );
            config([$key => $value]);
        }

        return back()->with('success', 'Settings saved!');
    }

    /* ================================================================
       SETUP (install)
       ================================================================ */

    public function setup(Request $request, string $slug)
    {
        try {
            $this->registry->install($slug);
            return back()->with('success', "Addon installed successfully! Please refresh the page.");
        } catch (\Throwable $e) {
            return back()->with('error', "Installation failed: " . $e->getMessage());
        }
    }

    /* ================================================================
       TOGGLE (enable / disable)
       ================================================================ */

    public function toggle(Request $request, string $slug)
    {
        try {
            $enabled = $this->registry->toggle($slug);
            $msg = $enabled ? "Addon enabled." : "Addon disabled.";
            return back()->with('success', $msg . " Please refresh the page to apply changes.");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /* ================================================================
       UNINSTALL
       ================================================================ */

    public function uninstall(Request $request, string $slug)
    {
        $hard = $request->boolean('hard', false);

        try {
            $this->registry->uninstall($slug, $hard);
            $msg = $hard ? "Addon hard uninstalled (migrations rolled back, data deleted)." : "Addon disabled safely.";
            return back()->with('success', $msg);
        } catch (\Throwable $e) {
            return back()->with('error', "Uninstall failed: " . $e->getMessage());
        }
    }

    /* ================================================================
       UPDATE
       ================================================================ */

    public function update(Request $request, string $slug)
    {
        try {
            $this->registry->update($slug);
            return back()->with('success', "Addon updated successfully!");
        } catch (\Throwable $e) {
            return back()->with('error', "Update failed: " . $e->getMessage());
        }
    }

    /* ================================================================
       HELPERS
       ================================================================ */

    protected function isAdmin(): bool
    {
        return Auth::check() && Auth::user()?->role?->is_admin === true;
    }
}
