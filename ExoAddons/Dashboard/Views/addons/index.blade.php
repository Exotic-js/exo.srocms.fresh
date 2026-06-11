@extends('exodash::layout')

@section('title', 'Addons')

@section('content')

@if(session('success'))
    <div class="exo-alert exo-alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="exo-alert exo-alert-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
@endif

{{-- ── Page Header ─────────────────────────────────────────────── --}}
<div class="exo-page-header">
    <div>
        <h1 class="exo-page-title"><i class="fas fa-puzzle-piece"></i> Addons</h1>
        <p class="exo-page-sub">Manage, install, and configure ExoAddons</p>
    </div>
    <div class="exo-page-stats">
        @php
            $installed  = collect($addons)->where('installed', true)->count();
            $discovered = collect($addons)->where('installed', false)->count();
            $broken     = collect($addons)->where('state', 'broken')->count();
        @endphp
        <div class="exo-mini-stat"><span class="exo-mini-val">{{ $installed }}</span><span class="exo-mini-lbl">Installed</span></div>
        <div class="exo-mini-stat"><span class="exo-mini-val">{{ $discovered }}</span><span class="exo-mini-lbl">Available</span></div>
        @if($broken)
        <div class="exo-mini-stat broken"><span class="exo-mini-val">{{ $broken }}</span><span class="exo-mini-lbl">Broken</span></div>
        @endif
    </div>
</div>

{{-- ── INSTALLED / ACTIVE ──────────────────────────────────────── --}}
@php $installedAddons = collect($addons)->where('installed', true); @endphp
@if($installedAddons->isNotEmpty())
<div class="exo-section-label"><i class="fas fa-check-circle"></i> Installed</div>
<div class="exo-addons-grid">
    @foreach($installedAddons as $slug => $addon)
    <div class="exo-addon-card state-{{ $addon['state'] }}">
        <div class="exo-addon-card-top">
            <div class="exo-addon-icon"><i class="fas fa-puzzle-piece"></i></div>
            <div class="exo-addon-badge state-{{ $addon['state'] }}">
                @switch($addon['state'])
                    @case('installed')        <i class="fas fa-check-circle"></i> Active @break
                    @case('update_available') <i class="fas fa-arrow-circle-up"></i> Update Available @break
                    @case('disabled')         <i class="fas fa-pause-circle"></i> Disabled @break
                    @case('broken')           <i class="fas fa-exclamation-triangle"></i> Broken @break
                @endswitch
            </div>
        </div>
        <div class="exo-addon-body">
            <h3 class="exo-addon-name">{{ $addon['name'] }}</h3>
            <p class="exo-addon-desc">{{ $addon['description'] }}</p>
            <div class="exo-addon-meta">
                <span><i class="fas fa-tag"></i> v{{ $addon['version'] }}</span>
                @if($addon['installed_at'])
                <span><i class="fas fa-clock"></i> {{ \Carbon\Carbon::parse($addon['installed_at'])->diffForHumans() }}</span>
                @endif
                @if($addon['health'] === false)
                <span class="meta-warn"><i class="fas fa-heartbeat"></i> Health Failed</span>
                @endif
            </div>
        </div>
        <div class="exo-addon-actions">
            @if($addon['state'] === 'broken')
                <form method="POST" action="{{ route('exodash.uninstall', $addon['slug']) }}"
                      onsubmit="return confirm('Hard uninstall? This will roll back migrations and delete all data.')">
                    @csrf
                    <input type="hidden" name="hard" value="1">
                    <button class="exo-btn exo-btn-danger exo-btn-sm">
                        <i class="fas fa-trash"></i> Remove Record
                    </button>
                </form>
            @elseif($addon['state'] === 'update_available')
                <form method="POST" action="{{ route('exodash.update', $addon['slug']) }}">
                    @csrf
                    <button class="exo-btn exo-btn-primary exo-btn-sm"><i class="fas fa-arrow-up"></i> Update</button>
                </form>
                @if($addon['settings_view'])
                <a href="{{ route('exodash.manage', $addon['slug']) }}" class="exo-btn exo-btn-ghost exo-btn-sm">
                    <i class="fas fa-cog"></i> Manage
                </a>
                @endif
            @else
                @if($addon['settings_view'])
                <a href="{{ route('exodash.manage', $addon['slug']) }}" class="exo-btn exo-btn-ghost exo-btn-sm">
                    <i class="fas fa-cog"></i> Manage
                </a>
                @endif
                <form method="POST" action="{{ route('exodash.toggle', $addon['slug']) }}">
                    @csrf
                    <button class="exo-btn exo-btn-ghost exo-btn-sm">
                        @if($addon['enabled'])
                        <i class="fas fa-pause"></i> Disable
                        @else
                        <i class="fas fa-play"></i> Enable
                        @endif
                    </button>
                </form>
                <form method="POST" action="{{ route('exodash.uninstall', $addon['slug']) }}"
                      onsubmit="return confirm('Hard uninstall {{ $addon['name'] }}? This will roll back migrations and delete all data.')">
                    @csrf
                    <input type="hidden" name="hard" value="1">
                    <button class="exo-btn exo-btn-danger exo-btn-sm"><i class="fas fa-trash"></i></button>
                </form>
            @endif
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- ── AVAILABLE (discovered but not installed) ────────────────── --}}
@php $availableAddons = collect($addons)->where('installed', false); @endphp
@if($availableAddons->isNotEmpty())
<div class="exo-section-label" style="margin-top:2rem"><i class="fas fa-box-open"></i> Available</div>
<div class="exo-addons-grid">
    @foreach($availableAddons as $slug => $addon)
    <div class="exo-addon-card state-discovered">
        <div class="exo-addon-card-top">
            <div class="exo-addon-icon dim"><i class="fas fa-puzzle-piece"></i></div>
            <div class="exo-addon-badge state-discovered"><i class="fas fa-search"></i> Discovered</div>
        </div>
        <div class="exo-addon-body">
            <h3 class="exo-addon-name">{{ $addon['name'] }}</h3>
            <p class="exo-addon-desc">{{ $addon['description'] }}</p>
            <div class="exo-addon-meta">
                <span><i class="fas fa-tag"></i> v{{ $addon['version'] }}</span>
                <span><i class="fas fa-user"></i> {{ $addon['author'] }}</span>
                @if(!empty($addon['requires']))
                <span><i class="fas fa-link"></i> Requires: {{ implode(', ', $addon['requires']) }}</span>
                @endif
            </div>
        </div>
        <div class="exo-addon-actions">
            <form method="POST" action="{{ route('exodash.setup', $addon['slug']) }}">
                @csrf
                <button class="exo-btn exo-btn-primary exo-btn-sm">
                    <i class="fas fa-download"></i> Setup
                </button>
            </form>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- Empty State --}}
@if(empty($addons))
<div class="exo-empty">
    <i class="fas fa-puzzle-piece"></i>
    <h3>No Addons Found</h3>
    <p>Drop an addon folder into <code>ExoAddons/</code> with an <code>addon.json</code> manifest to get started.</p>
</div>
@endif

@endsection
