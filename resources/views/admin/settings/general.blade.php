
<form method="POST" action="{{ route('admin.settings.update') }}">
    @csrf

    <div class="row mb-3">
        <label for="site_title" class="col-md-2 col-form-label text-md-end">{{ __('Site Title') }}</label>

        <div class="col-md-10">
            <input id="site_title" type="text" class="form-control @error('site_title') is-invalid @enderror" name="site_title" value="{{ $data['site_title'] ?? '' }}" placeholder="" required>

            @error('site_title')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>

    <div class="row mb-3">
        <label for="site_desc" class="col-md-2 col-form-label text-md-end">{{ __('Site Description') }}</label>

        <div class="col-md-10">
            <input id="site_desc" type="text" class="form-control @error('site_desc') is-invalid @enderror" name="site_desc" value="{{ $data['site_desc'] ?? '' }}" placeholder="" required>

            @error('site_desc')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>

    <div class="row mb-3">
        <label for="site_url" class="col-md-2 col-form-label text-md-end">{{ __('Site URL') }}</label>

        <div class="col-md-10">
            <input id="site_url" type="text" class="form-control @error('site_url') is-invalid @enderror" name="site_url" value="{{ $data['site_url'] ?? '' }}" placeholder="" required>

            @error('site_url')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>

    <div class="row mb-3">
        <label for="site_favicon" class="col-md-2 col-form-label text-md-end">{{ __('Site Favicon') }}</label>

        <div class="col-md-10">
            <input id="site_favicon" type="text" class="form-control @error('site_favicon') is-invalid @enderror" name="site_favicon" value="{{ $data['site_favicon'] ?? '' }}" placeholder="" required>

            @error('site_favicon')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>

    <div class="row mb-3">
        <label for="site_logo" class="col-md-2 col-form-label text-md-end">{{ __('Site Logo') }}</label>

        <div class="col-md-10">
            <input id="site_logo" type="text" class="form-control @error('site_logo') is-invalid @enderror" name="site_logo" value="{{ $data['site_logo'] ?? '' }}" placeholder="" required>

            @error('site_logo')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>

    <div class="row mb-3">
        <label for="hero_background" class="col-md-2 col-form-label text-md-end">{{ __('Background') }}</label>

        <div class="col-md-10">
            <input id="hero_background" type="text" class="form-control @error('hero_background') is-invalid @enderror" name="hero_background" value="{{ $data['hero_background'] ?? '' }}" placeholder="" required>

            @error('hero_background')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>

    <div class="row mb-3">
        <label for="max_player" class="col-md-2 col-form-label text-md-end">{{ __('Max Online Player') }}</label>

        <div class="col-md-10">
            <input id="max_player" type="number" class="form-control @error('max_player') is-invalid @enderror" name="max_player" value="{{ $data['max_player'] ?? '' }}" placeholder="" required>

            @error('max_player')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>

    <div class="row mb-3">
        <label for="fake_player" class="col-md-2 col-form-label text-md-end">{{ __('Add Fake Player') }}</label>

        <div class="col-md-10">
            <input id="fake_player" type="number" class="form-control @error('fake_player') is-invalid @enderror" name="fake_player" value="{{ $data['fake_player'] ?? '' }}" placeholder="" required>

            @error('fake_player')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>

    <div class="row mb-3">
        <label for="max_level" class="col-md-2 col-form-label text-md-end">{{ __('Max Character Level') }}</label>

        <div class="col-md-10">
            <input id="max_level" type="number" class="form-control @error('max_level') is-invalid @enderror" name="max_level" value="{{ $data['max_level'] ?? '' }}" placeholder="" required>

            @error('max_level')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>

    <div class="row mb-3">
        <label for="dark_mode" class="col-md-2 col-form-label text-md-end">{{ __('Dark Mode') }}</label>

        <div class="col-md-10">
            <select class="form-select" name="dark_mode" aria-label="Default select example">
                <option value="switch" {{ config('settings.dark_mode') == 'switch' ? 'selected' : '' }}>Switch</option>
                <option value="light" {{ config('settings.dark_mode') == 'light' ? 'selected' : '' }}>Light</option>
                <option value="dark" {{ config('settings.dark_mode') == 'dark' ? 'selected' : '' }}>Dark</option>
            </select>

            @error('dark_mode')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>

    <div class="row mb-3">
        <label for="default_locale" class="col-md-2 col-form-label text-md-end">{{ __('Default Language') }}</label>

        <div class="col-md-10">
            <select class="form-select" name="default_locale" aria-label="Default select example">
                <option value="switch" {{ config('settings.default_locale') == 'switch' ? 'selected' : '' }}>Switch</option>

                @foreach(config('global.languages') as $key => $item)
                    <option value="{{ $key }}" {{ config('settings.default_locale') == $key ? 'selected' : '' }}>{{ $item['name'] }}</option>
                @endforeach
            </select>

            @error('default_locale')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>

    <div class="row mb-3">
        <label for="theme" class="col-md-2 col-form-label text-md-end">{{ __('Theme') }}</label>

        <div class="col-md-10">
            <select class="form-select" name="theme" aria-label="Default select example">
                <option value="default" {{ config('settings.theme') == 'default' ? 'selected' : '' }}>Default</option>
                @php
                    if (is_dir(resource_path('themes'))) {
                        foreach (scandir(resource_path('themes')) as $dir) {
                            if ($dir !== '.' && $dir !== '..' && is_dir(resource_path('themes') . '/' . $dir)) {
                                $themes[] = $dir;
                            }
                        }
                    }
                @endphp

                @isset($themes)
                    @foreach($themes as $theme)
                        <option value="{{ $theme }}" {{ config('settings.theme') === $theme ? 'selected' : '' }}>
                            {{ ucfirst($theme) }}
                        </option>
                    @endforeach
                @endisset
            </select>

            @error('theme')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>

    <div class="row mb-3">
        <label for="timezone" class="col-md-2 col-form-label text-md-end">{{ __('Timezone') }}</label>

        <div class="col-md-10">
            <select class="form-select" name="timezone" aria-label="Default select example">
                @foreach(\DateTimeZone::listIdentifiers() as $tz)
                    <option value="{{ $tz }}" {{ config('settings.timezone') === $tz ? 'selected' : '' }}>
                        {{ $tz }}
                    </option>
                @endforeach
            </select>

            @error('timezone')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>

    <div class="row mb-3">
        <label for="update_type" class="col-md-2 col-form-label text-md-end">{{ __('Update Profile Type') }}</label>

        <div class="col-md-10">
            <select class="form-select" name="update_type" aria-label="Default select example">
                <option value="standard" {{ config('settings.update_type') == 'standard' ? 'selected' : '' }}>Standard</option>
                <option value="verify_code" {{ config('settings.update_type') == 'verify_code' ? 'selected' : '' }}>Verification Code</option>
            </select>

            @error('update_type')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>

    <div class="row mb-3">
        <label for="disable_register" class="col-md-2 col-form-label text-md-end">{{ __('Disable Register') }}</label>

        <div class="col-md-10">
            <div class="form-check">
                <input type="hidden" name="disable_register" value="0">
                <input class="form-check-input" type="checkbox" name="disable_register" value="1" id="disable_register" {{ config('settings.disable_register') ? 'checked' : '' }}>
            </div>

            @error('disable_register')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>

    <div class="row mb-3">
        <label for="register_confirm" class="col-md-2 col-form-label text-md-end">{{ __('Register Confirmation') }}</label>

        <div class="col-md-10">
            <div class="form-check">
                <input type="hidden" name="register_confirm" value="0">
                <input class="form-check-input" type="checkbox" name="register_confirm" value="1" id="register_confirm" {{ config('settings.register_confirm') ? 'checked' : '' }}>
            </div>

            @error('register_confirm')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>

    <div class="row mb-3">
        <label for="duplicate_email" class="col-md-2 col-form-label text-md-end">{{ __('Duplicate Email (vSRO)') }}</label>

        <div class="col-md-10">
            <div class="form-check">
                <input type="hidden" name="duplicate_email" value="0">
                <input class="form-check-input" type="checkbox" name="duplicate_email" value="1" id="duplicate_email" {{ config('settings.duplicate_email') ? 'checked' : '' }}>
            </div>

            @error('duplicate_email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>

    <div class="row mb-3">
        <label for="agree_terms" class="col-md-2 col-form-label text-md-end">{{ __('Agree Terms') }}</label>

        <div class="col-md-10">
            <div class="form-check">
                <input type="hidden" name="agree_terms" value="0">
                <input class="form-check-input" type="checkbox" name="agree_terms" value="1" id="agree_terms" {{ config('settings.agree_terms') ? 'checked' : '' }}>
            </div>

            @error('agree_terms')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>

    <div class="row mb-0">
        <div class="col-md-10 offset-md-2">
            <button type="submit" class="btn btn-primary">
                {{ __('Save') }}
            </button>
        </div>
    </div>
</form>