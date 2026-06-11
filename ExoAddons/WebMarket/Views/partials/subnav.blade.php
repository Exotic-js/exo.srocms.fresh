<div class="market-subnav mb-4">
    {{-- Page Title Row --}}
    <div class="ranking-header-ornament">
        <div class="line"></div>
        <div style="display:flex; align-items:center; gap:14px; padding: 0 20px;">
            <div class="golden-hero-diamond" style="width:14px;height:14px;margin:0;"><div class="golden-hero-diamond-glow"></div></div>
            <span style="font-family: var(--font-heading); font-size:1.4rem; font-weight:700; color: var(--gold-light); letter-spacing:3px; text-transform:uppercase;">
                @if($activeTab === 'browse') <i class="fas fa-store" style="color:var(--gold-primary);"></i> SRO Web Market
                @elseif($activeTab === 'sell') <i class="fas fa-tags" style="color:var(--gold-primary);"></i> Place Listing
                @elseif($activeTab === 'claims') <i class="fas fa-box-open" style="color:var(--gold-primary);"></i> Pending Claims
                @endif
            </span>
            <div class="golden-hero-diamond" style="width:14px;height:14px;margin:0;"><div class="golden-hero-diamond-glow"></div></div>
        </div>
        <div class="line"></div>
    </div>

    {{-- Subnav Tabs --}}
    <div class="d-flex justify-content-center gap-3 mt-3">
        {{-- Browse tab — always visible --}}
        <a href="{{ route('market.index') }}" class="market-tab-btn {{ $activeTab === 'browse' ? 'market-tab-active' : '' }}">
            <i class="fas fa-search"></i> {{ __('Browse') }}
        </a>

        @auth
        <a href="{{ route('market.sell') }}" class="market-tab-btn {{ $activeTab === 'sell' ? 'market-tab-active' : '' }}">
            <i class="fas fa-tags"></i> {{ __('List Item') }}
        </a>
        <a href="{{ route('market.claims') }}" class="market-tab-btn {{ $activeTab === 'claims' ? 'market-tab-active' : '' }}">
            <i class="fas fa-box-open"></i> {{ __('Pending Claims') }}
            @if(isset($pendingClaimsCount) && $pendingClaimsCount > 0)
                <span class="market-tab-badge">{{ $pendingClaimsCount }}</span>
            @endif
        </a>
        @endauth
    </div>

    {{-- Golden separator --}}
    <div class="golden-glow-divider mt-4" style="margin-bottom:0;"></div>
</div>

<style>
.market-tab-btn {
    position: relative;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 22px;
    background: linear-gradient(180deg, #1f180f 0%, #0a0805 100%);
    border: 1px solid #4a381c;
    color: #e0d0a6;
    font-family: var(--font-heading);
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    text-decoration: none;
    clip-path: polygon(10px 0%, calc(100% - 10px) 0%, 100% 50%, calc(100% - 10px) 100%, 10px 100%, 0% 50%);
    transition: all 0.25s ease;
}
.market-tab-btn:hover {
    background: linear-gradient(180deg, #2e2010 0%, #150e06 100%);
    border-color: var(--gold-primary);
    color: var(--gold-light);
    filter: brightness(1.15);
}
.market-tab-active {
    background: linear-gradient(180deg, #cfad6b 0%, #7a5423 100%) !important;
    border-color: #cfad6b !important;
    color: #000 !important;
    font-weight: 900 !important;
    box-shadow: 0 0 15px rgba(207,173,107,0.35);
}
.market-tab-active i { color: #000 !important; }
.market-tab-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 18px;
    height: 18px;
    background: #c0392b;
    border-radius: 50%;
    font-size: 10px;
    font-weight: 900;
    color: #fff;
    font-family: var(--font-body);
}
</style>
