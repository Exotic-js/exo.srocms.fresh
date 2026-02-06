@auth
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title">{{ __('Welcome') }}, {{ auth()->user()->username }}</h5>

            <ul class="list-group list-group-flush mb-3">
                @if(config('global.server.version') === 'vSRO')
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>{{ __('Silk') }}</strong>
                        <span class="">{{ number_format(auth()->user()->tbUser->getSkSilk->silk_own ?? 0) }}</span>
                    </li>
                @else
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>{{ __('Premium Silk') }}</strong>
                        <span class="">{{ number_format(auth()->user()->muUser->JCash->PremiumSilk ?? 0) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>{{ __('Silk') }}</strong>
                        <span class="">{{ number_format(auth()->user()->muUser->JCash->Silk ?? 0) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>{{ __('VIP') }}</strong>
                        <span class="">
                            @isset(auth()->user()->muUser->muVIPInfo->VIPUserType)
                                <img src="{{ asset(config('ranking.vip_level')['level'][auth()->user()->muUser->muVIPInfo->VIPLv]['image']) }}" width="24" height="24" alt="">
                                <span>{{ config('ranking.vip_level')['level'][auth()->user()->muUser->muVIPInfo->VIPLv]['name'] }}</span>
                            @else
                                <span>{{ __('None') }}</span>
                            @endisset
                        </span>
                    </li>
                @endif
            </ul>

            <div class="d-grid gap-2">
                <a href="{{ route('profile') }}" class="btn btn-outline-secondary {{ request()->routeIs('profile') ? 'active' : '' }}">{{ __('Account Panel') }}</a>
                <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary {{ request()->routeIs('profile.edit') ? 'active' : '' }}">{{ __('Account Settings') }}</a>
                <a href="{{ route('profile.donate') }}" class="btn btn-outline-secondary {{ request()->routeIs('profile.donate') ? 'active' : '' }}">{{ __('Donate') }}</a>
                <a href="{{ route('profile.silk-history') }}" class="btn btn-outline-secondary {{ request()->routeIs('profile.silk-history') ? 'active' : '' }}">{{ __('Silk History') }}</a>
                <a href="{{ route('profile.voucher') }}" class="btn btn-outline-secondary {{ request()->routeIs('profile.voucher') ? 'active' : '' }}">{{ __('Voucher') }}</a>
                <a href="{{ route('profile.vote') }}" class="btn btn-outline-secondary {{ request()->routeIs('profile.vote') ? 'active' : '' }}">{{ __('Vote4Silk') }}</a>
                <a href="{{ route('profile.referral') }}" class="btn btn-outline-secondary {{ request()->routeIs('profile.referral') ? 'active' : '' }}">{{ __('Referral') }}</a>
                <a href="{{ route('profile.tickets') }}" class="btn btn-outline-secondary {{ request()->routeIs('profile.tickets') ? 'active' : '' }}">{{ __('Tickets') }}</a>
                <a href="{{ route('logout') }}" class="btn btn-outline-secondary" onclick="event.preventDefault(); fetch('{{ route('logout') }}', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}}).then(()=>window.location.href='{{ url('/') }}')">{{ __('Logout') }}</a>
            </div>
        </div>
    </div>
@endauth
