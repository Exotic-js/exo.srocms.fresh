<div class="table-responsive">
    <table class="table table-striped">
        <thead class="table-dark">
            <tr>
                <th scope="col">{{ __('Rank') }}</th>
                <th scope="col">{{ __('NickName') }}</th>
                <th scope="col">{{ __('JobLevel') }}</th>
                <th scope="col">{{ __('Kills') }}</th>
                <th scope="col">{{ __('Points') }}</th>
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
                        <a href="{{ route('ranking.character.view', ['name' => $value->CharName16]) }}" class="text-decoration-none">{{ $value->NickName16 }}</a>
                    </td>
                    <td>{{ $value->JobLevel ?? $value->Level }}</td>
                    <td>{{ $value->KillCount ?? 0 }}</td>
                    <td>{{ $value->ReputationPoint ?? $value->Exp }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">{{ __('No Records Found!') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
