@if(config('widgets.sox_drop.enabled'))
<div class="card widget-soxdrop mb-5">
    <div class="card-header">
        <img src="{{ asset('themes/global/assets/images/widget-icon-soxdrop.png') }}" alt="" height="31">
        <h3>Latest Sox Drop</h3>
    </div>
    <div class="card-body">
        <ul class="list-unstyled">
            @forelse($soxDrop as $row)
            <li>
                <div class="item-image">
                    <img src="{{ asset('images/sro/' . $row->AssocFileIcon128 . '.png') }}" alt="" height="32" width="32">
                </div>
                <div class="item-content">
                    <span>
                        @if(!empty($row->CharName16))
                            [<a href="{{ route('ranking.character.view', ['name' => $row->CharName16]) }}" class="text-decoration-none">{{ $row->CharName16 }}</a>]
                        @else
                            [{{ __('NoName') }}]
                        @endif
                        {{ __('has just obtained a') }}
                        [{{ $row->ENG ?? $row->RealName }}]
                        {{ __('The Item was obtained from:') }}
                        [{{ $row->MobCode }}]
                    </span>
                </div>
            </li>
            @empty
                <p class="text-center">{{ __('No Records Found!') }}</p>
            @endforelse
        </ul>
    </div>
</div>
@endif
