@if(config('widgets.unique_history.enabled'))
    <div class="card mb-4">
        <div class="card-header">
            {{ __('Unique History') }}
        </div>
        <div class="card-body">
            <ul class="list-unstyled">
                @forelse($uniqueHistory as $row)
                    <li class="mb-3">
                        <p class="mb-0">{{ config('ranking.uniques')[$row->Value]['name'] }}</p>
                        <small>
                            {{ __('Killed by:') }}
                            @if(!empty($row->CharName16))
                                <a href="{{ route('ranking.character.view', ['name' => $row->CharName16]) }}" class="text-decoration-none">{{ $row->CharName16 }}</a>
                            @else
                                <span>{{ __('None') }}</span>
                            @endif
                            {{ \Carbon\Carbon::make($row->EventTime)->diffForHumans() }}
                        </small>
                    </li>
                @empty
                    <p class="text-center">{{ __('No Records Found!') }}</p>
                @endforelse
            </ul>
        </div>
    </div>
@endif
