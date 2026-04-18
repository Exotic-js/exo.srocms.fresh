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
                <li class="nav-item me-2" role="presentation">
                    <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                            data-bs-toggle="tab"
                            data-bs-target="#gw-{{ $key }}"
                            type="button" role="tab">
                        {{ $gateway['label'] ?? $gateway['name'] ?? ucfirst($key) }}
                    </button>
                </li>
            @endforeach
        </ul>

        <form method="POST" action="{{ route('admin.settings.update') }}" onsubmit="serializeDonate()">
            @csrf

            <div class="tab-content" id="donateTabsContent">
                @foreach($gateways as $key => $gateway)
                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }} mb-4"
                         id="gw-{{ $key }}" role="tabpanel">

                        {{-- Common fields --}}
                        <div class="mb-3">
                            @if(in_array($key, ['paymentwall', 'fawaterk', 'hipopay']))
                                <div class="alert alert-info mb-3">
                                    <h6><i class="fas fa-info-circle"></i> {{ __('Webhook URL') }}</h6>
                                    <p class="mb-2">{{ __('Configure your webhook URL as:') }}</p>
                                    <strong>{{ config('app.url') }}/webhook/{{ $key }}</strong>
                                    <p class="mb-0 text-muted"><small>{{ __('Make sure to configure this webhook URL in your payment gateway dashboard to receive payment notifications.') }}</small></p>
                                </div>
                            @endif
                            
                            <div class="fw-semibold mb-3">{{ __('General') }}</div>
                            <div class="mb-3">
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
                            <div>
                                @if($key === 'paypal')
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Client ID') }}</label>
                                        <input type="text" class="form-control"
                                               id="{{ $key }}_client_id"
                                               value="{{ $gateway['client_id'] ?? '' }}"
                                               placeholder="PAYPAL_CLIENT_ID">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Client Secret') }}</label>
                                        <input type="password" class="form-control"
                                               id="{{ $key }}_client_secret"
                                               value="{{ $gateway['client_secret'] ?? '' }}"
                                               placeholder="PAYPAL_CLIENT_SECRET">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Mode') }}</label>
                                        <select class="form-select" id="{{ $key }}_mode">
                                            <option value="sandbox" {{ ($gateway['mode'] ?? 'sandbox') === 'sandbox' ? 'selected' : '' }}>Sandbox</option>
                                            <option value="live" {{ ($gateway['mode'] ?? 'sandbox') === 'live' ? 'selected' : '' }}>Live</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Endpoint') }}</label>
                                        <input type="text" class="form-control"
                                               id="{{ $key }}_endpoint"
                                               value="{{ $gateway['endpoint'] ?? '' }}"
                                               placeholder="https://api-m.sandbox.paypal.com">
                                    </div>
                                @elseif($key === 'stripe')
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Secret Key') }}</label>
                                        <input type="text" class="form-control"
                                               id="{{ $key }}_secret_key"
                                               value="{{ $gateway['secret_key'] ?? '' }}"
                                               placeholder="STRIPE_SECRET_KEY">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Publishable Key') }}</label>
                                        <input type="text" class="form-control"
                                               id="{{ $key }}_publishable_key"
                                               value="{{ $gateway['publishable_key'] ?? '' }}"
                                               placeholder="STRIPE_PUBLISHABLE_KEY">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Endpoint') }}</label>
                                        <input type="text" class="form-control"
                                               id="{{ $key }}_endpoint"
                                               value="{{ $gateway['endpoint'] ?? '' }}"
                                               placeholder="https://api.stripe.com">
                                    </div>
                                @elseif($key === 'paymentwall')
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Public Key') }}</label>
                                        <input type="text" class="form-control"
                                               id="{{ $key }}_public_key"
                                               value="{{ $gateway['public_key'] ?? '' }}"
                                               placeholder="YOUR_PROJECT_KEY">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Private Key') }}</label>
                                        <input type="password" class="form-control"
                                               id="{{ $key }}_private_key"
                                               value="{{ $gateway['private_key'] ?? '' }}"
                                               placeholder="YOUR_SECRET_KEY">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Widget Code') }}</label>
                                        <input type="text" class="form-control"
                                               id="{{ $key }}_widget_code"
                                               value="{{ $gateway['widget_code'] ?? '' }}"
                                               placeholder="p1_1">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('API Type') }}</label>
                                        <select class="form-select" id="{{ $key }}_api_type">
                                            <option value="vc" {{ ($gateway['api_type'] ?? 'vc') === 'vc' ? 'selected' : '' }}>VC</option>
                                            <option value="direct" {{ ($gateway['api_type'] ?? 'vc') === 'direct' ? 'selected' : '' }}>Direct</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Authorized IPs') }}</label>
                                        <textarea class="form-control" id="{{ $key }}_authorized_ips" rows="4" placeholder="174.36.92.186&#10;174.36.96.66&#10;174.36.92.187">{{ implode("\n", $gateway['authorized_ips'] ?? []) }}</textarea>
                                        <small class="text-muted">{{ __('One IP per line') }}</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Authorized Ranges') }}</label>
                                        <textarea class="form-control" id="{{ $key }}_authorized_ranges" rows="2" placeholder="216.127.71.0/24">{{ implode("\n", $gateway['authorized_ranges'] ?? []) }}</textarea>
                                        <small class="text-muted">{{ __('One range per line (e.g., 216.127.71.0/24)') }}</small>
                                    </div>
                                @elseif($key === 'coinpayments')
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Endpoint') }}</label>
                                        <input type="text" class="form-control"
                                               id="{{ $key }}_endpoint"
                                               value="{{ $gateway['endpoint'] ?? '' }}"
                                               placeholder="https://api.coinpayments.com">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Merchant ID') }}</label>
                                        <input type="text" class="form-control"
                                               id="{{ $key }}_merchant_id"
                                               value="{{ $gateway['merchant_id'] ?? '' }}"
                                               placeholder="COINPAYMENTS_MERCHANT_ID">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Client ID') }}</label>
                                        <input type="text" class="form-control"
                                               id="{{ $key }}_client_id"
                                               value="{{ $gateway['client_id'] ?? '' }}"
                                               placeholder="COINPAYMENTS_CLIENT_ID">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Client Secret') }}</label>
                                        <input type="password" class="form-control"
                                               id="{{ $key }}_client_secret"
                                               value="{{ $gateway['client_secret'] ?? '' }}"
                                               placeholder="COINPAYMENTS_CLIENT_SECRET">
                                    </div>
                                @elseif($key === 'fawaterk')
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Mode') }}</label>
                                        <select class="form-select" id="{{ $key }}_mode">
                                            <option value="staging" {{ ($gateway['mode'] ?? 'staging') === 'staging' ? 'selected' : '' }}>Staging</option>
                                            <option value="production" {{ ($gateway['mode'] ?? 'staging') === 'production' ? 'selected' : '' }}>Production</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Endpoint') }}</label>
                                        <input type="text" class="form-control"
                                               id="{{ $key }}_endpoint"
                                               value="{{ $gateway['endpoint'] ?? '' }}"
                                               placeholder="https://staging.fawaterk.com">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('API Key') }}</label>
                                        <input type="text" class="form-control"
                                               id="{{ $key }}_api_key"
                                               value="{{ $gateway['api_key'] ?? '' }}"
                                               placeholder="FAWATERK_API_KEY">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Provider Key') }}</label>
                                        <input type="text" class="form-control"
                                               id="{{ $key }}_provider_key"
                                               value="{{ $gateway['provider_key'] ?? '' }}"
                                               placeholder="FAWATERK_PROVIDER_KEY">
                                    </div>
                                @elseif($key === 'maxicard')
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Endpoint') }}</label>
                                        <input type="text" class="form-control"
                                               id="{{ $key }}_endpoint"
                                               value="{{ $gateway['endpoint'] ?? '' }}"
                                               placeholder="https://www.maxigame.org/epin/yukle.php">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('API Key') }}</label>
                                        <input type="text" class="form-control"
                                               id="{{ $key }}_api_key"
                                               value="{{ $gateway['api_key'] ?? '' }}"
                                               placeholder="MAXICARD_API_KEY">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('API Password') }}</label>
                                        <input type="password" class="form-control"
                                               id="{{ $key }}_api_password"
                                               value="{{ $gateway['api_password'] ?? '' }}"
                                               placeholder="MAXICARD_API_PASSWORD">
                                    </div>
                                @elseif($key === 'hipocard')
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Mode') }}</label>
                                        <select class="form-select" id="{{ $key }}_mode">
                                            <option value="sandbox" {{ ($gateway['mode'] ?? 'sandbox') === 'sandbox' ? 'selected' : '' }}>Sandbox</option>
                                            <option value="production" {{ ($gateway['mode'] ?? 'sandbox') === 'production' ? 'selected' : '' }}>Production</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Endpoint') }}</label>
                                        <input type="text" class="form-control"
                                               id="{{ $key }}_endpoint"
                                               value="{{ $gateway['endpoint'] ?? '' }}"
                                               placeholder="https://www.hipopotamya.com/api/sandbox/v1/hipocard/epins">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('API Key') }}</label>
                                        <input type="text" class="form-control"
                                               id="{{ $key }}_api_key"
                                               value="{{ $gateway['api_key'] ?? '' }}"
                                               placeholder="HIPOCARD_API_KEY">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('API Password') }}</label>
                                        <input type="password" class="form-control"
                                               id="{{ $key }}_api_password"
                                               value="{{ $gateway['api_password'] ?? '' }}"
                                               placeholder="HIPOCARD_API_PASSWORD">
                                    </div>
                                @elseif($key === 'hipopay')
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Commission Type') }}</label>
                                        <select class="form-select" id="{{ $key }}_commission_type">
                                            <option value="1" {{ ($gateway['commission_type'] ?? 1) === 1 ? 'selected' : '' }}>Type 1</option>
                                            <option value="2" {{ ($gateway['commission_type'] ?? 1) === 2 ? 'selected' : '' }}>Type 2</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Endpoint') }}</label>
                                        <input type="text" class="form-control"
                                               id="{{ $key }}_endpoint"
                                               value="{{ $gateway['endpoint'] ?? '' }}"
                                               placeholder="https://www.hipopotamya.com/api/v1/merchants/payment/token">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('API Key') }}</label>
                                        <input type="text" class="form-control"
                                               id="{{ $key }}_api_key"
                                               value="{{ $gateway['api_key'] ?? '' }}"
                                               placeholder="HIPOPAY_API_KEY">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('API Password') }}</label>
                                        <input type="password" class="form-control"
                                               id="{{ $key }}_api_password"
                                               value="{{ $gateway['api_password'] ?? '' }}"
                                               placeholder="HIPOPAY_API_PASSWORD">
                                    </div>
                                @else
                                    <div class="alert alert-info">
                                        {{ __('No specific configuration fields available for this gateway.') }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Packages --}}
                        <div class="mb-3">
                            <div class="fw-semibold mb-3">{{ __('Packages') }}</div>
                            <div class="mb-3">
                                <button type="button" class="btn btn-secondary btn-sm"
                                        onclick="addPkg('pkg-table-{{ $key }}')">
                                    {{ __('+ Add Package') }}
                                </button>
                            </div>

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
                                            <td><input type="text" class="form-control form-control-sm" data-pkg="name" value="{{ $pkg['name'] ?? '' }}"></td>
                                            <td><input type="number" class="form-control form-control-sm" data-pkg="price" value="{{ $pkg['price'] ?? '' }}" step="0.01" min="0"></td>
                                            <td><input type="number" class="form-control form-control-sm" data-pkg="value" value="{{ $pkg['value'] ?? '' }}" min="0"></td>
                                            <td><button type="button" class="btn btn-sm btn-danger" onclick="removePkg(this)">{{ __('Remove') }}</button></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td><input type="text" class="form-control" data-pkg="name" placeholder="500 Silk"></td>
                                            <td><input type="number" class="form-control" data-pkg="price" placeholder="5.00" step="0.01" min="0"></td>
                                            <td><input type="number" class="form-control" data-pkg="value" placeholder="500" min="0"></td>
                                            <td><button type="button" class="btn btn-sm btn-danger" onclick="removePkg(this)">{{ __('Remove') }}</button></td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
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
                <td><input type="text" class="form-control" data-pkg="name" placeholder="500 Silk"></td>
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
                    if (el.tagName === 'TEXTAREA' && (fieldKey === 'authorized_ips' || fieldKey === 'authorized_ranges')) {
                        // Convert textarea to array by splitting on newlines
                        const value = el.value.trim();
                        payload[fieldKey] = value ? value.split('\n').filter(ip => ip.trim()) : [];
                    } else {
                        payload[fieldKey] = el.type === 'checkbox' ? el.checked : el.value;
                    }
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

        // Auto-update endpoint URLs when mode changes
        document.addEventListener('DOMContentLoaded', function() {
            // PayPal mode switching
            const paypalModeSelect = document.getElementById('paypal_mode');
            const paypalEndpointInput = document.getElementById('paypal_endpoint');
            
            if (paypalModeSelect && paypalEndpointInput) {
                paypalModeSelect.addEventListener('change', function() {
                    if (this.value === 'live') {
                        paypalEndpointInput.value = 'https://api-m.paypal.com';
                    } else {
                        paypalEndpointInput.value = 'https://api-m.sandbox.paypal.com';
                    }
                });
            }
            
            // Fawaterk mode switching
            const fawaterkModeSelect = document.getElementById('fawaterk_mode');
            const fawaterkEndpointInput = document.getElementById('fawaterk_endpoint');
            
            if (fawaterkModeSelect && fawaterkEndpointInput) {
                fawaterkModeSelect.addEventListener('change', function() {
                    if (this.value === 'production') {
                        fawaterkEndpointInput.value = 'https://app.fawaterk.com';
                    } else {
                        fawaterkEndpointInput.value = 'https://staging.fawaterk.com';
                    }
                });
            }
            
            // HipoCard mode switching
            const hipocardModeSelect = document.getElementById('hipocard_mode');
            const hipocardEndpointInput = document.getElementById('hipocard_endpoint');
            
            if (hipocardModeSelect && hipocardEndpointInput) {
                hipocardModeSelect.addEventListener('change', function() {
                    if (this.value === 'production') {
                        hipocardEndpointInput.value = 'https://www.hipopotamya.com/api/v1/hipocard/epins';
                    } else {
                        hipocardEndpointInput.value = 'https://www.hipopotamya.com/api/sandbox/v1/hipocard/epins';
                    }
                });
            }
        });
    </script>
@endsection