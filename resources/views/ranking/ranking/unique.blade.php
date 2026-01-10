<div class="table-responsive">
    <table class="table table-striped">
        <thead class="table-dark">
            <tr>
                <th scope="col">{{ __('Rank') }}</th>
                <th scope="col">{{ __('Name') }}</th>
                <th scope="col">{{ __('Guild') }}</th>
                <th scope="col">{{ __('Level') }}</th>
                <th scope="col">{{ __('Points') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $key => $value)
            <tr>
                <td>
                    @if($key < 3)
                        <img src="{{ asset(config('ranking.top_image')[$key + 1]) }}" alt=""/>
                    @else
                        {{ $key + 1 }}
                    @endif
                    </td>
                    <td>
                        @if($value->RefObjID > 2000)
                            <img src="{{ asset(config('ranking.character_race')[1]['image']) }}" width="16" height="16" alt=""/>
                        @else
                            <img src="{{ asset(config('ranking.character_race')[0]['image']) }}" width="16" height="16" alt=""/>
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
                    <td>{{ $value->Points }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">{{ __('No Records Found!') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<p class="mb-2 text-center">
    @forelse(config('ranking.uniques') as $value)
        <span>{{ $value['name'] }} [{{ $value['points'] }} {{ __('points') }}]</span>,
    @empty
    @endforelse
</p>
