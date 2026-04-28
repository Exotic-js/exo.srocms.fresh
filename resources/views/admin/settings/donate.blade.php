@extends('admin.layouts.app')
@section('title', __('Donate Settings'))

@section('content')
    <div class="container">

        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">{{ __('Donate Settings') }}</h1>
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
            @foreach($gateways as $key => $gateway)
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                            data-bs-toggle="tab"
                            data-bs-target="#gw-{{ $key }}"
                            type="button" role="tab">
                        {{ $gateway['name'] ?? ucfirst($key) }}
                    </button>
                </li>
            @endforeach
        </ul>

        <form method="POST" action="{{ route('admin.settings.update') }}" id="donateForm">
            @csrf

            <div class="tab-content">
                @foreach($gateways as $key => $gateway)
                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                         id="gw-{{ $key }}" role="tabpanel">

                        @if(in_array($key, ['paymentwall', 'fawaterk', 'hipopay']))
                            <div class="alert alert-info mb-3">
                                {{ __('Webhook URL:') }} <code>{{ config('app.url') }}/webhook/{{ $key }}</code>
                            </div>
                        @endif

                        <h5 class="fw-semibold mb-3">{{ __('General') }}</h5>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                       id="{{ $key }}_enabled"
                                    {{ !empty($gateway['enabled']) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="{{ $key }}_enabled">{{ __('Enabled') }}</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Display Name') }}</label>
                            <input type="text" class="form-control" id="{{ $key }}_name"
                                   value="{{ $gateway['name'] ?? ucfirst($key) }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Currency') }}</label>
                            <input type="text" class="form-control" id="{{ $key }}_currency"
                                   value="{{ $gateway['currency'] ?? 'USD' }}" placeholder="USD">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Image Path') }}</label>
                            <input type="text" class="form-control" id="{{ $key }}_image"
                                   value="{{ $gateway['image'] ?? '' }}">
                        </div>

                        <h5 class="fw-semibold mb-3 mt-4">{{ __('Credentials') }}</h5>

                        @if($key === 'paypal')
                            <div class="mb-3">
                                <label class="form-label">{{ __('Mode') }}</label>
                                <select class="form-select" id="{{ $key }}_mode">
                                    <option value="sandbox" {{ ($gateway['mode'] ?? 'sandbox') === 'sandbox' ? 'selected' : '' }}>Sandbox</option>
                                    <option value="live"    {{ ($gateway['mode'] ?? 'sandbox') === 'live'    ? 'selected' : '' }}>Live</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('Endpoint') }}</label>
                                <input type="text" class="form-control" id="{{ $key }}_endpoint"
                                       value="{{ $gateway['endpoint'] ?? '' }}"
                                       placeholder="https://api-m.sandbox.paypal.com">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('Client ID') }}</label>
                                <input type="text" class="form-control" id="{{ $key }}_client_id"
                                       value="{{ $gateway['client_id'] ?? '' }}" placeholder="PAYPAL_CLIENT_ID">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('Client Secret') }}</label>
                                <input type="password" class="form-control" id="{{ $key }}_client_secret"
                                       value="{{ $gateway['client_secret'] ?? '' }}" placeholder="PAYPAL_CLIENT_SECRET">
                            </div>

                        @elseif($key === 'stripe')
                            <div class="mb-3">
                                <label class="form-label">{{ __('Endpoint') }}</label>
                                <input type="text" class="form-control" id="{{ $key }}_endpoint"
                                       value="{{ $gateway['endpoint'] ?? '' }}" placeholder="https://api.stripe.com">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('Secret Key') }}</label>
                                <input type="password" class="form-control" id="{{ $key }}_secret_key"
                                       value="{{ $gateway['secret_key'] ?? '' }}" placeholder="STRIPE_SECRET_KEY">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('Publishable Key') }}</label>
                                <input type="text" class="form-control" id="{{ $key }}_publishable_key"
                                       value="{{ $gateway['publishable_key'] ?? '' }}" placeholder="STRIPE_PUBLISHABLE_KEY">
                            </div>

                        @elseif($key === 'paymentwall')
                            <div class="mb-3">
                                <label class="form-label">{{ __('Public Key') }}</label>
                                <input type="text" class="form-control" id="{{ $key }}_public_key"
                                       value="{{ $gateway['public_key'] ?? '' }}" placeholder="YOUR_PROJECT_KEY">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('Private Key') }}</label>
                                <input type="password" class="form-control" id="{{ $key }}_private_key"
                                       value="{{ $gateway['private_key'] ?? '' }}" placeholder="YOUR_SECRET_KEY">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('Widget Code') }}</label>
                                <input type="text" class="form-control" id="{{ $key }}_widget_code"
                                       value="{{ $gateway['widget_code'] ?? '' }}" placeholder="p1_1">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('API Type') }}</label>
                                <select class="form-select" id="{{ $key }}_api_type">
                                    <option value="vc"     {{ ($gateway['api_type'] ?? 'vc') === 'vc'     ? 'selected' : '' }}>VC</option>
                                    <option value="direct" {{ ($gateway['api_type'] ?? 'vc') === 'direct' ? 'selected' : '' }}>Direct</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('Authorized IPs') }}</label>
                                <textarea class="form-control" id="{{ $key }}_authorized_ips" rows="4"
                                          placeholder="174.36.92.186">{{ implode("\n", $gateway['authorized_ips'] ?? []) }}</textarea>
                                <div class="form-text">{{ __('One IP per line') }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('Authorized Ranges') }}</label>
                                <textarea class="form-control" id="{{ $key }}_authorized_ranges" rows="4"
                                          placeholder="216.127.71.0/24">{{ implode("\n", $gateway['authorized_ranges'] ?? []) }}</textarea>
                                <div class="form-text">{{ __('One CIDR range per line') }}</div>
                            </div>

                        @elseif($key === 'coinpayments')
                            <div class="mb-3">
                                <label class="form-label">{{ __('Endpoint') }}</label>
                                <input type="text" class="form-control" id="{{ $key }}_endpoint"
                                       value="{{ $gateway['endpoint'] ?? '' }}" placeholder="https://api.coinpayments.com">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('Merchant ID') }}</label>
                                <input type="text" class="form-control" id="{{ $key }}_merchant_id"
                                       value="{{ $gateway['merchant_id'] ?? '' }}" placeholder="COINPAYMENTS_MERCHANT_ID">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('Client ID') }}</label>
                                <input type="text" class="form-control" id="{{ $key }}_client_id"
                                       value="{{ $gateway['client_id'] ?? '' }}" placeholder="COINPAYMENTS_CLIENT_ID">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('Client Secret') }}</label>
                                <input type="password" class="form-control" id="{{ $key }}_client_secret"
                                       value="{{ $gateway['client_secret'] ?? '' }}" placeholder="COINPAYMENTS_CLIENT_SECRET">
                            </div>

                        @elseif($key === 'fawaterk')
                            <div class="mb-3">
                                <label class="form-label">{{ __('Mode') }}</label>
                                <select class="form-select" id="{{ $key }}_mode">
                                    <option value="staging"    {{ ($gateway['mode'] ?? 'staging') === 'staging'    ? 'selected' : '' }}>Staging</option>
                                    <option value="production" {{ ($gateway['mode'] ?? 'staging') === 'production' ? 'selected' : '' }}>Production</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('Endpoint') }}</label>
                                <input type="text" class="form-control" id="{{ $key }}_endpoint"
                                       value="{{ $gateway['endpoint'] ?? '' }}" placeholder="https://staging.fawaterk.com">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('API Key') }}</label>
                                <input type="text" class="form-control" id="{{ $key }}_api_key"
                                       value="{{ $gateway['api_key'] ?? '' }}" placeholder="FAWATERK_API_KEY">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('Provider Key') }}</label>
                                <input type="text" class="form-control" id="{{ $key }}_provider_key"
                                       value="{{ $gateway['provider_key'] ?? '' }}" placeholder="FAWATERK_PROVIDER_KEY">
                            </div>

                        @elseif($key === 'maxicard')
                            <div class="mb-3">
                                <label class="form-label">{{ __('Endpoint') }}</label>
                                <input type="text" class="form-control" id="{{ $key }}_endpoint"
                                       value="{{ $gateway['endpoint'] ?? '' }}" placeholder="https://www.maxigame.org/epin/yukle.php">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('API Key') }}</label>
                                <input type="text" class="form-control" id="{{ $key }}_api_key"
                                       value="{{ $gateway['api_key'] ?? '' }}" placeholder="MAXICARD_API_KEY">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('API Password') }}</label>
                                <input type="password" class="form-control" id="{{ $key }}_api_password"
                                       value="{{ $gateway['api_password'] ?? '' }}" placeholder="MAXICARD_API_PASSWORD">
                            </div>

                        @elseif($key === 'hipocard')
                            <div class="mb-3">
                                <label class="form-label">{{ __('Mode') }}</label>
                                <select class="form-select" id="{{ $key }}_mode">
                                    <option value="sandbox"    {{ ($gateway['mode'] ?? 'sandbox') === 'sandbox'    ? 'selected' : '' }}>Sandbox</option>
                                    <option value="production" {{ ($gateway['mode'] ?? 'sandbox') === 'production' ? 'selected' : '' }}>Production</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('Endpoint') }}</label>
                                <input type="text" class="form-control" id="{{ $key }}_endpoint"
                                       value="{{ $gateway['endpoint'] ?? '' }}"
                                       placeholder="https://www.hipopotamya.com/api/sandbox/v1/hipocard/epins">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('API Key') }}</label>
                                <input type="text" class="form-control" id="{{ $key }}_api_key"
                                       value="{{ $gateway['api_key'] ?? '' }}" placeholder="HIPOCARD_API_KEY">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('API Password') }}</label>
                                <input type="password" class="form-control" id="{{ $key }}_api_password"
                                       value="{{ $gateway['api_password'] ?? '' }}" placeholder="HIPOCARD_API_PASSWORD">
                            </div>

                        @elseif($key === 'hipopay')
                            <div class="mb-3">
                                <label class="form-label">{{ __('Commission Type') }}</label>
                                <select class="form-select" id="{{ $key }}_commission_type">
                                    <option value="1" {{ ($gateway['commission_type'] ?? 1) == 1 ? 'selected' : '' }}>{{ __('Type 1') }}</option>
                                    <option value="2" {{ ($gateway['commission_type'] ?? 1) == 2 ? 'selected' : '' }}>{{ __('Type 2') }}</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('Endpoint') }}</label>
                                <input type="text" class="form-control" id="{{ $key }}_endpoint"
                                       value="{{ $gateway['endpoint'] ?? '' }}"
                                       placeholder="https://www.hipopotamya.com/api/v1/merchants/payment/token">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('API Key') }}</label>
                                <input type="text" class="form-control" id="{{ $key }}_api_key"
                                       value="{{ $gateway['api_key'] ?? '' }}" placeholder="HIPOPAY_API_KEY">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('API Password') }}</label>
                                <input type="password" class="form-control" id="{{ $key }}_api_password"
                                       value="{{ $gateway['api_password'] ?? '' }}" placeholder="HIPOPAY_API_PASSWORD">
                            </div>

                        @else
                            <div class="alert alert-info">
                                {{ __('No specific credential fields available for this gateway.') }}
                            </div>
                        @endif

                        <h5 class="fw-semibold mb-3 mt-4">{{ __('Packages') }}</h5>

                        <button type="button" class="btn btn-secondary btn-sm mb-3"
                                onclick="addPackageRow('pkg-table-{{ $key }}')">
                            {{ __('+ Add Package') }}
                        </button>

                        <table class="table table-bordered align-middle" id="pkg-table-{{ $key }}">
                            <thead class="table-light">
                            <tr>
                                <th>{{ __('Name') }}</th>
                                <th style="width: 130px;">{{ __('Price') }}</th>
                                <th style="width: 130px;">{{ __('Silk Value') }}</th>
                                <th style="width: 80px;">{{ __('Action') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($gateway['package'] ?? [] as $pkg)
                                <tr>
                                    <td><input type="text"   class="form-control form-control-sm" data-pkg="name"  value="{{ $pkg['name']  ?? '' }}"></td>
                                    <td><input type="number" class="form-control form-control-sm" data-pkg="price" value="{{ $pkg['price'] ?? '' }}" step="0.01" min="0"></td>
                                    <td><input type="number" class="form-control form-control-sm" data-pkg="value" value="{{ $pkg['value'] ?? '' }}" min="0"></td>
                                    <td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()">{{ __('Remove') }}</button></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

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
        function addPackageRow(tableId) {
            const tbody = document.getElementById(tableId).querySelector('tbody');
            const row   = tbody.insertRow();
            row.innerHTML = `
            <td><input type="text"   class="form-control form-control-sm" data-pkg="name"  placeholder="500 Silk"></td>
            <td><input type="number" class="form-control form-control-sm" data-pkg="price" placeholder="5.00" step="0.01" min="0"></td>
            <td><input type="number" class="form-control form-control-sm" data-pkg="value" placeholder="500" min="0"></td>
            <td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()">{{ __('Remove') }}</button></td>`;
        }

        function serializeGateway(key) {
            const commonFields = ['enabled', 'name', 'currency', 'image'];

            const payload = {
                enabled:  document.getElementById(key + '_enabled').checked,
                name:     document.getElementById(key + '_name').value,
                currency: document.getElementById(key + '_currency').value,
                image:    document.getElementById(key + '_image').value,
                route:    key,
            };

            document.querySelectorAll(`#gw-${key} [id^="${key}_"]`).forEach(el => {
                const fieldKey = el.id.slice(key.length + 1);
                if (commonFields.includes(fieldKey) || fieldKey.startsWith('payload')) return;

                if (el.tagName === 'TEXTAREA' && (fieldKey === 'authorized_ips' || fieldKey === 'authorized_ranges')) {
                    const trimmed = el.value.trim();
                    payload[fieldKey] = trimmed ? trimmed.split('\n').map(v => v.trim()).filter(Boolean) : [];
                } else {
                    payload[fieldKey] = el.type === 'checkbox' ? el.checked : el.value;
                }
            });

            payload.package = Array.from(document.querySelectorAll(`#pkg-table-${key} tbody tr`))
                .map(tr => ({
                    name:  tr.querySelector('[data-pkg="name"]').value,
                    price: parseFloat(tr.querySelector('[data-pkg="price"]').value) || 0,
                    value: parseInt(tr.querySelector('[data-pkg="value"]').value)   || 0,
                }))
                .filter(p => p.name || p.price || p.value);

            document.getElementById('payload-' + key).value = JSON.stringify(payload);
        }

        const endpointMap = {
            paypal:   { sandbox: 'https://api-m.sandbox.paypal.com',                         live:       'https://api-m.paypal.com' },
            fawaterk: { staging: 'https://staging.fawaterk.com',                              production: 'https://app.fawaterk.com' },
            hipocard: { sandbox: 'https://www.hipopotamya.com/api/sandbox/v1/hipocard/epins', production: 'https://www.hipopotamya.com/api/v1/hipocard/epins' },
        };

        document.addEventListener('DOMContentLoaded', function () {
            Object.entries(endpointMap).forEach(([key, modes]) => {
                const modeEl     = document.getElementById(key + '_mode');
                const endpointEl = document.getElementById(key + '_endpoint');
                if (modeEl && endpointEl) {
                    modeEl.addEventListener('change', function () {
                        endpointEl.value = modes[this.value] ?? endpointEl.value;
                    });
                }
            });
        });

        document.getElementById('donateForm').addEventListener('submit', function () {
            const gateways = @json(array_keys($gateways));
            gateways.forEach(serializeGateway);
        });
    </script>
@endsection
