<div class="offcanvas-md offcanvas-end bg-body-tertiary" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="sidebarMenuLabel">{{ config('settings.site_title') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebarMenu" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body d-md-flex flex-column p-0 pt-lg-3 overflow-y-auto">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('admin') ? 'active' : '' }}" aria-current="page" href="{{ route('admin') }}">
                    <svg class="bi" aria-hidden="true"><use xlink:href="#house-fill"/></svg>
                    {{ __('Dashboard') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('admin.users.index') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                    <svg class="bi" aria-hidden="true"><use xlink:href="#people"/></svg>
                    {{ __('Users') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('admin.characters.index') ? 'active' : '' }}" href="{{ route('admin.characters.index') }}">
                    <svg class="bi" aria-hidden="true"><use xlink:href="#people"/></svg>
                    {{ __('Characters') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('admin.news.index') ? 'active' : '' }}" href="{{ route('admin.news.index') }}">
                    <svg class="bi" aria-hidden="true"><use xlink:href="#file-earmark"/></svg>
                    {{ __('News') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('admin.download.index') ? 'active' : '' }}" href="{{ route('admin.download.index') }}">
                    <svg class="bi" aria-hidden="true"><use xlink:href="#file-earmark"/></svg>
                    {{ __('Download') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('admin.pages.index') ? 'active' : '' }}" href="{{ route('admin.pages.index') }}">
                    <svg class="bi" aria-hidden="true"><use xlink:href="#file-earmark"/></svg>
                    {{ __('Pages') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('admin.vouchers.index') ? 'active' : '' }}" href="{{ route('admin.vouchers.index') }}">
                    <svg class="bi" aria-hidden="true"><use xlink:href="#file-earmark-text"></use></svg>
                    {{ __('Vouchers') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('admin.tickets.index') ? 'active' : '' }}" href="{{ route('admin.tickets.index') }}">
                    <svg class="bi" aria-hidden="true"><use xlink:href="#file-earmark-text"></use></svg>
                    {{ __('Tickets') }}
                </a>
            </li>
        </ul>

        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-body-secondary text-uppercase">
            <span>Logs</span>
            <!--
            <a class="link-secondary" href="#" aria-label="Add a new report">
                <svg class="bi" aria-hidden="true"><use xlink:href="#plus-circle"></use></svg>
            </a>
            -->
        </h6>
        <ul class="nav flex-column mb-auto">
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('admin.donate.logs') ? 'active' : '' }}" href="{{ route('admin.donate.logs') }}">
                    <svg class="bi" aria-hidden="true"><use xlink:href="#file-earmark-text"></use></svg>
                    {{ __('Donate Logs') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('admin.referral.logs') ? 'active' : '' }}" href="{{ route('admin.referral.logs') }}">
                    <svg class="bi" aria-hidden="true"><use xlink:href="#file-earmark-text"></use></svg>
                    {{ __('Referral Logs') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('admin.vote.logs') ? 'active' : '' }}" href="{{ route('admin.vote.logs') }}">
                    <svg class="bi" aria-hidden="true"><use xlink:href="#file-earmark-text"></use></svg>
                    {{ __('Vote Logs') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('admin.smc.logs') ? 'active' : '' }}" href="{{ route('admin.smc.logs') }}">
                    <svg class="bi" aria-hidden="true"><use xlink:href="#file-earmark-text"></use></svg>
                    {{ __('SMC Logs') }}
                </a>
            </li>
        </ul>

        <hr class="my-3">

        <ul class="nav flex-column mb-auto">
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('admin.settings.index') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">
                    <svg class="bi" aria-hidden="true"><use xlink:href="#gear-wide-connected"/></svg>
                    {{ __('Settings') }}
                </a>
            </li>
            <li class="nav-item">
                <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Are you sure you want to Sign out?')">
                    @csrf
                    <a class="nav-link text-danger d-flex align-items-center gap-2" href="{{ route('logout') }}" onclick="event.preventDefault();this.closest('form').submit();">
                        <svg class="bi" aria-hidden="true"><use xlink:href="#door-closed"/></svg>
                        {{ __('Log Out') }}
                    </a>
                </form>
            </li>
        </ul>
    </div>
</div>
