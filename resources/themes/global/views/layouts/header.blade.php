<div class="modal fade" id="modalSearch" tabindex="-1" role="dialog" aria-labelledby="myModalSearchLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalSearchLabel">Search</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text">
                <form method="GET" action="{{ route('ranking') }}">
                    <div class="input-group mb-3">
                        <div class="input-group-append">
                            <select name="type" class="form-control">
                                <option value="player">Character</option>
                                <option value="guild">Guild</option>
                            </select>
                        </div>

                        <input type="text" name="search" placeholder="Charname, Guild" class="form-control" value="{{ request('search') }}">

                        <div class="input-group-append">
                            <button type="submit" name="submit" class="btn btn-primary" value="">Search</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<header class="golden-header position-absolute w-100 z-index-100" style="top: 0; left: 0;">
    <!-- Top Bar Hex -->
    <div class="topbar-container mx-auto mt-3" style="max-width: 1400px; position: relative; z-index: 10;">
        <!-- Background elements with clip-path -->
        <div class="topbar-bg-outer" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: #4a381c; clip-path: polygon(25px 0%, calc(100% - 25px) 0%, 100% 50%, calc(100% - 25px) 100%, 25px 100%, 0% 50%); z-index: 1;"></div>
        <div class="topbar-bg-inner" style="position: absolute; top: 1px; left: 1px; right: 1px; bottom: 1px; background: #080705; clip-path: polygon(24px 0%, calc(100% - 24px) 0%, 100% 50%, calc(100% - 24px) 100%, 24px 100%, 0% 50%); z-index: 2;"></div>
        
        <!-- Content on top, NO clip path -->
        <div class="topbar-content d-flex justify-content-between align-items-center px-5 py-2" style="position: relative; z-index: 3; font-size: 13px; color: #ccc;">
            <div class="topbar-text">
                {!! __('Begin Your <span style="color:#d8b163;">Revenor Saga!</span>, Where legends are born') !!}
            </div>
            <div class="topbar-socials d-flex align-items-center">
                <a class="text-decoration-none mx-2 text-warning fs-5" href="#"><i class="fab fa-youtube"></i></a>
                <a class="text-decoration-none mx-2 text-warning fs-5" href="#"><i class="fab fa-facebook-square"></i></a>
                <a class="text-decoration-none mx-2 text-warning fs-5" href="#"><i class="fab fa-discord"></i></a>
                @if(config('settings.default_locale', 'switch') == 'switch')
                    <span class="dropdown dropdown-language d-inline-block ms-3">
                        <a class="dropdown-toggle text-warning text-decoration-none" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="fi fi-{{ config('global.languages')[App::getLocale()]['flag'] }}"></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-dark" aria-labelledby="dropdownMenuButton1">
                            @foreach(config('global.languages') as $key => $item)
                                <li>
                                    <a class="dropdown-item" href="{{ route('locale', $key) }}">
                                        <span class="fi fi-{{ $item['flag'] }}"></span>
                                        {{ $item['name'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Main Navbar -->
    <nav class="golden-navbar position-relative mt-5">
        <div class="container-fluid d-flex align-items-center" style="max-width: 1500px;">
            
            <!-- Left Links -->
            <div class="nav-left-wrapper d-flex align-items-center justify-content-start" style="flex: 1; min-width: 0;">
                <ul class="golden-nav-links left-links mb-0 p-0 d-flex align-items-center m-0 flex-nowrap" style="gap: 12px; list-style: none; max-width: none;">
                    <li class="{{ request()->routeIs('home') ? 'active' : '' }}"><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
                    <li class="{{ request()->routeIs('download') ? 'active' : '' }}"><a href="{{ route('download') }}">{{ __('Download') }}</a></li>
                    <li class="{{ request()->routeIs('ranking') ? 'active' : '' }}"><a href="{{ route('ranking') }}">{{ __('Ranking') }}</a></li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">{{ __('System') }}</a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{ route('history.schedule') }}">{{ __('Event Schedule') }}</a>
                            <a class="dropdown-item" href="{{ route('history.unique') }}">{{ __('Unique Tracker') }}</a>
                            <a class="dropdown-item" href="{{ route('history.fortress') }}">{{ __('Fortress History') }}</a>
                        </div>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">{{ __('Rules') }}</a>
                        <div class="dropdown-menu">
                            @forelse ($pageNames as $row)
                                <a class="dropdown-item" href="{{ route('page.show', ['slug' => $row->slug]) }}">{{ $row->title }}</a>
                            @empty
                                <a class="dropdown-item" href="#">{{ __('No Rules') }}</a>
                            @endforelse
                        </div>
                    </li>
                    @if(config('affiliate.enabled', false))
                    <li class="{{ request()->routeIs('affiliate.*') ? 'active' : '' }}">
                        <a href="{{ route('affiliate.landing') }}">{{ __('Recruit') }}</a>
                    </li>
                    @endif
                    @if(Route::has('market.index') && config('webmarket.enabled', true))
                    <li class="{{ request()->routeIs('market.*') ? 'active' : '' }}">
                        <a href="{{ route('market.index') }}">{{ __('Market') }}</a>
                    </li>
                    @endif
                </ul>
            </div>

            <!-- Center Logo Spacer -->
            <div class="nav-center-spacer d-none d-xl-block" style="width: 360px; flex-shrink: 0;"></div>

            <!-- Right Buttons (Login/Register) -->
            <div class="nav-right-wrapper d-flex align-items-center justify-content-end" style="flex: 1; min-width: 0;">
                <div class="golden-auth-buttons d-flex align-items-center m-0 flex-nowrap" style="gap: 10px;">
                    @if (Route::has('login'))
                        @auth
                            <div class="dropdown d-inline-block">
                                <button class="btn-hex-wrapper dropdown-toggle border-0 bg-transparent p-0" type="button" data-bs-toggle="dropdown">
                                    <span class="btn-hex-inner">
                                        <span class="btn-hex-arrow">&lt;</span>
                                        <span>{{ auth()->user()->username }}</span>
                                        <span class="btn-hex-arrow">&gt;</span>
                                    </span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-dark">
                                    @if(config('global.server.version') === 'vSRO')
                                        <li><a class="dropdown-item text-center fw-bold text-warning" href="#">{{ auth()->user()->tbUser->getSkSilk->silk_own ?? 0 }} {{ __('Silk') }}</a></li>
                                    @else
                                        <li><a class="dropdown-item text-center fw-bold text-warning" href="#">{{ auth()->user()->muUser->JCash->PremiumSilk ?? 0 }} {{ __('Silk') }}</a></li>
                                    @endif
                                    <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="fas fa-user"></i> {{ __('Account') }}</a></li>
                                    <li><a class="dropdown-item" href="{{ route('profile.donate') }}"><i class="fas fa-coins"></i> {{ __('Donate') }}</a></li>
                                    @if(config('affiliate.enabled', false))
                                    <li><a class="dropdown-item" href="{{ route('affiliate.my-team') }}"><i class="fas fa-users"></i> {{ __('My Team') }}</a></li>
                                    @endif
                                    @if(auth()->user()->role?->is_admin)
                                        <li><a class="dropdown-item" href="{{ route('admin') }}"><i class="fas fa-wrench"></i> {{ __('Admin panel') }}</a></li>
                                    @endif
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <a class="dropdown-item" href="#" onclick="event.preventDefault();this.closest('form').submit();">
                                                <i class="fas fa-sign-out-alt"></i> {{ __('Log Out') }}
                                            </a>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @else
                            @if (Route::has('register'))
                                <div class="btn-hex-wrapper mx-1">
                                    <a href="{{ route('register') }}" class="btn-hex-inner text-decoration-none">
                                        <span class="btn-hex-arrow">&lt;</span>
                                        <span>{{ __('REGISTER') }}</span>
                                        <span class="btn-hex-arrow">&gt;</span>
                                    </a>
                                </div>
                            @endif
                            <div class="btn-hex-wrapper mx-1">
                                <a href="{{ route('login') }}" class="btn-hex-inner text-decoration-none">
                                    <span class="btn-hex-arrow">&lt;</span>
                                    <span>{{ __('LOGIN') }}</span>
                                    <span class="btn-hex-arrow">&gt;</span>
                                </a>
                            </div>
                        @endauth
                    @endif
                </div>
            </div>

        </div>

        <!-- Center Logo Overlay -->
        <div class="center-logo-hex-wrapper">
            <div class="center-logo-hex-inner">
                <a href="{{ url('/') }}">
                    <img src="{{ asset('themes/global/assets/images/logo.png') }}" class="golden-logo-main" alt="Logo">
                </a>
            </div>
        </div>
    </nav>
</header>
