@extends('admin.layouts.app')
@section('title', __('Donate Settings'))

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">{{ __('Donate Settings') }}</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <div class="btn-group me-2">
                    <form action="{{ route('admin.settings.clear-cache') }}" method="POST"
                          onsubmit="return confirm('Are you sure you want to clear all caches?')">
                        @csrf
                        <button type="submit" class="btn btn-danger">{{ __('Clear All Cache') }}</button>
                    </form>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success" role="alert">{{ session('success') }}</div>
        @endif



        {{-- Nav Tabs --}}
        <ul class="nav nav-tabs mb-3" id="donateTabs" role="tablist">
            @foreach($gateways as $key => $gateway)
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                            data-bs-toggle="tab"
                            data-bs-target="#gw-{{ $key }}"
                            type="button" role="tab">
                        {{ $gateway['label'] ?? $gateway['name'] ?? ucfirst($key) }}
                        @if(!empty($gateway['enabled']))
                            <span class="badge bg-success ms-1" style="font-size:0.65rem;">ON</span>
                        @else
                            <span class="badge bg-secondary ms-1" style="font-size:0.65rem;">OFF</span>
                        @endif
                    </button>
                </li>
            @endforeach
        </ul>

        <form method="POST" action="{{ route('admin.settings.update') }}" onsubmit="serializeDonate()">
            @csrf

            <div class="tab-content" id="donateTabsContent">
                @foreach($gateways as $key => $gateway)
                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                         id="gw-{{ $key }}" role="tabpanel">

                        {{-- Common fields --}}
                        <div class="mb-3">
                            <div class="fw-semibold mb-3">{{ __('General') }}</div>
                            <div>
                                <div class="mb-3">
                                    <label class="form-check mb-0">
                                        <input class="form-check-input" type="checkbox"
                                               id="{{ $key }}_enabled"
                                            {{ !empty($gateway['enabled']) ? 'checked' : '' }}>
                                        <span class="form-check-label fw-semibold">{{ __('Enabled') }}</span>
                                    </label>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Display Name') }}</label>
                                    <input type="text" class="form-control"
                                           id="{{ $key }}_name"
                                           value="{{ $gateway['name'] ?? $gateway['label'] ?? ucfirst($key) }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Currency') }}</label>
                                    <input type="text" class="form-control"
                                           id="{{ $key }}_currency"
                                           value="{{ $gateway['currency'] ?? 'USD' }}"
                                           placeholder="USD">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Image Path') }}</label>
                                    <input type="text" class="form-control"
                                           id="{{ $key }}_image"
                                           value="{{ $gateway['image'] ?? '' }}">
                                </div>
                            </div>
                        </div>

                        {{-- Gateway-specific fields --}}
                        <div class="mb-3">
                            <div class="fw-semibold mb-3">{{ __('Credentials & Configuration') }}</div>
                            <div>
                                @foreach($gateway['fields'] ?? [] as $fieldKey => $field)
                                    <div class="mb-3">
                                        <label class="form-label">{{ __($field['label']) }}</label>
                                        @if($field['type'] === 'select')
                                            <select class="form-select" id="{{ $key }}_{{ $fieldKey }}">
                                                @foreach($field['options'] as $optVal => $optLabel)
                                                    <option value="{{ $optVal }}"
                                                        {{ ($gateway[$fieldKey] ?? '') === $optVal ? 'selected' : '' }}>
                                                        {{ $optLabel }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @else
                                            <input type="{{ $field['type'] }}"
                                                   class="form-control"
                                                   id="{{ $key }}_{{ $fieldKey }}"
                                                   value="{{ $gateway[$fieldKey] ?? '' }}"
                                                   placeholder="{{ $field['placeholder'] ?? '' }}">
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Packages --}}
                        <div class="mb-3">
                            <div class="fw-semibold mb-3">{{ __('Packages') }}</div>
                            <div>
                                <button type="button" class="btn btn-secondary btn-sm"
                                        onclick="addPkg('pkg-table-{{ $key }}')">
                                    {{ __('+ Add Package') }}
                                </button>

                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle" id="pkg-table-{{ $key }}">
                                        <thead class="table-light">
                                        <tr>
                                            <th>{{ __('Name') }}</th>
                                            <th style="width:130px;">{{ __('Price') }}</th>
                                            <th style="width:130px;">{{ __('Silk Value') }}</th>
                                            <th style="width:80px;">{{ __('Action') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse($gateway['package'] ?? [] as $pkg)
                                            <tr>
                                                <td><input type="text"   class="form-control" data-pkg="name"  value="{{ $pkg['name']  ?? '' }}"></td>
                                                <td><input type="number" class="form-control" data-pkg="price" value="{{ $pkg['price'] ?? '' }}" step="0.01" min="0"></td>
                                                <td><input type="number" class="form-control" data-pkg="value" value="{{ $pkg['value'] ?? '' }}" min="0"></td>
                                                <td><button type="button" class="btn btn-sm btn-danger" onclick="removePkg(this)">{{ __('Remove') }}</button></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td><input type="text"   class="form-control" data-pkg="name"  placeholder="500 Silk"></td>
                                                <td><input type="number" class="form-control" data-pkg="price" placeholder="5.00" step="0.01" min="0"></td>
                                                <td><input type="number" class="form-control" data-pkg="value" placeholder="500" min="0"></td>
                                                <td><button type="button" class="btn btn-sm btn-danger" onclick="removePkg(this)">{{ __('Remove') }}</button></td>
                                            </tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" id="payload-{{ $key }}" name="{{ $key }}">
                    </div>

                @endforeach
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
            </div>
        </form>
    </div>

    <script>
        // ─── Packages ─────────────────────────────────────────────────────────────────
        function addPkg(tableId) {
            const tbody = document.getElementById(tableId).querySelector('tbody');
            const row   = tbody.insertRow();
            row.innerHTML = `
                <td><input type="text"   class="form-control" data-pkg="name"  placeholder="500 Silk"></td>
                <td><input type="number" class="form-control" data-pkg="price" placeholder="5.00" step="0.01" min="0"></td>
                <td><input type="number" class="form-control" data-pkg="value" placeholder="500" min="0"></td>
                <td><button type="button" class="btn btn-sm btn-danger" onclick="removePkg(this)">{{ __('Remove') }}</button></td>
            `;
        }

        function removePkg(btn) {
            btn.closest('tr').remove();
        }

        // ─── Serialize gateway before submit ─────────────────────────────────────────
        function serializeGateway(key) {
            // Common fields
            const payload = {
                enabled:  document.getElementById(key + '_enabled').checked,
                name:     document.getElementById(key + '_name').value,
                currency: document.getElementById(key + '_currency').value,
                image:    document.getElementById(key + '_image').value,
                route:    key,
            };

            // All extra credential fields (any input/select with id starting with key + '_'
            // that isn't one of the common ones)
            const common = ['enabled', 'name', 'currency', 'image'];
            document.querySelectorAll(`#gw-${key} [id^="${key}_"]`).forEach(el => {
                const fieldKey = el.id.replace(key + '_', '');
                if (!common.includes(fieldKey) && !fieldKey.startsWith('payload')) {
                    payload[fieldKey] = el.type === 'checkbox' ? el.checked : el.value;
                }
            });

            // Packages
            payload.package = Array.from(
                document.querySelectorAll(`#pkg-table-${key} tbody tr`)
            ).map(tr => ({
                name:  tr.querySelector('[data-pkg="name"]').value,
                price: parseFloat(tr.querySelector('[data-pkg="price"]').value) || 0,
                value: parseInt(tr.querySelector('[data-pkg="value"]').value)   || 0,
            })).filter(p => p.name || p.price || p.value);

            document.getElementById('payload-' + key).value = JSON.stringify(payload);
        }

        function serializeDonate() {
            const gateways = {!! json_encode(array_keys($gateways)) !!};
            gateways.forEach(serializeGateway);
        }
    </script>
@endsection
