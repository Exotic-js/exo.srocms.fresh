<div class="table-responsive">
    <table class="table table-striped">
        <thead class="table-dark">
            <tr>
                <th scope="col">{{ __('Rank') }}</th>
                <th scope="col">{{ __('NickName') }}</th>
                <th scope="col">{{ __('Job') }}</th>
                <th scope="col">{{ __('JobLevel') }}</th>
                <th scope="col">{{ __('Kills') }}</th>
                <th scope="col">{{ __('Points') }}</th>
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
                        <a href="{{ route('ranking.character.view', ['name' => $row->CharName16]) }}" class="text-decoration-none">{{ $row->NickName16 }}</a>
                    </td>
                    <td>
                        @if(config('global.server.version') === 'vSRO')
                            <img src="{{ asset(config('ranking.job_type_vsro')[$row->JobType]['small_image']) }}" alt=""/>
                            {{ config('ranking.job_type_vsro')[$row->JobType]['name'] }}
                        @else
                            <img src="{{ asset(config('ranking.job_type')[$row->JobType]['small_image']) }}" alt=""/>
                            {{ config('ranking.job_type')[$row->JobType]['name'] }}
                        @endif
                    </td>
                    <td>{{ $row->JobLevel ?? $row->Level }}</td>
                    <td>{{ $row->KillCount ?? 0 }}</td>
                    <td>{{ $row->ReputationPoint ?? $row->Exp }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">{{ __('No Records Found!') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
