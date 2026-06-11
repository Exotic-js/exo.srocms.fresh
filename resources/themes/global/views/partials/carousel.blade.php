<!-- Cinematic Slider Frame -->
<div class="cinematic-slider-container w-100 position-relative mb-4">
    
    <!-- Outer Corners for the Cinematic Frame -->
    <div class="slider-corner slider-corner-tl"></div>
    <div class="slider-corner slider-corner-tr"></div>
    <div class="slider-corner slider-corner-bl"></div>
    <div class="slider-corner slider-corner-br"></div>

    <div id="carouselExampleIndicators" class="carousel slide position-relative h-100" data-bs-ride="carousel">
        
        <div class="carousel-inner cinematic-carousel-inner h-100 w-100">
            <!-- Dark Vignette Overlay -->
            <div class="cinematic-slider-vignette position-absolute top-0 start-0 w-100 h-100 z-1"></div>
            
            @foreach(config('global.sliders') as $key => $row)
                <div class="carousel-item h-100 @if($key == 0) active @endif">
                    <img src="{{ $row['image'] }}" class="d-block w-100 h-100 object-fit-cover cinematic-slide-img" alt="Slider Image">
                </div>
            @endforeach
            
            @if (count(config('global.sliders')) > 0)
                <div class="carousel-indicators cinematic-indicators z-3">
                    @foreach(config('global.sliders') as $key => $row)
                        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="{{ $key }}" class="cinematic-dot @if($key == 0) active @endif" aria-current="true" aria-label="Slide {{ $key }}"></button>
                    @endforeach
                </div>
            @endif
        </div>

        @if (count(config('global.sliders')) > 0)
            <button class="carousel-control-prev cinematic-arrow z-3" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
                <span class="cinematic-arrow-icon prev">
                    <i class="fas fa-chevron-left"></i>
                </span>
            </button>
            <button class="carousel-control-next cinematic-arrow z-3" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
                <span class="cinematic-arrow-icon next">
                    <i class="fas fa-chevron-right"></i>
                </span>
            </button>
        @endif
    </div>
</div>
