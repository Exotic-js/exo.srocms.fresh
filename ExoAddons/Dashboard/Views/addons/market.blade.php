@extends('exodash::layout')

@section('title', 'Market — Config')
@section('page-title', 'ExoAddons.Market')

@section('content')

{{-- Header --}}
<div class="exo-page-header">
    <div>
        <h2 class="exo-page-title"><i class="fas fa-store" style="color:#8b5cf6"></i> ExoAddons.Market</h2>
        <p class="exo-page-sub">P2P Marketplace — players buy and sell items using Silk</p>
    </div>
    <div class="exo-addon-status {{ $addon['enabled'] ? 'status-on' : 'status-off' }}" style="font-size:.9rem;padding:.4rem 1rem">
        <span class="status-dot"></span>
        {{ $addon['enabled'] ? 'Active' : 'Disabled' }}
    </div>
</div>

{{-- Stats --}}
<div class="exo-stats-row">
    <div class="exo-stat-card">
        <div class="exo-stat-icon" style="color:#8b5cf6"><i class="fas fa-tags"></i></div>
        <div class="exo-stat-info">
            <div class="exo-stat-num">{{ number_format($addon['stats']['active']) }}</div>
            <div class="exo-stat-lbl">Active Listings</div>
        </div>
    </div>
    <div class="exo-stat-card">
        <div class="exo-stat-icon" style="color:#22c55e"><i class="fas fa-check-circle"></i></div>
        <div class="exo-stat-info">
            <div class="exo-stat-num">{{ number_format($addon['stats']['sold']) }}</div>
            <div class="exo-stat-lbl">Items Sold</div>
        </div>
    </div>
    <div class="exo-stat-card">
        <div class="exo-stat-icon" style="color:#f59e0b"><i class="fas fa-xmark-circle"></i></div>
        <div class="exo-stat-info">
            <div class="exo-stat-num">{{ number_format($addon['stats']['cancelled']) }}</div>
            <div class="exo-stat-lbl">Cancelled</div>
        </div>
    </div>
    <div class="exo-stat-card">
        <div class="exo-stat-icon" style="color:#06b6d4"><i class="fas fa-coins"></i></div>
        <div class="exo-stat-info">
            <div class="exo-stat-num">{{ number_format($addon['stats']['total_silk']) }}</div>
            <div class="exo-stat-lbl">Silk Traded</div>
        </div>
    </div>
</div>

{{-- Config Form --}}
<form method="POST" action="{{ route('exodash.save', 'market') }}">
    @csrf

    <div class="exo-card">
        <div class="exo-card-header">
            <i class="fas fa-sliders"></i> General Settings
        </div>
        <div class="exo-card-body">

            {{-- Enable/Disable --}}
            <div class="exo-field">
                <label class="exo-label">Marketplace Status</label>
                <label class="exo-toggle">
                    <input type="checkbox" name="enabled" value="1"
                           {{ filter_var($addon['settings']['enabled'] ?? false, FILTER_VALIDATE_BOOLEAN) ? 'checked' : '' }}>
                    <span class="exo-toggle-track">
                        <span class="exo-toggle-thumb"></span>
                    </span>
                    <span class="exo-toggle-label">Enable Marketplace</span>
                </label>
                <p class="exo-hint">When disabled, all /market routes return 404.</p>
            </div>

            {{-- Currency --}}
            <div class="exo-field">
                <label class="exo-label">Silk Currency</label>
                <select name="currency" class="exo-select">
                    <option value="silk_own"   {{ ($addon['settings']['currency'] ?? 'silk_own') === 'silk_own'   ? 'selected' : '' }}>silk_own</option>
                    <option value="silk_gift"  {{ ($addon['settings']['currency'] ?? 'silk_own') === 'silk_gift'  ? 'selected' : '' }}>silk_gift</option>
                    <option value="silk_point" {{ ($addon['settings']['currency'] ?? 'silk_own') === 'silk_point' ? 'selected' : '' }}>silk_point</option>
                </select>
                <p class="exo-hint">Which silk column to deduct from buyers and pay to sellers.</p>
            </div>

        </div>
    </div>

    <div class="exo-card" style="margin-top:1.25rem">
        <div class="exo-card-header">
            <i class="fas fa-percentage"></i> Pricing & Limits
        </div>
        <div class="exo-card-body">

            <div class="exo-fields-row">
                {{-- Fee % --}}
                <div class="exo-field">
                    <label class="exo-label">Seller Fee %</label>
                    <div class="exo-input-wrap">
                        <i class="fas fa-percent exo-input-icon"></i>
                        <input type="number" name="fee" class="exo-input"
                               min="0" max="50" step="0.5"
                               value="{{ $addon['settings']['fee'] ?? 5 }}">
                    </div>
                    <p class="exo-hint">Deducted from seller's payment. 5 = 5%.</p>
                </div>

                {{-- Max Active Listings --}}
                <div class="exo-field">
                    <label class="exo-label">Max Active Listings per User</label>
                    <div class="exo-input-wrap">
                        <i class="fas fa-list-check exo-input-icon"></i>
                        <input type="number" name="maxList" class="exo-input"
                               min="1" max="100"
                               value="{{ $addon['settings']['maxList'] ?? 10 }}">
                    </div>
                    <p class="exo-hint">Maximum concurrent listings a seller can have.</p>
                </div>
            </div>

            <div class="exo-fields-row">
                {{-- Min Price --}}
                <div class="exo-field">
                    <label class="exo-label">Minimum Price (Silk)</label>
                    <div class="exo-input-wrap">
                        <i class="fas fa-arrow-down exo-input-icon"></i>
                        <input type="number" name="minP" class="exo-input"
                               min="1"
                               value="{{ $addon['settings']['minP'] ?? 1 }}">
                    </div>
                </div>

                {{-- Max Price --}}
                <div class="exo-field">
                    <label class="exo-label">Maximum Price (Silk)</label>
                    <div class="exo-input-wrap">
                        <i class="fas fa-arrow-up exo-input-icon"></i>
                        <input type="number" name="maxP" class="exo-input"
                               value="{{ $addon['settings']['maxP'] ?? 999999 }}">
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="exo-form-actions">
        <a href="{{ route('exodash.addons') }}" class="exo-btn exo-btn-ghost">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        <button type="submit" class="exo-btn exo-btn-primary">
            <i class="fas fa-save"></i> Save Settings
        </button>
    </div>

</form>

@endsection
