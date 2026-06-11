@extends('layouts.full')
@section('title', __('Ranking'))
@section('hero')
    <header>
        <div id="parallax" style="background-image: url({{ asset('themes/global/assets/images/guild-bg.png') }});"></div>

        <div class="Waves">
            <svg class="waves" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 24 150 28" preserveAspectRatio="none" shape-rendering="auto">
                <defs>
                    <path id="gentle-wave" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z"></path>
                </defs>
                <g class="parallax">
                    <use xlink:href="#gentle-wave" x="48" y="0" fill="rgba(21,21,25,0.7"></use>
                    <use xlink:href="#gentle-wave" x="48" y="3" fill="rgba(21,21,25,0.5)"></use>
                    <use xlink:href="#gentle-wave" x="48" y="5" fill="rgba(21,21,25,0.3)"></use>
                    <use xlink:href="#gentle-wave" x="48" y="7" fill="rgba(21,21,25,1)"></use>
                </g>
            </svg>
        </div>
    </header>
@stop
@section('content')
    <div class="card page-card page-card-black page-guild">
        <div class="card-header">
            <h3>{{ $data->Name }}</h3>
        </div>

        <div class="card-body p-5">
            <div class="card">
                <div class="card guild-info">
                    <div class="card-body text-center">
                        @if(isset($data->Crest))
                            <div class="d-block m-auto">
                                <img src="{{ route('ranking.guild.crest', ['bin' => $data->Crest]) }}" alt="" width="32" height="32">
                            </div>
                        @endif
                        <h4 class="mt-1">{{ $data->Name }}</h4>
                        <p class="mb-0">Level {{ $data->Lvl }} Guild / Have <span>{{ $data->TotalMember }} Members</span></p>
                        <p><span>{{ $data->ItemPoints }}</span> Combined Item Points</p>
                    </div>
                </div>

                <div id="pServerCMSGuildMember">
                    @include('ranking.guild.partials.guild-members')
                </div>

                <div class="mt-4">
                    @include('ranking.guild.partials.guild-alliances')
                </div>
            </div>
        </div>
    </div>
@endsection
