@extends('layouts.full')

@section('title', 'Pending Claims')

@push('styles')
<link rel="stylesheet" href="{{ asset('ExoAddons/WebMarket/css/market.css') }}">
@endpush

@section('content')
@include('web-market::partials.subnav', ['activeTab' => 'claims'])

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

{{-- ===================== REQUIREMENTS NOTICE ===================== --}}
<div class="market-notice mb-4">
    <div class="market-notice-title">
        <i class="fas fa-info-circle"></i> How to Claim
    </div>
    <ul class="market-notice-list">
        <li>Select a character below and click <strong>Send to Chest</strong> to receive your item.</li>
        <li>Items are delivered through the Vanguard <strong>Chest Box</strong>.</li>
        <li>After claiming, open your in-game web/item chest to collect the item.</li>
    </ul>
</div>

{{-- ===================== CLAIMS LIST ===================== --}}
@if($claims->isEmpty())
    <div class="market-empty-state">
        <div class="market-empty-icon">
            <i class="fas fa-box-open"></i>
        </div>
        <h3>No Pending Claims</h3>
        <p>Purchased items or cancelled listings will show up here.</p>
        <a href="{{ route('market.index') }}" class="btn-golden-buy" style="display:inline-block; width:auto; padding:10px 32px; text-decoration:none;">
            <i class="fas fa-search"></i> Browse Market
        </a>
    </div>
@else
    <div class="claims-grid">
        @foreach($claims as $claim)
            <div class="claim-card">

                {{-- Card Header --}}
                <div class="claim-card-header">
                    @php
                        $typeBadgeClass = match($claim->type) {
                            'purchase'      => 'claim-badge-purchase',
                            'return'        => 'claim-badge-return',
                            'gold_proceeds' => 'claim-badge-gold',
                            default         => 'claim-badge-return',
                        };
                        $typeLabel = strtoupper(str_replace('_', ' ', $claim->type));
                    @endphp
                    <span class="claim-badge {{ $typeBadgeClass }}">{{ $typeLabel }}</span>
                    <span class="claim-time">
                        <i class="fas fa-calendar-alt"></i> {{ $claim->created_at->diffForHumans() }}
                    </span>
                </div>

                {{-- Card Body: Item / Gold Preview --}}
                <div class="claim-card-body">
                    @if($claim->type === 'gold_proceeds')
                        <div class="claim-item-preview">
                            <div class="currency-icon gold" style="width:48px; height:48px; font-size:1.4rem; flex-shrink:0;">G</div>
                            <div>
                                <div class="claim-item-name" style="color:#69cf69;">
                                    {{ number_format($claim->item_data_json['gold_amount'] ?? 0) }} Gold
                                </div>
                                <div class="claim-item-sub">Proceeds from listing sale</div>
                            </div>
                        </div>
                    @else
                        @php
                            $parsed = $claim->parsed_item;
                            $imgUrl = asset('images/sro/icon_default.png');
                            if ($parsed && !empty($parsed->ImgPath)) {
                                $fullIconPath = public_path('images/sro/' . $parsed->ImgPath);
                                $imgUrl = file_exists($fullIconPath)
                                    ? asset('images/sro/' . $parsed->ImgPath)
                                    : asset('images/sro/icon_default.png');
                            }
                        @endphp
                        @if($parsed)
                            <div class="claim-item-preview">
                                <div class="item-icon-box" style="width:52px; height:52px; flex-shrink:0;">
                                    <img src="{{ $imgUrl }}" alt="Item"
                                         onerror="this.src='{{ asset('images/sro/icon_default.png') }}'">
                                </div>
                                <div>
                                    <div class="claim-item-name">
                                        {{ $parsed->ItemName }}
                                        @if($parsed->OptLevel > 0)
                                            &nbsp;<span class="plus-badge">+{{ $parsed->OptLevel }}</span>
                                        @endif
                                    </div>
                                    <div class="claim-item-sub">
                                        @if($parsed->Degree)Degree {{ $parsed->Degree }}@endif
                                        @if($parsed->SoxType && $parsed->SoxType !== 'Normal')
                                            &nbsp;<span style="color:#87cefa;">{{ $parsed->SoxType }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="claim-item-preview">
                                <div class="golden-alert golden-alert-danger" style="margin:0; width:100%; font-size:12px;">
                                    <i class="fas fa-exclamation-triangle"></i> Item data could not be parsed.
                                </div>
                            </div>
                        @endif
                    @endif

                    {{-- Claim to Character Form --}}
                    <form method="POST" action="{{ route('market.claims.claim', $claim->id) }}" class="claim-form">
                        @csrf
                        <div class="filter-group">
                            <label for="char_id-{{ $claim->id }}">Claim to Character</label>
                            <select name="char_id" id="char_id-{{ $claim->id }}" class="filter-control" required>
                                <option value="">-- Choose Character --</option>
                                @foreach($characters as $char)
                                    <option value="{{ $char->CharID }}">
                                        {{ $char->CharName16 }} (Lv. {{ $char->CurLevel }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn-golden-buy mt-2">
            <i class="fas fa-warehouse"></i> Send to Chest
        </button>
                    </form>
                </div>

            </div>
        @endforeach
    </div>
@endif

@endsection
