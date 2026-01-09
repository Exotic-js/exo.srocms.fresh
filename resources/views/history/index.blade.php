@extends('layouts.full')
@section('title', __('History'))

@section('content')
    <div class="container">
        <div class="card border-0">
            <div class="card-body p-0">
                <h1 class="mb-4 text-center">Server Stats</h1>
                <hr>

                <div class="row">
                    <div class="row justify-content-center mb-4">
                        <div class="col-md-4 mb-3">
                            @include('partials.online-counter')
                            @include('partials.server-info')
                        </div>
                        @if(config("widgets.fortress_war.enabled"))
                        <div class="col-md-4 mb-3">
                            @include('partials.fortress-war')
                            <a class="btn btn-primary w-100" href="{{ route('history.fortress') }}">{{ __('More') }}</a>
                        </div>
                        @endif
                        @if(config("widgets.event_schedule.enabled"))
                        <div class="col-md-4 mb-3">
                            @include('partials.event-schedule')
                            <a class="btn btn-primary w-100" href="{{ route('history.schedule') }}">{{ __('More') }}</a>
                        </div>
                        @endif
                    </div>

                    <h1 class="mb-4 text-center">Server History</h1>
                    <hr>

                    <div class="row justify-content-center mb-4">
                        @if(config("widgets.unique_history.enabled"))
                        <div class="col-md-6 mb-3">
                            @include('partials.unique-history')
                            <a class="btn btn-primary w-100" href="{{ route('history.unique') }}">{{ __('More') }}</a>
                            <a class="btn btn-primary mt-2 w-100" href="{{ route('history.unique-advanced') }}">{{ __('Advanced Tracker') }}</a>
                        </div>
                        @endif
                        @if(config("widgets.globals_history.enabled"))
                        <div class="col-md-6 mb-3">
                            @include('partials.globals-history')
                            <a class="btn btn-primary w-100" href="{{ route('history.global') }}">{{ __('More') }}</a>
                        </div>
                        @endif
                    </div>

                    <h1 class="mb-4 text-center">Top Ranking</h1>
                    <hr>

                    <div class="row justify-content-center mb-4">
                        @if(config("widgets.top_player.enabled"))
                        <div class="col-md-6 mb-3">
                            @include('partials.top-player')
                            <a class="btn btn-primary w-100" href="{{ route('ranking') }}">{{ __('More') }}</a>
                        </div>
                        @endif
                        @if(config("widgets.top_guild.enabled"))
                        <div class="col-md-6 mb-3">
                            @include('partials.top-guild')
                            <a class="btn btn-primary w-100" href="{{ route('ranking') }}">{{ __('More') }}</a>
                        </div>
                        @endif
                    </div>

                    <h1 class="mb-4 text-center">Item Logs</h1>
                    <hr>

                    <div class="row justify-content-center mb-4">
                        @if(config("widgets.sox_plus.enabled"))
                        <div class="col-md-6 mb-3">
                            @include('partials.sox-plus')
                            <a class="btn btn-primary w-100" href="{{ route('history.item-plus') }}">{{ __('More') }}</a>
                        </div>
                        @endif
                        @if(config("widgets.sox_drop.enabled"))
                        <div class="col-md-6 mb-3">
                            @include('partials.sox-drop')
                            <a class="btn btn-primary w-100" href="{{ route('history.item-drop') }}">{{ __('More') }}</a>
                        </div>
                        @endif
                    </div>

                    <h1 class="mb-4 text-center">Pvp Logs</h1>
                    <hr>

                    <div class="row justify-content-center mb-4">
                        @if(config("widgets.pvp_kills.enabled"))
                        <div class="col-md-6 mb-3">
                            @include('partials.pvp-kills')
                            <a class="btn btn-primary w-100" href="{{ route('history.pvp-kill') }}">{{ __('More') }}</a>
                        </div>
                        @endif
                        @if(config("widgets.job_kills.enabled"))
                        <div class="col-md-6 mb-3">
                            @include('partials.job-kills')
                            <a class="btn btn-primary w-100" href="{{ route('history.job-kill') }}">{{ __('More') }}</a>
                        </div>
                        @endif
                    </div>

                    <h1 class="mb-4 text-center">Discord Server</h1>
                    <hr>

                    <div class="mb-4">
                        @include('partials.discord')
                        <a class="btn btn-primary w-100" href="https://discord.gg/mix-store">{{ __('Join') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
