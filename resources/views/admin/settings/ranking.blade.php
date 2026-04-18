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
                <button class="nav-link active" id="ranking-menu-tab" data-bs-toggle="tab" data-bs-target="#ranking-menu" type="button" role="tab" aria-controls="ranking-menu" aria-selected="true">
                    {{ __('Ranking Menu') }}
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="ranking-hidden-tab" data-bs-toggle="tab" data-bs-target="#ranking-hidden" type="button" role="tab" aria-controls="ranking-hidden" aria-selected="false">
                    {{ __('Hidden Character') }}
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="ranking-uniques-tab" data-bs-toggle="tab" data-bs-target="#ranking-uniques" type="button" role="tab" aria-controls="ranking-uniques" aria-selected="false">
                    {{ __('Uniques') }}
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="ranking-hwan-tab" data-bs-toggle="tab" data-bs-target="#ranking-hwan" type="button" role="tab" aria-controls="ranking-hwan" aria-selected="false">
                    {{ __('Hwan level') }}
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="ranking-custom-tab" data-bs-toggle="tab" data-bs-target="#ranking-custom" type="button" role="tab" aria-controls="ranking-custom" aria-selected="false">
                    {{ __('Custom Ranking') }}
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="character-tab" data-bs-toggle="tab" data-bs-target="#character" type="button" role="tab" aria-controls="character" aria-selected="false">
                    {{ __('Character') }}
                </button>
            </li>
        </ul>

        <form method="POST" action="{{ route('admin.settings.update') }}" onsubmit="serializeRanking()">
            @csrf
            <input type="hidden" id="ranking_source" value="{{ json_encode($ranking, JSON_UNESCAPED_SLASHES) }}">
            <input type="hidden" id="ranking_payload" name="ranking" value="{{ json_encode($ranking, JSON_UNESCAPED_SLASHES) }}">

            <div class="tab-content" id="settingsTabsContent">
                <div class="tab-pane fade show active" id="ranking-menu" role="tabpanel" aria-labelledby="ranking-menu-tab">
                    <div class="mb-3">
                        <div class="fw-semibold mb-3">{{ __('Ranking Menu') }}</div>
                        <div>
                            @foreach($ranking['menu'] ?? [] as $menuKey => $item)
                                <div class="row align-items-center mb-3">
                                    <div class="col-auto">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" data-rk="menu_enabled" data-key="{{ $menuKey }}" {{ !empty($item['enabled']) ? 'checked' : '' }}>
                                            <label class="form-check-label ms-2">{{ $item['name'] ?? $menuKey }}</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control form-control-sm" data-rk="menu_name" data-key="{{ $menuKey }}" value="{{ $item['name'] ?? '' }}" placeholder="{{ __('Label') }}">
                                    </div>
                                    <input type="hidden" data-rk="menu_route" data-key="{{ $menuKey }}" value="{{ is_array($item['route']) ? json_encode($item['route'], JSON_UNESCAPED_SLASHES) : ($item['route'] ?? '') }}">
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="fw-semibold mb-3">{{ __('Job Ranking Menu') }}</div>
                        <div>
                            @foreach($ranking['job_menu'] ?? [] as $jobKey => $item)
                                <div class="row align-items-center mb-3">
                                    <div class="col-auto">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" data-rk="job_menu_enabled" data-key="{{ $jobKey }}" {{ !empty($item['enabled']) ? 'checked' : '' }}>
                                            <label class="form-check-label ms-2">{{ $item['name'] ?? $jobKey }}</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control form-control-sm" data-rk="job_menu_name" data-key="{{ $jobKey }}" value="{{ $item['name'] ?? '' }}" placeholder="{{ __('Label') }}">
                                    </div>
                                    <input type="hidden" data-rk="job_menu_route" data-key="{{ $jobKey }}" value="{{ is_array($item['route']) ? json_encode($item['route'], JSON_UNESCAPED_SLASHES) : ($item['route'] ?? '') }}">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="ranking-hidden" role="tabpanel" aria-labelledby="ranking-hidden-tab">
                    <div class="mb-3">
                        <div class="fw-semibold mb-3">{{ __('Hidden characters') }}</div>
                        <div class="mb-3">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="addHiddenCharacterRow()">{{ __('+ Add Character') }}</button>
                        </div>
                        <div>
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0" id="hiddenCharactersTable">
                                    <thead class="table-light">
                                    <tr>
                                        <th>{{ __('Hidden characters') }}</th>
                                        <th style="width:120px;">{{ __('Action') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($ranking['hidden']['characters'] ?? [] as $character)
                                        <tr>
                                            <td><input type="text" class="form-control form-control-sm" data-rk="hidden_character" value="{{ $character }}"></td>
                                            <td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()">{{ __('Remove') }}</button></td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="fw-semibold mb-3">{{ __('Hidden guilds') }}</div>
                        <div class="mb-3">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="addHiddenGuildRow()">{{ __('+ Add Guild') }}</button>
                        </div>
                        <div>
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0" id="hiddenGuildsTable">
                                    <thead class="table-light">
                                    <tr>
                                        <th>{{ __('Hidden guilds') }}</th>
                                        <th style="width:120px;">{{ __('Action') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($ranking['hidden']['guilds'] ?? [] as $guild)
                                        <tr>
                                            <td><input type="text" class="form-control form-control-sm" data-rk="hidden_guild" value="{{ $guild }}"></td>
                                            <td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()">{{ __('Remove') }}</button></td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="ranking-uniques" role="tabpanel" aria-labelledby="ranking-uniques-tab">
                    <div class="mb-3">
                        <div class="fw-semibold mb-3">{{ __('Unique Ranking Points') }}</div>
                        <div class="mb-3">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="addUniqueRow()">{{ __('+ Add Unique') }}</button>
                        </div>
                        <div>
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0" id="uniquesTable">
                                    <thead class="table-light">
                                    <tr>
                                        <th>{{ __('Key') }}</th>
                                        <th>{{ __('ID') }}</th>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Image') }}</th>
                                        <th>{{ __('Points') }}</th>
                                        <th style="width:100px;">{{ __('Action') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($ranking['uniques'] ?? [] as $uniqueKey => $unique)
                                        <tr>
                                            <td><input type="text" class="form-control form-control-sm" data-rk="unique_key" value="{{ $uniqueKey }}"></td>
                                            <td><input type="number" class="form-control form-control-sm" data-rk="unique_id" value="{{ $unique['id'] ?? '' }}"></td>
                                            <td><input type="text" class="form-control form-control-sm" data-rk="unique_name" value="{{ $unique['name'] ?? '' }}"></td>
                                            <td><input type="text" class="form-control form-control-sm" data-rk="unique_image" value="{{ $unique['image'] ?? '' }}"></td>
                                            <td><input type="number" class="form-control form-control-sm" data-rk="unique_points" value="{{ $unique['points'] ?? 0 }}"></td>
                                            <td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()">{{ __('Remove') }}</button></td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="ranking-hwan" role="tabpanel" aria-labelledby="ranking-hwan-tab">
                    <div class="mb-3">
                        <div class="fw-semibold mb-3">{{ __('Hwan Level Labels') }}</div>
                        <div class="mb-3">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="addHwanLevelRow()">{{ __('+ Add Hwan Level') }}</button>
                        </div>
                        <div>
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0" id="hwanLevelTable">
                                    <thead class="table-light">
                                    <tr>
                                        <th>{{ __('Race') }}</th>
                                        <th>{{ __('Level') }}</th>
                                        <th>{{ __('Label') }}</th>
                                        <th style="width:120px;">{{ __('Action') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($ranking['hwan_level'] ?? [] as $raceIndex => $levels)
                                        @foreach($levels as $level => $label)
                                            <tr>
                                                <td>
                                                    <select class="form-select form-select-sm" data-rk="hwan_level_race">
                                                        <option value="0" {{ $raceIndex === 0 ? 'selected' : '' }}>{{ __('Chinese') }}</option>
                                                        <option value="1" {{ $raceIndex === 1 ? 'selected' : '' }}>{{ __('Europe') }}</option>
                                                    </select>
                                                </td>
                                                <td><input type="number" class="form-control form-control-sm" min="0" data-rk="hwan_level_level" value="{{ $level }}"></td>
                                                <td><input type="text" class="form-control form-control-sm" data-rk="hwan_level_label" value="{{ $label }}"></td>
                                                <td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()">{{ __('Remove') }}</button></td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="ranking-custom" role="tabpanel" aria-labelledby="ranking-custom-tab">
                    <div class="fw-semibold mb-3">{{ __('Custom Ranking') }}</div>
                    <div class="mb-4">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="addCustomRankCard()">{{ __('+ Add Custom Ranking') }}</button>
                    </div>
                    <div class="alert alert-info mb-4">
                        <p class="mb-2 fw-semibold">{{ __('Custom Ranking Guide') }}</p>
                        <p class="mb-1 small">{{ __('Replace the SQL query to return the expected fields.') }}</p>
                        <p class="mb-1 small">{{ __('Return') }} <code>CharName</code> {{ __('and') }} <code>GuildName</code> {{ __('to render links.') }}</p>
                        <p class="mb-0 small">{{ __('Return') }} <code>RefObjID</code> {{ __('to show the race icon.') }}</p>
                    </div>
                    <div id="customRankingList">
                        @foreach($ranking['custom'] ?? [] as $customKey => $custom)
                            <div class="mb-3 custom-ranking-item">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>{{ $customKey }}</strong>
                                                <div class="text-muted small">{{ $custom['name'] ?? '' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                        <div class="col-12">
                                            <div class="form-check mt-1">
                                                <input class="form-check-input" type="checkbox" data-rk="custom_enabled" id="custom_enabled_{{ $customKey }}" {{ !empty($custom['enabled']) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="custom_enabled_{{ $customKey }}">{{ __('Enabled') }}</label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">{{ __('Key') }}</label>
                                            <input type="text" class="form-control form-control-sm" data-rk="custom_key" value="{{ $customKey }}">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">{{ __('Name') }}</label>
                                            <input type="text" class="form-control form-control-sm" data-rk="custom_name" value="{{ $custom['name'] ?? '' }}">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">{{ __('Query') }}</label>
                                            <textarea class="form-control form-control-sm" data-rk="custom_query" rows="3">{{ $custom['query'] ?? '' }}</textarea>
                                        </div>
                                        <div class="col-12">
                                            <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.custom-ranking-item').remove()">{{ __('Remove') }}</button>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="tab-pane fade" id="character" role="tabpanel" aria-labelledby="character-tab">
                    <div class="mb-3">
                        <div class="fw-semibold mb-3">{{ __('Character Ranking Extras') }}</div>
                        <div>
                            <div class="d-flex flex-column gap-2">
                                @foreach([ 'character_status' => __('Character status'), 'character_build' => __('Character build'), 'character_buff' => __('Character buff'), 'character_job' => __('Character job'), 'character_unique_history' => __('Character unique history'), 'character_global_history' => __('Character global history'), 'character_pvp_kill' => __('Character PvP kill'), 'character_job_kill' => __('Character job kill') ] as $key => $label)
                                    <label class="form-check mb-0">
                                        <input class="form-check-input" type="checkbox" id="extra_{{ $key }}" data-rk="extra" data-key="{{ $key }}" {{ !empty($ranking['extra'][$key]) ? 'checked' : '' }}>
                                        <span class="form-check-label ms-2">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
            </div>
        </form>
    </div>

    <script>
        function addHiddenCharacterRow() {
            const tbody = document.querySelector('#hiddenCharactersTable tbody');
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><input type="text" class="form-control form-control-sm" data-rk="hidden_character"></td>
                <td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()">{{ __('Remove') }}</button></td>
            `;
            tbody.appendChild(row);
        }

        function addHiddenGuildRow() {
            const tbody = document.querySelector('#hiddenGuildsTable tbody');
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><input type="text" class="form-control form-control-sm" data-rk="hidden_guild"></td>
                <td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()">{{ __('Remove') }}</button></td>
            `;
            tbody.appendChild(row);
        }

        function addUniqueRow() {
            const tbody = document.querySelector('#uniquesTable tbody');
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><input type="text" class="form-control form-control-sm" data-rk="unique_key"></td>
                <td><input type="number" class="form-control form-control-sm" data-rk="unique_id"></td>
                <td><input type="text" class="form-control form-control-sm" data-rk="unique_name"></td>
                <td><input type="text" class="form-control form-control-sm" data-rk="unique_image"></td>
                <td><input type="number" class="form-control form-control-sm" data-rk="unique_points"></td>
                <td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()">{{ __('Remove') }}</button></td>
            `;
            tbody.appendChild(row);
        }

        function addCustomRankCard() {
            const container = document.getElementById('customRankingList');
            const card = document.createElement('div');
            card.className = 'mb-3 custom-ranking-item';
            card.innerHTML = `
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ __('New custom ranking') }}</strong>
                                <div class="text-muted small">{{ __('Fill the fields below') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                        <div class="col-12">
                            <div class="form-check mt-1">
                                <input class="form-check-input" type="checkbox" data-rk="custom_enabled">
                                <label class="form-check-label">{{ __('Enabled') }}</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ __('Key') }}</label>
                            <input type="text" class="form-control form-control-sm" data-rk="custom_key">
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ __('Name') }}</label>
                            <input type="text" class="form-control form-control-sm" data-rk="custom_name">
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ __('Query') }}</label>
                            <textarea class="form-control form-control-sm" data-rk="custom_query" rows="3"></textarea>
                        </div>
                        <div class="col-12">
                            <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.custom-ranking-item').remove()">{{ __('Remove') }}</button>
                        </div>
                        </div>
                    </div>
                </div>
            `;
            container.appendChild(card);
        }

        function addHwanLevelRow() {
            const tbody = document.querySelector('#hwanLevelTable tbody');
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <select class="form-select form-select-sm" data-rk="hwan_level_race">
                        <option value="0">{{ __('Chinese') }}</option>
                        <option value="1">{{ __('Europe') }}</option>
                    </select>
                </td>
                <td><input type="number" class="form-control form-control-sm" min="0" data-rk="hwan_level_level" value="0"></td>
                <td><input type="text" class="form-control form-control-sm" data-rk="hwan_level_label" value=""></td>
                <td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()">{{ __('Remove') }}</button></td>
            `;
            tbody.appendChild(row);
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

            const menu = {};
            document.querySelectorAll('[data-rk="menu_enabled"]').forEach(input => {
                const key = input.dataset.key;
                menu[key] = menu[key] || {};
                menu[key].enabled = input.checked;
            });
            document.querySelectorAll('[data-rk="menu_name"]').forEach(input => {
                const key = input.dataset.key;
                menu[key] = menu[key] || {};
                menu[key].name = input.value;
            });
            document.querySelectorAll('[data-rk="menu_route"]').forEach(input => {
                const key = input.dataset.key;
                menu[key] = menu[key] || {};
                try {
                    menu[key].route = JSON.parse(input.value);
                } catch (e) {
                    menu[key].route = input.value;
                }
            });
            ranking.menu = menu;

            const jobMenu = {};
            document.querySelectorAll('[data-rk="job_menu_enabled"]').forEach(input => {
                const key = input.dataset.key;
                jobMenu[key] = jobMenu[key] || {};
                jobMenu[key].enabled = input.checked;
            });
            document.querySelectorAll('[data-rk="job_menu_name"]').forEach(input => {
                const key = input.dataset.key;
                jobMenu[key] = jobMenu[key] || {};
                jobMenu[key].name = input.value;
            });
            document.querySelectorAll('[data-rk="job_menu_route"]').forEach(input => {
                const key = input.dataset.key;
                jobMenu[key] = jobMenu[key] || {};
                try {
                    jobMenu[key].route = JSON.parse(input.value);
                } catch (e) {
                    jobMenu[key].route = input.value;
                }
            });
            ranking.job_menu = jobMenu;

            ranking.hidden = {
                characters: Array.from(document.querySelectorAll('[data-rk="hidden_character"]')).map(input => input.value.trim()).filter(Boolean),
                guilds: Array.from(document.querySelectorAll('[data-rk="hidden_guild"]')).map(input => input.value.trim()).filter(Boolean),
            };

            const uniques = {};
            document.querySelectorAll('#uniquesTable tbody tr').forEach(row => {
                const keyInput = row.querySelector('[data-rk="unique_key"]');
                const key = keyInput ? keyInput.value.trim() : '';
                if (!key) return;
                uniques[key] = {
                    id: parseInt(row.querySelector('[data-rk="unique_id"]').value, 10) || 0,
                    name: row.querySelector('[data-rk="unique_name"]').value,
                    image: row.querySelector('[data-rk="unique_image"]').value,
                    points: parseInt(row.querySelector('[data-rk="unique_points"]').value, 10) || 0,
                };
            });
            ranking.uniques = uniques;

            const custom = {};
            document.querySelectorAll('.custom-ranking-item').forEach(card => {
                const keyInput = card.querySelector('[data-rk="custom_key"]');
                const key = keyInput ? keyInput.value.trim() : '';
                if (!key) return;
                const enabledInput = card.querySelector('[data-rk="custom_enabled"]');
                const nameInput = card.querySelector('[data-rk="custom_name"]');
                const queryInput = card.querySelector('[data-rk="custom_query"]');
                custom[key] = {
                    enabled: enabledInput ? enabledInput.checked : false,
                    name: nameInput ? nameInput.value : '',
                    query: queryInput ? queryInput.value : '',
                };
            });
            ranking.custom = custom;

            ranking.hwan_level = {};
            document.querySelectorAll('#hwanLevelTable tbody tr').forEach(row => {
                const raceInput = row.querySelector('[data-rk="hwan_level_race"]');
                const levelInput = row.querySelector('[data-rk="hwan_level_level"]');
                const labelInput = row.querySelector('[data-rk="hwan_level_label"]');
                if (!raceInput || !levelInput || !labelInput) {
                    return;
                }

                const race = raceInput.value;
                const level = parseInt(levelInput.value, 10);
                if (Number.isNaN(level)) {
                    return;
                }

                ranking.hwan_level[race] = ranking.hwan_level[race] || {};
                ranking.hwan_level[race][level] = labelInput.value;
            });

            const extra = {};
            document.querySelectorAll('[data-rk="extra"]').forEach(input => {
                extra[input.dataset.key] = input.checked;
            });
            ranking.extra = extra;

            document.getElementById('ranking_payload').value = JSON.stringify(ranking);
        }
    </script>
@endsection
