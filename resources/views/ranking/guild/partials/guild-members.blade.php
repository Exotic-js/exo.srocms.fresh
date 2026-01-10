<div class="table-responsive">
    <table class="table table-striped">
        <thead class="table-dark">
            <tr>
                <th scope="col">{{ __('Rank') }}</th>
                <th scope="col">{{ __('Character Name') }}</th>
                <th scope="col">{{ __('Join Date') }}</th>
                <th scope="col">{{ __('Title') }}</th>
                <th scope="col">{{ __('Donation (GB)') }}</th>
            </tr>
        </thead>
        <tbody>
            @php $i = 1; @endphp
            @forelse($data->members as $row)
                <tr>
                    <td>{{ $i }}</td>
                    <td>
                        @if($row->RefObjID > 2000)
                            <img src="{{ asset(config('ranking.character_race')[1]['image']) }}" width="16" height="16" alt=""/>
                        @else
                            <img src="{{ asset(config('ranking.character_race')[0]['image']) }}" width="16" height="16" alt=""/>
                        @endif
                        <a href="{{ route('ranking.character.view', ['name' => $row->CharName]) }}" class="text-decoration-none">{{ $row->CharName }}</a>
                    </td>
                    <td>{{ date('d-m-Y', strtotime($row->JoinDate)) }}</td>
                    <td>
                        @if($row->SiegeAuthority > 0)
                            @if (array_key_exists($row->SiegeAuthority, config('ranking.guild_authority')))
                                {{ config('ranking.guild_authority')[$row->SiegeAuthority] }}
                            @endif
                        @else
                            {{ __('Member') }}
                        @endif
                    </td>
                    <td>{{ $row->GP_Donation }}</td>
                </tr>
                @php $i++ @endphp
            @empty
                <tr>
                    <td colspan="5" class="text-center">{{ __('No Records Found!') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
