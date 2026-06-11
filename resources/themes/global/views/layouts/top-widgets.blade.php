
<section class="top-widgets">
    <div class="container">
        <div class="row">
            <div class="col-xl-3 col-lg-6 col-md-12 col-sm-12 col-12 d-none d-md-block mt-3">
                @include('partials.online-counter')
            </div>
            <div class="col-xl-3 col-lg-6 col-md-12 col-sm-12 col-12 d-none d-md-block mt-3">
                <div class="card">
                    <div class="card-header">
                        <img src="{{ asset('themes/global/assets/images/widget-icon-time.png') }}" alt="" height="31">
                        <h3>{{ __('Server Time:') }}</h3>
                    </div>
                    <div class="card-body py-0">
                        <!--<span id="idTimerClock">{{ date('H:i:s') }}</span>-->
                        <span id="clock">{{ date('H:i:s') }}</span>
                        <script type="text/javascript">
                            function updateClock() {
                                var currentTime = new Date();

                                var currentHours = currentTime.getHours();
                                var currentMinutes = currentTime.getMinutes();
                                var currentSeconds = currentTime.getSeconds();
                                currentMinutes = (currentMinutes < 10 ? "0" : "") + currentMinutes;
                                currentSeconds = (currentSeconds < 10 ? "0" : "") + currentSeconds;
                                var timeOfDay = (currentHours < 12) ? "AM" : "PM";
                                currentHours = (currentHours > 12) ? currentHours - 12 : currentHours;
                                currentHours = (currentHours == 0) ? 12 : currentHours;
                                var currentTimeString = currentHours + ":" + currentMinutes + ":" + currentSeconds + " " + timeOfDay;
                                document.getElementById("clock").firstChild.nodeValue = currentTimeString;
                                setTimeout(updateClock, 1000);
                            }
                            updateClock();
                        </script>
                    </div>
                </div>
            </div>
            @auth
                <div class="col-xl-3 col-lg-6 col-md-12 col-sm-12 col-12 d-none d-md-block mt-3">
                    <a href="{{ route('profile') }}" class="btn-home btn-login">
                        <h3>{{ __('Account') }}</h3>
                    </a>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-12 col-sm-12 col-12 d-none d-md-block mt-3">
                    <a href="{{ route('profile.donate') }}" class="btn-home btn-register">
                        <h3>{{ __('Donate') }}</h3>
                    </a>
                </div>
            @else
                <div class="col-xl-3 col-lg-6 col-md-12 col-sm-12 col-12 d-none d-md-block mt-3">
                    <a href="{{ route('login') }}" class="btn-home btn-login">
                        <h3>{{ __('Log in') }}</h3>
                    </a>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-12 col-sm-12 col-12 d-none d-md-block mt-3">
                    <a href="{{ route('register') }}" class="btn-home btn-register">
                        <h3>{{ __('Register') }}</h3>
                    </a>
                </div>
            @endauth
        </div>
    </div>
</section>
