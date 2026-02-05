<div class="table-responsive">
    <table class="table table-striped">
        <tbody>
        <tr>
            <td>{{ __('Character Name:') }}</td>
            <td>{{ $data->CharName16 }}</td>
        </tr>
        <tr>
            <td>{{ __('JobName:') }}</td>
            @if(!config("settings.job_name_jid_{$data->jid}") || auth()->user()?->role?->is_admin)
                @if(!empty($data->NickName16))
                    <td>{{ $data->NickName16 }}</td>
                @else
                    <td>{{ __('None') }}</td>
                @endif
            @else
                <td>{{ __('Hidden') }}</td>
            @endif
        </tr>
        <tr>
            <td>{{ __('Guild:') }}</td>
            <td>
                @if($data->guild->ID > 0)
                    <a href="{{ route('ranking.guild.view', ['name' => $data->guild->Name]) }}" class="text-decoration-none">{{ $data->guild->Name }}</a>
                @else
                    <span>{{ __('None') }}</span>
                @endif
            </td>
        </tr>
        <tr>
            <td>{{ __('Race:') }}</td>
            <td>
                @if($data->RefObjID > 2000)
                    <img src="{{ asset(config('ranking.character_race')[1]['image']) }}" width="16" height="16" alt=""/>
                    <span>{{ config('ranking.character_race')[1]['name'] }}</span>
                @else
                    <img src="{{ asset(config('ranking.character_race')[0]['image']) }}" width="16" height="16" alt=""/>
                    <span>{{ config('ranking.character_race')[0]['name'] }}</span>
                @endif
            </td>
        </tr>
        <tr>
            <td>{{ __('Level:') }}</td>
            <td>{{ $data->CurLevel }} / {{ config('settings.max_level', 140) }}</td>
        </tr>
        <tr>
            <td>{{ __('Item Points:') }}</td>
            <td>{{ $data->ItemPoints }}</td>
        </tr>
        @if(config('ranking.extra.pvp_kill_logs') && $data->pvpKill)
            <tr>
                <td>{{ __('Pvp K/D:') }}</td>
                <td>{{ $data->pvpKill->KillCount ?? 0 }} / {{ $data->pvpKill->DeathCount ?? 0 }}</td>
            </tr>
        @endif
        @if(config('ranking.extra.job_kill_logs') && $data->jobKill)
        <tr>
            <td>{{ __('Job K/D:') }}</td>
            <td>{{ $data->jobKill->KillCount ?? 0 }} / {{ $data->jobKill->DeathCount ?? 0 }}</td>
        </tr>
        @endif
        <tr>
            <td>{{ __('Title:') }}</td>
            <td style="color: #ffc345">
                @if($data->HwanLevel > 0)
                    @if($data->RefObjID > 2000)
                        [{{ config('ranking.hwan_level')[1][$data->HwanLevel] ?? '' }}]
                    @else
                        [{{ config('ranking.hwan_level')[2][$data->HwanLevel] ?? '' }}]
                    @endif
                @else
                    []
                @endif
            </td>
        </tr>
        @if(config('ranking.extra.character_status'))
        <tr>
            <td>{{ __('Status:') }}</td>
            <td>
                @if($data->isOnline)
                    <img src="{{ asset('images/login_window_eu_located_green.png') }}" width="16" height="16" alt=""/>
                    <span class="text-muted">{{ __('Online') }}</span>
                @else
                    <img src="{{ asset('images/login_window_eu_located_red.png') }}" width="16" height="16" alt=""/>
                    <span class="text-muted">{{ __('Offline') }}</span>
                @endif
            </td>
        </tr>
        @endif
        </tbody>
    </table>
</div>
