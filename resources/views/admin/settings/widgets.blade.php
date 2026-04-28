@extends('admin.layouts.app')
@section('title', __('Widgets Settings'))

@section('content')
    <div class="container">

        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">{{ __('Widgets Settings') }}</h1>
            <form action="{{ route('admin.settings.clear-cache') }}" method="POST"
                  onsubmit="return confirm('{{ __('Are you sure you want to clear all caches?') }}')">
                @csrf
                <button type="submit" class="btn btn-danger">{{ __('Clear All Cache') }}</button>
            </form>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <ul class="nav nav-tabs mb-3" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-widgets" type="button" role="tab">{{ __('Widgets') }}</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-discord" type="button" role="tab">{{ __('Discord') }}</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-server-info" type="button" role="tab">{{ __('Server Info') }}</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-schedule" type="button" role="tab">{{ __('Event Schedule') }}</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-fortress-war" type="button" role="tab">{{ __('Fortress War') }}</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-custom-widgets" type="button" role="tab">{{ __('Custom Widgets') }}</button>
            </li>
        </ul>

        <form method="POST" action="{{ route('admin.settings.update') }}" id="widgetsForm">
            @csrf

            <div class="tab-content">

                {{-- ===================== WIDGETS ===================== --}}
                <div class="tab-pane fade show active" id="tab-widgets" role="tabpanel">

                    <h5 class="fw-semibold mb-3">{{ __('Widget Toggles') }}</h5>
                    <p class="text-muted small mb-3">{{ __('Enable each widget and set how many rows it should display.') }}</p>

                    @foreach($limitWidgets as $widget)
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                       id="{{ $widget['id'] }}_enabled"
                                    {{ !empty($widgets[$widget['id']]['enabled']) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="{{ $widget['id'] }}_enabled">
                                    {{ __($widget['label']) }}
                                </label>
                            </div>
                            <div class="mt-1">
                                <label class="form-label small text-muted mb-1">{{ __('Limit') }}</label>
                                <input type="number" class="form-control form-control-sm"
                                       style="max-width: 100px;"
                                       id="{{ $widget['id'] }}_limit"
                                       value="{{ $widgets[$widget['id']]['limit'] ?? 5 }}"
                                       min="1" max="50">
                            </div>
                        </div>
                    @endforeach

                    @foreach($limitWidgets as $widget)
                        <input type="hidden" id="{{ $widget['id'] }}" name="{{ $widget['id'] }}">
                    @endforeach
                </div>

                {{-- ===================== DISCORD ===================== --}}
                <div class="tab-pane fade" id="tab-discord" role="tabpanel">
                    <input type="hidden" id="discord_payload" name="discord">

                    <h5 class="fw-semibold mb-3">{{ __('Discord Widget') }}</h5>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="discord_enabled"
                                {{ !empty($discord['enabled']) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="discord_enabled">{{ __('Enable Discord Widget') }}</label>
                        </div>
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

                {{-- ===================== SERVER INFO ===================== --}}
                <div class="tab-pane fade" id="tab-server-info" role="tabpanel">
                    <input type="hidden" id="server_info_payload" name="server_info">

                    <h5 class="fw-semibold mb-3">{{ __('Server Info Widget') }}</h5>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="server_info_enabled"
                                {{ !empty($serverInfo['enabled']) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="server_info_enabled">{{ __('Enable Server Info') }}</label>
                        </div>
                    </div>

                    <button type="button" class="btn btn-secondary btn-sm mb-3" onclick="addServerInfoRow()">{{ __('+ Add Row') }}</button>

                    <table class="table table-bordered align-middle" id="serverInfoTable">
                        <thead class="table-light">
                        <tr>
                            <th>{{ __('Icon (HTML)') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Value') }}</th>
                            <th style="width: 80px;">{{ __('Action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($serverInfo['data'] ?? [] as $row)
                            <tr>
                                <td><input type="text" class="form-control form-control-sm" data-si="icon"  value="{{ $row['icon']  ?? '' }}"></td>
                                <td><input type="text" class="form-control form-control-sm" data-si="name"  value="{{ $row['name']  ?? '' }}"></td>
                                <td><input type="text" class="form-control form-control-sm" data-si="value" value="{{ $row['value'] ?? '' }}"></td>
                                <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">{{ __('Remove') }}</button></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- ===================== EVENT SCHEDULE ===================== --}}
                <div class="tab-pane fade" id="tab-schedule" role="tabpanel">
                    <input type="hidden" id="event_schedule_payload" name="event_schedule">

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="event_schedule_enabled"
                                {{ !empty($eventSchedule['enabled']) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="event_schedule_enabled">{{ __('Enable Event Schedule') }}</label>
                        </div>
                        <div class="form-text">{{ __('Add original game events (by Event ID) or fully custom events with their own schedule.') }}</div>
                    </div>

                    <button type="button" class="btn btn-secondary btn-sm mb-3" id="showAddEventBtn" onclick="showAddEventForm()">
                        {{ __('+ Add Event') }}
                    </button>

                    <div id="eventsContainer">
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
                                    <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.event-item').remove()">{{ __('Remove') }}</button>
                                </div>
                            </div>
                        @endforeach

                        @foreach($eventSchedule['custom'] ?? [] as $key => $event)
                            <div class="card mb-3 event-item event-custom">
                                <div class="card-header">
                                    <span class="badge bg-success me-2">{{ __('Custom') }}</span>
                                    <span class="fw-semibold event-card-title">{{ $event['name'] ?? __('Unnamed') }}</span>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" data-ce="enabled"
                                                {{ !empty($event['enabled']) ? 'checked' : '' }}>
                                            <label class="form-check-label">{{ __('Enabled') }}</label>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Event Name') }}</label>
                                        <input type="text" class="form-control" data-ce="name"
                                               value="{{ $event['name'] ?? '' }}" oninput="updateCustomTitle(this)">
                                    </div>

                                    {{-- Multiple times --}}
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Times') }}</label>
                                        <div class="form-text mb-2">{{ __('Add one or more start times for this event.') }}</div>
                                        <button type="button" class="btn btn-secondary btn-sm mb-2" onclick="addTimeRow(this)">{{ __('+ Add Time') }}</button>
                                        <table class="table table-bordered align-middle table-sm times-table">
                                            <thead class="table-light">
                                            <tr>
                                                <th style="width: 120px;">{{ __('Hour (0-23)') }}</th>
                                                <th style="width: 120px;">{{ __('Minute (0-59)') }}</th>
                                                <th style="width: 80px;">{{ __('Action') }}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @php
                                                $hours = is_array($event['hour'] ?? null) ? $event['hour'] : [$event['hour'] ?? 0];
                                                $mins  = is_array($event['min']  ?? null) ? $event['min']  : [$event['min']  ?? 0];
                                            @endphp
                                            @foreach($hours as $ti => $h)
                                                <tr>
                                                    <td><input type="number" class="form-control form-control-sm" data-ce-time="hour" min="0" max="23" value="{{ $h }}"></td>
                                                    <td><input type="number" class="form-control form-control-sm" data-ce-time="min"  min="0" max="59" value="{{ $mins[$ti] ?? 0 }}"></td>
                                                    <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">{{ __('Remove') }}</button></td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Duration (seconds)') }}</label>
                                        <input type="number" class="form-control" data-ce="duration"
                                               min="0" value="{{ $event['duration'] ?? 3600 }}">
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
                                    <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.event-item').remove()">{{ __('Remove') }}</button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Add Event Form --}}
                    <div id="addEventForm" class="card border-primary mb-3" style="display: none;">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <span class="fw-semibold">{{ __('New Event') }}</span>
                            <button type="button" class="btn-close btn-close-white" onclick="hideAddEventForm()"></button>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">{{ __('Event Type') }}</label>
                                <select class="form-select" id="newEventType" onchange="switchEventType(this.value)">
                                    <option value="original">{{ __('Original — built-in game event by ID') }}</option>
                                    <option value="custom">{{ __('Custom — custom name, days & schedule') }}</option>
                                </select>
                            </div>

                            <div id="newEventOriginalFields">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Event ID') }}</label>
                                    <input type="number" class="form-control" id="newOrigId" placeholder="e.g. 10">
                                    <div class="form-text">{{ __('Numeric game event ID') }}</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Display Name') }}</label>
                                    <input type="text" class="form-control" id="newOrigName" placeholder="e.g. Roc">
                                </div>
                            </div>

                            <div id="newEventCustomFields" style="display: none;">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="newCeEnabled" checked>
                                        <label class="form-check-label">{{ __('Enabled') }}</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Event Name') }}</label>
                                    <input type="text" class="form-control" id="newCeName">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Times') }}</label>
                                    <div class="form-text mb-2">{{ __('Add one or more start times.') }}</div>
                                    <button type="button" class="btn btn-secondary btn-sm mb-2" onclick="addNewFormTimeRow()">{{ __('+ Add Time') }}</button>
                                    <table class="table table-bordered align-middle table-sm" id="newCeTimesTable">
                                        <thead class="table-light">
                                        <tr>
                                            <th style="width: 120px;">{{ __('Hour (0-23)') }}</th>
                                            <th style="width: 120px;">{{ __('Minute (0-59)') }}</th>
                                            <th style="width: 80px;">{{ __('Action') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td><input type="number" class="form-control form-control-sm" data-new-time="hour" min="0" max="23" value="0"></td>
                                            <td><input type="number" class="form-control form-control-sm" data-new-time="min"  min="0" max="59" value="0"></td>
                                            <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">{{ __('Remove') }}</button></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Duration (seconds)') }}</label>
                                    <input type="number" class="form-control" id="newCeDuration" min="0" value="3600">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Days') }}</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach(['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $day)
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" id="newDay{{ $day }}"
                                                       data-new-day="{{ $day }}">
                                                <label class="form-check-label" for="newDay{{ $day }}">{{ $day }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-primary btn-sm" onclick="confirmAddEvent()">{{ __('Add') }}</button>
                                <button type="button" class="btn btn-secondary btn-sm" onclick="hideAddEventForm()">{{ __('Cancel') }}</button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===================== FORTRESS WAR ===================== --}}
                <div class="tab-pane fade" id="tab-fortress-war" role="tabpanel">
                    <input type="hidden" id="fortress_war_payload" name="fortress_war">

                    <h5 class="fw-semibold mb-3">{{ __('Fortress War Widget') }}</h5>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="fw_enabled"
                                {{ !empty($fortressWar['enabled']) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="fw_enabled">{{ __('Enable Fortress War Widget') }}</label>
                        </div>
                        <div class="form-text">{{ __('Customize the display name and image path for each fortress. Fortress IDs are fixed.') }}</div>
                    </div>

                    <table class="table table-bordered align-middle" id="fortressTable">
                        <thead class="table-light">
                        <tr>
                            <th style="width: 80px;" class="text-center">{{ __('ID') }}</th>
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
                                <td><input type="text" class="form-control form-control-sm" data-fw="name"  value="{{ $fort['name']  ?? '' }}"></td>
                                <td><input type="text" class="form-control form-control-sm" data-fw="image" value="{{ $fort['image'] ?? '' }}"></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- ===================== CUSTOM WIDGETS ===================== --}}
                <div class="tab-pane fade" id="tab-custom-widgets" role="tabpanel">
                    <input type="hidden" id="custom_payload" name="custom">

                    <p class="text-muted small mb-3">{{ __('Each custom widget has a unique key, an optional Blade template, an optional SQL query, and an enable toggle.') }}</p>

                    <button type="button" class="btn btn-secondary btn-sm mb-3" onclick="addCustomWidget()">{{ __('+ Add Custom Widget') }}</button>

                    <div id="customWidgetsContainer">
                        @foreach($customWidgets as $widgetKey => $widget)
                            <div class="card mb-3 custom-widget-item">
                                <div class="card-header fw-semibold custom-widget-title">{{ $widgetKey }}</div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" data-cw="enabled"
                                                {{ !empty($widget['enabled']) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-semibold">{{ __('Enabled') }}</label>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Widget Key') }}</label>
                                        <input type="text" class="form-control" data-cw="key"
                                               value="{{ $widgetKey }}" oninput="updateCustomWidgetTitle(this)"
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
                                                  rows="6" placeholder="SELECT ...">{{ $widget['query'] ?? '' }}</textarea>
                                        <div class="form-text">{{ __('Use :Limit and :CharID as named parameters where needed.') }}</div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.custom-widget-item').remove()">{{ __('Remove') }}</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
            </div>
        </form>
    </div>

    <script>
        // ─── Shared row remove ────────────────────────────────────────────────────────
        function removeRow(btn) {
            btn.closest('tr').remove();
        }

        // ─── Server Info ──────────────────────────────────────────────────────────────
        function addServerInfoRow() {
            const tbody = document.querySelector('#serverInfoTable tbody');
            const tr    = document.createElement('tr');
            tr.innerHTML = `
            <td><input type="text" class="form-control form-control-sm" data-si="icon"></td>
            <td><input type="text" class="form-control form-control-sm" data-si="name"></td>
            <td><input type="text" class="form-control form-control-sm" data-si="value"></td>
            <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">{{ __('Remove') }}</button></td>`;
            tbody.appendChild(tr);
        }

        // ─── Event Schedule ───────────────────────────────────────────────────────────
        function showAddEventForm() {
            document.getElementById('addEventForm').style.display    = '';
            document.getElementById('showAddEventBtn').style.display = 'none';
            document.getElementById('newEventType').value   = 'original';
            document.getElementById('newOrigId').value      = '';
            document.getElementById('newOrigName').value    = '';
            document.getElementById('newCeName').value      = '';
            document.getElementById('newCeDuration').value  = '3600';
            document.getElementById('newCeEnabled').checked = true;
            document.querySelectorAll('[data-new-day]').forEach(cb => cb.checked = false);
            // Reset times table to one empty row
            const tbody = document.querySelector('#newCeTimesTable tbody');
            tbody.innerHTML = `
            <tr>
                <td><input type="number" class="form-control form-control-sm" data-new-time="hour" min="0" max="23" value="0"></td>
                <td><input type="number" class="form-control form-control-sm" data-new-time="min"  min="0" max="59" value="0"></td>
                <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">{{ __('Remove') }}</button></td>
            </tr>`;
            switchEventType('original');
        }

        function hideAddEventForm() {
            document.getElementById('addEventForm').style.display    = 'none';
            document.getElementById('showAddEventBtn').style.display = '';
        }

        function switchEventType(type) {
            document.getElementById('newEventOriginalFields').style.display = type === 'original' ? '' : 'none';
            document.getElementById('newEventCustomFields').style.display   = type === 'custom'   ? '' : 'none';
        }

        function addNewFormTimeRow() {
            const tbody = document.querySelector('#newCeTimesTable tbody');
            const tr    = document.createElement('tr');
            tr.innerHTML = `
            <td><input type="number" class="form-control form-control-sm" data-new-time="hour" min="0" max="23" value="0"></td>
            <td><input type="number" class="form-control form-control-sm" data-new-time="min"  min="0" max="59" value="0"></td>
            <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">{{ __('Remove') }}</button></td>`;
            tbody.appendChild(tr);
        }

        function addTimeRow(btn) {
            const table = btn.closest('.card-body').querySelector('.times-table tbody');
            const tr    = document.createElement('tr');
            tr.innerHTML = `
            <td><input type="number" class="form-control form-control-sm" data-ce-time="hour" min="0" max="23" value="0"></td>
            <td><input type="number" class="form-control form-control-sm" data-ce-time="min"  min="0" max="59" value="0"></td>
            <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">{{ __('Remove') }}</button></td>`;
            table.appendChild(tr);
        }

        function buildCustomEventCard(name, enabled, times, duration, days) {
            const daysHtml = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday']
                .map(d => `<div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" data-ce-day="${d}" ${days.includes(d) ? 'checked' : ''}>
                <label class="form-check-label">${d}</label>
            </div>`).join('');

            const timesRows = times.map(t => `
            <tr>
                <td><input type="number" class="form-control form-control-sm" data-ce-time="hour" min="0" max="23" value="${t.hour}"></td>
                <td><input type="number" class="form-control form-control-sm" data-ce-time="min"  min="0" max="59" value="${t.min}"></td>
                <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">{{ __('Remove') }}</button></td>
            </tr>`).join('');

            return `
            <div class="card-header">
                <span class="badge bg-success me-2">{{ __('Custom') }}</span>
                <span class="fw-semibold event-card-title">${name || '{{ __("Unnamed") }}'}</span>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" data-ce="enabled" ${enabled ? 'checked' : ''}>
                        <label class="form-check-label">{{ __('Enabled') }}</label>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('Event Name') }}</label>
                    <input type="text" class="form-control" data-ce="name" value="${name}" oninput="updateCustomTitle(this)">
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('Times') }}</label>
                    <div class="form-text mb-2">{{ __('Add one or more start times for this event.') }}</div>
                    <button type="button" class="btn btn-secondary btn-sm mb-2" onclick="addTimeRow(this)">{{ __('+ Add Time') }}</button>
                    <table class="table table-bordered align-middle table-sm times-table">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 120px;">{{ __('Hour (0-23)') }}</th>
                                <th style="width: 120px;">{{ __('Minute (0-59)') }}</th>
                                <th style="width: 80px;">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>${timesRows}</tbody>
                    </table>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('Duration (seconds)') }}</label>
                    <input type="number" class="form-control" data-ce="duration" min="0" value="${duration}">
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('Days') }}</label>
                    <div class="d-flex flex-wrap gap-2">${daysHtml}</div>
                </div>
                <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.event-item').remove()">{{ __('Remove') }}</button>
            </div>`;
        }

        function confirmAddEvent() {
            const type      = document.getElementById('newEventType').value;
            const container = document.getElementById('eventsContainer');
            const card      = document.createElement('div');

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
                        <input type="number" class="form-control" data-orig="id" value="${id}" oninput="updateOriginalTitle(this)">
                        <div class="form-text">{{ __('Use the numeric game event ID') }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Display Name') }}</label>
                        <input type="text" class="form-control" data-orig="name" value="${name}" oninput="updateOriginalTitle(this)">
                    </div>
                    <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.event-item').remove()">{{ __('Remove') }}</button>
                </div>`;
            } else {
                const name     = document.getElementById('newCeName').value.trim();
                const duration = parseInt(document.getElementById('newCeDuration').value) || 3600;
                const enabled  = document.getElementById('newCeEnabled').checked;
                const days     = Array.from(document.querySelectorAll('[data-new-day]:checked')).map(cb => cb.dataset.newDay);
                const times    = Array.from(document.querySelectorAll('#newCeTimesTable tbody tr')).map(tr => ({
                    hour: parseInt(tr.querySelector('[data-new-time="hour"]').value) || 0,
                    min:  parseInt(tr.querySelector('[data-new-time="min"]').value)  || 0,
                }));
                if (times.length === 0) times.push({ hour: 0, min: 0 });

                card.className = 'card mb-3 event-item event-custom';
                card.innerHTML = buildCustomEventCard(name, enabled, times, duration, days);
            }

            container.appendChild(card);
            hideAddEventForm();
        }

        function updateOriginalTitle(input) {
            const card = input.closest('.event-item');
            const id   = card.querySelector('[data-orig="id"]').value;
            const name = card.querySelector('[data-orig="name"]').value;
            card.querySelector('.event-card-title').textContent = name ? `${name} (ID: ${id})` : `ID: ${id}`;
        }

        function updateCustomTitle(input) {
            const card = input.closest('.event-item');
            card.querySelector('.event-card-title').textContent =
                card.querySelector('[data-ce="name"]').value || '{{ __("Unnamed") }}';
        }

        // ─── Custom Widgets ───────────────────────────────────────────────────────────
        function updateCustomWidgetTitle(input) {
            input.closest('.custom-widget-item').querySelector('.custom-widget-title').textContent =
                input.value || '{{ __("Unnamed") }}';
        }

        function addCustomWidget() {
            const container = document.getElementById('customWidgetsContainer');
            const card = document.createElement('div');
            card.className = 'card mb-3 custom-widget-item';
            card.innerHTML = `
            <div class="card-header fw-semibold custom-widget-title">{{ __('New Widget') }}</div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" data-cw="enabled">
                        <label class="form-check-label fw-semibold">{{ __('Enabled') }}</label>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('Widget Key') }}</label>
                    <input type="text" class="form-control" data-cw="key"
                           oninput="updateCustomWidgetTitle(this)" placeholder="e.g. owned_titles">
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('Template') }}</label>
                    <input type="text" class="form-control" data-cw="template" placeholder="e.g. partials.my-widget">
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('SQL Query') }}</label>
                    <textarea class="form-control font-monospace" data-cw="query" rows="6" placeholder="SELECT ..."></textarea>
                    <div class="form-text">{{ __('Use :Limit and :CharID as named parameters where needed.') }}</div>
                </div>
                <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.custom-widget-item').remove()">{{ __('Remove') }}</button>
            </div>`;
            container.appendChild(card);
        }

        // ─── Serializers ──────────────────────────────────────────────────────────────
        function serializeWidgetToggles() {
            @foreach($limitWidgets as $widget)
            document.getElementById('{{ $widget['id'] }}').value = JSON.stringify({
                enabled: document.getElementById('{{ $widget['id'] }}_enabled')?.checked ?? false,
                limit:   parseInt(document.getElementById('{{ $widget['id'] }}_limit')?.value) || 5,
            });
            @endforeach
        }

        function serializeDiscord() {
            document.getElementById('discord_payload').value = JSON.stringify({
                enabled:    document.getElementById('discord_enabled').checked,
                server_id:  document.getElementById('discord_server_id').value,
                channel_id: document.getElementById('discord_channel_id').value,
                theme:      document.getElementById('discord_theme').value,
            });
        }

        function serializeServerInfo() {
            // Read only surviving DOM rows
            const rows = Array.from(document.querySelectorAll('#serverInfoTable tbody tr'))
                .map(tr => ({
                    icon:  tr.querySelector('[data-si="icon"]').value,
                    name:  tr.querySelector('[data-si="name"]').value,
                    value: tr.querySelector('[data-si="value"]').value,
                }))
                .filter(r => r.icon || r.name || r.value);

            document.getElementById('server_info_payload').value = JSON.stringify({
                enabled: document.getElementById('server_info_enabled').checked,
                data:    rows,
            });
        }

        function serializeEventSchedule() {
            const names  = {};
            const custom = {};
            let   customIdx = 101;

            document.querySelectorAll('#eventsContainer .event-item').forEach(card => {
                if (card.classList.contains('event-original')) {
                    const id   = card.querySelector('[data-orig="id"]').value;
                    const name = card.querySelector('[data-orig="name"]').value;
                    if (id) names[id] = name;

                } else if (card.classList.contains('event-custom')) {
                    // Collect all time rows
                    const timeRows = Array.from(card.querySelectorAll('.times-table tbody tr'));
                    const hours    = timeRows.map(tr => parseInt(tr.querySelector('[data-ce-time="hour"]').value) || 0);
                    const mins     = timeRows.map(tr => parseInt(tr.querySelector('[data-ce-time="min"]').value)  || 0);
                    const days     = Array.from(card.querySelectorAll('[data-ce-day]:checked'))
                        .map(cb => cb.getAttribute('data-ce-day'));

                    custom[customIdx++] = {
                        enabled:  card.querySelector('[data-ce="enabled"]').checked,
                        name:     card.querySelector('[data-ce="name"]').value,
                        days:     days,
                        // Store as arrays when multiple, scalar when single (backward-compat)
                        hour:     hours.length === 1 ? hours[0] : hours,
                        min:      mins.length  === 1 ? mins[0]  : mins,
                        duration: parseInt(card.querySelector('[data-ce="duration"]').value) || 3600,
                    };
                }
            });

            document.getElementById('event_schedule_payload').value = JSON.stringify({
                enabled: document.getElementById('event_schedule_enabled').checked,
                names:   names,
                custom:  custom,
            });
        }

        function serializeFortressWar() {
            const names = {};
            document.querySelectorAll('#fortressTable tbody tr').forEach(tr => {
                const id    = tr.querySelector('[data-fw="id"]').value;
                const name  = tr.querySelector('[data-fw="name"]').value;
                const image = tr.querySelector('[data-fw="image"]').value;
                if (id) names[id] = { name, image };
            });
            document.getElementById('fortress_war_payload').value = JSON.stringify({
                enabled: document.getElementById('fw_enabled').checked,
                names:   names,
            });
        }

        function serializeCustomWidgets() {
            const result = {};
            document.querySelectorAll('#customWidgetsContainer .custom-widget-item').forEach(card => {
                const key = card.querySelector('[data-cw="key"]').value.trim();
                if (!key) return;
                result[key] = {
                    enabled:  card.querySelector('[data-cw="enabled"]').checked,
                    template: card.querySelector('[data-cw="template"]').value.trim(),
                    query:    card.querySelector('[data-cw="query"]').value.trim(),
                };
            });
            document.getElementById('custom_payload').value = JSON.stringify(result);
        }

        document.getElementById('widgetsForm').addEventListener('submit', function () {
            serializeWidgetToggles();
            serializeDiscord();
            serializeServerInfo();
            serializeEventSchedule();
            serializeFortressWar();
            serializeCustomWidgets();
        });
    </script>
@endsection
