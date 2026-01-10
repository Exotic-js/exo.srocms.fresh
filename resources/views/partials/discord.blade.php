@if(config('widgets.discord.enabled'))
    <div class="mb-4">
        <iframe src="https://discordapp.com/widget?id={{ config('widgets.discord')['server_id'] }}&theme={{ config('widgets.discord')['theme'] }}" width="100%" height="500" allowtransparency="true" frameborder="0"></iframe>
    </div>
@endif
