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
        $data = Setting::cached();

        return view('admin.settings.index', compact('data'));
    }

    public function general()
    {
        $data = Setting::cached();

        return view('admin.settings.general', compact('data'));
    }

    public function widgets()
    {
        $data = Setting::cached();

        return view('admin.settings.widgets', compact('data'));
    }

    public function donate()
    {
        $data = Setting::cached();

        return view('admin.settings.donate', compact('data'));
    }

    public function ranking()
    {
        $data = Setting::cached();

        return view('admin.settings.ranking', compact('data'));
    }

    public function update(Request $request)
    {
        abort_unless(auth()->user()?->role->is_admin, 403);

        foreach ($request->except('_token') as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => is_array($value) ? json_encode($value) : $value]);
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
}
