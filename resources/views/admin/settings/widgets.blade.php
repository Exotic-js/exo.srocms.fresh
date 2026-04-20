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
                        <button type="submit" class="btn btn-danger">Clear All Cache</button>
                    </form>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success" role="alert">{{ session('success') }}</div>
        @endif

        <ul class="nav nav-tabs mb-3" id="settingsTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#widgets" type="button" role="tab">
                    {{ __('Widgets') }}
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#discord" type="button" role="tab">
                    {{ __('Discord') }}
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#server-info" type="button" role="tab">
                    {{ __('Server Info') }}
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#schedule" type="button" role="tab">
                    {{ __('Event Schedule') }}
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#fortress-war" type="button" role="tab">
                    {{ __('Fortress War') }}
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#custom-widgets" type="button" role="tab">
                    {{ __('Custom Widgets') }}
                </button>
            </li>
        </ul>

        <form method="POST" action="{{ route('admin.settings.update') }}" id="settingsForm">
            @csrf

            <div class="tab-content" id="settingsTabsContent">

                {{-- ===================== WIDGETS TAB ===================== --}}
                <div class="tab-pane fade show active" id="widgets" role="tabpanel">
                    @foreach($limitWidgets as $widget)
                        <div class="mb-3">
                            <label class="form-check">
                                <input class="form-check-input" type="checkbox"
                                       id="{{ $widget['id'] }}_enabled"
                                    {{ !empty($widgets[$widget['id']]['enabled']) ? 'checked' : '' }}>
                                <span class="form-check-label fw-semibold">{{ __($widget['label']) }}</span>
                            </label>
                            <label class="form-label small ms-3">{{ __('Limit') }}</label>
                            <input type="number" class="form-control form-control-sm d-inline-block" 
                                   style="width: 80px;"
                                   id="{{ $widget['id'] }}_limit"
                                   value="{{ $widgets[$widget['id']]['limit'] ?? 5 }}"
                                   min="1" max="50">
                        </div>
                    @endforeach
                </div>

                {{-- ===================== DISCORD TAB ===================== --}}
                <div class="tab-pane fade" id="discord" role="tabpanel">
                    <div>
                        <div>
                            <div class="mb-3">
                                <label class="form-check">
                                    <input class="form-check-input" type="checkbox" id="discord_enabled"
                                        {{ $discord['enabled'] ?? false ? 'checked' : '' }}>
                                    <span class="form-check-label fw-semibold">{{ __('Enable Discord Widget') }}</span>
                                </label>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('Server ID') }}</label>
                                <input type="text" class="form-control" id="discord_server_id"
                                       value="{{ $discord['server_id'] ?? '' }}" placeholder="e.g. 1004443821570019338">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('Channel ID') }}</label>
                                <input type="text" class="form-control" id="discord_channel_id"
                                       value="{{ $discord['channel_id'] ?? '' }}" placeholder="e.g. 1374482240427528254">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('Theme') }}</label>
                                <select class="form-select" id="discord_theme">
                                    <option value="dark"  {{ ($discord['theme'] ?? 'dark') === 'dark'  ? 'selected' : '' }}>{{ __('Dark') }}</option>
                                    <option value="light" {{ ($discord['theme'] ?? 'dark') === 'light' ? 'selected' : '' }}>{{ __('Light') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===================== EVENT SCHEDULE TAB ===================== --}}
                <div class="tab-pane fade" id="schedule" role="tabpanel">
                    <div class="mb-3">
                        <label class="form-check">
                            <input class="form-check-input" type="checkbox" id="event_schedule_enabled"
                                {{ !empty($eventSchedule['enabled']) ? 'checked' : '' }}>
                            <span class="form-check-label fw-semibold">{{ __('Enable Event Schedule') }}</span>
                        </label>
                    </div>

                    <p class="text-muted small mb-3">
                        {{ __('Add original game events (by Event ID) or fully custom events with their own schedule.') }}
                    </p>

                    <button type="button" class="btn btn-secondary mb-3" id="showAddFormBtn" onclick="showAddForm()">
                        {{ __('+ Add Event') }}
                    </button>

                    {{-- Event cards container --}}
                    <div id="eventsContainer">

                        {{-- Render saved original events (names) --}}
                        @foreach($eventSchedule['names'] ?? [] as $id => $name)
                            <div class="card mb-3 event-item event-original">
                                <div class="card-header">
                                    <span class="badge bg-primary me-2">{{ __('Original') }}</span>
                                    <span class="fw-semibold event-card-title">{{ $name }}</span>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Event ID') }}</label>
                                        <input type="number" class="form-control" data-orig="id" value="{{ $id }}"
                                               oninput="updateOriginalTitle(this)">
                                        <div class="form-text">{{ __('Use the numeric game event ID') }}</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Display Name') }}</label>
                                        <input type="text" class="form-control" data-orig="name" value="{{ $name }}"
                                               oninput="updateOriginalTitle(this)">
                                    </div>
                                    <div class="d-flex justify-content-start">
                                        <button type="button" class="btn btn-sm btn-danger" onclick="removeEvent(this)">{{ __('Remove') }}</button>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        {{-- Render saved custom events --}}
                        @foreach($eventSchedule['custom'] ?? [] as $key => $event)
                            <div class="card mb-3 event-item event-custom">
                                <div class="card-header">
                                    <span class="badge bg-success me-2">{{ __('Custom') }}</span>
                                    <span class="fw-semibold event-card-title">{{ $event['name'] ?? __('Unnamed') }}</span>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-check">
                                            <input class="form-check-input" type="checkbox" data-ce="enabled"
                                                {{ $event['enabled'] ?? false ? 'checked' : '' }}>
                                            <span class="form-check-label">{{ __('Enabled') }}</span>
                                        </label>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Event Name') }}</label>
                                        <input type="text" class="form-control" data-ce="name"
                                               value="{{ $event['name'] ?? '' }}"
                                               oninput="updateCustomTitle(this)">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Hours (0-23)') }}</label>
                                        <div class="d-flex flex-wrap gap-2">
                                            @for($hour = 0; $hour <= 23; $hour++)
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox"
                                                           data-ce-hour="{{ $hour }}"
                                                        {{ isset($event['hour']) && $hour == $event['hour'] ? 'checked' : '' }}>
                                                    <label class="form-check-label">{{ $hour }}</label>
                                                </div>
                                            @endfor
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Minutes (0-59)') }}</label>
                                        <div class="d-flex flex-wrap gap-2">
                                            @for($minute = 0; $minute <= 59; $minute++)
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox"
                                                           data-ce-minute="{{ $minute }}"
                                                        {{ isset($event['min']) && $minute == $event['min'] ? 'checked' : '' }}>
                                                    <label class="form-check-label">{{ $minute }}</label>
                                                </div>
                                            @endfor
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Duration (seconds)') }}</label>
                                        <input type="number" class="form-control" min="0" data-ce="duration" value="{{ $event['duration'] ?? 3600 }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Days') }}</label>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach(['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $day)
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox"
                                                           data-ce-day="{{ $day }}"
                                                        {{ in_array($day, $event['days'] ?? []) ? 'checked' : '' }}>
                                                    <label class="form-check-label">{{ $day }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-start">
                                        <button type="button" class="btn btn-sm btn-danger" onclick="removeEvent(this)">{{ __('Remove') }}</button>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>{{-- #eventsContainer --}}

                    {{-- Inline Add Event Form --}}
                    <div id="addEventForm" class="card border-primary mb-3" style="display:none;">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <span class="fw-semibold">{{ __('New Event') }}</span>
                            <button type="button" class="btn-close btn-close-white" onclick="hideAddForm()"></button>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">{{ __('Event Type') }}</label>
                                <select class="form-select" id="newEventType" onchange="switchEventType(this.value)">
                                    <option value="original">{{ __('Original Event — built-in game event by ID') }}</option>
                                    <option value="custom">{{ __('Custom Event — custom name, days & schedule') }}</option>
                                </select>
                            </div>

                            {{-- Original fields --}}
                            <div id="newEventOriginalFields">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('Event ID') }}</label>
                                            <input type="number" class="form-control" id="newOrigId" placeholder="e.g. 10">
                                            <div class="form-text">{{ __('Numeric game event ID') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('Display Name') }}</label>
                                            <input type="text" class="form-control" id="newOrigName" placeholder="e.g. Roc">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Custom fields --}}
                            <div id="newEventCustomFields" style="display:none;">
                                <div class="mb-3">
                                    <label class="form-check">
                                        <input class="form-check-input" type="checkbox" id="newCeEnabled" checked>
                                        <span class="form-check-label">{{ __('Enabled') }}</span>
                                    </label>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Event Name') }}</label>
                                    <input type="text" class="form-control" id="newCeName">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Hours (0-23)') }}</label>
                                    <div class="d-flex flex-wrap gap-2" id="newCeHours">
                                        @for($hour = 0; $hour <= 23; $hour++)
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" id="newHour{{ $hour }}">
                                                <label class="form-check-label" for="newHour{{ $hour }}">{{ $hour }}</label>
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Minutes (0-59)') }}</label>
                                    <div class="d-flex flex-wrap gap-2" id="newCeMinutes">
                                        @for($minute = 0; $minute <= 59; $minute++)
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" id="newMinute{{ $minute }}">
                                                <label class="form-check-label" for="newMinute{{ $minute }}">{{ $minute }}</label>
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Duration (seconds)') }}</label>
                                    <input type="number" class="form-control" min="0" id="newCeDuration" value="3600">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Days') }}</label>
                                    <div class="d-flex flex-wrap gap-2" id="newCeDays">
                                        @foreach(['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $day)
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" id="newDay{{ $day }}">
                                                <label class="form-check-label" for="newDay{{ $day }}">{{ $day }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3 d-flex gap-2">
                                <button type="button" class="btn btn-primary" onclick="confirmAddEvent()">{{ __('Add') }}</button>
                                <button type="button" class="btn btn-secondary" onclick="hideAddForm()">{{ __('Cancel') }}</button>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="event_schedule" name="event_schedule">
                </div>

                {{-- ===================== FORTRESS WAR TAB ===================== --}}
                <div class="tab-pane fade" id="fortress-war" role="tabpanel">
                    <div class="mb-3">
                        <label class="form-check">
                            <input class="form-check-input" type="checkbox" id="fw_enabled"
                                {{ !empty($fortressWar['enabled']) ? 'checked' : '' }}>
                            <span class="form-check-label fw-semibold">{{ __('Enable Fortress War') }}</span>
                        </label>
                    </div>

                    <p class="text-muted small mb-3">
                        {{ __('Customize the display name and image path for each fortress. Fortress IDs are fixed.') }}
                    </p>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" id="fortressTable">
                            <thead class="table-light">
                            <tr>
                                <th style="width:80px;" class="text-center">{{ __('ID') }}</th>
                                <th>{{ __('Display Name') }}</th>
                                <th>{{ __('Image Path') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($fortressWar['names'] ?? [] as $id => $fort)
                                <tr>
                                    <td class="text-center">
                                        <span class="badge bg-secondary fs-6">{{ $id }}</span>
                                        <input type="hidden" data-fw="id" value="{{ $id }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" data-fw="name" value="{{ $fort['name'] }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" data-fw="image" value="{{ $fort['image'] }}">
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <input type="hidden" id="fortress_war" name="fortress_war">
                </div>

                {{-- ===================== SERVER INFO TAB ===================== --}}
                <div class="tab-pane fade" id="server-info" role="tabpanel">
                    <div class="mb-3">
                        <label class="form-check">
                            <input class="form-check-input" type="checkbox" id="server_info_enabled"
                                {{ !empty($serverInfo['enabled']) ? 'checked' : '' }}>
                            <span class="form-check-label fw-semibold">{{ __('Enable Server Info') }}</span>
                        </label>
                    </div>

                    <button type="button" class="btn btn-secondary mb-3" onclick="addRow()">{{ __('+ Add Row') }}</button>

                    <table class="table table-bordered align-middle" id="serverInfoTable">
                        <thead class="table-light">
                        <tr>
                            <th>{{ __('Icon') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Value') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($serverInfo['data'] ?? [] as $row)
                            <tr>
                                <td><input type="text" class="form-control form-control-sm" value="{{ $row['icon'] ?? '' }}" data-key="icon"></td>
                                <td><input type="text" class="form-control form-control-sm" value="{{ $row['name'] ?? '' }}" data-key="name"></td>
                                <td><input type="text" class="form-control form-control-sm" value="{{ $row['value'] ?? '' }}" data-key="value"></td>
                                <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">{{ __('Remove') }}</button></td>
                            </tr>
                        @empty
                            <tr>
                                <td><input type="text" class="form-control form-control-sm" data-key="icon"></td>
                                <td><input type="text" class="form-control form-control-sm" data-key="name"></td>
                                <td><input type="text" class="form-control form-control-sm" data-key="value"></td>
                                <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">{{ __('Remove') }}</button></td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>

                    <input type="hidden" id="server_info" name="server_info">
                </div>


                {{-- ===================== CUSTOM WIDGETS TAB ===================== --}}
                <div class="tab-pane fade" id="custom-widgets" role="tabpanel">

                    <p class="text-muted small mb-3">
                        {{ __('Each custom widget has a unique key, an optional Blade template, an optional SQL query, and an enable toggle.') }}
                    </p>

                    <button type="button" class="btn btn-secondary mb-3" onclick="addCw()">{{ __('+ Add Custom Widget') }}</button>

                    <div id="customWidgetsContainer">
                        @foreach($customWidgets as $widgetKey => $widget)
                            <div class="card mb-3 custom-widget-item">
                                <div class="card-header">
                                    <span class="fw-semibold custom-widget-title">{{ $widgetKey }}</span>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-check">
                                            <input class="form-check-input" type="checkbox" data-cw="enabled"
                                                {{ $widget['enabled'] ?? false ? 'checked' : '' }}>
                                            <span class="form-check-label fw-semibold">{{ __('Enabled') }}</span>
                                        </label>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Widget Key') }}</label>
                                        <input type="text" class="form-control" data-cw="key"
                                               value="{{ $widgetKey }}"
                                               oninput="updateCwTitle(this)"
                                               placeholder="e.g. owned_titles">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Template') }}</label>
                                        <input type="text" class="form-control" data-cw="template"
                                               value="{{ $widget['template'] ?? '' }}"
                                               placeholder="e.g. partials.character-owned-titles">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('SQL Query') }}</label>
                                        <textarea class="form-control font-monospace" data-cw="query"
                                                  rows="6"
                                                  placeholder="SELECT ...">{{ $widget['query'] ?? '' }}</textarea>
                                        <div class="form-text">{{ __('Use :Limit and :CharID as named parameters where needed.') }}</div>
                                    </div>
                                    <div class="d-flex justify-content-start mt-3">
                                        <button type="button" class="btn btn-sm btn-danger" onclick="removeCw(this)">{{ __('Remove') }}</button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <input type="hidden" id="custom" name="custom">
                </div>

            </div>{{-- .tab-content --}}

            {{-- Hidden inputs for widget data --}}
        <input type="hidden" id="globals_history" name="globals_history">
        <input type="hidden" id="unique_history" name="unique_history">
        <input type="hidden" id="top_player" name="top_player">
        <input type="hidden" id="top_guild" name="top_guild">
        <input type="hidden" id="sox_plus" name="sox_plus">
        <input type="hidden" id="sox_drop" name="sox_drop">
        <input type="hidden" id="pvp_kills" name="pvp_kills">
        <input type="hidden" id="job_kills" name="job_kills">
        <input type="hidden" id="discord_data" name="discord">

        <div class="mt-4">
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
            </div>
        </form>
    </div>

    <script>
        // ─── Widgets ──────────────────────────────────────────────────────────────────
        function serializeWidgetToggles() {
            ['globals_history','unique_history','top_player','top_guild','sox_plus','sox_drop','pvp_kills','job_kills']
                .forEach(id => {
                    const enabledElement = document.getElementById(id + '_enabled');
                    const limitElement = document.getElementById(id + '_limit');
                    const hiddenElement = document.getElementById(id);
                    
                    if (!hiddenElement) {
                        console.error('Missing hidden input for widget:', id);
                        return;
                    }
                    
                    hiddenElement.value = JSON.stringify({
                        enabled: enabledElement ? enabledElement.checked : false,
                        limit: limitElement ? parseInt(limitElement.value) || 5 : 5,
                    });
                });
        }

        // ─── Discord ──────────────────────────────────────────────────────────────────
        function serializeDiscord() {
            console.log('=== serializeDiscord() called ===');
            const discordInput = document.getElementById('discord_data');
            const enabled = document.getElementById('discord_enabled').checked;
            const serverId = document.getElementById('discord_server_id').value;
            const channelId = document.getElementById('discord_channel_id').value;
            const theme = document.getElementById('discord_theme').value;
            
            discordInput.value = JSON.stringify({
                enabled: enabled,
                server_id: serverId,
                channel_id: channelId,
                theme: theme
            });
        }

        // ─── Event Schedule ───────────────────────────────────────────────────────────
        const weekDays = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];

        function showAddForm() {
            document.getElementById('addEventForm').style.display = '';
            document.getElementById('showAddFormBtn').style.display = 'none';
            // reset form
            document.getElementById('newEventType').value = 'original';
            document.getElementById('newOrigId').value = '';
            document.getElementById('newOrigName').value = '';
            document.getElementById('newCeName').value = '';
            document.getElementById('newCeDuration').value = '3600';
            document.getElementById('newCeEnabled').checked = true;
            document.querySelectorAll('#newCeDays input[type=checkbox]').forEach(cb => cb.checked = false);
            document.querySelectorAll('#newCeHours input[type=checkbox]').forEach(cb => cb.checked = false);
            document.querySelectorAll('#newCeMinutes input[type=checkbox]').forEach(cb => cb.checked = false);
            switchEventType('original');
        }

        function hideAddForm() {
            document.getElementById('addEventForm').style.display = 'none';
            document.getElementById('showAddFormBtn').style.display = '';
        }

        function switchEventType(type) {
            document.getElementById('newEventOriginalFields').style.display = type === 'original' ? '' : 'none';
            document.getElementById('newEventCustomFields').style.display   = type === 'custom'   ? '' : 'none';
        }

        function confirmAddEvent() {
            const type = document.getElementById('newEventType').value;
            const container = document.getElementById('eventsContainer');
            const card = document.createElement('div');

            if (type === 'original') {
                const id   = document.getElementById('newOrigId').value.trim();
                const name = document.getElementById('newOrigName').value.trim();
                if (!id) { alert('{{ __("Please enter an Event ID.") }}'); return; }
                card.className = 'card mb-3 event-item event-original';
                card.innerHTML = `
                    <div class="card-header">
                        <span class="badge bg-primary me-2">{{ __('Original') }}</span>
                        <span class="fw-semibold event-card-title">${name || 'ID: ' + id}</span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Event ID') }}</label>
                            <input type="number" class="form-control" data-orig="id" value="${id}"
                                   oninput="updateOriginalTitle(this)">
                            <div class="form-text">{{ __('Use the numeric game event ID') }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Display Name') }}</label>
                            <input type="text" class="form-control" data-orig="name" value="${name}"
                                   oninput="updateOriginalTitle(this)">
                        </div>
                        <div class="d-flex justify-content-start">
                            <button type="button" class="btn btn-sm btn-danger" onclick="removeEvent(this)">{{ __('Remove') }}</button>
                        </div>
                    </div>`;
            } else {
                const name     = document.getElementById('newCeName').value.trim();
                const enabled  = document.getElementById('newCeEnabled').checked;
                const duration = document.getElementById('newCeDuration').value;
                const days     = Array.from(document.querySelectorAll('#newCeDays input:checked'))
                    .map(cb => cb.closest('.form-check-inline').querySelector('label').textContent.trim());
                const hours    = Array.from(document.querySelectorAll('#newCeHours input:checked'))
                    .map(cb => cb.getAttribute('data-ce-hour'));
                const minutes  = Array.from(document.querySelectorAll('#newCeMinutes input:checked'))
                    .map(cb => cb.getAttribute('data-ce-minute'));
                
                card.className = 'card mb-3 event-item event-custom';
                card.innerHTML = `
                    <div class="card-header">
                        <span class="badge bg-success me-2">{{ __('Custom') }}</span>
                        <span class="fw-semibold event-card-title">${name || '{{ __("Unnamed") }}'}</span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-check">
                                <input class="form-check-input" type="checkbox" data-ce="enabled" ${enabled ? 'checked' : ''}>
                                <span class="form-check-label">{{ __('Enabled') }}</span>
                            </label>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Event Name') }}</label>
                            <input type="text" class="form-control" data-ce="name" value="${name}"
                                   oninput="updateCustomTitle(this)">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Hours (0-23)') }}</label>
                            <div class="d-flex flex-wrap gap-2">
                                ${Array.from({length: 24}, (_, h) => `
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox"
                                               data-ce-hour="${h}" ${hours.includes(h.toString()) ? 'checked' : ''}>
                                        <label class="form-check-label">${h}</label>
                                    </div>`).join('')}
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Minutes (0-59)') }}</label>
                            <div class="d-flex flex-wrap gap-2">
                                ${Array.from({length: 60}, (_, m) => `
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox"
                                               data-ce-minute="${m}" ${minutes.includes(m.toString()) ? 'checked' : ''}>
                                        <label class="form-check-label">${m}</label>
                                    </div>`).join('')}
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Duration (seconds)') }}</label>
                            <input type="number" class="form-control" min="0" data-ce="duration" value="${duration}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Days') }}</label>
                            <div class="d-flex flex-wrap gap-2">
                                ${weekDays.map(d => `
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" data-ce-day="${d}" ${days.includes(d) ? 'checked' : ''}>
                                        <label class="form-check-label">${d}</label>
                                    </div>`).join('')}
                            </div>
                        </div>
                        <div class="d-flex justify-content-start">
                            <button type="button" class="btn btn-sm btn-danger" onclick="removeEvent(this)">{{ __('Remove') }}</button>
                        </div>
                    </div>`;
            }

            container.appendChild(card);
            hideAddForm();
        }

        function removeEvent(btn) {
            btn.closest('.event-item').remove();
        }

        function updateOriginalTitle(input) {
            const card = input.closest('.event-item');
            const id   = card.querySelector('[data-orig="id"]').value;
            const name = card.querySelector('[data-orig="name"]').value;
            card.querySelector('.event-card-title').textContent = name ? `${name} (ID: ${id})` : `ID: ${id}`;
        }

        function updateCustomTitle(input) {
            const card = input.closest('.event-item');
            const name = card.querySelector('[data-ce="name"]').value;
            card.querySelector('.event-card-title').textContent = name || '{{ __("Unnamed") }}';
        }

        function serializeEventSchedule() {
            const names  = {};
            const custom = {};
            let customIdx = 101;

            document.querySelectorAll('#eventsContainer .event-item').forEach(card => {
                if (card.classList.contains('event-original')) {
                    const id   = card.querySelector('[data-orig="id"]').value;
                    const name = card.querySelector('[data-orig="name"]').value;
                    if (id) names[id] = name;
                } else if (card.classList.contains('event-custom')) {
                    const selectedDays = Array.from(card.querySelectorAll('[data-ce-day]'))
                        .filter(cb => cb.checked)
                        .map(cb => cb.getAttribute('data-ce-day'));

                    const selectedHours = Array.from(card.querySelectorAll('[data-ce-hour]'))
                        .filter(cb => cb.checked)
                        .map(cb => parseInt(cb.getAttribute('data-ce-hour')));

                    const selectedMinutes = Array.from(card.querySelectorAll('[data-ce-minute]'))
                        .filter(cb => cb.checked)
                        .map(cb => parseInt(cb.getAttribute('data-ce-minute')));

                    // Handle single integer values to match config format exactly
                    const hour = selectedHours.length === 0 ? 8 : selectedHours[0];
                    const min = selectedMinutes.length === 0 ? 0 : selectedMinutes[0];

                    custom[customIdx++] = {
                        enabled:  card.querySelector('[data-ce="enabled"]').checked,
                        name:     card.querySelector('[data-ce="name"]').value,
                        days:     selectedDays,
                        hour:     hour,
                        min:      min,
                        duration: parseInt(card.querySelector('[data-ce="duration"]').value) || 3600,
                    };
                }
            });

            document.getElementById('event_schedule').value = JSON.stringify({
                enabled: document.getElementById('event_schedule_enabled').checked,
                names:   names,
                custom:  custom,
            });
        }

        // ─── Fortress War ─────────────────────────────────────────────────────────────
        function serializeFortressWar() {
            const names = {};
            document.querySelectorAll('#fortressTable tbody tr').forEach(tr => {
                const id    = tr.querySelector('[data-fw="id"]').value;
                const name  = tr.querySelector('[data-fw="name"]').value;
                const image = tr.querySelector('[data-fw="image"]').value;
                if (id) names[id] = { name, image };
            });

            document.getElementById('fortress_war').value = JSON.stringify({
                enabled: document.getElementById('fw_enabled').checked,
                names:   names,
            });
        }

        // ─── Server Info ──────────────────────────────────────────────────────────────
        function addRow() {
            const tbody = document.getElementById('serverInfoTable').querySelector('tbody');
            const row   = tbody.insertRow();
            row.innerHTML = `
                <td><input type="text" class="form-control form-control-sm" data-key="icon"></td>
                <td><input type="text" class="form-control form-control-sm" data-key="name"></td>
                <td><input type="text" class="form-control form-control-sm" data-key="value"></td>
                <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">{{ __('Remove') }}</button></td>
            `;
        }

        function removeRow(btn) {
            btn.closest('tr').remove();
        }

        function serializeServerInfo() {
            const rows = Array.from(document.querySelectorAll('#serverInfoTable tbody tr')).map(tr => ({
                icon:  tr.querySelector('[data-key="icon"]').value,
                name:  tr.querySelector('[data-key="name"]').value,
                value: tr.querySelector('[data-key="value"]').value,
            })).filter(r => r.icon || r.name || r.value);

            document.getElementById('server_info').value = JSON.stringify({
                enabled: document.getElementById('server_info_enabled').checked,
                data:    rows,
            });
        }

        // ─── Custom Widgets ───────────────────────────────────────────────────────────
        function updateCwTitle(input) {
            const card = input.closest('.custom-widget-item');
            card.querySelector('.custom-widget-title').textContent = input.value || '{{ __("Unnamed") }}';
        }

        function addCw() {
            const container = document.getElementById('customWidgetsContainer');
            const card = document.createElement('div');
            card.className = 'card mb-3 custom-widget-item border-success';
            card.innerHTML = `
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <span class="fw-semibold custom-widget-title">{{ __('New Widget') }}</span>
                    <button type="button" class="btn-close btn-close-white" onclick="removeCw(this)"></button>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-check">
                            <input class="form-check-input" type="checkbox" data-cw="enabled">
                            <span class="form-check-label fw-semibold">{{ __('Enabled') }}</span>
                        </label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Widget Key') }}</label>
                        <input type="text" class="form-control" data-cw="key"
                               oninput="updateCwTitle(this)"
                               placeholder="e.g. owned_titles">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Template') }}</label>
                        <input type="text" class="form-control" data-cw="template"
                               placeholder="e.g. partials.my-widget">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('SQL Query') }}</label>
                        <textarea class="form-control font-monospace" data-cw="query"
                                  rows="6"
                                  placeholder="SELECT ..."></textarea>
                        <div class="form-text">{{ __('Use :Limit and :CharID as named parameters where needed.') }}</div>
                    </div>
                    <div class="d-flex justify-content-start mt-3">
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeCw(this)">{{ __('Remove') }}</button>
                    </div>
                </div>`;
            container.appendChild(card);
        }

        function removeCw(btn) {
            btn.closest('.custom-widget-item').remove();
        }

        function serializeCustomWidgets() {
            const result = {};
            document.querySelectorAll('#customWidgetsContainer .custom-widget-item').forEach(card => {
                const key      = card.querySelector('[data-cw="key"]').value.trim();
                const template = card.querySelector('[data-cw="template"]').value.trim();
                const query    = card.querySelector('[data-cw="query"]').value.trim();
                const enabled  = card.querySelector('[data-cw="enabled"]').checked;
                if (key) result[key] = { enabled, template, query };
            });
            document.getElementById('custom').value = JSON.stringify(result);
        }

        function serializeWidgets() {
            try {
                serializeWidgetToggles();
            } catch (e) {
                console.error('Error in serializeWidgetToggles:', e);
            }

            try {
                serializeDiscord();
            } catch (e) {
                console.error('Error in serializeDiscord:', e);
            }

            try {
                serializeEventSchedule();
            } catch (e) {
                console.error('Error in serializeEventSchedule:', e);
            }

            try {
                serializeFortressWar();
            } catch (e) {
                console.error('Error in serializeFortressWar:', e);
            }

            try {
                serializeServerInfo();
            } catch (e) {
                console.error('Error in serializeServerInfo:', e);
            }

            try {
                serializeCustomWidgets();
            } catch (e) {
                console.error('Error in serializeCustomWidgets:', e);
            }
        }

        // Form submission handler
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('settingsForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    // Serialize all form data SYNCHRONOUSLY before submission
                    serializeWidgetToggles();
                    serializeDiscord();
                    serializeEventSchedule();
                    serializeFortressWar();
                    serializeServerInfo();
                    serializeCustomWidgets();
                    
                    // Now submit immediately
                    form.submit();
                });
            }
        });

    </script>
@endsection
