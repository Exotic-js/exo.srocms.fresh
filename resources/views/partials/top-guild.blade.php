@if(config('widgets.top_guild.enabled'))
    <div class="card mb-4">
        <div class="card-header">
            {{ __('Top Guild') }}
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">{{ __('Rank') }}</th>
                            <th scope="col">{{ __('Name') }}</th>
                            <th scope="col">{{ __('Points') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topGuild as $key => $row)
                            <tr>
                                <td>
                                    @if($key < 3)
                                        <img src="{{ asset(config('ranking.top_image')[$key + 1]) }}" alt=""/>
                                    @else
                                        {{ $key + 1 }}
                                    @endif
                                </td>
                                <td>
                                    @if($row->Name)
                                    <a href="{{ route('ranking.guild.view', ['name' => $row->Name]) }}" class="text-decoration-none">{{ $row->Name }}</a>
                                    @endif
                                </td>
                                <td>{{ $row->ItemPoints }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">{{ __('No Records Found!') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif
