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
        $settings = Setting::cached();

        return view('admin.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        foreach ($request->except('_token') as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => is_array($value) ? json_encode($value) : $value]);
        }

        cache()->forget('settings');

        return back()->with('success', 'Settings updated!');
    }

    public function clearCache()
    {
        abort_unless(auth()->user()?->role->is_admin, 403);

        Artisan::call('optimize:clear');
        cache()->forget('settings');

        return back()->with('success', 'All caches have been cleared!');
    }
}
