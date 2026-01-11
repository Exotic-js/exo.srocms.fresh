<div class="table-responsive">
    <table class="table table-striped">
        <thead class="table-dark">
        <tr>
            <th scope="col">{{ __('Rank') }}</th>
            <th scope="col">{{ __('Name') }}</th>
            <th scope="col">{{ __('Kills/Death') }}</th>
        </tr>
        </thead>
        <tbody>
            @forelse($data as $key => $row)
            <tr>
                <td>
                    @if($key < 3)
                        <img src="{{ asset(config('ranking.top_image')[$key + 1]) }}" alt=""/>
                    @else
                        {{ $key + 1 }}
                    @endif
                    </td>
                    <td>
                        @if($row->RefObjID > 2000)
                            <img src="{{ asset(config('ranking.character_race')[1]['image']) }}" width="16" height="16" alt=""/>
                        @else
                            <img src="{{ asset(config('ranking.character_race')[0]['image']) }}" width="16" height="16" alt=""/>
                        @endif
                        <a href="{{ route('ranking.character.view', ['name' => $row->CharName16]) }}" class="text-decoration-none">{{ $row->CharName16 }}</a>
                    </td>
                    <td>
                        {{ $row->GuildWarKill }} / {{ $row->GuildWarKilled }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">{{ __('No Records Found!') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
