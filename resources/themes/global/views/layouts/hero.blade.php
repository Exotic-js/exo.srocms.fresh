<header class="golden-page-hero">

    <div class="container position-relative z-index-2">
        <div class="row">
            <div class="intro d-none d-lg-block">
                @include('partials.fortress-war')
            </div>
        </div>
    </div>

    <div id="parallax">
        <div class="parallax-layer parallax-layer-figure" data-depth="0.2"></div>
        <div class="parallax-layer parallax-layer-char" data-depth="0.6"></div>
    </div>

    {{-- Top dark shadow --}}
    <div class="golden-page-hero__top-shadow"></div>

    {{-- Mountain silhouette transition --}}
    <div class="golden-page-hero__mountain-wrap">

        {{-- Gold glow ridge line (SVG, same path) --}}
        <svg class="golden-page-hero__mountain-glow"
             viewBox="0 0 1440 90" preserveAspectRatio="none"
             xmlns="http://www.w3.org/2000/svg">
            <polyline
                points="0,70 80,55 160,68 240,38 300,52 380,20 440,40 500,28 560,48 620,15 680,35 740,10 800,30 860,50 920,22 980,42 1040,18 1100,38 1160,58 1220,30 1300,50 1380,35 1440,45"
                fill="none"
                stroke="url(#goldGrad)"
                stroke-width="1.5"
                filter="url(#goldGlow)"
            />
            <defs>
                <linearGradient id="goldGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%"   stop-color="rgba(207,173,107,0)"/>
                    <stop offset="20%"  stop-color="rgba(207,173,107,0.8)"/>
                    <stop offset="50%"  stop-color="rgba(255,220,100,1)"/>
                    <stop offset="80%"  stop-color="rgba(207,173,107,0.8)"/>
                    <stop offset="100%" stop-color="rgba(207,173,107,0)"/>
                </linearGradient>
                <filter id="goldGlow" x="-20%" y="-200%" width="140%" height="500%">
                    <feGaussianBlur stdDeviation="2.5" result="blur"/>
                    <feMerge><feMergeNode in="blur"/><feMergeNode in="SourceGraphic"/></feMerge>
                </filter>
            </defs>
        </svg>

        {{-- Dark mountain fill covering bottom --}}
        <svg class="golden-page-hero__mountain-fill"
             viewBox="0 0 1440 90" preserveAspectRatio="none"
             xmlns="http://www.w3.org/2000/svg">
            <polygon
                points="0,70 80,55 160,68 240,38 300,52 380,20 440,40 500,28 560,48 620,15 680,35 740,10 800,30 860,50 920,22 980,42 1040,18 1100,38 1160,58 1220,30 1300,50 1380,35 1440,45 1440,90 0,90"
                fill="rgb(10,8,5)"
            />
        </svg>

        {{-- Shimmer sweep on the ridge --}}
        <div class="golden-page-hero__ridge-shimmer"></div>
    </div>

</header>
