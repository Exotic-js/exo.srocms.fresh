@if(config('widgets.discord.enabled'))
    <div class="card widget-discord mb-5">
        <div class="card-header">
            <img src="{{ asset('themes/global/assets/images/widget-icon-discord.png') }}" alt="" height="31">
            <h3>Discord</h3>
        </div>
        <div class="card-body">
            <div class="p-3">
                <iframe src="https://discord.com/widget?id={{ config('widgets.discord')['server_id'] }}&theme={{ config('widgets.discord')['theme'] }}" width="100%" height="280" allowtransparency="true" frameborder="0" sandbox="allow-popups allow-popups-to-escape-sandbox allow-same-origin allow-scripts"></iframe>
            </div>
        </div>
    </div>
@endif
