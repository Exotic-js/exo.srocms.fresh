@extends('admin.layouts.app')
@section('title', __('Settings'))

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Settings</h1>

            <div class="btn-toolbar mb-2 mb-md-0">
                <div class="btn-group me-2">
                    <form action="{{ route('admin.settings.clear-cache') }}" method="POST" onsubmit="return confirm('Are you sure you want to clear all caches?')">
                        @csrf
                        <button type="submit" class="btn btn-danger">
                            Clear All Cache
                        </button>
                    </form>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <ul class="nav nav-tabs mb-3" id="settingsTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab" aria-controls="general" aria-selected="true">
                    {{ __('General') }}
                </button>
            </li>

            <!--
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="misc-tab" data-bs-toggle="tab" data-bs-target="#misc" type="button" role="tab" aria-controls="misc" aria-selected="false">
                    {{ __('Misc') }}
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab" aria-controls="history" aria-selected="false">
                    {{ __('History pages') }}
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="cache-tab" data-bs-toggle="tab" data-bs-target="#cache" type="button" role="tab" aria-controls="cache" aria-selected="false">
                    {{ __('Cache') }}
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-referral" type="button" role="tab">
                    {{ __('Referral') }}
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-tickets" type="button" role="tab">
                    {{ __('Tickets') }}
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-sliders" type="button" role="tab">
                    {{ __('Sliders') }}
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-footer-general" type="button" role="tab">
                    {{ __('General Links') }}
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-footer-social" type="button" role="tab">
                    {{ __('Social Links') }}
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-footer-backlink" type="button" role="tab">
                    {{ __('Backlinks') }}
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-vote" type="button" role="tab">
                    {{ __('Vote') }}
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-mail" type="button" role="tab">
                    {{ __('Mail') }}
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-captcha" type="button" role="tab">
                    {{ __('Captcha') }}
                </button>
            </li>
            -->
        </ul>

        <form method="POST" action="{{ route('admin.settings.update') }}" onsubmit="serializeGeneral()">
            @csrf

            <div class="tab-content" id="settingsTabsContent">

                {{-- ===================== GENERAL ===================== --}}
                <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                    <h5 class="fw-semibold mb-3">{{ __('Site Settings') }}</h5>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Site Title') }}</label>
                        <input type="text" class="form-control @error('site_title') is-invalid @enderror" name="site_title" value="{{ $settings['site_title'] ?? '' }}" required>
                        @error('site_title')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Site Description') }}</label>
                        <input type="text" class="form-control @error('site_desc') is-invalid @enderror" name="site_desc" value="{{ $settings['site_desc'] ?? '' }}" required>
                        @error('site_desc')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Site URL') }}</label>
                        <input type="text" class="form-control @error('site_url') is-invalid @enderror" name="site_url" value="{{ $settings['site_url'] ?? '' }}" required>
                        @error('site_url')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Site Favicon') }}</label>
                        <input type="text" class="form-control @error('site_favicon') is-invalid @enderror" name="site_favicon" value="{{ $settings['site_favicon'] ?? '' }}" required>
                        @error('site_favicon')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Site Logo') }}</label>
                        <input type="text" class="form-control @error('site_logo') is-invalid @enderror" name="site_logo" value="{{ $settings['site_logo'] ?? '' }}" required>
                        @error('site_logo')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Background') }}</label>
                        <input type="text" class="form-control @error('hero_background') is-invalid @enderror" name="hero_background" value="{{ $settings['hero_background'] ?? '' }}" required>
                        @error('hero_background')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <h5 class="fw-semibold mb-3">{{ __('Server Settings') }}</h5>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Max Online Player') }}</label>
                        <input type="number" class="form-control @error('max_player') is-invalid @enderror" name="max_player" value="{{ $settings['max_player'] ?? '' }}" required>
                        @error('max_player')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Add Fake Player') }}</label>
                        <input type="number" class="form-control @error('fake_player') is-invalid @enderror" name="fake_player" value="{{ $settings['fake_player'] ?? '' }}" required>
                        @error('fake_player')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Max Character Level') }}</label>
                        <input type="number" class="form-control @error('max_level') is-invalid @enderror" name="max_level" value="{{ $settings['max_level'] ?? '' }}" required>
                        @error('max_level')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <h5 class="fw-semibold mb-3">{{ __('Appearance') }}</h5>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Dark Mode') }}</label>
                        <select class="form-select" name="dark_mode">
                            <option value="switch" {{ ($settings['dark_mode'] ?? '') === 'switch' ? 'selected' : '' }}>Switch</option>
                            <option value="light"  {{ ($settings['dark_mode'] ?? '') === 'light'  ? 'selected' : '' }}>Light</option>
                            <option value="dark"   {{ ($settings['dark_mode'] ?? '') === 'dark'   ? 'selected' : '' }}>Dark</option>
                        </select>
                        @error('dark_mode')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Default Language') }}</label>
                        <select class="form-select" name="default_locale">
                            <option value="switch" {{ ($settings['default_locale'] ?? '') === 'switch' ? 'selected' : '' }}>Switch</option>
                            @foreach($languages as $key => $item)
                                <option value="{{ $key }}" {{ ($settings['default_locale'] ?? '') === $key ? 'selected' : '' }}>{{ $item['name'] }}</option>
                            @endforeach
                        </select>
                        @error('default_locale')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Theme') }}</label>
                        <select class="form-select" name="theme">
                            <option value="default" {{ ($settings['theme'] ?? '') === 'default' ? 'selected' : '' }}>Default</option>
                            @foreach($themes as $theme)
                                <option value="{{ $theme }}" {{ ($settings['theme'] ?? '') === $theme ? 'selected' : '' }}>
                                    {{ ucfirst($theme) }}
                                </option>
                            @endforeach
                        </select>
                        @error('theme')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Timezone') }}</label>
                        <select class="form-select" name="timezone">
                            @foreach($timezones as $tz)
                                <option value="{{ $tz }}" {{ ($settings['timezone'] ?? '') === $tz ? 'selected' : '' }}>{{ $tz }}</option>
                            @endforeach
                        </select>
                        @error('timezone')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <h5 class="fw-semibold mb-3">{{ __('User Settings') }}</h5>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Update Profile Type') }}</label>
                        <select class="form-select" name="update_type">
                            <option value="standard"    {{ ($settings['update_type'] ?? '') === 'standard'    ? 'selected' : '' }}>Standard</option>
                            <option value="verify_code" {{ ($settings['update_type'] ?? '') === 'verify_code' ? 'selected' : '' }}>Verification Code</option>
                        </select>
                        @error('update_type')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-check-label">{{ __('Disable Register') }}</label>
                        <div class="form-check">
                            <input type="hidden" name="disable_register" value="0">
                            <input class="form-check-input" type="checkbox" name="disable_register" value="1" id="disable_register" {{ !empty($settings['disable_register']) ? 'checked' : '' }}>
                            <label class="form-check-label" for="disable_register">{{ __('Enabled') }}</label>
                        </div>
                        @error('disable_register')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-check-label">{{ __('Register Confirmation') }}</label>
                        <div class="form-check">
                            <input type="hidden" name="register_confirm" value="0">
                            <input class="form-check-input" type="checkbox" name="register_confirm" value="1" id="register_confirm" {{ !empty($settings['register_confirm']) ? 'checked' : '' }}>
                            <label class="form-check-label" for="register_confirm">{{ __('Enabled') }}</label>
                        </div>
                        @error('register_confirm')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-check-label">{{ __('Duplicate Email (vSRO)') }}</label>
                        <div class="form-check">
                            <input type="hidden" name="duplicate_email" value="0">
                            <input class="form-check-input" type="checkbox" name="duplicate_email" value="1" id="duplicate_email" {{ !empty($settings['duplicate_email']) ? 'checked' : '' }}>
                            <label class="form-check-label" for="duplicate_email">{{ __('Enabled') }}</label>
                        </div>
                        @error('duplicate_email')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-check-label">{{ __('Agree Terms') }}</label>
                        <div class="form-check">
                            <input type="hidden" name="agree_terms" value="0">
                            <input class="form-check-input" type="checkbox" name="agree_terms" value="1" id="agree_terms" {{ !empty($settings['agree_terms']) ? 'checked' : '' }}>
                            <label class="form-check-label" for="agree_terms">{{ __('Enabled') }}</label>
                        </div>
                        @error('agree_terms')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- ===================== MISC ===================== --}}
                <div class="tab-pane fade" id="misc" role="tabpanel" aria-labelledby="misc-tab">
                </div>

                {{-- ===================== HISTORY ===================== --}}
                <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
                    <input type="hidden" id="ranking_source" value="{{ json_encode($ranking, JSON_UNESCAPED_SLASHES) }}">
                    <input type="hidden" id="ranking_payload" name="ranking" value="{{ json_encode($ranking, JSON_UNESCAPED_SLASHES) }}">

                    <div class="mb-3">
                        <div>
                            <h5 class="fw-semibold mb-3">{{ __('History pages') }}</h5>
                            <div class="d-flex flex-column gap-2">
                                @foreach([ 'event_schedule' => __('Event schedule'), 'unique_tracker' => __('Unique tracker'), 'advanced_unique_tracker' => __('Advanced unique tracker'), 'fortress_history' => __('Fortress history'), 'global_history' => __('Global history'), 'pvp_kill_logs' => __('PvP kill logs'), 'job_kill_logs' => __('Job kill logs'), 'item_plus_logs' => __('Item plus logs'), 'item_drop_logs' => __('Item drop logs') ] as $key => $label)
                                    <label class="form-check mb-0">
                                        <input class="form-check-input" type="checkbox" id="extra_{{ $key }}" data-rk="extra" data-key="{{ $key }}" {{ !empty($ranking['extra'][$key]) ? 'checked' : '' }}>
                                        <span class="form-check-label ms-2">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===================== CACHE ===================== --}}
                <div class="tab-pane fade" id="cache" role="tabpanel" aria-labelledby="cache-tab">
                    <div>
                        <div class="fw-semibold mb-3">{{ __('Cache Settings') }}</div>
                        <div>
                            @foreach($cache as $key => $value)
                                <div class="mb-3">
                                    <label class="form-label">{{ ucwords(str_replace('_', ' ', $key)) }} ({{ __('seconds') }})</label>
                                    <input type="number" class="form-control" name="cache[{{ $key }}]" value="{{ $value }}" min="0">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- ===================== REFERRAL ===================== --}}
                <div class="tab-pane fade" id="tab-referral" role="tabpanel">
                    <div>
                        <div>
                            <div class="mb-3">
                                <label class="form-check">
                                    <input class="form-check-input" type="checkbox" id="referral_enabled"
                                        {{ $referral['enabled'] ?? false ? 'checked' : '' }}>
                                    <span class="form-check-label fw-semibold">{{ __('Enable Referral System') }}</span>
                                </label>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('Reward Points') }}</label>
                                <input type="number" class="form-control" id="referral_reward_points"
                                       min="0" value="{{ $referral['reward_points'] ?? 5 }}">
                                <div class="form-text">{{ __('Points awarded per referral. Set to 0 to disable rewards.') }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('Minimum Redeem') }}</label>
                                <input type="number" class="form-control" id="referral_minimum_redeem"
                                       min="0" value="{{ $referral['minimum_redeem'] ?? 25 }}">
                                <div class="form-text">{{ __('Minimum points required before a user can redeem.') }}</div>
                            </div>
                        </div>
                    </div>

                <input type="hidden" id="referral" name="referral">
            </div>

            {{-- ===================== TICKETS ===================== --}}
            <div class="tab-pane fade" id="tab-tickets" role="tabpanel">
                <div class="mb-3">
                    <label class="form-check">
                        <input class="form-check-input" type="checkbox" id="tickets_enabled"
                            {{ $tickets['enabled'] ?? false ? 'checked' : '' }}>
                        <span class="form-check-label fw-semibold">{{ __('Enable Ticket System') }}</span>
                    </label>
                </div>

                <h6 class="mb-1">{{ __('Categories') }}</h6>
                <p class="text-muted small mb-3">{{ __('Define the ticket categories users can choose from.') }}</p>

                <div>
                    <button type="button" class="btn btn-secondary btn-sm mb-3" onclick="addTicketCategory()">
                        {{ __('+ Add Category') }}
                    </button>
                    <table class="table table-bordered" id="ticketCategoriesTable">
                        <thead class="table-light">
                        <tr>
                            <th>{{ __('Key') }}</th>
                            <th>{{ __('Label') }}</th>
                            <th style="width:80px;">{{ __('Action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($tickets['categories'] ?? [] as $catKey => $catLabel)
                            <tr>
                                <td><input type="text" class="form-control form-control-sm" data-tc="key"   value="{{ $catKey }}"></td>
                                <td><input type="text" class="form-control form-control-sm" data-tc="label" value="{{ $catLabel }}"></td>
                                <td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()">{{ __('Remove') }}</button></td>
                            </tr>
                        @empty
                            <tr>
                                <td><input type="text" class="form-control form-control-sm" data-tc="key"   placeholder="sales"></td>
                                <td><input type="text" class="form-control form-control-sm" data-tc="label" placeholder="Sales"></td>
                                <td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()">{{ __('Remove') }}</button></td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <input type="hidden" id="tickets" name="tickets">
            </div>

            {{-- ===================== SLIDERS ===================== --}}
            <div class="tab-pane fade" id="tab-sliders" role="tabpanel">
                <p class="text-muted small mb-3">{{ __('Manage homepage carousel slides.') }}</p>

                <button type="button" class="btn btn-secondary mb-3" onclick="addSlide()">
                    {{ __('+ Add Slide') }}
                </button>

                <div id="slidersContainer">
                    @foreach($sliders as $index => $slide)
                        <div class="card mb-3 slider-item">
                            <div class="card-header slider-title">
                                <span class="fw-semibold">{{ __('Slide') }} #{{ $index + 1 }}</span>
                            </div>
                            <div class="card-body slider-body">
                                <div class="row g-3">
                                    <div class="col-md-8">
                                        <label class="form-label">{{ __('Title') }}</label>
                                        <input type="text" class="form-control" data-sl="title" value="{{ $slide['title'] ?? '' }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">{{ __('Title Color') }}</label>
                                        <div class="input-group">
                                            <input type="color" class="form-control form-control-color"
                                                   data-sl="title_color_picker"
                                                   value="{{ $slide['title_color'] ?? '#ffffff' }}"
                                                   oninput="syncColor(this, 'title_color')">
                                            <input type="text" class="form-control" data-sl="title_color"
                                                   value="{{ $slide['title_color'] ?? '#ffffff' }}"
                                                   oninput="syncColorText(this, 'title_color_picker')">
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label">{{ __('Description') }}</label>
                                        <textarea class="form-control" data-sl="desc" rows="2">{{ $slide['desc'] ?? '' }}</textarea>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">{{ __('Description Color') }}</label>
                                        <div class="input-group">
                                            <input type="color" class="form-control form-control-color"
                                                   data-sl="desc_color_picker"
                                                   value="{{ $slide['desc_color'] ?? '#ffffff' }}"
                                                   oninput="syncColor(this, 'desc_color')">
                                            <input type="text" class="form-control" data-sl="desc_color"
                                                   value="{{ $slide['desc_color'] ?? '#ffffff' }}"
                                                   oninput="syncColorText(this, 'desc_color_picker')">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">{{ __('Image URL') }}</label>
                                        <input type="text" class="form-control" data-sl="image" value="{{ $slide['image'] ?? '' }}" placeholder="https://...">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">{{ __('Button Label') }}</label>
                                        <input type="text" class="form-control" data-sl="btn_label" value="{{ $slide['btn_label'] ?? '' }}" placeholder="Sign Up">
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label">{{ __('Button URL') }}</label>
                                        <input type="text" class="form-control" data-sl="btn_url" value="{{ $slide['btn_url'] ?? '#' }}" placeholder="#">
                                    </div>
                                </div>
                                <div class="d-flex mt-3">
                                    <button type="button" class="btn btn-sm btn-danger"
                                            onclick="this.closest('.slider-item').remove(); reindexSliders()">
                                        {{ __('Remove') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <input type="hidden" id="sliders" name="sliders">
            </div>

            {{-- ===================== FOOTER ===================== --}}
            {{-- ===================== GENERAL LINKS ===================== --}}
            <div class="tab-pane fade" id="tab-footer-general" role="tabpanel">
                <p class="text-muted small mb-3">{{ __('Navigation links shown in the footer.') }}</p>

                <button type="button" class="btn btn-secondary btn-sm mb-3" onclick="addFooterGeneralRow()">{{ __('+ Add Row') }}</button>

                <table class="table table-bordered align-middle" id="footer-general">
                    <thead class="table-light">
                    <tr>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('URL') }}</th>
                        <th style="width:80px;">{{ __('Action') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($footer['general'] ?? [] as $item)
                        <tr>
                            <td><input type="text" class="form-control form-control-sm" data-fi="name"  value="{{ $item['name']  ?? '' }}"></td>
                            <td><input type="text" class="form-control form-control-sm" data-fi="url"   value="{{ $item['url']   ?? '' }}"></td>
                            <td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()">{{ __('Remove') }}</button></td>
                        </tr>
                    @empty
                        <tr>
                            <td><input type="text" class="form-control form-control-sm" data-fi="name"  placeholder="{{ __('Home') }}"></td>
                            <td><input type="text" class="form-control form-control-sm" data-fi="url"   placeholder="#"></td>
                            <td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()">{{ __('Remove') }}</button></td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                <input type="hidden" id="footer" name="footer">
            </div>

            {{-- ===================== SOCIAL LINKS ===================== --}}
            <div class="tab-pane fade" id="tab-footer-social" role="tabpanel">
                <p class="text-muted small mb-3">{{ __('Social media links. Use Font Awesome icon HTML in the Image/Icon column.') }}</p>

                <button type="button" class="btn btn-secondary btn-sm mb-3" onclick="addFooterSocialRow()">{{ __('+ Add Row') }}</button>

                <table class="table table-bordered align-middle" id="footer-social">
                    <thead class="table-light">
                    <tr>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('URL') }}</th>
                        <th>{{ __('Icon (FA HTML)') }}</th>
                        <th style="width:80px;">{{ __('Action') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($footer['social'] ?? [] as $item)
                        <tr>
                            <td><input type="text" class="form-control form-control-sm" data-fi="name"  value="{{ $item['name']  ?? '' }}"></td>
                            <td><input type="text" class="form-control form-control-sm" data-fi="url"   value="{{ $item['url']   ?? '' }}"></td>
                            <td><input type="text" class="form-control form-control-sm font-monospace" data-fi="image" value="{{ $item['image'] ?? '' }}" placeholder='<i class="fab fa-facebook-f"></i>'></td>
                            <td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()">{{ __('Remove') }}</button></td>
                        </tr>
                    @empty
                        <tr>
                            <td><input type="text" class="form-control form-control-sm" data-fi="name"  placeholder="Facebook"></td>
                            <td><input type="text" class="form-control form-control-sm" data-fi="url"   placeholder="https://facebook.com/..."></td>
                            <td><input type="text" class="form-control form-control-sm font-monospace" data-fi="image" placeholder='<i class="fab fa-facebook-f"></i>'></td>
                            <td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()">{{ __('Remove') }}</button></td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ===================== BACKLINKS ===================== --}}
            <div class="tab-pane fade" id="tab-footer-backlink" role="tabpanel">
                <p class="text-muted small mb-3">{{ __('External sites that link back to your server. Use an image URL or leave empty.') }}</p>

                <button type="button" class="btn btn-secondary btn-sm mb-3" onclick="addFooterBacklinkRow()">{{ __('+ Add Row') }}</button>

                <table class="table table-bordered align-middle" id="footer-backlink">
                    <thead class="table-light">
                    <tr>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('URL') }}</th>
                        <th>{{ __('Image URL') }}</th>
                        <th style="width:80px;">{{ __('Action') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($footer['backlink'] ?? [] as $item)
                        <tr>
                            <td><input type="text" class="form-control form-control-sm" data-fi="name"  value="{{ $item['name']  ?? '' }}"></td>
                            <td><input type="text" class="form-control form-control-sm" data-fi="url"   value="{{ $item['url']   ?? '' }}"></td>
                            <td><input type="text" class="form-control form-control-sm" data-fi="image" value="{{ $item['image'] ?? '' }}" placeholder="https://example.com/logo.png"></td>
                            <td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()">{{ __('Remove') }}</button></td>
                        </tr>
                    @empty
                        <tr>
                            <td><input type="text" class="form-control form-control-sm" data-fi="name"  placeholder="Elitepvpers"></td>
                            <td><input type="text" class="form-control form-control-sm" data-fi="url"   placeholder="https://..."></td>
                            <td><input type="text" class="form-control form-control-sm" data-fi="image" placeholder="https://example.com/logo.png"></td>
                            <td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()">{{ __('Remove') }}</button></td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ===================== VOTE ===================== --}}
            <div class="tab-pane fade" id="tab-vote" role="tabpanel">
                <div id="voteContainer">
                    @foreach($vote as $voteKey => $voteDefault)
                        <div class="card mb-3">
                            <div class="card-header fw-semibold">{{ $voteDefault['name'] }}</div>
                            <div class="card-body">
                                <div class="alert alert-info py-2 mb-3" role="alert">
                                    {{ __('Postback URL:') }} <code>{{ $appUrl }}/postback/{{ $voteKey }}</code>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox"
                                           id="vote_{{ $voteKey }}_enabled"
                                        {{ !empty($voteDefault['enabled']) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="vote_{{ $voteKey }}_enabled">{{ __('Enabled') }}</label>
                                </div>

                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">{{ __('Vote Name') }}</label>
                                        <input type="text" class="form-control form-control-sm font-monospace"
                                               id="vote_{{ $voteKey }}_name"
                                               value="{{ $voteDefault['name'] ?? '' }}"
                                               placeholder="">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">{{ __('Vote URL') }}</label>
                                        <input type="text" class="form-control form-control-sm font-monospace"
                                               id="vote_{{ $voteKey }}_url"
                                               value="{{ $voteDefault['url'] ?? '' }}"
                                               placeholder="https://...{JID}">
                                        <div class="form-text">{{ __("Use {JID} as placeholder for the player's username.") }}</div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">{{ __('Vote Image') }}</label>
                                        <input type="text" class="form-control form-control-sm font-monospace"
                                               id="vote_{{ $voteKey }}_image"
                                               value="{{ $voteDefault['image'] ?? '' }}"
                                               placeholder="">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">{{ __('Reward Points') }}</label>
                                        <input type="number" class="form-control form-control-sm"
                                               id="vote_{{ $voteKey }}_reward"
                                               min="0" value="{{ $voteDefault['reward'] ?? 5 }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">{{ __('Timeout (hours)') }}</label>
                                        <input type="number" class="form-control form-control-sm"
                                               id="vote_{{ $voteKey }}_timeout"
                                               min="1" value="{{ $voteDefault['timeout'] ?? 12 }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">{{ __('Allowed IP(s)') }}</label>
                                        <input type="text" class="form-control form-control-sm font-monospace"
                                               id="vote_{{ $voteKey }}_ip"
                                               value="{{ $voteDefault['ip'] ?? '' }}"
                                               placeholder="{{ $voteDefault['ip'] }}">
                                    </div>
                                    @if($voteKey === 'vote4rewards')
                                        <div class="col-12">
                                            <label class="form-label">{{ __('Webhook Secret') }}</label>
                                            <input type="password" class="form-control form-control-sm"
                                                   id="vote_{{ $voteKey }}_webhook_secret"
                                                   value="{{ $voteDefault['webhook_secret'] ?? '' }}"
                                                   placeholder="Q7A9DA2xVdkL3rP0B8mNfH5S3LJcWgUy">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <input type="hidden" id="vote" name="vote">
            </div>

            {{-- ===================== MAIL ===================== --}}
            <div class="tab-pane fade" id="tab-mail" role="tabpanel">
                <div>
                    <div class="fw-semibold mb-3">{{ __('Mail Configuration') }}</div>
                    <div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('Mailer') }}</label>
                            <select class="form-select" id="mail_mailer">
                                @foreach(['log' => 'Log (debug)', 'smtp' => 'SMTP', 'sendmail' => 'Sendmail', 'mailgun' => 'Mailgun', 'ses' => 'Amazon SES', 'postmark' => 'Postmark'] as $val => $label)
                                    <option value="{{ $val }}" {{ ($mail['MAIL_MAILER'] ?? 'log') === $val ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">{{ __('Use "Log" for local testing — emails are written to storage/logs instead of being sent.') }}</div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Host') }}</label>
                                <input type="text" class="form-control" id="mail_host"
                                       value="{{ $mail['MAIL_HOST'] ?? '127.0.0.1' }}"
                                       placeholder="smtp.mailtrap.io">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{ __('Port') }}</label>
                                <input type="number" class="form-control" id="mail_port"
                                       value="{{ $mail['MAIL_PORT'] ?? 2525 }}"
                                       placeholder="2525">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{ __('Scheme') }}</label>
                                <select class="form-select" id="mail_scheme">
                                    @foreach(['null' => 'None', 'tls' => 'TLS', 'ssl' => 'SSL'] as $val => $label)
                                        <option value="{{ $val }}" {{ ($mail['MAIL_SCHEME'] ?? 'null') === $val ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Username') }}</label>
                                <input type="text" class="form-control" id="mail_username"
                                       value="{{ ($mail['MAIL_USERNAME'] ?? 'null') !== 'null' ? ($mail['MAIL_USERNAME'] ?? '') : '' }}"
                                       placeholder="null">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Password') }}</label>
                                <input type="password" class="form-control" id="mail_password"
                                       value="{{ ($mail['MAIL_PASSWORD'] ?? 'null') !== 'null' ? ($mail['MAIL_PASSWORD'] ?? '') : '' }}"
                                       placeholder="null">
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('From Address') }}</label>
                                <input type="email" class="form-control" id="mail_from_address"
                                       value="{{ $mail['MAIL_FROM_ADDRESS'] ?? 'hello@example.com' }}"
                                       placeholder="hello@example.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('From Name') }}</label>
                                <input type="text" class="form-control" id="mail_from_name"
                                       value="{{ $mail['MAIL_FROM_NAME'] ?? $appName }}"
                                       placeholder="{{ $appName }}">
                            </div>
                        </div>

                    </div>
                </div>

                <input type="hidden" id="mail" name="mail">
            </div>


            {{-- ===================== CAPTCHA ===================== --}}
            <div class="tab-pane fade" id="tab-captcha" role="tabpanel">
                <div>
                    <div class="fw-semibold mb-3">{{ __('reCAPTCHA Configuration') }}</div>
                    <div>

                        <div class="mb-3">
                            <label class="form-check">
                                <input class="form-check-input" type="checkbox" id="captcha_enabled"
                                    {{ $captcha['enabled'] ?? false ? 'checked' : '' }}>
                                <span class="form-check-label fw-semibold">{{ __('Enable Captcha') }}</span>
                            </label>
                            <div class="form-text">{{ __('Protects login, register, and contact forms from bots.') }}</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('Site Key') }}</label>
                            <input type="text" class="form-control" id="captcha_sitekey"
                                   value="{{ $captcha['sitekey'] ?? '' }}"
                                   placeholder="NOCAPTCHA_SITEKEY">
                            <div class="form-text">
                                {{ __('The public key used in the frontend widget.') }}
                                <a href="https://www.google.com/recaptcha/admin" target="_blank">{{ __('Get keys from Google') }} &nearr;</a>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('Secret Key') }}</label>
                            <input type="password" class="form-control" id="captcha_secret"
                                   value="{{ $captcha['secret'] ?? '' }}"
                                   placeholder="NOCAPTCHA_SECRET">
                            <div class="form-text">{{ __('The private key used for server-side verification. Keep this secret.') }}</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('Timeout (seconds)') }}</label>
                            <input type="number" class="form-control" id="captcha_timeout"
                                   min="1" max="120"
                                   value="{{ $captcha['options']['timeout'] ?? 30 }}">
                            <div class="form-text">{{ __('How long the captcha response token is valid.') }}</div>
                        </div>

                    </div>
                </div>

                <input type="hidden" id="captcha" name="captcha">
            </div>

    </div>{{-- .tab-content --}}

    <div class="mt-4">
        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
    </div>
    </form>
    </div>

    <script>
        // ─── Referral ─────────────────────────────────────────────────────────────────
        function serializeReferral() {
            document.getElementById('referral').value = JSON.stringify({
                enabled:        document.getElementById('referral_enabled').checked,
                reward_points:  parseInt(document.getElementById('referral_reward_points').value)  || 0,
                minimum_redeem: parseInt(document.getElementById('referral_minimum_redeem').value) || 0,
            });
        }

        function serializeRanking() {
            let baseRanking = {};
            const rawValue = document.getElementById('ranking_source')?.value || '';
            if (rawValue) {
                try {
                    baseRanking = JSON.parse(rawValue);
                } catch (e) {
                    baseRanking = {};
                }
            }

            const ranking = Object.assign({}, baseRanking);

            ranking.extra = {};
            document.querySelectorAll('[data-rk="extra"]').forEach(input => {
                ranking.extra[input.dataset.key] = input.checked;
            });

            document.getElementById('ranking_payload').value = JSON.stringify(ranking);
        }

        // ─── Tickets ──────────────────────────────────────────────────────────────────
        function addTicketCategory() {
            const tbody = document.querySelector('#ticketCategoriesTable tbody');
            const row   = tbody.insertRow();
            row.innerHTML = `
                <td><input type="text" class="form-control form-control-sm" data-tc="key"   placeholder="category_key"></td>
                <td><input type="text" class="form-control form-control-sm" data-tc="label" placeholder="Category Label"></td>
                <td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()">{{ __('Remove') }}</button></td>
            `;
        }

        function serializeTickets() {
            const categories = {};
            document.querySelectorAll('#ticketCategoriesTable tbody tr').forEach(tr => {
                const key   = tr.querySelector('[data-tc="key"]').value.trim();
                const label = tr.querySelector('[data-tc="label"]').value.trim();
                if (key) categories[key] = label;
            });
            document.getElementById('tickets').value = JSON.stringify({
                enabled:    document.getElementById('tickets_enabled').checked,
                categories: categories,
            });
        }

        // ─── Sliders ──────────────────────────────────────────────────────────────────
        function reindexSliders() {
            document.querySelectorAll('#slidersContainer .slider-item').forEach((card, i) => {
                card.querySelector('.slider-title span').textContent = `{{ __('Slide') }} #${i + 1}`;
            });
        }

        function syncColor(picker, targetKey) {
            picker.closest('.row, .slider-body').querySelector(`[data-sl="${targetKey}"]`).value = picker.value;
        }

        function syncColorText(input, pickerKey) {
            const picker = input.closest('.input-group').querySelector(`[data-sl="${pickerKey}"]`);
            if (picker && /^#[0-9a-fA-F]{6}$/.test(input.value)) picker.value = input.value;
        }

        function addSlide() {
            const container = document.getElementById('slidersContainer');
            const index     = container.querySelectorAll('.slider-item').length + 1;
            const card      = document.createElement('div');
            card.className  = 'card mb-3 slider-item';
            card.innerHTML  = `
                <div class="card-header slider-title">
                    <span class="fw-semibold">{{ __('Slide') }} #${index}</span>
                    </div>
        <div class="card-body slider-body">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">{{ __('Title') }}</label>
                            <input type="text" class="form-control" data-sl="title">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('Title Color') }}</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color"
                                       data-sl="title_color_picker" value="#ffffff"
                                       oninput="syncColor(this, 'title_color')">
                                <input type="text" class="form-control" data-sl="title_color" value="#ffffff"
                                       oninput="syncColorText(this, 'title_color_picker')">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">{{ __('Description') }}</label>
                            <textarea class="form-control" data-sl="desc" rows="2"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('Description Color') }}</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color"
                                       data-sl="desc_color_picker" value="#ffffff"
                                       oninput="syncColor(this, 'desc_color')">
                                <input type="text" class="form-control" data-sl="desc_color" value="#ffffff"
                                       oninput="syncColorText(this, 'desc_color_picker')">
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ __('Image URL') }}</label>
                            <input type="text" class="form-control" data-sl="image" placeholder="https://...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('Button Label') }}</label>
                            <input type="text" class="form-control" data-sl="btn_label" placeholder="Sign Up">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">{{ __('Button URL') }}</label>
                            <input type="text" class="form-control" data-sl="btn_url" placeholder="#">
                        </div>
                    </div>
                    <div class="d-flex mt-3">
                        <button type="button" class="btn btn-sm btn-danger"
                                onclick="this.closest('.slider-item').remove(); reindexSliders()">
                            {{ __('Remove') }}
                        </button>
                    </div>
    </div>`;
            container.appendChild(card);
        }

        function serializeSliders() {
            const slides = Array.from(document.querySelectorAll('#slidersContainer .slider-item')).map(card => ({
                title:       card.querySelector('[data-sl="title"]').value,
                title_color: card.querySelector('[data-sl="title_color"]').value,
                desc:        card.querySelector('[data-sl="desc"]').value,
                desc_color:  card.querySelector('[data-sl="desc_color"]').value,
                image:       card.querySelector('[data-sl="image"]').value,
                btn_label:   card.querySelector('[data-sl="btn_label"]').value,
                btn_url:     card.querySelector('[data-sl="btn_url"]').value,
            }));
            document.getElementById('sliders').value = JSON.stringify(slides);
        }

        // ─── Footer ───────────────────────────────────────────────────────────────────
        // ─── Footer (shared serialize, called from each sub-tab) ────────────────────
        function addFooterGeneralRow() {
            const tbody = document.querySelector('#footer-general tbody');
            const row   = tbody.insertRow();
            row.innerHTML = `
                <td><input type="text" class="form-control form-control-sm" data-fi="name"  placeholder="{{ __('Home') }}"></td>
                <td><input type="text" class="form-control form-control-sm" data-fi="url"   placeholder="#"></td>
                <td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()">{{ __('Remove') }}</button></td>
            `;
        }

        function addFooterSocialRow() {
            const tbody = document.querySelector('#footer-social tbody');
            const row   = tbody.insertRow();
            row.innerHTML = `
                <td><input type="text" class="form-control form-control-sm" data-fi="name"  placeholder="Facebook"></td>
                <td><input type="text" class="form-control form-control-sm" data-fi="url"   placeholder="https://facebook.com/..."></td>
                <td><input type="text" class="form-control form-control-sm font-monospace" data-fi="image" placeholder='&lt;i class="fab fa-facebook-f"&gt;&lt;/i&gt;'></td>
                <td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()">{{ __('Remove') }}</button></td>
            `;
        }

        function addFooterBacklinkRow() {
            const tbody = document.querySelector('#footer-backlink tbody');
            const row   = tbody.insertRow();
            row.innerHTML = `
                <td><input type="text" class="form-control form-control-sm" data-fi="name"  placeholder="Elitepvpers"></td>
                <td><input type="text" class="form-control form-control-sm" data-fi="url"   placeholder="https://..."></td>
                <td><input type="text" class="form-control form-control-sm" data-fi="image" placeholder="https://example.com/logo.png"></td>
                <td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()">{{ __('Remove') }}</button></td>
            `;
        }

        function serializeFooter() {
            // Collect whichever footer tables are present in the active tab
            const sections = ['general', 'social', 'backlink'];

            // Load existing saved footer so we don't lose sections from other tabs
            let existing = {};
            const existingInput = document.getElementById('footer');
            if (existingInput && existingInput.value) {
                try { existing = JSON.parse(existingInput.value); } catch(e) {}
            }

            sections.forEach(section => {
                const table = document.getElementById('footer-' + section);
                if (!table) return;
                existing[section] = Array.from(table.querySelectorAll('tbody tr')).map(tr => ({
                    name:  tr.querySelector('[data-fi="name"]').value,
                    url:   tr.querySelector('[data-fi="url"]').value,
                    image: tr.querySelector('[data-fi="image"]') ? tr.querySelector('[data-fi="image"]').value : '',
                })).filter(r => r.name || r.url);
            });

            // Set on all footer hidden inputs (one per sub-form)
            document.querySelectorAll('#footer').forEach(el => el.value = JSON.stringify(existing));
        }

        // ─── Vote ─────────────────────────────────────────────────────────────────────
        const voteKeys = {!! json_encode(array_keys($vote)) !!};

        function serializeVote() {
            const result = {};
            voteKeys.forEach(key => {
                const enabled = document.getElementById('vote_' + key + '_enabled');
                const name     = document.getElementById('vote_' + key + '_name');
                const url     = document.getElementById('vote_' + key + '_url');
                const image     = document.getElementById('vote_' + key + '_image');
                const reward  = document.getElementById('vote_' + key + '_reward');
                const timeout = document.getElementById('vote_' + key + '_timeout');
                const ip      = document.getElementById('vote_' + key + '_ip');
                if (!enabled) return;
                result[key] = {
                    enabled: enabled.checked,
                    route:   key,
                    name:     name     ? name.value     : '',
                    url:     url     ? url.value     : '',
                    image:     image     ? image.value     : '',
                    reward:  reward  ? parseInt(reward.value)  || 5  : 5,
                    timeout: timeout ? parseInt(timeout.value) || 12 : 12,
                    ip:      ip      ? ip.value      : '',
                };
                // vote4rewards webhook secret
                const ws = document.getElementById('vote_' + key + '_webhook_secret');
                if (ws) result[key].webhook_secret = ws.value;
            });
            document.getElementById('vote').value = JSON.stringify(result);
        }

        // ─── Mail ─────────────────────────────────────────────────────────────────────
        function serializeMail() {
            document.getElementById('mail').value = JSON.stringify({
                MAIL_MAILER:       document.getElementById('mail_mailer').value,
                MAIL_HOST:         document.getElementById('mail_host').value,
                MAIL_PORT:         document.getElementById('mail_port').value,
                MAIL_SCHEME:       document.getElementById('mail_scheme').value,
                MAIL_USERNAME:     document.getElementById('mail_username').value || 'null',
                MAIL_PASSWORD:     document.getElementById('mail_password').value || 'null',
                MAIL_FROM_ADDRESS: document.getElementById('mail_from_address').value,
                MAIL_FROM_NAME:    document.getElementById('mail_from_name').value,
            });
        }

        // ─── Captcha ──────────────────────────────────────────────────────────────────
        function serializeCaptcha() {
            document.getElementById('captcha').value = JSON.stringify({
                enabled: document.getElementById('captcha_enabled').checked,
                sitekey: document.getElementById('captcha_sitekey').value,
                secret:  document.getElementById('captcha_secret').value,
                options: {
                    timeout: parseInt(document.getElementById('captcha_timeout').value) || 30,
                },
            });
        }

        function serializeGeneral() {
            serializeRanking();
            serializeReferral();
            serializeTickets();
            serializeSliders();
            serializeFooter();
            serializeVote();
            serializeMail();
            serializeCaptcha();
        }
    </script>
@endsection
