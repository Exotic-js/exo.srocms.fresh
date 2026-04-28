@extends('admin.layouts.app')
@section('title', __('Ranking Settings'))

@section('content')
    <div class="container">

        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">{{ __('Ranking Settings') }}</h1>
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
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-ranking-menu" type="button" role="tab">{{ __('Ranking Menu') }}</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-hidden" type="button" role="tab">{{ __('Hidden Characters') }}</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-uniques" type="button" role="tab">{{ __('Uniques') }}</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-hwan" type="button" role="tab">{{ __('Hwan Level') }}</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-custom" type="button" role="tab">{{ __('Custom Ranking') }}</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-character" type="button" role="tab">{{ __('Character') }}</button>
            </li>
        </ul>

        <form method="POST" action="{{ route('admin.settings.update') }}" id="rankingForm">
            @csrf
            <input type="hidden" id="ranking_source"  value="{{ json_encode($ranking, JSON_UNESCAPED_SLASHES) }}">
            <input type="hidden" id="ranking_payload" name="ranking" value="{{ json_encode($ranking, JSON_UNESCAPED_SLASHES) }}">

            <div class="tab-content">

                {{-- ===================== RANKING MENU ===================== --}}
                <div class="tab-pane fade show active" id="tab-ranking-menu" role="tabpanel">

                    <h5 class="fw-semibold mb-3">{{ __('Ranking Menu') }}</h5>

                    @foreach($ranking['menu'] ?? [] as $menuKey => $item)
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="form-check mb-0">
                                <input class="form-check-input" type="checkbox"
                                       data-rk="menu_enabled" data-key="{{ $menuKey }}"
                                    {{ !empty($item['enabled']) ? 'checked' : '' }}>
                            </div>
                            <input type="text" class="form-control form-control-sm"
                                   style="max-width: 260px;"
                                   data-rk="menu_name" data-key="{{ $menuKey }}"
                                   value="{{ $item['name'] ?? '' }}"
                                   placeholder="{{ __('Label') }}">
                            <span class="text-muted small">{{ $menuKey }}</span>
                            <input type="hidden" data-rk="menu_route" data-key="{{ $menuKey }}"
                                   value="{{ is_array($item['route'] ?? null) ? json_encode($item['route'], JSON_UNESCAPED_SLASHES) : ($item['route'] ?? '') }}">
                        </div>
                    @endforeach

                    <h5 class="fw-semibold mb-3 mt-4">{{ __('Job Ranking Menu') }}</h5>

                    @foreach($ranking['job_menu'] ?? [] as $jobKey => $item)
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="form-check mb-0">
                                <input class="form-check-input" type="checkbox"
                                       data-rk="job_menu_enabled" data-key="{{ $jobKey }}"
                                    {{ !empty($item['enabled']) ? 'checked' : '' }}>
                            </div>
                            <input type="text" class="form-control form-control-sm"
                                   style="max-width: 260px;"
                                   data-rk="job_menu_name" data-key="{{ $jobKey }}"
                                   value="{{ $item['name'] ?? '' }}"
                                   placeholder="{{ __('Label') }}">
                            <span class="text-muted small">{{ $jobKey }}</span>
                            <input type="hidden" data-rk="job_menu_route" data-key="{{ $jobKey }}"
                                   value="{{ is_array($item['route'] ?? null) ? json_encode($item['route'], JSON_UNESCAPED_SLASHES) : ($item['route'] ?? '') }}">
                        </div>
                    @endforeach
                </div>

                {{-- ===================== HIDDEN CHARACTERS ===================== --}}
                <div class="tab-pane fade" id="tab-hidden" role="tabpanel">

                    <h5 class="fw-semibold mb-3">{{ __('Hidden Characters') }}</h5>
                    <p class="text-muted small mb-3">{{ __('Characters excluded from all ranking lists.') }}</p>

                    <button type="button" class="btn btn-secondary btn-sm mb-3"
                            onclick="addHiddenRow('hiddenCharactersTable', 'hidden_character')">{{ __('+ Add Character') }}</button>

                    <table class="table table-bordered align-middle mb-4" id="hiddenCharactersTable">
                        <thead class="table-light">
                        <tr>
                            <th>{{ __('Character Name') }}</th>
                            <th style="width: 80px;">{{ __('Action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($ranking['hidden']['characters'] ?? [] as $character)
                            <tr>
                                <td><input type="text" class="form-control form-control-sm" data-rk="hidden_character" value="{{ $character }}"></td>
                                <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">{{ __('Remove') }}</button></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <h5 class="fw-semibold mb-3">{{ __('Hidden Guilds') }}</h5>
                    <p class="text-muted small mb-3">{{ __('Guilds excluded from all ranking lists.') }}</p>

                    <button type="button" class="btn btn-secondary btn-sm mb-3"
                            onclick="addHiddenRow('hiddenGuildsTable', 'hidden_guild')">{{ __('+ Add Guild') }}</button>

                    <table class="table table-bordered align-middle" id="hiddenGuildsTable">
                        <thead class="table-light">
                        <tr>
                            <th>{{ __('Guild Name') }}</th>
                            <th style="width: 80px;">{{ __('Action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($ranking['hidden']['guilds'] ?? [] as $guild)
                            <tr>
                                <td><input type="text" class="form-control form-control-sm" data-rk="hidden_guild" value="{{ $guild }}"></td>
                                <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">{{ __('Remove') }}</button></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- ===================== UNIQUES ===================== --}}
                <div class="tab-pane fade" id="tab-uniques" role="tabpanel">

                    <h5 class="fw-semibold mb-3">{{ __('Unique Ranking Points') }}</h5>
                    <p class="text-muted small mb-3">{{ __('Assign point values to each unique boss kill for the unique ranking.') }}</p>

                    <button type="button" class="btn btn-secondary btn-sm mb-3" onclick="addUniqueRow()">{{ __('+ Add Unique') }}</button>

                    <table class="table table-bordered align-middle" id="uniquesTable">
                        <thead class="table-light">
                        <tr>
                            <th>{{ __('Key') }}</th>
                            <th>{{ __('ID') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Image') }}</th>
                            <th style="width: 90px;">{{ __('Points') }}</th>
                            <th style="width: 80px;">{{ __('Action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($ranking['uniques'] ?? [] as $uniqueKey => $unique)
                            <tr>
                                <td><input type="text"   class="form-control form-control-sm" data-rk="unique_key"    value="{{ $uniqueKey }}"></td>
                                <td><input type="number" class="form-control form-control-sm" data-rk="unique_id"     value="{{ $unique['id'] ?? '' }}"></td>
                                <td><input type="text"   class="form-control form-control-sm" data-rk="unique_name"   value="{{ $unique['name'] ?? '' }}"></td>
                                <td><input type="text"   class="form-control form-control-sm" data-rk="unique_image"  value="{{ $unique['image'] ?? '' }}"></td>
                                <td><input type="number" class="form-control form-control-sm" data-rk="unique_points" value="{{ $unique['points'] ?? 0 }}"></td>
                                <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">{{ __('Remove') }}</button></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- ===================== HWAN LEVEL ===================== --}}
                <div class="tab-pane fade" id="tab-hwan" role="tabpanel">

                    <h5 class="fw-semibold mb-3">{{ __('Hwan Level Labels') }}</h5>
                    <p class="text-muted small mb-3">{{ __('Map numeric hwan levels to display labels per race.') }}</p>

                    <button type="button" class="btn btn-secondary btn-sm mb-3" onclick="addHwanLevelRow()">{{ __('+ Add Hwan Level') }}</button>

                    <table class="table table-bordered align-middle" id="hwanLevelTable">
                        <thead class="table-light">
                        <tr>
                            <th style="width: 140px;">{{ __('Race') }}</th>
                            <th style="width: 100px;">{{ __('Level') }}</th>
                            <th>{{ __('Label') }}</th>
                            <th style="width: 80px;">{{ __('Action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($ranking['hwan_level'] ?? [] as $raceIndex => $levels)
                            @foreach($levels as $level => $label)
                                <tr>
                                    <td>
                                        <select class="form-select form-select-sm" data-rk="hwan_level_race">
                                            <option value="0" {{ $raceIndex == 0 ? 'selected' : '' }}>{{ __('Chinese') }}</option>
                                            <option value="1" {{ $raceIndex == 1 ? 'selected' : '' }}>{{ __('Europe') }}</option>
                                        </select>
                                    </td>
                                    <td><input type="number" class="form-control form-control-sm" min="0" data-rk="hwan_level_level" value="{{ $level }}"></td>
                                    <td><input type="text"   class="form-control form-control-sm" data-rk="hwan_level_label" value="{{ $label }}"></td>
                                    <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">{{ __('Remove') }}</button></td>
                                </tr>
                            @endforeach
                        @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- ===================== CUSTOM RANKING ===================== --}}
                <div class="tab-pane fade" id="tab-custom" role="tabpanel">

                    <div class="alert alert-info mb-3">
                        <p class="fw-semibold mb-1">{{ __('Custom Ranking Guide') }}</p>
                        <p class="small mb-1">{{ __('Replace the SQL query to return the expected fields.') }}</p>
                        <p class="small mb-1">{{ __('Return') }} <code>CharName</code> {{ __('and') }} <code>GuildName</code> {{ __('to render links.') }}</p>
                        <p class="small mb-0">{{ __('Return') }} <code>RefObjID</code> {{ __('to show the race icon.') }}</p>
                    </div>

                    <button type="button" class="btn btn-secondary btn-sm mb-3" onclick="addCustomRankCard()">{{ __('+ Add Custom Ranking') }}</button>

                    <div id="customRankingList">
                        @foreach($ranking['custom'] ?? [] as $customKey => $custom)
                            <div class="card mb-3 custom-ranking-item">
                                <div class="card-header fw-semibold">{{ $customKey }}</div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                   data-rk="custom_enabled"
                                                {{ !empty($custom['enabled']) ? 'checked' : '' }}>
                                            <label class="form-check-label">{{ __('Enabled') }}</label>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Key') }}</label>
                                        <input type="text" class="form-control" data-rk="custom_key" value="{{ $customKey }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Name') }}</label>
                                        <input type="text" class="form-control" data-rk="custom_name" value="{{ $custom['name'] ?? '' }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Query') }}</label>
                                        <textarea class="form-control font-monospace" data-rk="custom_query" rows="5">{{ $custom['query'] ?? '' }}</textarea>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-danger"
                                            onclick="this.closest('.custom-ranking-item').remove()">{{ __('Remove') }}</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- ===================== CHARACTER ===================== --}}
                <div class="tab-pane fade" id="tab-character" role="tabpanel">

                    <h5 class="fw-semibold mb-3">{{ __('Character Ranking Extras') }}</h5>
                    <p class="text-muted small mb-3">{{ __('Choose which extra sections to show on the character ranking page.') }}</p>

                    <div class="d-flex flex-column gap-2">
                        @foreach([
                            'character_status'         => __('Character Status'),
                            'character_build'          => __('Character Build'),
                            'character_buff'           => __('Character Buff'),
                            'character_job'            => __('Character Job'),
                            'character_unique_history' => __('Character Unique History'),
                            'character_global_history' => __('Character Global History'),
                            'character_pvp_kill'       => __('Character PvP Kill'),
                            'character_job_kill'       => __('Character Job Kill'),
                        ] as $key => $label)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                       id="char_extra_{{ $key }}"
                                       data-rk="extra" data-key="{{ $key }}"
                                    {{ !empty($ranking['extra'][$key]) ? 'checked' : '' }}>
                                <label class="form-check-label" for="char_extra_{{ $key }}">{{ $label }}</label>
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

        // ─── Row helpers ──────────────────────────────────────────────────────────────
        function addHiddenRow(tableId, attr) {
            const tbody = document.querySelector('#' + tableId + ' tbody');
            const tr    = document.createElement('tr');
            tr.innerHTML = `
            <td><input type="text" class="form-control form-control-sm" data-rk="${attr}"></td>
            <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">{{ __('Remove') }}</button></td>`;
            tbody.appendChild(tr);
        }

        function addUniqueRow() {
            const tbody = document.querySelector('#uniquesTable tbody');
            const tr    = document.createElement('tr');
            tr.innerHTML = `
            <td><input type="text"   class="form-control form-control-sm" data-rk="unique_key"></td>
            <td><input type="number" class="form-control form-control-sm" data-rk="unique_id"></td>
            <td><input type="text"   class="form-control form-control-sm" data-rk="unique_name"></td>
            <td><input type="text"   class="form-control form-control-sm" data-rk="unique_image"></td>
            <td><input type="number" class="form-control form-control-sm" data-rk="unique_points" value="0"></td>
            <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">{{ __('Remove') }}</button></td>`;
            tbody.appendChild(tr);
        }

        function addHwanLevelRow() {
            const tbody = document.querySelector('#hwanLevelTable tbody');
            const tr    = document.createElement('tr');
            tr.innerHTML = `
            <td>
                <select class="form-select form-select-sm" data-rk="hwan_level_race">
                    <option value="0">{{ __('Chinese') }}</option>
                    <option value="1">{{ __('Europe') }}</option>
                </select>
            </td>
            <td><input type="number" class="form-control form-control-sm" min="0" data-rk="hwan_level_level" value="0"></td>
            <td><input type="text"   class="form-control form-control-sm" data-rk="hwan_level_label"></td>
            <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">{{ __('Remove') }}</button></td>`;
            tbody.appendChild(tr);
        }

        function addCustomRankCard() {
            const container = document.getElementById('customRankingList');
            const card      = document.createElement('div');
            card.className  = 'card mb-3 custom-ranking-item';
            card.innerHTML  = `
            <div class="card-header fw-semibold">{{ __('New Custom Ranking') }}</div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" data-rk="custom_enabled">
                        <label class="form-check-label">{{ __('Enabled') }}</label>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('Key') }}</label>
                    <input type="text" class="form-control" data-rk="custom_key">
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('Name') }}</label>
                    <input type="text" class="form-control" data-rk="custom_name">
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('Query') }}</label>
                    <textarea class="form-control font-monospace" data-rk="custom_query" rows="5"></textarea>
                </div>
                <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.custom-ranking-item').remove()">{{ __('Remove') }}</button>
            </div>`;
            container.appendChild(card);
        }

        // ─── Serializer ───────────────────────────────────────────────────────────────
        function serializeRanking() {
            let ranking = {};
            try {
                ranking = JSON.parse(document.getElementById('ranking_source').value || '{}');
            } catch {
                ranking = {};
            }

            // Menu
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
                try { menu[key].route = JSON.parse(input.value); } catch { menu[key].route = input.value; }
            });
            ranking.menu = menu;

            // Job Menu
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
                try { jobMenu[key].route = JSON.parse(input.value); } catch { jobMenu[key].route = input.value; }
            });
            ranking.job_menu = jobMenu;

            // Hidden — read only surviving DOM rows
            ranking.hidden = {
                characters: Array.from(document.querySelectorAll('#hiddenCharactersTable tbody tr'))
                    .map(tr => tr.querySelector('[data-rk="hidden_character"]').value.trim())
                    .filter(Boolean),
                guilds: Array.from(document.querySelectorAll('#hiddenGuildsTable tbody tr'))
                    .map(tr => tr.querySelector('[data-rk="hidden_guild"]').value.trim())
                    .filter(Boolean),
            };

            // Uniques — read only surviving DOM rows
            const uniques = {};
            document.querySelectorAll('#uniquesTable tbody tr').forEach(tr => {
                const key = tr.querySelector('[data-rk="unique_key"]')?.value.trim();
                if (!key) return;
                uniques[key] = {
                    id:     parseInt(tr.querySelector('[data-rk="unique_id"]').value)     || 0,
                    name:   tr.querySelector('[data-rk="unique_name"]').value,
                    image:  tr.querySelector('[data-rk="unique_image"]').value,
                    points: parseInt(tr.querySelector('[data-rk="unique_points"]').value) || 0,
                };
            });
            ranking.uniques = uniques;

            // Hwan level — read only surviving DOM rows
            const hwanLevel = {};
            document.querySelectorAll('#hwanLevelTable tbody tr').forEach(tr => {
                const race  = tr.querySelector('[data-rk="hwan_level_race"]')?.value;
                const level = parseInt(tr.querySelector('[data-rk="hwan_level_level"]')?.value);
                const label = tr.querySelector('[data-rk="hwan_level_label"]')?.value;
                if (race === undefined || isNaN(level)) return;
                hwanLevel[race]        = hwanLevel[race] || {};
                hwanLevel[race][level] = label;
            });
            ranking.hwan_level = hwanLevel;

            // Custom ranking — route is auto-built from key, no image/route fields in UI
            const custom = {};
            document.querySelectorAll('.custom-ranking-item').forEach(card => {
                const key = card.querySelector('[data-rk="custom_key"]')?.value.trim();
                if (!key) return;
                custom[key] = {
                    enabled: card.querySelector('[data-rk="custom_enabled"]')?.checked ?? false,
                    name:    card.querySelector('[data-rk="custom_name"]')?.value.trim()  ?? '',
                    route: {
                        name:   'ranking.custom',
                        params: { type: key },
                    },
                    query: card.querySelector('[data-rk="custom_query"]')?.value ?? '',
                };
            });
            ranking.custom = custom;

            // Extra toggles
            const extra = {};
            document.querySelectorAll('[data-rk="extra"]').forEach(input => {
                extra[input.dataset.key] = input.checked;
            });
            ranking.extra = extra;

            document.getElementById('ranking_payload').value = JSON.stringify(ranking);
        }

        document.getElementById('rankingForm').addEventListener('submit', serializeRanking);
    </script>
@endsection
