<div class="card">
    <div class="card-header">
        <img src="{{ asset('themes/global/assets/images/widget-icon-status.png') }}" alt="" height="31">
        <h3>Server Status</h3>
    </div>
    <div class="card-body py-0">
        <div class="progress">
            @php $progress = ceil(($onlineCounter->onlinePlayer+$onlineCounter->fakePlayer)*100/$onlineCounter->maxPlayer); @endphp
            <div class="progress-bar" role="progressbar" style="width: {{ $progress }}%" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
    </div>
</div>
