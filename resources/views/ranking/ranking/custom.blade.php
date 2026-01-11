<div class="table-responsive">
    <table class="table table-striped">
        <thead class="table-dark">
        <tr>
            <th scope="col">{{ __('#') }}</th>
            @foreach(array_filter(array_keys((array) $data->first()), fn($col) => !in_array($col, ['ID', 'CharID', 'GuildID', 'RefObjID'])) as $col)
                <th>{{ ucfirst($col) }}</th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        @if($data->isNotEmpty())
            @foreach($data as $key => $entry)
                <tr>
                    <td>
                        @if($key < 3)
                            <img src="{{ asset(config('ranking.top_image')[$key + 1]) }}" alt=""/>
                        @else
                            {{ $key + 1 }}
                        @endif
                    </td>
                    @foreach(array_filter(array_keys((array) $data->first()), fn($col) => !in_array($col, ['ID', 'CharID', 'GuildID', 'RefObjID'])) as $col)
                        <td>
                            @if($col === 'CharName')
                                @if(in_array('RefObjID', array_keys((array) $data->first())))
                                    @if($entry->RefObjID > 2000)
                                        <img src="{{ asset(config('ranking.character_race')[1]['image']) }}" width="16" height="16" alt=""/>
                                    @else
                                        <img src="{{ asset(config('ranking.character_race')[0]['image']) }}" width="16" height="16" alt=""/>
                                    @endif
                                @endif
                                <a href="{{ route('ranking.character.view', ['name' => $entry->$col]) }}" class="text-decoration-none">{{ $entry->$col }}</a>
                            @elseif($col === 'GuildName')
                                <a href="{{ route('ranking.guild.view', ['name' => $entry->$col]) }}" class="text-decoration-none">{{ $entry->$col }}</a>
                            @else
                                {{ $entry->$col }}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="{{ count($columns) + 1 }}" class="text-center">{{ __('No Records Found!') }}</td>
            </tr>
        @endif
        </tbody>
    </table>
</div>
