@extends('admin.layouts.app')
@section('title', __('Dashboard'))

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Dashboard</h1>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <p>Total Accounts Registered</p>
                        <h2>{{ $userCount }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <p>Total In-game Characters</p>
                        <h2>{{ $charCount }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <p>Total Amount of Gold</p>
                        <h2>{{ number_format($totalGold , 0, ',')}}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <p>Total Amount of Silk</p>
                        <h2>{{ number_format($totalSilk , 0, ',')}}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row my-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <p>Online Players</p>
                        <h2>{{ $onlineCounter->onlinePlayer }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <p>Support tickets</p>
                        <h2>{{ $ticketCount }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <p>Votes</p>
                        <h2>{{ $voteCount }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <p>Donate</p>
                        <h2>{{ number_format($totalDonate, 0, ',', '.') }}$</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row my-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <p>PHP Memory used</p>
                        <h4>{{ number_format($systemInfo->memoryUsage / 1024 / 1024, 2) }}M / {{ $systemInfo->memoryLimit }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <p>Disk Space</p>
                        <h4>{{ number_format($systemInfo->diskFree / 1024 / 1024 / 1024, 2) }}GB / {{ number_format($systemInfo->diskTotal / 1024 / 1024 / 1024, 2) }}GB</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <p>Laravel</p>
                        <h4>Debug: {{ $systemInfo->appDebug ? 'true' : 'false' }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <p>Admins</p>
                        <h4>{{ $systemInfo->adminCount }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mt-3 mb-3 border-bottom">
            <h1 class="h2">iSRO-CMS Updates</h1>

            <div class="btn-toolbar mb-2 mb-md-0">
                <div class="btn-group me-2">
                    <a href="https://discord.gg/4MqzAHGU4e" target="_blank" class="btn btn-outline-warning">Join Discord</a>
                </div>
            </div>
        </div>

        <div class="row">
            <widgetbot
                server="1004443821570019338"
                channel="1374482240427528254"
                width="100%"
                height="600"
            ></widgetbot>
            <script src="https://cdn.jsdelivr.net/npm/@widgetbot/html-embed"></script>
        </div>
    </div>
@endsection
