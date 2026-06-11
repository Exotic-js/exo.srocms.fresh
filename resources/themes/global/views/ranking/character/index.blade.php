@extends('layouts.full')
@section('title', __('Character') . ' - ' .$data->CharName16)

@section('content')
<div class="cv-wrapper">
    <div class="cv-grid">

        {{-- ============================================================
             COL 1 — INVENTORY PANEL
             ============================================================ --}}
        <div class="cv-card cv-card--inv">

            {{-- Name + Guild header --}}
            <div class="cv-inv-header"
                 style="background-image:url('{{ asset('themes/global/assets/images/character-panel-bg.png') }}')">
                <h2 class="cv-inv-name">{{ $data->CharName16 }}</h2>
                @if($data->ID > 0)
                    <p class="cv-inv-guild">Guild: <a href="{{ route('ranking.guild.view', ['name' => $data->Name]) }}" class="cv-link">{{ $data->Name }}</a></p>
                @else
                    <p class="cv-inv-guild">Guild: <em class="cv-muted">None</em></p>
                @endif
            </div>

            {{-- Inventory body --}}
            <div class="cv-inv-panels">

                {{-- SET --}}
                <div id="cv-pane-set">
                    <div class="cv-inv-body">
                        @if($data->RefObjID > 2000)
                            <img class="cv-race-badge" src="{{ asset('themes/global/assets/images/char-icon-europe.png') }}" alt="">
                        @else
                            <img class="cv-race-badge" src="{{ asset('themes/global/assets/images/char-icon-chinese.png') }}" alt="">
                        @endif

                        @if(config('global.server.version') === 'vSRO')
                            <img class="cv-model" src="{{ asset('images/character_full/'.config('ranking.character_image_vsro')[$data->RefObjID]) }}" alt="">
                        @else
                            <img class="cv-model" src="{{ asset('images/character_full/'.config('ranking.character_image')[$data->RefObjID]) }}" alt="">
                        @endif

                        <div class="cv-slots">
                            @include('ranking.character.partials.inventory.inventory-view', ['inventorySetList' => $data->getCharInventorySet(12, 0, 8)])
                        </div>
                    </div>
                </div>

                {{-- JOB --}}
                @if(config('global.server.version') !== 'vSRO')
                <div id="cv-pane-job" class="cv-hidden">
                    <div class="cv-inv-body">
                        @if(config('global.server.version') === 'vSRO')
                            <img class="cv-model" src="{{ asset('images/character_full/'.config('ranking.character_image_vsro')[$data->RefObjID]) }}" alt="">
                        @else
                            <img class="cv-model" src="{{ asset('images/character_full/'.config('ranking.character_image')[$data->RefObjID]) }}" alt="">
                        @endif
                        <div class="cv-slots">
                            @include('ranking.character.partials.inventory.inventory-job-view', ['inventoryJobList' => $data->charInventoryJob])
                        </div>
                    </div>
                </div>
                @endif

                {{-- AVATAR --}}
                <div id="cv-pane-avatar" class="cv-hidden">
                    <div class="cv-inv-body">
                        @if(config('global.server.version') === 'vSRO')
                            <img class="cv-model" src="{{ asset('images/character_full/'.config('ranking.character_image_vsro')[$data->RefObjID]) }}" alt="">
                        @else
                            <img class="cv-model" src="{{ asset('images/character_full/'.config('ranking.character_image')[$data->RefObjID]) }}" alt="">
                        @endif
                        <div class="cv-slots">
                            @include('ranking.character.partials.inventory.inventory-avatar-view', ['inventoryAvatarList' => $data->charInventoryAvatar])
                        </div>
                    </div>
                </div>

            </div>

            {{-- Tab switcher --}}
            <div class="cv-tabs">
                <button class="cv-tab cv-tab--active" data-pane="cv-pane-set">INVENTORY</button>
                @if(config('global.server.version') !== 'vSRO')
                <button class="cv-tab" data-pane="cv-pane-job">JOB</button>
                @endif
                <button class="cv-tab" data-pane="cv-pane-avatar">AVATAR</button>
            </div>
        </div>

        {{-- ============================================================
             COL 2 — CHARACTER STATS  (plain text, no icons)
                    + LATEST GLOBAL CHATTING at bottom
             ============================================================ --}}
        <div class="cv-card cv-card--info">

            {{-- Stats --}}
            <div class="cv-stats">

                <div class="cv-stat-row">
                    <span class="cv-stat-lbl">Nickname:</span>
                    <span class="cv-stat-val">{{ $data->CharName16 }}</span>
                </div>

                <div class="cv-stat-row">
                    <span class="cv-stat-lbl">Guildname:</span>
                    <span class="cv-stat-val">
                        @if($data->ID > 0)
                            <a href="{{ route('ranking.guild.view', ['name' => $data->Name]) }}" class="cv-link">{{ $data->Name }}</a>
                            @if(isset($data->GuildLevel)) | Level {{ $data->GuildLevel }} @endif
                        @else
                            <span class="cv-muted">None</span>
                        @endif
                    </span>
                </div>

                <div class="cv-stat-row">
                    <span class="cv-stat-lbl">Item Points:</span>
                    <span class="cv-stat-val">{{ $data->ItemPoints }}</span>
                </div>

                <div class="cv-stat-row">
                    <span class="cv-stat-lbl">Level:</span>
                    <span class="cv-stat-val">{{ $data->CurLevel }}</span>
                </div>

                <div class="cv-stat-row">
                    <span class="cv-stat-lbl">Race:</span>
                    <span class="cv-stat-val">
                        @if($data->RefObjID > 2000)
                            {{ config('ranking.character_race')[1]['name'] }}
                        @else
                            {{ config('ranking.character_race')[0]['name'] }}
                        @endif
                    </span>
                </div>

                <div class="cv-stat-row">
                    <span class="cv-stat-lbl">STR:</span>
                    <span class="cv-stat-val">{{ $data->Strength }}</span>
                </div>

                <div class="cv-stat-row">
                    <span class="cv-stat-lbl">INT:</span>
                    <span class="cv-stat-val">{{ $data->Intellect }}</span>
                </div>

                @if(config('ranking.extra.pvp_kill_logs') && $data->pvpKill)
                <div class="cv-stat-row">
                    <span class="cv-stat-lbl">PvP K/D:</span>
                    <span class="cv-stat-val">{{ $data->pvpKill->KillCount ?? 0 }} / {{ $data->pvpKill->DeathCount ?? 0 }}</span>
                </div>
                @endif

                @if(config('ranking.extra.job_kill_logs') && $data->jobKill)
                <div class="cv-stat-row">
                    <span class="cv-stat-lbl">Job K/D:</span>
                    <span class="cv-stat-val">{{ $data->jobKill->KillCount ?? 0 }} / {{ $data->jobKill->DeathCount ?? 0 }}</span>
                </div>
                @endif

                @if(config('ranking.extra.character_job') && $data->charJob->JobType)
                <div class="cv-stat-row">
                    <span class="cv-stat-lbl">Job:</span>
                    <span class="cv-stat-val">
                        @if(config('global.server.version') === 'vSRO')
                            {{ config('ranking.job_type_vsro')[$data->charJob->JobType]['name'] }} | Lv {{ $data->charJob->JobLevel ?? $data->charJob->Level }}
                        @else
                            {{ config('ranking.job_type')[$data->charJob->JobType]['name'] }} | Lv {{ $data->charJob->JobLevel ?? $data->charJob->Level }}
                        @endif
                    </span>
                </div>
                @endif

                @if(config('ranking.extra.character_status'))
                <div class="cv-stat-row">
                    <span class="cv-stat-lbl">Status:</span>
                    <span class="cv-stat-val">
                        @if($data->isOnline)
                            <span class="cv-online-dot"></span> Online
                        @else
                            <span class="cv-offline-dot"></span> Offline
                        @endif
                    </span>
                </div>
                @endif

                @if(config('ranking.extra.character_build') && $data->buildInfo)
                <div class="cv-stat-row">
                    <span class="cv-stat-lbl">Build:</span>
                    <span class="cv-stat-val">
                        @foreach($data->buildInfo as $key => $row)
                            @if(isset(config('ranking.skill_mastery')[$row->MasteryID]))
                                {{ config('ranking.skill_mastery')[$row->MasteryID]['name'] }}@if($key < count($data->buildInfo) - 1) / @endif
                            @endif
                        @endforeach
                    </span>
                </div>
                @endif

                @if($data->HwanLevel > 0)
                <div class="cv-stat-row">
                    <span class="cv-stat-lbl">Title:</span>
                    <span class="cv-stat-val cv-gold">
                        @if($data->RefObjID > 2000)
                            [{{ config('ranking.hwan_level')[1][$data->HwanLevel] ?? '' }}]
                        @else
                            [{{ config('ranking.hwan_level')[2][$data->HwanLevel] ?? '' }}]
                        @endif
                    </span>
                </div>
                @endif

                @if(config('ranking.extra.character_buff') && $data->buffInfo)
                <div class="cv-stat-row">
                    <span class="cv-stat-lbl">Buffs:</span>
                    <span class="cv-stat-val">
                        <div class="d-flex gap-1 flex-wrap">
                            @foreach($data->buffInfo as $row)
                                <img src="{{ asset('images/sro/'.$row->UI_IconFile_PNG) }}" title="{{ $row->UI_SkillName }}" width="22" height="22">
                            @endforeach
                        </div>
                    </span>
                </div>
                @endif
            </div>

            {{-- LATEST GLOBAL CHATTING — bottom of center column --}}
            @if(config('widgets.globals_history.enabled'))
            <div class="cv-history-block">
                <div class="cv-history-title">LATEST GLOBAL CHATTING</div>
                <div class="cv-history-body">
                    @forelse($data->globalHistory as $row)
                        <div class="cv-history-row">
                            <span class="cv-chat-msg">{!! $row->Comment !!}</span>
                            <span class="cv-chat-time">{{ \Carbon\Carbon::make($row->EventTime)->diffForHumans() }}</span>
                        </div>
                    @empty
                        <div class="cv-no-data">There is no data to show!</div>
                    @endforelse
                </div>
            </div>
            @endif
        </div>

        {{-- ============================================================
             COL 3 — UNIQUE KILLS + JOB KILLS
             ============================================================ --}}
        <div class="cv-col-right">

            {{-- Unique Kills --}}
            @if(config('widgets.unique_history.enabled'))
            <div class="cv-card cv-card--history">
                <div class="cv-history-title">LATEST UNIQUE KILLS</div>
                <div class="cv-history-body">
                    @forelse($data->uniqueHistory as $row)
                        <div class="cv-history-row">
                            <span>{{ config('ranking.uniques')[$row->Value]['name'] ?? 'Unknown' }}</span>
                            <span class="cv-gold">+{{ config('ranking.uniques')[$row->Value]['points'] ?? 0 }}</span>
                            <span class="cv-chat-time">{{ \Carbon\Carbon::make($row->EventTime)->diffForHumans() }}</span>
                        </div>
                    @empty
                        <div class="cv-no-data">There is no data to show!</div>
                    @endforelse
                </div>
            </div>
            @endif

            {{-- Job Kills --}}
            <div class="cv-card cv-card--history" style="margin-top:12px">
                <div class="cv-history-title">LATEST JOB KILLS</div>
                <div class="cv-history-body">
                    @if(isset($data->jobHistory) && $data->jobHistory->count())
                        @foreach($data->jobHistory as $row)
                            <div class="cv-history-row">
                                <span>{{ $row->TargetCharName ?? __('killed') }}</span>
                                <span class="cv-chat-time">{{ \Carbon\Carbon::make($row->EventTime)->diffForHumans() }}</span>
                            </div>
                        @endforeach
                    @else
                        <div class="cv-history-row"><span class="cv-muted">killed</span></div>
                    @endif
                </div>
            </div>

        </div>

    </div>{{-- .cv-grid --}}
</div>
@endsection

@push('styles')
<style>
/* ================================================================
   CHARACTER VIEW — Exact reference match
   ================================================================ */

.cv-wrapper {
    padding: 20px 14px 40px;
    max-width: 1180px;
    margin: 0 auto;
}

/* 3-column grid */
.cv-grid {
    display: grid;
    grid-template-columns: 340px 1fr 260px;
    gap: 14px;
    align-items: start;
}
@media(max-width:1080px){ .cv-grid{ grid-template-columns:1fr 1fr; } .cv-col-right{ grid-column:1/-1; display:grid; grid-template-columns:1fr 1fr; gap:14px; } }
@media(max-width:600px) { .cv-grid{ grid-template-columns:1fr; } .cv-col-right{ grid-template-columns:1fr; } }

/* Base card */
.cv-card {
    background: rgba(8, 6, 3, 0.82);
    border: 1px solid rgba(160, 120, 30, 0.3);
    border-radius: 2px;
    backdrop-filter: blur(6px);
    /* NO overflow:hidden — tooltip must be able to escape */
}

/* ================================================================
   INVENTORY PANEL
   ================================================================ */
.cv-inv-header {
    text-align: center;
    padding: 14px 10px 10px;
    border-bottom: 1px solid rgba(160,120,30,0.2);
    background-size: cover !important;
    background-position: center !important;
    position: relative;
}
.cv-inv-name {
    font-family: 'Cinzel', serif;
    font-size: 1.05rem;
    color: #d4a827;
    margin: 0 0 3px;
    letter-spacing: 1.5px;
    text-shadow: 0 0 12px rgba(212,168,39,0.5);
}
.cv-inv-guild { font-size: 0.75rem; color: #999; margin: 0; }

/* Inventory body — character centered, slots layered */
.cv-inv-body {
    position: relative;
    min-height: 260px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 10px 6px;
    /* overflow must be visible so tooltip can escape */
    overflow: visible;
}
.cv-race-badge {
    position: absolute;
    top: 8px; left: 8px;
    z-index: 2; opacity: .7;
    width: 20px;
}
.cv-model {
    position: absolute;
    top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    max-height: 220px;
    pointer-events: none;
    z-index: 0;
    filter: drop-shadow(0 4px 18px rgba(0,0,0,0.6));
}
.cv-slots {
    position: relative;
    z-index: 1;
    width: 100%;
}

/* Override inventory table */
.cv-slots .table-inventory tr td {
    background: none !important;
    border: none !important;
    padding: 3px !important;
}
.cv-slots .table-inventory { margin: 0 auto; }

/* ================================================================
   ITEM SLOT + TOOLTIP
   ================================================================ */
.sro-item-detail {
    background: rgba(10, 8, 4, 0.8);
    width: 36px;
    margin: 0 auto;
    position: relative;   /* MUST be relative for tooltip positioning */
}
.sro-item-detail.sro-item-special {
    background: rgba(160, 80, 0, 0.6);
}
.sro-item-detail.sro-item-special .sro-item-special-seal {
    z-index: 4;
}
.sro-item-detail .item {
    width: 34px;
    height: 34px;
    float: left;
    margin: 1px;
    padding: 0 !important;
    color: #fff;
    background: rgba(15, 12, 5, 0.85);
    border: 1px solid rgba(160, 120, 30, 0.35);
    border-radius: 2px;
    position: relative;
    cursor: pointer;
}
.sro-item-detail .item img {
    position: absolute;
    width: 32px;
    height: 32px;
}
.sro-item-detail .item .amount {
    background: rgba(50,50,50,0.6);
    padding: 1px 2px;
    position: absolute;
    bottom: 0; right: 0;
    font-size: 9px;
    color: #fff;
    z-index: 3;
}
/* The info tooltip — hidden, shown by JS near cursor */
.sro-item-detail .info {
    display: none !important; /* override style.css :hover rule */
}
.sro-item-detail:hover .info {
    display: none !important; /* override style.css :hover rule */
}
/* Fixed floating tooltip shown by JS */
#cv-item-tooltip {
    position: fixed;
    z-index: 99999;
    width: 240px;
    background: rgba(18, 20, 42, 0.97);
    border: 1px solid rgba(128, 139, 186, 0.55);
    border-radius: 4px;
    padding: 8px 10px;
    color: #fff;
    font-size: 11px;
    line-height: 17px;
    text-align: left;
    pointer-events: none;
    box-shadow: 0 6px 24px rgba(0,0,0,0.8);
    display: none;
    white-space: normal;
}

/* TABS */
.cv-tabs {
    display: flex;
    border-top: 1px solid rgba(160,120,30,0.25);
}
.cv-tab {
    flex: 1;
    padding: 9px 4px;
    background: rgba(10,8,4,0.9);
    border: none;
    border-right: 1px solid rgba(160,120,30,0.15);
    color: #666;
    font-family: 'Cinzel', serif;
    font-size: 0.63rem;
    letter-spacing: 1.5px;
    cursor: pointer;
    transition: color .2s, background .2s;
}
.cv-tab:last-child { border-right: none; }
.cv-tab:hover { color: #d4a827; background: rgba(160,120,30,0.06); }
.cv-tab--active {
    color: #d4a827;
    background: rgba(160,120,30,0.06);
    border-bottom: 2px solid #d4a827;
}

/* ================================================================
   STATS PANEL — plain text, no icons
   ================================================================ */
.cv-card--info { padding: 0; }

.cv-stats { padding: 2px 0; }

.cv-stat-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 18px;
    font-size: 0.82rem;
    border-bottom: 1px solid rgba(160,120,30,0.08);
    transition: background .15s;
}
.cv-stat-row:hover { background: rgba(160,120,30,0.05); }

.cv-stat-lbl { color: #888; white-space: nowrap; }
.cv-stat-val { color: #ddd; text-align: right; }

.cv-gold { color: #d4a827 !important; }
.cv-muted { color: #555; font-style: normal; }
.cv-link { color: #d4a827; text-decoration: none; }
.cv-link:hover { color: #f0c84a; }

.cv-online-dot, .cv-offline-dot {
    display: inline-block; width: 8px; height: 8px; border-radius: 50%; margin-right: 4px;
}
.cv-online-dot  { background: #4caf50; box-shadow: 0 0 5px #4caf50; }
.cv-offline-dot { background: #e53935; box-shadow: 0 0 5px #e53935; }

/* ================================================================
   HISTORY BLOCK (Global Chat at bottom of col 2)
   ================================================================ */
.cv-history-block {
    border-top: 1px solid rgba(160,120,30,0.2);
    margin-top: 4px;
}

/* ================================================================
   HISTORY CARDS (col 3)
   ================================================================ */
.cv-card--history { overflow: hidden; }

.cv-history-title {
    padding: 9px 14px;
    font-family: 'Cinzel', serif;
    font-size: 0.62rem;
    letter-spacing: 2.5px;
    color: #c9a42a;
    text-align: center;
    background: rgba(8,6,3,0.7);
    border-bottom: 1px solid rgba(160,120,30,0.18);
}

.cv-history-body { padding: 10px 14px; }

.cv-history-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 8px;
    padding: 5px 0;
    font-size: 0.78rem;
    color: #ccc;
    border-bottom: 1px solid rgba(160,120,30,0.06);
    flex-wrap: wrap;
}
.cv-history-row:last-child { border-bottom: none; }

.cv-chat-msg { flex: 1; color: #bbb; }
.cv-chat-time { color: #555; font-size: 0.7rem; white-space: nowrap; }

/* Red "no data" badge — matches reference */
.cv-no-data {
    background: rgba(140, 20, 20, 0.25);
    border: 1px solid rgba(150, 30, 30, 0.5);
    border-radius: 2px;
    color: #c0392b;
    font-size: 0.75rem;
    text-align: center;
    padding: 7px 10px;
}

/* Right column flex */
.cv-col-right { display: flex; flex-direction: column; }

.sro-item-detail .item > img { position:absolute; width:32px; height:32px; }
.cv-hidden { display: none !important; }
</style>
@endpush

@push('scripts')
<script>
/* Override the global itemInfo() from function.js — prevents duplicate .tooltip divs */
window.itemInfo = function() {};

(function(){
    /* ---- Tab switching ---- */
    var tabs = document.querySelectorAll('.cv-tab');
    var allPanes = ['cv-pane-set', 'cv-pane-job', 'cv-pane-avatar'];
    tabs.forEach(function(tab){
        tab.addEventListener('click', function(){
            var target = this.dataset.pane;
            allPanes.forEach(function(id){ var el=document.getElementById(id); if(el) el.classList.add('cv-hidden'); });
            var show = document.getElementById(target);
            if(show) show.classList.remove('cv-hidden');
            tabs.forEach(function(t){ t.classList.remove('cv-tab--active'); });
            this.classList.add('cv-tab--active');
        });
    });

    /* ---- Item tooltip — single fixed floating box ---- */
    // Create the tooltip container once
    var tip = document.createElement('div');
    tip.id = 'cv-item-tooltip';
    document.body.appendChild(tip);

    var offset = 16; // distance from cursor

    document.querySelectorAll('.sro-item-detail').forEach(function(slot){
        var infoEl = slot.querySelector('.info');
        if (!infoEl) return;

        slot.addEventListener('mouseenter', function(){
            tip.innerHTML = infoEl.innerHTML;
            tip.style.display = 'block';
        });

        slot.addEventListener('mousemove', function(e){
            var tw = tip.offsetWidth  || 240;
            var th = tip.offsetHeight || 200;
            var vw = window.innerWidth;
            var vh = window.innerHeight;

            var x = e.clientX + offset;
            var y = e.clientY + offset;

            // flip left if overflows right edge
            if (x + tw > vw - 10) x = e.clientX - tw - offset;
            // flip up if overflows bottom edge
            if (y + th > vh - 10) y = e.clientY - th - offset;

            tip.style.left = x + 'px';
            tip.style.top  = y + 'px';
        });

        slot.addEventListener('mouseleave', function(){
            tip.style.display = 'none';
        });
    });
})();
</script>
@endpush
