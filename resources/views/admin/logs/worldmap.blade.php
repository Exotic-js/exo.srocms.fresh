@extends('admin.layouts.app')
@section('title', __('World Map'))

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">World Map</h1>
    </div>

    <div class="col-12 my-5">
        <div class="row">
            <div class="col-md-3 sidebar-wrapper" style="position: initial; left: auto">
                <div id="search" class="sidebar-search">
                    <div>
                        <div class="input-group">
                            <input type="text" class="form-control search-menu" placeholder="Search... X,Y?">
                            <button class="btn btn-outline-secondary" type="button">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div id="navigation" class="sidebar-menu">
                    <ul>
                        <li class="header-menu">
                            <span>Navigation</span>
                        </li>
                        <li class="sidebar-dropdown">
                            <a href="#">
                                <i class="fa fa-university"></i>
                                <span>Towns</span>
                            </a>
                            <div class="sidebar-submenu">
                                <ul>
                                    <li>
                                        <a href="#" onclick="xSROMap.FlyView(6434,1044)">Jangan
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" onclick="xSROMap.FlyView(3554,2112)">Donwhang</a>
                                    </li>
                                    <li>
                                        <a href="#" onclick="xSROMap.FlyView(114,47.25)">Hotan</a>
                                    </li>
                                    <li>
                                        <a href="#" onclick="xSROMap.FlyView(-5184,2889)">Samarkand</a>
                                    </li>
                                    <li>
                                        <a href="#" onclick="xSROMap.FlyView(-10681,2584)">Constantinople</a>
                                    </li>
                                    <li>
                                        <a href="#" onclick="xSROMap.FlyView(-16147,75)">Alexandria (North)</a>
                                    </li>
                                    <li>
                                        <a href="#" onclick="xSROMap.FlyView(-16641,-275)">Alexandria (South)</a>
                                    </li>
                                    <li>
                                        <a href="#" onclick="xSROMap.FlyView(-8525,-717)">Baghdad</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="sidebar-dropdown">
                            <a href="#">
                                <i class="fa fa-compass"></i>
                                <span>Zones</span>
                            </a>
                            <div class="sidebar-submenu">
                                <ul>
                                    <li>
                                        <a href="#" onclick="xSROMap.SetView(5109,420)">Tiger Mountain</a>
                                    </li>
                                    <li>
                                        <a href="#" onclick="xSROMap.SetView(2592.75,-5.25)">Tarim Basin</a>
                                    </li>
                                    <li>
                                        <a href="#" onclick="xSROMap.SetView(-1410,-255.75)">Karakoram</a>
                                    </li>
                                    <li>
                                        <a href="#" onclick="xSROMap.SetView(-561,2037)">Taklamakan</a>
                                    </li>
                                    <li>
                                        <a href="#" onclick="xSROMap.SetView(-4401,-141)">Mountain Roc</a>
                                    </li>
                                    <li>
                                        <a href="#" onclick="xSROMap.SetView(-4224,2496)">Central Asia</a>
                                    </li>
                                    <li>
                                        <a href="#" onclick="xSROMap.SetView(-6891,2373)">Asia Minor</a>
                                    </li>
                                    <li>
                                        <a href="#" onclick="xSROMap.SetView(-11853,2979)">Eastern Europe</a>
                                    </li>
                                    <li>
                                        <a href="#" onclick="xSROMap.SetView(-12378,-1344)">Storm and Cloud Desert</a>
                                    </li>
                                    <li>
                                        <a href="#" onclick="xSROMap.SetView(-12996,-3264)">King's Valley</a>
                                    </li>
                                    <li>
                                        <a href="#" onclick="xSROMap.SetView(-9360,-777)">Kirk Field</a>
                                    </li>
                                    <li>
                                        <a href="#" onclick="xSROMap.SetView(-10356,-2733)">Phantom Desert</a>
                                    </li>
                                    <li>
                                        <a href="#" onclick="xSROMap.SetView(-8787,-2370)">Flaming Tree</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="sidebar-dropdown">
                            <a href="#">
                                <i class="far fa-globe-americas"></i>
                                <span>Areas</span>
                            </a>
                            <div class="sidebar-submenu">
                                <ul>
                                    <li class="sidebar-submenu-dropdown">
                                        <a href="#">
                                            <span>Donwhang Stone Cave</span>
                                        </a>
                                        <div class="sidebar-submenu-submenu">
                                            <ul>
                                                <li>
                                                    <a href="#" onclick="xSROMap.SetView(0,0,0,32769)">Donwhang Dungeon B1 (Lv.61~64)</a>
                                                </li>
                                                <li>
                                                    <a href="#" onclick="xSROMap.SetView(0,0,115,32769)">Donwhang Dungeon B2 (Lv.64~66)</a>
                                                </li>
                                                <li>
                                                    <a href="#" onclick="xSROMap.SetView(0,0,230,32769)">Donwhang Dungeon B3 (Lv.65~68)</a>
                                                </li>
                                                <li>
                                                    <a href="#" onclick="xSROMap.SetView(0,0,345,32769)">Donwhang Dungeon B4 (Lv.69~70)</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                    <li class="sidebar-submenu-dropdown">
                                        <a href="#">
                                            <span>Tomb of Qui-Shin</span>
                                        </a>
                                        <div class="sidebar-submenu-submenu">
                                            <ul>
                                                <li>
                                                    <a href="#" onclick="xSROMap.SetView(0,0,0,32775)">Qin-Shi Tomb B1 (Lv.81~85)</a>
                                                </li>
                                                <li>
                                                    <a href="#" onclick="xSROMap.SetView(0,0,0,32774)">Qin-Shi Tomb B2 (Lv.86~90)</a>
                                                </li>
                                                <li>
                                                    <a href="#" onclick="xSROMap.SetView(0,0,0,32773)">Qin-Shi Tomb B3 (Lv.90~95)</a>
                                                </li>
                                                <li>
                                                    <a href="#" onclick="xSROMap.SetView(0,0,0,32772)">Qin-Shi Tomb B4 (Lv.96~99)</a>
                                                </li>
                                                <li>
                                                    <a href="#" onclick="xSROMap.SetView(0,0,0,32771)">Qin-Shi Tomb B5</a>
                                                </li>
                                                <li>
                                                    <a href="#" onclick="xSROMap.SetView(0,0,0,32770)">Qin-Shi Tomb B6</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                    <li>
                                        <a href="#" onclick="xSROMap.SetView(0,0,0,32784)">Temple</a>
                                    </li>
                                    <li>
                                        <a href="#" onclick="xSROMap.SetView(0,0,0,32786)">Flame Mountain</a>
                                    </li>
                                    <li>
                                        <a href="#" onclick="xSROMap.SetView(0,0,0,32785)">Cave of Meditation</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li id="navigation-npc" class="sidebar-dropdown">
                            <a href="#">
                                <i class="fa fa-user-shield"></i>
                                <span title="Non Player Character">NPC's</span>
                                <span class="badge badge-pill badge-primary">A-Z</span>
                            </a>
                            <div class="sidebar-submenu">
                                <ul>
                                    <!--li>
                                        <a href="#" onclick="xSROMap.SetView(x,y,z,region)">NPC Name</a>
                                    </li-->
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-9">
                <div style="min-height: 600px;position: relative;">
                    <div id="map" class="my-5"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('styles')
    <!-- Leaflet -->
    <link rel="stylesheet" type="text/css" href="{{ asset('xSROMap/assets/fonts/font-awesome-5.11.1/css/all.min.css') }}">

    <link rel="stylesheet" type="text/css" href="{{ asset('xSROMap/assets/js/leaflet/Leaflet.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('xSROMap/assets/js/leaflet/Leaflet.Geoman.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('xSROMap/assets/js/leaflet/Leaflet.Easy-Button.css') }}">
    <!-- My style -->
    <link rel="stylesheet" type="text/css" href="{{ asset('xSROMap/assets/css/main.css') }}">
@endpush
@push('scripts')
    <!-- Leaflet -->
    <script src="{{ asset('xSROMap/assets/js/leaflet/Leaflet.min.js') }}"></script>
    <script src="{{ asset('xSROMap/assets/js/leaflet/Leaflet.Geoman.min.js') }}"></script>
    <script src="{{ asset('xSROMap/assets/js/leaflet/Leaflet.Easy-Button.js') }}"></script>
    <!-- Main -->
    <script src="{{ asset('xSROMap/assets/js/xSROMap.js') }}"></script>
    <script src="{{ asset('xSROMap/assets/js/main.js') }}"></script>

    <script type="text/javascript">
        jQuery(function() {
            @forelse($data as $row)
                xSROMap.AddPlayer("{{ $row->CharName16 }}",'<a href="{{ route('admin.characters.view', $row->CharID) }}" target="_blank"><b>{{ $row->CharName16 }}</b></a><br>', {{ $row->PosX }}, {{ $row->PosZ }}, {{ $row->PosY }}, {{ $row->LatestRegion }});
            @empty
            @endforelse
        });
    </script>
@endpush
