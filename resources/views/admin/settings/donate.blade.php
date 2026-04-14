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

        @php
            /*
             * Each gateway defines which extra fields it needs beyond the common ones.
             * 'fields' => [ key => ['label', 'type', 'placeholder'] ]
             * type: text | password | select
             */
            $gateways = [
                'paypal' => [
                    'label'  => 'PayPal',
                    'fields' => [
                        'endpoint'      => ['label' => 'Endpoint URL',     'type' => 'text',     'placeholder' => 'https://api-m.sandbox.paypal.com'],
                        'client_id'     => ['label' => 'Client ID',        'type' => 'text',     'placeholder' => 'PAYPAL_CLIENT_ID'],
                        'client_secret' => ['label' => 'Client Secret',    'type' => 'password', 'placeholder' => 'PAYPAL_CLIENT_SECRET'],
                        'mode'          => ['label' => 'Mode',             'type' => 'select',   'options' => ['sandbox' => 'Sandbox', 'live' => 'Live']],
                    ],
                ],
                'stripe' => [
                    'label'  => 'Stripe',
                    'fields' => [
                        'endpoint'         => ['label' => 'Endpoint URL',      'type' => 'text',     'placeholder' => 'https://api.stripe.com'],
                        'secret_key'       => ['label' => 'Secret Key',        'type' => 'password', 'placeholder' => 'STRIPE_SECRET_KEY'],
                        'publishable_key'  => ['label' => 'Publishable Key',   'type' => 'text',     'placeholder' => 'STRIPE_PUBLISHABLE_KEY'],
                    ],
                ],
                'paymentwall' => [
                    'label'  => 'Paymentwall',
                    'fields' => [
                        'public_key'   => ['label' => 'Public Key',    'type' => 'text',     'placeholder' => 'YOUR_PROJECT_KEY'],
                        'private_key'  => ['label' => 'Private Key',   'type' => 'password', 'placeholder' => 'YOUR_SECRET_KEY'],
                        'widget_code'  => ['label' => 'Widget Code',   'type' => 'text',     'placeholder' => 'p1_1'],
                        'api_type'     => ['label' => 'API Type',      'type' => 'text',     'placeholder' => 'vc'],
                    ],
                ],
                'coinpayments' => [
                    'label'  => 'CoinPayments',
                    'fields' => [
                        'endpoint'      => ['label' => 'Endpoint URL',   'type' => 'text',     'placeholder' => 'https://api.coinpayments.com'],
                        'merchant_id'   => ['label' => 'Merchant ID',    'type' => 'text',     'placeholder' => 'COINPAYMENTS_MERCHANT_ID'],
                        'client_id'     => ['label' => 'Client ID',      'type' => 'text',     'placeholder' => 'COINPAYMENTS_CLIENT_ID'],
                        'client_secret' => ['label' => 'Client Secret',  'type' => 'password', 'placeholder' => 'COINPAYMENTS_CLIENT_SECRET'],
                    ],
                ],
                'fawaterk' => [
                    'label'  => 'Fawaterk',
                    'fields' => [
                        'endpoint'     => ['label' => 'Endpoint URL',  'type' => 'text',     'placeholder' => 'https://app.fawaterk.com'],
                        'api_key'      => ['label' => 'API Key',       'type' => 'password', 'placeholder' => 'FAWATERK_API_KEY'],
                        'provider_key' => ['label' => 'Provider Key',  'type' => 'password', 'placeholder' => 'FAWATERK_PROVIDER_KEY'],
                    ],
                ],
                'maxicard' => [
                    'label'  => 'MaxiCard',
                    'fields' => [
                        'endpoint'     => ['label' => 'Endpoint URL',  'type' => 'text',     'placeholder' => 'https://www.maxigame.org/epin/yukle.php'],
                        'api_key'      => ['label' => 'API Key',       'type' => 'password', 'placeholder' => 'MAXICARD_API_KEY'],
                        'api_password' => ['label' => 'API Password',  'type' => 'password', 'placeholder' => 'MAXICARD_API_PASSWORD'],
                    ],
                ],
                'hipocard' => [
                    'label'  => 'HipoCard',
                    'fields' => [
                        'endpoint'     => ['label' => 'Endpoint URL',  'type' => 'text',     'placeholder' => 'https://www.hipopotamya.com/api/v1/hipocard/epins'],
                        'api_key'      => ['label' => 'API Key',       'type' => 'password', 'placeholder' => 'HIPOCARD_API_KEY'],
                        'api_password' => ['label' => 'API Password',  'type' => 'password', 'placeholder' => 'HIPOCARD_API_PASSWORD'],
                    ],
                ],
                'hipopay' => [
                    'label'  => 'HipoPay',
                    'fields' => [
                        'endpoint'         => ['label' => 'Endpoint URL',    'type' => 'text',     'placeholder' => 'https://www.hipopotamya.com/api/v1/merchants/payment/token'],
                        'api_key'          => ['label' => 'API Key',         'type' => 'password', 'placeholder' => 'HIPOPAY_API_KEY'],
                        'api_password'     => ['label' => 'API Password',    'type' => 'password', 'placeholder' => 'HIPOPAY_API_PASSWORD'],
                        'commission_type'  => ['label' => 'Commission Type', 'type' => 'text',     'placeholder' => '1'],
                    ],
                ],
            ];
        @endphp

        {{-- Nav Tabs --}}
        <ul class="nav nav-tabs mb-3" id="donateTabs" role="tablist">
            @foreach($gateways as $key => $gateway)
                @php
                    $gwData  = json_decode($data[$key] ?? '{}', true) ?? [];
                    $enabled = $gwData['enabled'] ?? false;
                @endphp
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                            data-bs-toggle="tab"
                            data-bs-target="#gw-{{ $key }}"
                            type="button" role="tab">
                        {{ $gateway['label'] }}
                        @if($enabled)
                            <span class="badge bg-success ms-1" style="font-size:0.65rem;">ON</span>
                        @else
                            <span class="badge bg-secondary ms-1" style="font-size:0.65rem;">OFF</span>
                        @endif
                    </button>
                </li>
            @endforeach
        </ul>

        <div class="tab-content" id="donateTabsContent">
            @foreach($gateways as $key => $gateway)
                @php
                    $gwData = json_decode($data[$key] ?? '{}', true) ?? [];
                @endphp

                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                     id="gw-{{ $key }}" role="tabpanel">

                    <form method="POST" action="{{ route('admin.settings.update') }}"
                          onsubmit="serializeGateway('{{ $key }}')">
                        @csrf

                        {{-- Common fields --}}
                        <div class="card mb-3">
                            <div class="card-header fw-semibold">{{ __('General') }}</div>
                            <div class="card-body">
                                <div class="row g-3">

                                    <div class="col-md-2 d-flex align-items-center">
                                        <label class="form-check mb-0">
                                            <input class="form-check-input" type="checkbox"
                                                   id="{{ $key }}_enabled"
                                                {{ $gwData['enabled'] ?? false ? 'checked' : '' }}>
                                            <span class="form-check-label fw-semibold">{{ __('Enabled') }}</span>
                                        </label>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">{{ __('Display Name') }}</label>
                                        <input type="text" class="form-control"
                                               id="{{ $key }}_name"
                                               value="{{ $gwData['name'] ?? $gateway['label'] }}">
                                    </div>

                                    <div class="col-md-2">
                                        <label class="form-label">{{ __('Currency') }}</label>
                                        <input type="text" class="form-control"
                                               id="{{ $key }}_currency"
                                               value="{{ $gwData['currency'] ?? 'USD' }}"
                                               placeholder="USD">
                                    </div>

                                    <div class="col-md-5">
                                        <label class="form-label">{{ __('Image Path') }}</label>
                                        <input type="text" class="form-control"
                                               id="{{ $key }}_image"
                                               value="{{ $gwData['image'] ?? '' }}"
                                               placeholder="images/donate/{{ $key }}.png">
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- Gateway-specific fields --}}
                        <div class="card mb-3">
                            <div class="card-header fw-semibold">{{ __('Credentials & Configuration') }}</div>
                            <div class="card-body">
                                <div class="row g-3">
                                    @foreach($gateway['fields'] as $fieldKey => $field)
                                        <div class="col-md-6">
                                            <label class="form-label">{{ __($field['label']) }}</label>
                                            @if($field['type'] === 'select')
                                                <select class="form-select" id="{{ $key }}_{{ $fieldKey }}">
                                                    @foreach($field['options'] as $optVal => $optLabel)
                                                        <option value="{{ $optVal }}"
                                                            {{ ($gwData[$fieldKey] ?? '') === $optVal ? 'selected' : '' }}>
                                                            {{ $optLabel }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <input type="{{ $field['type'] }}"
                                                       class="form-control"
                                                       id="{{ $key }}_{{ $fieldKey }}"
                                                       value="{{ $gwData[$fieldKey] ?? '' }}"
                                                       placeholder="{{ $field['placeholder'] ?? '' }}">
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Packages --}}
                        <div class="card mb-3">
                            <div class="card-header fw-semibold">{{ __('Packages') }}</div>
                            <div class="card-body">
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
                                        @forelse($gwData['package'] ?? [] as $pkg)
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
                                <button type="button" class="btn btn-secondary btn-sm"
                                        onclick="addPkg('pkg-table-{{ $key }}')">
                                    {{ __('+ Add Package') }}
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            {{ __('Save Changes') }}
                        </button>

                        <input type="hidden" id="payload-{{ $key }}" name="{{ $key }}">
                    </form>
                </div>

            @endforeach
        </div>
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
    </script>
@endsection
