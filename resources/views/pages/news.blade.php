@extends('layouts.full')
@section('title', __('News'))

@section('content')
    <div class="container">
        <div class="card border-0">
            <div class="card-body">
                <nav class="mb-4">
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <button class="nav-link active" id="nav-all-news-tab" data-bs-toggle="tab" data-bs-target="#nav-all-news" type="button" role="tab" aria-controls="nav-all-news" aria-selected="true">All News</button>
                        <button class="nav-link" id="nav-news-tab" data-bs-toggle="tab" data-bs-target="#nav-news" type="button" role="tab" aria-controls="nav-news" aria-selected="false">News</button>
                        <button class="nav-link" id="nav-events-tab" data-bs-toggle="tab" data-bs-target="#nav-events" type="button" role="tab" aria-controls="nav-events" aria-selected="false">Events</button>
                        <button class="nav-link" id="nav-updates-tab" data-bs-toggle="tab" data-bs-target="#nav-updates" type="button" role="tab" aria-controls="nav-updates" aria-selected="false">Updates</button>
                    </div>
                </nav>
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-all-news" role="tabpanel" aria-labelledby="nav-all-news-tab" tabindex="0">
                        <div class="row g-4">
                            @forelse($data as $row)
                                <div class="col-lg-4">
                                    <div class="card h-100">
                                        @if ($row->image)
                                            <img src="{{ $row->image }}" class="card-img-top" alt="..." style="height: 200px;">
                                        @else
                                            <div class="bg-secondary" style="height: 200px;">
                                                <div class="h-100 d-flex align-items-center justify-content-center text-white">
                                                    [News Image Placeholder]
                                                </div>
                                            </div>
                                        @endif
                                        <div class="card-body">
                                            <div class="small mb-2 font-cinzel">
                                                @switch($row->category)
                                                    @case('news')
                                                        <span class="badge text-bg-warning">{{ __('News') }}</span>
                                                        @break
                                                    @case('update')
                                                        <span class="badge text-bg-primary">{{ __('Update') }}</span>
                                                        @break
                                                    @case('event')
                                                        <span class="badge text-bg-success">{{ __('Event') }}</span>
                                                        @break
                                                    @default
                                                        <span class="badge text-bg-warning">{{ __('News') }}</span>
                                                @endswitch
                                                {{ $row->published_at->format("M j, Y") }}
                                            </div>
                                            <a href="{{ route('pages.post.show', ['slug' => $row->slug]) }}" class="text-decoration-none">
                                                <h3 class="card-title fw-bold font-cinzel h5">{{ \Illuminate\Support\Str::words(strip_tags($row->title), 3, '...') }}</h3>
                                            </a>
                                            <div class="card-text">
                                                {{ \Illuminate\Support\Str::words(strip_tags($row->content), 20, '...') }}
                                            </div>
                                            <a href="{{ route('pages.post.show', ['slug' => $row->slug]) }}" class="text-decoration-none font-cinzel mt-4">
                                                Read More →
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="alert alert-danger text-center" role="alert">
                                    {{ __('No Posts Available!') }}
                                </div>
                            @endforelse
                        </div>
                    </div>
                    <div class="tab-pane fade" id="nav-news" role="tabpanel" aria-labelledby="nav-news-tab" tabindex="0">
                        <div class="row g-4">
                            @forelse($data as $row)
                                @if($row->category == 'news')
                                    <div class="col-lg-4">
                                        <div class="card h-100">
                                            @if ($row->image)
                                                <img src="{{ $row->image }}" class="card-img-top" alt="..." style="height: 200px;">
                                            @else
                                                <div class="bg-secondary" style="height: 200px;">
                                                    <div class="h-100 d-flex align-items-center justify-content-center text-white">
                                                        [News Image Placeholder]
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="card-body">
                                                <div class="small mb-2 font-cinzel">
                                                    @switch($row->category)
                                                        @case('news')
                                                            <span class="badge text-bg-warning">{{ __('News') }}</span>
                                                            @break
                                                        @case('update')
                                                            <span class="badge text-bg-primary">{{ __('Update') }}</span>
                                                            @break
                                                        @case('event')
                                                            <span class="badge text-bg-success">{{ __('Event') }}</span>
                                                            @break
                                                        @default
                                                            <span class="badge text-bg-warning">{{ __('News') }}</span>
                                                    @endswitch
                                                    {{ $row->published_at->format("M j, Y") }}
                                                </div>
                                                <a href="{{ route('pages.post.show', ['slug' => $row->slug]) }}" class="text-decoration-none">
                                                    <h3 class="card-title fw-bold font-cinzel h5">{{ \Illuminate\Support\Str::words(strip_tags($row->title), 3, '...') }}</h3>
                                                </a>
                                                <div class="card-text">
                                                    {{ \Illuminate\Support\Str::words(strip_tags($row->content), 20, '...') }}
                                                </div>
                                                <a href="{{ route('pages.post.show', ['slug' => $row->slug]) }}" class="text-decoration-none font-cinzel mt-4">
                                                    Read More →
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @empty
                            @endforelse
                        </div>
                    </div>
                    <div class="tab-pane fade" id="nav-updates" role="tabpanel" aria-labelledby="nav-updates-tab" tabindex="0">
                        <div class="row g-4">
                            @forelse($data as $row)
                                @if($row->category == 'update')
                                    <div class="col-lg-4">
                                        <div class="card h-100">
                                            @if ($row->image)
                                                <img src="{{ $row->image }}" class="card-img-top" alt="..." style="height: 200px;">
                                            @else
                                                <div class="bg-secondary" style="height: 200px;">
                                                    <div class="h-100 d-flex align-items-center justify-content-center text-white">
                                                        [News Image Placeholder]
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="card-body">
                                                <div class="small mb-2 font-cinzel">
                                                    @switch($row->category)
                                                        @case('news')
                                                            <span class="badge text-bg-warning">{{ __('News') }}</span>
                                                            @break
                                                        @case('update')
                                                            <span class="badge text-bg-primary">{{ __('Update') }}</span>
                                                            @break
                                                        @case('event')
                                                            <span class="badge text-bg-success">{{ __('Event') }}</span>
                                                            @break
                                                        @default
                                                            <span class="badge text-bg-warning">{{ __('News') }}</span>
                                                    @endswitch
                                                    {{ $row->published_at->format("M j, Y") }}
                                                </div>
                                                <a href="{{ route('pages.post.show', ['slug' => $row->slug]) }}" class="text-decoration-none">
                                                    <h3 class="card-title fw-bold font-cinzel h5">{{ \Illuminate\Support\Str::words(strip_tags($row->title), 3, '...') }}</h3>
                                                </a>
                                                <div class="card-text">
                                                    {{ \Illuminate\Support\Str::words(strip_tags($row->content), 20, '...') }}
                                                </div>
                                                <a href="{{ route('pages.post.show', ['slug' => $row->slug]) }}" class="text-decoration-none font-cinzel mt-4">
                                                    Read More →
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @empty
                            @endforelse
                        </div>
                    </div>
                    <div class="tab-pane fade" id="nav-events" role="tabpanel" aria-labelledby="nav-events-tab" tabindex="0">
                        <div class="row g-4">
                            @forelse($data as $row)
                                @if($row->category == 'event')
                                    <div class="col-lg-4">
                                        <div class="card h-100">
                                            @if ($row->image)
                                                <img src="{{ $row->image }}" class="card-img-top" alt="..." style="height: 200px;">
                                            @else
                                                <div class="bg-secondary" style="height: 200px;">
                                                    <div class="h-100 d-flex align-items-center justify-content-center text-white">
                                                        [News Image Placeholder]
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="card-body">
                                                <div class="small mb-2 font-cinzel">
                                                    @switch($row->category)
                                                        @case('news')
                                                            <span class="badge text-bg-warning">{{ __('News') }}</span>
                                                            @break
                                                        @case('update')
                                                            <span class="badge text-bg-primary">{{ __('Update') }}</span>
                                                            @break
                                                        @case('event')
                                                            <span class="badge text-bg-success">{{ __('Event') }}</span>
                                                            @break
                                                        @default
                                                            <span class="badge text-bg-warning">{{ __('News') }}</span>
                                                    @endswitch
                                                    {{ $row->published_at->format("M j, Y") }}
                                                </div>
                                                <a href="{{ route('pages.post.show', ['slug' => $row->slug]) }}" class="text-decoration-none">
                                                    <h3 class="card-title fw-bold font-cinzel h5">{{ \Illuminate\Support\Str::words(strip_tags($row->title), 3, '...') }}</h3>
                                                </a>
                                                <div class="card-text">
                                                    {{ \Illuminate\Support\Str::words(strip_tags($row->content), 20, '...') }}
                                                </div>
                                                <a href="{{ route('pages.post.show', ['slug' => $row->slug]) }}" class="text-decoration-none font-cinzel mt-4">
                                                    Read More →
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @empty
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
