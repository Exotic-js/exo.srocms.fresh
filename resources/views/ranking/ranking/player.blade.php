<div class="mt-1">
    <form method="GET" action="{{ route('ranking') }}" class="mb-4">
        <input type="hidden" name="type" value="player">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search player...') }}" class="form-control d-inline w-auto">
        <button type="submit" class="btn btn-sm btn-outline-secondary">{{ __('Search') }}</button>
    </form>
</div>

<div class="table-responsive">
    <table class="table table-striped">
        <thead class="table-dark">
            <tr>
                <th scope="col">{{ __('Rank') }}</th>
                <th scope="col">{{ __('Name') }}</th>
                <th scope="col">{{ __('Guild') }}</th>
                <th scope="col">{{ __('Level') }}</th>
                <th scope="col">{{ __('Item Points') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $key => $value)
                <tr>
                    <td>
                        @if($key < 3)
                            <img src="{{ asset($config->topImage[$key + 1]) }}" alt=""/>
                        @else
                            {{ $key + 1 }}
                        @endif
                    </td>
                    <td>
                        @if($value->RefObjID > 2000)
                            <img src="{{ asset($config->characterRace[1]['image']) }}" width="16" height="16" alt=""/>
                        @else
                            <img src="{{ asset($config->characterRace[0]['image']) }}" width="16" height="16" alt=""/>
                        @endif
                        <a href="{{ route('ranking.character.view', ['name' => $value->CharName16]) }}" class="text-decoration-none">{{ $value->CharName16 }}</a>
                    </td>
                    <td>
                        @if($value->ID > 0)
                            <a href="{{ route('ranking.guild.view', ['name' => $value->Name]) }}" class="text-decoration-none">{{ $value->Name }}</a>
                        @else
                            <span>{{ __('None') }}</span>
                        @endif
                    </td>
                    <td>{{ $value->CurLevel }}</td>
                    <td>{{ $value->ItemPoints }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">{{ __('No Records Found!') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
