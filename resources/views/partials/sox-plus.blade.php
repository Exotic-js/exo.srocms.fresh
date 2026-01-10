@if(config('widgets.sox_plus.enabled'))
    <div class="card mb-4">
        <div class="card-header">
            {{ __('Sox Plus') }}
        </div>
        <div class="card-body">
            <ul class="list-unstyled">
                @forelse($soxPlus as $row)
                    <li>
                        <p>
                            <img src="{{ asset('images/sro/' . $row->AssocFileIcon128 . '.png') }}" alt="" width="32" height="32" class="">
                            @if(!empty($row->CharName16))
                                [<a href="{{ route('ranking.character.view', ['name' => $row->CharName16]) }}" class="text-decoration-none">{{ $row->CharName16 }}</a>]
                            @else
                                [{{ __('NoName') }}]
                            @endif
                            {{ __('has successfully enchanted their') }}
                            [{{ $row->ENG ?? $row->RealName }}]
                            {{ __('to') }}
                            [+{{ $row->PlusValue }}]
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
