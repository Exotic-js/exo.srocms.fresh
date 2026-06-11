@extends('exodash::layout')

@section('title', 'Web Market — Settings')
@section('page-title', 'Web Market Settings')

@push('styles')
<style>
.wm-cp-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}
.wm-cp-section-title {
    font-size: 11px;
    font-weight: 600;
    letter-spacing: .07em;
    text-transform: uppercase;
    color: var(--text-3, #606068);
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    gap: 6px;
    border-bottom: 1px solid var(--border, #2a2a2e);
    padding-bottom: 8px;
}
.exo-input-plain {
    width: 100%;
    padding: .65rem 1rem;
    background: var(--exo-surface2);
    border: 1px solid var(--exo-border);
    border-radius: 8px;
    color: var(--exo-text);
    font-size: .88rem;
    font-family: var(--exo-font);
    transition: border-color .18s ease;
    outline: none;
}
.exo-input-plain:focus {
    border-color: var(--exo-purple);
    box-shadow: 0 0 0 3px var(--exo-purple-glow);
}
.exo-field { margin-bottom: 1.25rem; }
.exo-label {
    display: block;
    font-size: .83rem;
    font-weight: 600;
    color: var(--exo-text);
    margin-bottom: .45rem;
}
.exo-hint {
    font-size: .76rem;
    color: var(--exo-text-sub);
    margin-top: .35rem;
}
.exo-toggle {
    display: inline-flex;
    align-items: center;
    cursor: pointer;
}
.exo-toggle input {
    display: none;
}
.exo-toggle-track {
    width: 42px;
    height: 22px;
    background: #3f3f46;
    border-radius: 99px;
    position: relative;
    margin-right: 10px;
    transition: background 0.2s ease;
}
.exo-toggle-thumb {
    width: 16px;
    height: 16px;
    background: white;
    border-radius: 50%;
    position: absolute;
    top: 3px;
    left: 3px;
    transition: transform 0.2s ease;
}
.exo-toggle input:checked + .exo-toggle-track {
    background: #a855f7;
}
.exo-toggle input:checked + .exo-toggle-track .exo-toggle-thumb {
    transform: translateX(20px);
}
.exo-toggle-label {
    font-size: 0.9rem;
    font-weight: 600;
}
</style>
@endpush

@section('content')

@php
// Load current settings from DB (passed from DashboardController::manage())
$enabled  = isset($configs['webmarket.enabled'])  ? (bool) $configs['webmarket.enabled']  : true;
$tax      = $configs['webmarket.tax']              ?? 5;
$maxListings = $configs['webmarket.max_active_listings'] ?? 5;
$duration = $configs['webmarket.listing_duration_days'] ?? 7;
$minLevel = $configs['webmarket.min_level']        ?? 1;
@endphp

{{-- Page Header --}}
<div class="exo-page-header">
    <div>
        <a href="{{ route('exodash.addons') }}" class="exo-btn exo-btn-ghost exo-btn-sm" style="margin-bottom:.6rem">
            <i class="fas fa-arrow-left"></i> Back to Addons
        </a>
        <h1 class="exo-page-title"><i class="fas fa-cog"></i> Web Market Control Panel</h1>
        <p class="exo-page-sub">Configure player-to-player trade commissions, listing bounds, level restricts, and status</p>
    </div>
    <div style="display:flex;gap:.5rem;align-items:flex-start">
        <button type="submit" form="market-cp-form" class="exo-btn exo-btn-primary">
            <i class="fas fa-save"></i> Save Settings
        </button>
    </div>
</div>

<form id="market-cp-form" method="POST" action="{{ route('exodash.save', 'web-market') }}">
@csrf

<div class="exo-card" style="margin-bottom:1.5rem">
    <div class="exo-card-header"><i class="fas fa-shopping-cart"></i> Marketplace Settings</div>
    <div class="exo-card-body">
        
        <div class="wm-cp-section-title"><i class="fas fa-toggle-on"></i> Status Toggles</div>
        <div class="exo-field">
            <label class="exo-toggle">
                <input type="hidden" name="webmarket.enabled" value="0">
                <input type="checkbox" name="webmarket.enabled" value="1" {{ $enabled ? 'checked' : '' }}>
                <span class="exo-toggle-track"><span class="exo-toggle-thumb"></span></span>
                <span class="exo-toggle-label">Web Market System Enabled</span>
            </label>
            <p class="exo-hint">When disabled, player-to-player web market index and sell pages will display a 404.</p>
        </div>

        <div class="wm-cp-section-title" style="margin-top:1.5rem"><i class="fas fa-coins"></i> Economy & Taxation</div>
        <div class="wm-cp-grid">
            <div class="exo-field">
                <label class="exo-label">Sales Commission Tax (%)</label>
                <input type="number" name="webmarket.tax" class="exo-input-plain" min="0" max="99" value="{{ $tax }}">
                <p class="exo-hint">Percentage fee deducted from the final sale price (e.g. 5%). The rest is credited to the seller.</p>
            </div>
            
            <div class="exo-field">
                <label class="exo-label">Minimum Level to List Items</label>
                <input type="number" name="webmarket.min_level" class="exo-input-plain" min="1" max="150" value="{{ $minLevel }}">
                <p class="exo-hint">Only characters at or above this level can put items up for sale.</p>
            </div>
        </div>

        <div class="wm-cp-section-title" style="margin-top:1.5rem"><i class="fas fa-sliders-h"></i> Listing Constraints</div>
        <div class="wm-cp-grid">
            <div class="exo-field">
                <label class="exo-label">Max Active Listings per Character</label>
                <input type="number" name="webmarket.max_active_listings" class="exo-input-plain" min="1" max="50" value="{{ $maxListings }}">
                <p class="exo-hint">The maximum number of active items a single character can list at any time.</p>
            </div>

            <div class="exo-field">
                <label class="exo-label">Listing Expiration (Days)</label>
                <input type="number" name="webmarket.listing_duration_days" class="exo-input-plain" min="1" max="90" value="{{ $duration }}">
                <p class="exo-hint">The number of days a listing remains active before expiring and becoming a pending claim.</p>
            </div>
        </div>

    </div>
</div>

<div class="exo-form-actions">
    <a href="{{ route('exodash.addons') }}" class="exo-btn exo-btn-ghost">
        <i class="fas fa-times"></i> Cancel
    </a>
    <button type="submit" class="exo-btn exo-btn-primary">
        <i class="fas fa-save"></i> Save All Settings
    </button>
</div>

</form>
@endsection
