@extends('layouts.full')

@section('title', 'Web Market')

@push('styles')
<link rel="stylesheet" href="{{ asset('ExoAddons/WebMarket/css/market.css') }}">
@endpush

@section('content')
@include('web-market::partials.subnav', ['activeTab' => 'browse'])

@if(session('success'))
    <div class="golden-alert golden-alert-success mb-3">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="golden-alert golden-alert-danger mb-3">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
@endif

{{-- ======================== FILTER BAR ======================== --}}
<div class="market-filters-bar mb-4">
    <form method="GET" action="{{ route('market.index') }}">
        <div class="market-filters-grid">
            <div class="filter-group">
                <label for="search">Keyword</label>
                <input type="text" id="search" name="search" class="filter-control"
                       placeholder="Item name, seller..." value="{{ request('search') }}">
            </div>

            <div class="filter-group">
                <label for="type">Slot Category</label>
                <select id="type" name="type" class="filter-control">
                    <option value="">All Categories</option>
                    <option value="weapon"    {{ request('type') == 'weapon'    ? 'selected' : '' }}>Weapons</option>
                    <option value="shield"    {{ request('type') == 'shield'    ? 'selected' : '' }}>Shields</option>
                    <option value="armor"     {{ request('type') == 'armor'     ? 'selected' : '' }}>Armors</option>
                    <option value="accessory" {{ request('type') == 'accessory' ? 'selected' : '' }}>Accessories</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="plus">Minimum Plus</label>
                <select id="plus" name="plus" class="filter-control">
                    <option value="">Any Plus</option>
                    @for($i = 1; $i <= 15; $i++)
                        <option value="{{ $i }}" {{ request('plus') == $i ? 'selected' : '' }}>+{{ $i }}</option>
                    @endfor
                </select>
            </div>

            <div class="filter-group">
                <label for="currency">Currency</label>
                <select id="currency" name="currency" class="filter-control">
                    <option value="">Any Currency</option>
                    <option value="gold"   {{ request('currency') == 'gold'   ? 'selected' : '' }}>Gold</option>
                    <option value="silk"   {{ request('currency') == 'silk'   ? 'selected' : '' }}>Silk</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="sort">Sorting</label>
                <select id="sort" name="sort" class="filter-control">
                    <option value="latest"     {{ request('sort') == 'latest'     ? 'selected' : '' }}>Latest Listings</option>
                    <option value="price_asc"  {{ request('sort') == 'price_asc'  ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                    <option value="plus_desc"  {{ request('sort') == 'plus_desc'  ? 'selected' : '' }}>Plus: High to Low</option>
                </select>
            </div>

            <div class="filter-group filter-actions">
                <label>&nbsp;</label>
                <div class="d-flex gap-2 align-items-center">
                    <button type="submit" class="btn-market-search flex-grow-1">
                        <i class="fas fa-filter"></i> Apply
                    </button>
                    <a href="{{ route('market.index') }}" class="btn-market-reset" title="Reset filters">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- ======================== LISTINGS GRID ======================== --}}
@if($listings->isEmpty())
    <div class="market-empty-state">
        <div class="market-empty-icon">
            <i class="fas fa-store-slash"></i>
        </div>
        <h3>No Active Listings</h3>
        <p>Be the first to list an item in the market!</p>
        @auth
        <a href="{{ route('market.sell') }}" class="btn-golden-buy" style="display:inline-block; width:auto; padding:10px 32px; text-decoration:none;">
            <i class="fas fa-tags"></i> List an Item
        </a>
        @endauth
    </div>
@else
    <div class="listings-grid">
        @foreach($listings as $item)
            @php
                // item_data_json is cast to array by model — always cast to object for property access
                $parsed  = (object) ($item->item_data_json ?? []);
                $isSox   = !empty($parsed->SoxType) && $parsed->SoxType !== 'Normal';
                $soxKey  = $isSox ? strtolower(str_replace(' ', '-', $parsed->SoxType ?? '')) : '';

                // Icon path (null-safe — old listings may lack AssocFileIcon128)
                $iconPath     = str_replace('\\', '/', trim($parsed->AssocFileIcon128 ?? ''));
                $iconPath     = preg_replace('/\.ddj$/i', '', $iconPath);
                $iconPath     = strtolower($iconPath . '.png');
                $fullIconPath = public_path('images/sro/' . $iconPath);
                $imgUrl       = file_exists($fullIconPath) ? asset('images/sro/' . $iconPath) : asset('images/sro/icon_default.png');

                // Item display name — null-safe fallback chain
                $displayName = $parsed->ItemName ?? $parsed->CodeName128 ?? $parsed->NameStrID128 ?? 'Unknown Item';

                // Tooltip HTML
                $tooltipHtml = '<div class="tooltip-header">';
                $tooltipHtml .= '<div class="tooltip-item-name">' . e($displayName) . ($item->plus_opt > 0 ? ' (+' . $item->plus_opt . ')' : '') . '</div>';
                if ($isSox) {
                    $tooltipHtml .= '<div style="color:#87cefa; font-weight:700;">' . e($parsed->SoxType ?? '') . ' (' . e($parsed->SoxName ?? '') . ')</div>';
                }
                $tooltipHtml .= '</div><div class="tooltip-white-info">';
                $tooltipHtml .= '<div>Degree: ' . ($parsed->Degree ?? 'N/A') . ' Deg</div>';
                if (!empty($parsed->Gender))    { $tooltipHtml .= '<div>Gender: ' . e($parsed->Gender) . '</div>'; }
                if (!empty($parsed->ReqLevel1)) { $tooltipHtml .= '<div>Required Lv.: ' . ($parsed->ReqLevel1 ?? '') . '</div>'; }
                $wInfo = (object) ($parsed->WhiteInfo ?? []);
                if (!empty($wInfo->PAtack))    { $tooltipHtml .= '<div>' . e($wInfo->PAtack) . '</div>'; }
                if (!empty($wInfo->MAtack))    { $tooltipHtml .= '<div>' . e($wInfo->MAtack) . '</div>'; }
                if (!empty($wInfo->PDefance))  { $tooltipHtml .= '<div>' . e($wInfo->PDefance) . '</div>'; }
                if (!empty($wInfo->MDefance))  { $tooltipHtml .= '<div>' . e($wInfo->MDefance) . '</div>'; }
                if (!empty($wInfo->Durability)){ $tooltipHtml .= '<div>' . e($wInfo->Durability) . '</div>'; }
                if (!empty($wInfo->Critical))  { $tooltipHtml .= '<div>' . e($wInfo->Critical) . '</div>'; }
                $tooltipHtml .= '</div>';
                $bInfo = $parsed->BlueInfo ?? [];
                if (!empty($bInfo)) {
                    $tooltipHtml .= '<div class="tooltip-blue-info">';
                    foreach ($bInfo as $blue) { $tooltipHtml .= '<div>• ' . e($blue['name'] ?? $blue['code'] ?? '') . '</div>'; }
                    $tooltipHtml .= '</div>';
                }
                if (!empty($parsed->ItemDesc)) {
                    $tooltipHtml .= '<div class="tooltip-desc">' . e($parsed->ItemDesc) . '</div>';
                }
            @endphp

            <div class="item-card {{ $isSox ? 'is-sox' : '' }}" data-tooltip="{{ $tooltipHtml }}">

                {{-- SOX Badge --}}
                @if($isSox)
                    <span class="sox-badge seal-{{ $soxKey }}">{{ $parsed->SoxType }}</span>
                @endif

                {{-- Card Header: Icon + Name --}}
                <div class="item-card-header">
                    <div class="item-icon-box">
                        <img src="{{ $imgUrl }}" alt="Item Icon" onerror="this.src='{{ asset('images/sro/icon_default.png') }}'">
                    </div>
                    <div class="item-title-meta">
                        <h5 class="item-name-title">{{ $displayName }}</h5>
                        @if($item->plus_opt > 0)
                            <span class="plus-badge">+{{ $item->plus_opt }}</span>
                        @endif
                    </div>
                </div>

                {{-- Card Body: Specs + Price --}}
                <div class="item-card-body">
                    <div class="item-specs-list">
                        <div class="item-spec-row">
                            <span>Degree</span>
                            <span class="spec-val-highlight">{{ $parsed->Degree ?? 'N/A' }} Deg</span>
                        </div>
                        <div class="item-spec-row">
                            <span>Req. Level</span>
                            <span class="spec-val-highlight">Lv. {{ $parsed->ReqLevel1 ?? '1' }}</span>
                        </div>
                        <div class="item-spec-row">
                            <span>Seller</span>
                            <span class="spec-val-highlight" style="color:#7ab8d4;">{{ $item->char_name }}</span>
                        </div>
                        <div class="item-spec-row">
                            <span>Expires</span>
                            <span class="spec-val-highlight">{{ $item->expires_at->diffForHumans() }}</span>
                        </div>
                    </div>

                    <div class="price-display-box">
                        <span class="currency-icon {{ $item->currency }}">{{ strtoupper(substr($item->currency, 0, 1)) }}</span>
                        <span>
                            {{ number_format($item->price) }}
                            {{ ucfirst($item->currency) }}
                        </span>
                    </div>
                </div>

                {{-- Card Actions --}}
                <div class="item-card-actions">
                    @if(Auth::check() && Auth::user()->jid === $item->account_id)
                        {{-- Owner cancel --}}
                        <button type="button" class="btn-market-danger w-100"
                                data-bs-toggle="modal" data-bs-target="#cancelModal-{{ $item->id }}">
                            <i class="fas fa-times-circle"></i> Cancel Listing
                        </button>
                    @elseif(Auth::check())
                        @if($item->currency === 'gold')
                            {{-- Gold: needs character selection --}}
                            <button class="btn-golden-buy" data-bs-toggle="modal" data-bs-target="#buyModal-{{ $item->id }}">
                                <i class="fas fa-coins"></i> Buy with Gold
                            </button>
                        @elseif($item->currency === 'silk')
                            {{-- Silk: custom confirm modal --}}
                            <button class="btn-golden-buy" data-bs-toggle="modal" data-bs-target="#silkModal-{{ $item->id }}">
                                <i class="fas fa-gem"></i> Buy with Silk
                            </button>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn-golden-buy" style="display:block; text-align:center; text-decoration:none;">
                            <i class="fas fa-sign-in-alt"></i> Login to Buy
                        </a>
                    @endif
                </div>
            </div>

            {{-- Gold Purchase Modal (outside card to avoid z-index nesting issues) --}}
            @if(Auth::check() && Auth::user()->jid !== $item->account_id && $item->currency === 'gold')
            <div class="modal fade" id="buyModal-{{ $item->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content golden-modal">
                        <div class="modal-header golden-modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-coins" style="color:var(--gold-primary);"></i>
                                Complete Gold Purchase
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="POST" action="{{ route('market.buy', $item->id) }}">
                            @csrf
                            <div class="modal-body golden-modal-body">
                                {{-- Item preview --}}
                                <div class="golden-modal-item-preview">
                                    <img src="{{ $imgUrl }}" class="golden-modal-item-icon" alt="">
                                    <div>
                                        <div class="golden-modal-item-name">{{ $parsed->ItemName ?? $parsed->CodeName128 ?? $parsed->NameStrID128 ?? 'Unknown Item' }}
                                            @if($item->plus_opt > 0)<span class="plus-badge ms-1">+{{ $item->plus_opt }}</span>@endif
                                        </div>
                                        <div class="golden-modal-price">
                                            <span class="currency-icon gold" style="display:inline-flex; vertical-align:middle; margin-right:5px;">G</span>
                                            {{ number_format($item->price) }} Gold
                                        </div>
                                    </div>
                                </div>
                                <p class="golden-modal-note">
                                    <i class="fas fa-bolt" style="color:var(--gold-primary);"></i>
                                    Gold is deducted live via <strong>Vanguard Filter</strong> — works online or offline!
                                </p>
                                <div class="filter-group">
                                    <label for="buyer_char_id_{{ $item->id }}">Select Character</label>
                                    <select name="buyer_char_id" id="buyer_char_id_{{ $item->id }}" class="filter-control" required>
                                        <option value="">-- Choose Character --</option>
                                        @foreach($buyerChars as $bc)
                                            <option value="{{ $bc->CharID }}">
                                                {{ $bc->CharName16 }} (~{{ number_format($bc->RemainGold ?? 0) }} Gold)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer golden-modal-footer">
                                <button type="button" class="btn-market-reset" data-bs-dismiss="modal" style="padding:9px 20px;">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                                <button type="submit" class="btn-golden-buy" style="clip-path:none; padding:9px 24px;">
                                    <i class="fas fa-coins"></i> Confirm Purchase
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif

            {{-- Silk Purchase Modal --}}
            @if(Auth::check() && Auth::user()->jid !== $item->account_id && $item->currency === 'silk')
            <div class="modal fade" id="silkModal-{{ $item->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content golden-modal">
                        <div class="modal-header golden-modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-gem" style="color:#a78bfa;"></i>
                                Confirm Silk Purchase
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="POST" action="{{ route('market.buy', $item->id) }}">
                            @csrf
                            <div class="modal-body golden-modal-body">
                                {{-- Item preview --}}
                                <div class="golden-modal-item-preview">
                                    <img src="{{ $imgUrl }}" class="golden-modal-item-icon" alt="">
                                    <div>
                                        <div class="golden-modal-item-name">{{ $displayName }}
                                            @if($item->plus_opt > 0)<span class="plus-badge ms-1">+{{ $item->plus_opt }}</span>@endif
                                        </div>
                                        <div class="golden-modal-price">
                                            <span style="color:#a78bfa; font-weight:700;">&#9830;</span>
                                            {{ number_format($item->price) }} Silk
                                        </div>
                                    </div>
                                </div>
                                <p class="golden-modal-note">
                                    <i class="fas fa-gem" style="color:#a78bfa;"></i>
                                    Silk will be <strong>deducted from your account</strong> instantly upon confirmation.
                                </p>
                            </div>
                            <div class="modal-footer golden-modal-footer">
                                <button type="button" class="btn-market-reset" data-bs-dismiss="modal" style="padding:9px 20px;">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                                <button type="submit" class="btn-golden-buy" style="clip-path:none; padding:9px 24px; background:linear-gradient(135deg,#7c3aed,#a78bfa);">
                                    <i class="fas fa-gem"></i> Confirm Purchase
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif

            {{-- Cancel Listing Modal --}}
            @auth
            @if(Auth::user()->jid === $item->account_id)
            <div class="modal fade" id="cancelModal-{{ $item->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content golden-modal">
                        <div class="modal-header golden-modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-times-circle" style="color:#f87171;"></i>
                                Cancel Listing
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="POST" action="{{ route('market.cancel', $item->id) }}">
                            @csrf
                            <div class="modal-body golden-modal-body">
                                <div class="golden-modal-item-preview">
                                    <img src="{{ $imgUrl }}" class="golden-modal-item-icon" alt="">
                                    <div>
                                        <div class="golden-modal-item-name">{{ $displayName }}
                                            @if($item->plus_opt > 0)<span class="plus-badge ms-1">+{{ $item->plus_opt }}</span>@endif
                                        </div>
                                    </div>
                                </div>
                                <p class="golden-modal-note" style="color:#f87171; border-color:#f8717133;">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    The item will be <strong>returned to your character</strong> via the game server.
                                </p>
                            </div>
                            <div class="modal-footer golden-modal-footer">
                                <button type="button" class="btn-market-reset" data-bs-dismiss="modal" style="padding:9px 20px;">
                                    <i class="fas fa-arrow-left"></i> Keep Listing
                                </button>
                                <button type="submit" class="btn-market-danger" style="padding:9px 24px;">
                                    <i class="fas fa-times-circle"></i> Cancel Listing
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif
            @endauth
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="mt-4 d-flex justify-content-center">
        {{ $listings->links('pagination::bootstrap-5') }}
    </div>
@endif

{{-- Global SRO Tooltip --}}
<div id="sro-tooltip-box" class="sro-tooltip"></div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    var tooltip = $('#sro-tooltip-box');

    $('.item-card').hover(function (e) {
        var html = $(this).data('tooltip');
        if (html) tooltip.html(html).show();
    }, function () {
        tooltip.hide();
    }).mousemove(function (e) {
        tooltip.css({ top: (e.pageY + 15) + 'px', left: (e.pageX + 15) + 'px' });
    });
});
</script>
@endpush
