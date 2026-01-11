<div class="mt-1">
    <form method="GET" action="{{ route('ranking') }}" class="mb-4">
        <input type="hidden" name="type" value="guild">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search guild...') }}" class="form-control d-inline w-auto">
        <button type="submit" class="btn btn-sm btn-outline-secondary">{{ __('Search') }}</button>
    </form>
</div>

<div class="table-responsive">
    <table class="table table-striped">
        <thead class="table-dark">
        <tr>
            <th scope="col">{{ __('Rank') }}</th>
            <th scope="col">{{ __('Name') }}</th>
            <th scope="col">{{ __('Level') }}</th>
            <th scope="col">{{ __('Members') }}</th>
            <th scope="col">{{ __('Total Item Points') }}</th>
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
                        @if(isset($row->CrestIcon))
                            <img src="{{ route('ranking.guild.crest', ['bin' => $row->CrestIcon]) }}" alt="" width="16" height="16">
                        @endif
                        <a href="{{ route('ranking.guild.view', ['name' => $row->Name]) }}" class="text-decoration-none">{{ $row->Name }}</a>
                    </td>
                    <td>{{ $row->Lvl }}</td>
                    <td>{{ $row->TotalMember }}</td>
                    <td>{{ $row->ItemPoints }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">{{ __('No Records Found!') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
