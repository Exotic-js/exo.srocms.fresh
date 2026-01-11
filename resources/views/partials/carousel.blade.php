<div id="myCarousel" class="carousel slide mb-6" data-bs-ride="carousel">
    @if (count(config('global.sliders')) > 0)
    <div class="carousel-indicators">
        @foreach(config('global.sliders') as $key => $row)
            <button type="button" data-bs-target="#myCarousel" data-bs-slide-to="{{ $key }}" @if($key == 0) class="active" @endif aria-current="true" aria-label="Slide {{ $key }}"></button>
        @endforeach
    </div>
    @endif
    <div class="carousel-inner">
        @foreach(config('global.sliders') as $key => $row)
            <div class="carousel-item @if($key == 0) active @endif" style="background-image: url({{ $row['image'] }}) !important; background-repeat: no-repeat; background-size: cover; background-position: center;">

                <div class="container">
                    <div class="carousel-caption" style="bottom: 8rem;">
                        <h1 style="color: {{ $row['title_color'] }};">{{ $row['title'] }}</h1>
                        <p class="opacity-75" style="color: {{ $row['desc_color'] }};">{{ $row['desc'] }}</p>
                        <p><a class="btn btn-lg btn-primary" href="{{ $row['btn_url'] }}">{{ $row['btn_label'] }}</a></p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @if (count(config('global.sliders')) > 0)
    <button class="carousel-control-prev" type="button" data-bs-target="#myCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#myCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
    @endif
</div>
