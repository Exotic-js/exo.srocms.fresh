<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ExoAddons') — {{ config('exodash.title') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;450;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('ExoAddons/Dashboard/css/dashboard.css') }}">
    @stack('styles')
</head>
<body>

<div class="exo-shell">

    {{-- SIDEBAR --}}
    <aside class="exo-sidebar">
        <div class="exo-sidebar-brand">
            <div class="exo-brand-icon">
                <i class="fas fa-puzzle-piece"></i>
            </div>
            <div>
                <span class="exo-brand-name">ExoAddons</span>
                <span class="exo-brand-version">v{{ config('exodash.version', '1.0.0') }}</span>
            </div>
        </div>

        <nav class="exo-nav">
            <div class="exo-nav-label">Management</div>
            <a href="{{ route('exodash.addons') }}" class="exo-nav-item {{ request()->routeIs('exodash.addons') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i>
                <span>All Addons</span>
            </a>

            @if(config('affiliate.enabled', false) && Route::has('affiliate.admin.index'))
                <div class="exo-nav-label">Affiliate</div>
                <a href="{{ route('affiliate.admin.index') }}" class="exo-nav-item {{ request()->routeIs('affiliate.admin.index') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span>Recruit Teams</span>
                </a>
                <a href="{{ route('affiliate.admin.settings') }}" class="exo-nav-item {{ request()->routeIs('affiliate.admin.settings') ? 'active' : '' }}">
                    <i class="fas fa-sliders-h"></i>
                    <span>Control Panel</span>
                </a>
            @endif

            @if(!empty($addons ?? []))
                <div class="exo-nav-label">Installed</div>
                @foreach($addons ?? [] as $slug => $addon)
                    <a href="{{ route('exodash.manage', $slug) }}"
                       class="exo-nav-item {{ request()->routeIs('exodash.manage') && request()->route('name') === $slug ? 'active' : '' }}">
                        <span class="exo-addon-dot {{ $addon['enabled'] ? 'dot-green' : 'dot-red' }}"></span>
                        <span>{{ $addon['name'] }}</span>
                    </a>
                @endforeach
            @endif
        </nav>

        <div class="exo-sidebar-footer">
            <div class="exo-admin-info">
                <div class="exo-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div>
                    <div class="exo-admin-name">{{ auth()->user()->name }}</div>
                    <div class="exo-admin-role">Administrator</div>
                </div>
            </div>
        </div>
    </aside>

    {{-- MAIN --}}
    <main class="exo-main">
        <header class="exo-topbar">
            <div class="exo-topbar-title">@yield('page-title', 'Dashboard')</div>
            <div class="exo-topbar-actions">
                @if(Route::has('market.index'))
                    <a href="{{ route('market.index') }}" class="exo-btn exo-btn-ghost" target="_blank">
                        <i class="fas fa-external-link-alt"></i> View Market
                    </a>
                @endif
                <form method="POST" action="{{ route('logout') }}" style="display:inline">
                    @csrf
                    <button type="submit" class="exo-btn exo-btn-ghost">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </header>

        <div class="exo-content">

            @if(session('success'))
                <div class="exo-alert exo-alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="exo-alert exo-alert-error">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>

</div>

@stack('scripts')
</body>
</html>
