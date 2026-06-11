@extends('layouts.app')
@section('title', __('Home'))

@php
    $isVSRO = config('global.server.version') === 'vSRO';
    
    // Top Trader
    $topTrader = $isVSRO 
        ? \App\Models\SRO\Shard\CharTrijob::getJobRanking(1, 1)->first() 
        : \App\Models\SRO\Shard\CharTradeConflictJob::getJobRanking(1, 3)->first();
    
    // Top Hunter
    $topHunter = $isVSRO 
        ? \App\Models\SRO\Shard\CharTrijob::getJobRanking(1, 3)->first() 
        : \App\Models\SRO\Shard\CharTradeConflictJob::getJobRanking(1, 1)->first();

    // Server Status
    $onlineCount = \App\Models\SRO\Account\ShardCurrentUser::getOnlineCounter();
    $fakePlayer = (int)config('settings.fake_player', 0);
    $totalOnline = $onlineCount + $fakePlayer;
    $maxPlayer = (int)config('settings.max_player', 1000);
    $onlinePercentage = ($maxPlayer > 0) ? min(100, ($totalOnline / $maxPlayer) * 100) : 0;
@endphp

@section('content')

<!-- MAIN CONTENT CONTAINER -->
<div class="custom-container" style="position: relative; z-index: 10;">

    <!-- Row 1: Ranking & Server Info -->
    <div class="row golden-row mb-5">
        <div class="col-lg-3 col-md-12">
            @include('partials.top-player')
        </div>
        <div class="col-lg-6 col-md-12">
            @include('partials.server-info')
        </div>
        <div class="col-lg-3 col-md-12">
            @if(config('widgets.top_guild.enabled'))
                <div class="card widget-ranking mb-5">
                    <div class="card-header">
                        <img src="{{ asset('themes/global/assets/images/widget-icon-ranking.png') }}" alt="" height="31">
                        <h3>{{ __('Guild Ranking') }}</h3>
                    </div>
                    <div class="card-body p-0 pt-3">
                        @include('partials.top-guild')
                    </div>
                </div>
            @endif
        </div>
    </div>
        
    <!-- Row 2: Carousel -->
    <div class="row mb-5">
        <div class="col-12 p-0">
            @include('partials.carousel')
        </div>
    </div>

    <!-- Row 3: Live Stats Cards -->
    <div class="row golden-row mb-5 gx-4 golden-stats-marquee">
        <!-- Box 1 -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="golden-stat-box cinematic text-center position-relative">
                <div class="corner-tl"></div><div class="corner-tr"></div><div class="corner-bl"></div><div class="corner-br"></div>
                <h3 class="stat-title">✦ {{ __('SERVER TIME') }} ✦</h3>
                <h2 id="idTimerClock" class="stat-value"></h2>
            </div>
        </div>
        <!-- Box 2 -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="golden-stat-box cinematic text-center position-relative">
                <div class="corner-tl"></div><div class="corner-tr"></div><div class="corner-bl"></div><div class="corner-br"></div>
                <h3 class="stat-title">✦ {{ __('GAME STATUS') }} ✦</h3>
                <div class="golden-progress cinematic mt-3">
                    <div class="progress-bar-fill" style="width: {{ $onlinePercentage }}%;"></div>
                    <span class="progress-text">{{ $totalOnline }} / {{ $maxPlayer }}</span>
                </div>
            </div>
        </div>
        <!-- Box 3 -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="golden-stat-box cinematic text-center position-relative">
                <div class="corner-tl"></div><div class="corner-tr"></div><div class="corner-bl"></div><div class="corner-br"></div>
                <h3 class="stat-title">✦ {{ __('TOP TRADER') }} ✦</h3>
                <h2 class="stat-value text-uppercase">{{ $topTrader ? $topTrader->CharName16 : 'NONE' }}</h2>
            </div>
        </div>
        <!-- Box 4 -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="golden-stat-box cinematic text-center position-relative">
                <div class="corner-tl"></div><div class="corner-tr"></div><div class="corner-bl"></div><div class="corner-br"></div>
                <h3 class="stat-title">✦ {{ __('TOP HUNTER') }} ✦</h3>
                <h2 class="stat-value text-uppercase">{{ $topHunter ? $topHunter->CharName16 : 'NONE' }}</h2>
            </div>
        </div>
    </div>

    <!-- Row 4: Kills, Character & Discord -->
    <div class="row golden-row mb-5 position-relative golden-bottom-row">
        <!-- Floating Character Graphic -->
        <div class="golden-floating-char">
            <!-- Will be styled via CSS -->
        </div>

        <div class="col-lg-4 col-md-12">
            @include('partials.unique-history')
        </div>
        <div class="col-lg-4 col-md-12">
            <!-- Empty column for character to show through -->
        </div>
        <div class="col-lg-4 col-md-12 z-index-2">
            @include('partials.discord')
        </div>
    </div> 
    
</div> <!-- END MAIN CONTENT CONTAINER -->

@endsection
