@extends('layouts.app')
@section('title', __('Downloads'))

@section('content')
    <!-- Download Links Section -->
    <div class="golden-download-grid mb-5 mt-4">
        <div class="row g-4 justify-content-center">
            @forelse($data as $row)
                <div class="col-lg-6 col-md-6">
                    <div class="golden-dl-card">
                        <div class="golden-dl-inner">
                            <div class="golden-dl-info {{ $row->image ? 'd-flex align-items-center text-start' : 'text-center' }}">
                                @if ($row->image)
                                    <div class="me-4 flex-shrink-0">
                                        <img src="{{ $row->image }}" alt="{{ $row->name }}" style="max-width: 90px; max-height: 90px; border-radius: 5px; box-shadow: 0 0 10px rgba(201, 160, 91, 0.2);">
                                    </div>
                                @endif
                                <div class="flex-grow-1">
                                    <h4 class="golden-dl-title text-warning">{{ $row->name }}</h4>
                                    <p class="golden-dl-detail">
                                        File Size: {{ $row->desc }}
                                    </p>
                                    <a href="{{ $row->url }}" target="_blank" class="golden-btn-download mt-2">
                                        <span class="arrow left">&lt;</span> Download <span class="arrow right">&gt;</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-danger text-center" role="alert">
                        {{ __('No Downloads Available!') }}
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Section Divider -->
    <div class="golden-section-title text-center mb-4 mt-5">
        <h3 class="font-cinzel text-warning fw-bold">SYSTEM REQUIREMENTS</h3>
    </div>

    <!-- System Requirements Table -->
    <div class="golden-sys-req-table mb-5">
        <table class="table table-dark table-bordered text-center align-middle m-0">
            <thead class="golden-table-head">
                <tr>
                    <th scope="col" width="20%">CATEGORY</th>
                    <th scope="col" width="40%">MINIMUM SYSTEM REQUIREMENTS</th>
                    <th scope="col" width="40%">RECOMMENDED SYSTEM REQUIREMENTS</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="fw-bold text-warning font-cinzel">CPU</td>
                    <td>Pentium 3 800MHz or higher</td>
                    <td>Intel i3 or higher</td>
                </tr>
                <tr>
                    <td class="fw-bold text-warning font-cinzel">RAM</td>
                    <td>2GB</td>
                    <td>4GB</td>
                </tr>
                <tr>
                    <td class="fw-bold text-warning font-cinzel">VGA</td>
                    <td>3D speed over GeForce2 or ATI 9000</td>
                    <td>3D speed over GeForce FX 5600 or ATI9500</td>
                </tr>
                <tr>
                    <td class="fw-bold text-warning font-cinzel">SOUND</td>
                    <td>DirectX 9.0c Compatibility card</td>
                    <td>DirectX 9.0c Compatibility card</td>
                </tr>
                <tr>
                    <td class="fw-bold text-warning font-cinzel">HDD</td>
                    <td>5GB or higher (including swap and temporary file)</td>
                    <td>8GB or higher (including swap and temporary file)</td>
                </tr>
                <tr>
                    <td class="fw-bold text-warning font-cinzel">OS</td>
                    <td>Windows 7 / 8</td>
                    <td>Windows 10 / 11</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Graphics Drivers / Partners -->
    <div class="golden-gfx-logos mb-5">
        <div class="row g-4 w-100 m-0">
            <div class="col-md-6 p-0 pe-md-2">
                <a href="https://www.nvidia.com/Download/index.aspx" target="_blank" class="text-decoration-none">
                    <div class="golden-gfx-box">
                        <img class="d-block mx-auto" src="{{ asset('themes/global/assets/images/logo-nvidia.png') }}" alt="NVIDIA">
                    </div>
                </a>
            </div>
            <div class="col-md-6 p-0 ps-md-2">
                <a href="https://www.amd.com/en/support" target="_blank" class="text-decoration-none">
                    <div class="golden-gfx-box">
                        <img class="d-block mx-auto" src="{{ asset('themes/global/assets/images/logo-amd.png') }}" alt="AMD">
                    </div>
                </a>
            </div>
        </div>
    </div>
@endsection
