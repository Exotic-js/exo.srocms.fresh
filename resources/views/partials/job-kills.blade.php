@if(config('widgets.job_kills.enabled'))
    <div class="card mb-4">
        <div class="card-header">
            {{ __('Job Kills') }}
        </div>
        <div class="card-body">
            <ul class="list-unstyled">
                @forelse($jobKills as $row)
                    <li>
                        <p>
                            @if($row->KillerCharName)
                                <a href="{{ route('ranking.character.view', ['name' => $row->KillerCharName]) }}" class="text-decoration-none">{{ $row->KillerCharName }}</a>
                            @endif
                            {{ __('Has killed:') }}
                            @if($row->DeadCharName)
                                <a href="{{ route('ranking.character.view', ['name' => $row->DeadCharName]) }}" class="text-decoration-none">{{ $row->DeadCharName }}</a>
                            @endif
                            {{ \Carbon\Carbon::make($row->EventTime)->diffForHumans() }}
                        </p>
                        <hr>
                    </li>
                @empty
                    <p class="text-center">{{ __('No Records Found!') }}</p>
                @endforelse
            </ul>
        </div>
    </div>
@endif
