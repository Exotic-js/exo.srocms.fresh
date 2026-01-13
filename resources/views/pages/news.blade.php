@extends('layouts.full')
@section('title', __('News'))

@section('content')
    <div class="container">
        <div class="card border-0">
            <div class="card-body">
                @php
                    $categories = ['all' => 'All News', 'news' => 'News', 'update' => 'Updates', 'event' => 'Events'];
                @endphp

                <nav class="mb-4">
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        @foreach($categories as $key => $label)
                            <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="nav-{{ $key }}-tab" data-bs-toggle="tab" data-bs-target="#nav-{{ $key }}" type="button" role="tab" aria-controls="nav-{{ $key }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </nav>

                <div class="tab-content" id="nav-tabContent">
                    @foreach($categories as $key => $label)
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="nav-{{ $key }}" role="tabpanel" tabindex="0">
                            <div class="row g-4">
                                @forelse($data as $row)
                                    @if($key === 'all' || $row->category === $key)
                                        <div class="col-lg-4">
                                            <div class="card h-100">
                                                @if($row->image)
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
                                                        <span class="badge text-bg-{{ match($row->category) {'news' => 'warning', 'update' => 'primary', 'event' => 'success', default => 'warning'} }}">{{ ucfirst($row->category) }}</span>
                                                        {{ $row->published_at->format("M j, Y") }}
                                                    </div>
                                                    <a href="{{ route('post.show', ['slug' => $row->slug]) }}" class="text-decoration-none">
                                                        <h3 class="card-title fw-bold font-cinzel h5">{{ \Illuminate\Support\Str::words(strip_tags($row->title), 3, '...') }}</h3>
                                                    </a>
                                                    <div class="card-text">{{ \Illuminate\Support\Str::words(strip_tags($row->content), 20, '...') }}</div>
                                                    <a href="{{ route('post.show', ['slug' => $row->slug]) }}" class="text-decoration-none font-cinzel mt-4">Read More →</a>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @empty
                                    <div class="alert alert-danger text-center" role="alert">
                                        {{ __('No Posts Available!') }}
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
