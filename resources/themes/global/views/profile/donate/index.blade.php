@extends('layouts.app')
@section('title', __('Donate'))

@section('sidebar')
    @include('profile.sidebar')
@stop

@section('content')
    <div class="container golden-main px-0">
        <div class="cinematic-panel w-100 p-4 mb-4" style="background: rgba(10, 8, 5, 0.7); border: 1px solid rgba(201, 160, 91, 0.2); backdrop-filter: blur(15px); border-radius: 4px;">
            <h3 class="cinematic-heading text-center mb-4" style="color: #ebd197; text-transform: uppercase; text-shadow: 0 0 10px rgba(201,160,91,0.5);">{{ __('Top Up Account') }}</h3>
            <p class="text-center mb-5" style="color: #dfcdb8;">{{ __('Choose a reliable payment method below to purchase premium silkoins.') }}</p>

            @if ($errors->any())
                <div class="alert cinematic-alert alert-danger" style="background-color: rgba(139, 0, 0, 0.4); border: 1px solid #ff3333; color: #ffcccc;">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="alert cinematic-alert alert-success" style="background-color: rgba(0, 100, 0, 0.4); border: 1px solid #33ff33; color: #ccffcc;">
                    {{ session('success') }}
                </div>
            @endif

            <div class="row justify-content-center">
                <!-- Payment Methods -->
                <div class="col-lg-7 col-md-12 mb-4">
                    <h5 class="cinematic-subheading mb-3" style="color: #ebd197; border-bottom: 1px dashed rgba(201, 160, 91, 0.3); padding-bottom: 10px;">{{ __('1. Select Payment Method') }}</h5>
                    <div class="d-flex justify-content-start flex-wrap cinematic-payment-methods">
                        @foreach($data as $key => $row)
                            @if($row['enabled'])
                                <div class="card m-2 d-flex cinematic-payment-card {{ $key == 0 ? 'selected' : '' }}" role="button" data-method="{{ $key }}">
                                    <div class="d-flex align-items-center justify-content-center px-3 pt-3 pb-2" style="height: 60px;">
                                        <img src="{{ asset($row['image']) }}" class="card-img-top object-fit-contain" alt="{{ $row['name'] }}" style="max-height: 100%; max-width: 100%;">
                                    </div>
                                    <div class="card-body text-center p-2 py-2" style="border-top: 1px solid rgba(255, 255, 255, 0.05);">
                                        <strong style="color: #dfcdb8; font-size: 13px;">{{ $row['name'] }}</strong>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <h5 class="cinematic-subheading mt-5 mb-3" style="color: #ebd197; border-bottom: 1px dashed rgba(201, 160, 91, 0.3); padding-bottom: 10px;">{{ __('2. Select Package') }}</h5>
                    <div id="content-donate" class="cinematic-donate-packages mt-3">
                        @php $method = array_key_first(array_filter($data, fn($v) => $v['enabled'])); @endphp
                        @include('profile.donate.' . $method, ['data' => $data[$method]])
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="col-lg-5 col-md-12 mb-4">
                    <div class="cinematic-auth-box p-4" style="background: rgba(0, 0, 0, 0.4); border: 1px solid rgba(201, 160, 91, 0.4); border-radius: 4px; box-shadow: inset 0 0 20px rgba(0, 0, 0, 0.8);">
                        <h4 class="text-center mb-4" style="color: #ffe3a1; border-bottom: 1px solid rgba(201, 160, 91, 0.2); padding-bottom: 15px;">{{ __('Order Summary') }}</h4>
                        
                        <div id="content-donate-details">
                            <form action="{{ route('profile.donate.process', ['method' => $method]) }}" method="POST">
                                @csrf
                                <input type="hidden" name="price" value="0">
                                
                                <div class="d-flex justify-content-between mb-3 mt-4">
                                    <span style="color: #a8a095;">{{ __('Selected Package') }}:</span>
                                    <span class="package-name fw-bold" style="color: #dfcdb8; text-align: right;">{{ __('Select a package') }}</span>
                                </div>
                                
                                <div class="d-flex justify-content-between mb-4 pb-4" style="border-bottom: 1px dashed rgba(201, 160, 91, 0.3);">
                                    <span style="color: #a8a095;">{{ __('Payment Method') }}:</span>
                                    <span class="active-method-name fw-bold" style="color: #dfcdb8; text-align: right;">{{ $data[$method]['name'] ?? 'None' }}</span>
                                </div>
                                
                                <div class="d-flex justify-content-between mb-5 align-items-end">
                                    <h5 style="color: #a8a095; margin: 0;">{{ __('Total Amount') }}:</h5>
                                    <h3 class="package-price mb-0" style="color: #ebd197; text-shadow: 0 0 10px rgba(201,160,91,0.5);">0 USD</h3>
                                </div>

                                <button type="submit" class="btn cinematic-btn-primary w-100 py-3 text-uppercase fw-bold fs-5" disabled>
                                    <i class="fas fa-shopping-cart me-2"></i> {{ __('Complete Payment') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            // Helper function to extract numeric price and currency
            function updatePriceDisplay(element) {
                const name = element.data('name');
                const price = element.data('price');
                const currency = element.data('currency');
                
                $('#content-donate-details .package-name').text(name);
                $('#content-donate-details .package-price').text(`${price} ${currency}`);
            }

            $('[data-method]').on('click', function (e) {
                let method = $(this).data('method');
                let methodName = $(this).find('strong').text();
                
                if (location.protocol === 'https:' && method.startsWith('http:')) {
                    method = method.replace(/^http:/, 'https:');
                }

                $('[data-method]').removeClass('selected');
                $(this).addClass('selected');

                $('#content-donate-details form').attr('action', `/profile/donate/${method}/process`);
                $('.active-method-name').text(methodName);

                $('#content-donate').html(`
                <div style="text-align: center; padding: 40px;">
                    <i class="fas fa-spinner fa-spin fa-2x" style="color: #ebd197;"></i>
                </div>
                `);

                $.get(`/profile/donate/${method}`, function (res) {
                    $('#content-donate').html(res);

                    $('input[name=price]').val(0);
                    $('#content-donate-details button[type=submit]').prop('disabled', true);
                    $('#content-donate-details .package-name').text('Select a package');
                    $('#content-donate-details .package-price').text('0 USD');
                }).fail(function () {
                    $('#content-donate').html('<div class="alert alert-danger" style="background-color: rgba(139, 0, 0, 0.4); border: 1px solid #ff3333; color: #ffcccc;">Failed to load package options.</div>');
                });

                if (['maxicard', 'hipocard'].includes(method)) {
                    $('#content-donate-details button[type=submit]').prop('disabled', true).html('<i class="fas fa-times me-2"></i> Not Available');
                } else {
                    $('#content-donate-details button[type=submit]').prop('disabled', false).html('<i class="fas fa-shopping-cart me-2"></i> Buy Now');
                }
            });

            $(document).on('click', '#content-donate .card', function (e) {
                const method = $('[data-method].selected').data('method');
                const price = $(this).data('price');

                $('#content-donate .card').removeClass('selected');
                $(this).addClass('selected');

                $('input[name=price]').val(price);

                if (['maxicard', 'hipocard'].includes(method)) {
                    $('#content-donate-details button[type=submit]').prop('disabled', true).html('<i class="fas fa-times me-2"></i> Not Available');
                } else {
                    $('#content-donate-details button[type=submit]').prop('disabled', false).html('<i class="fas fa-shopping-cart me-2"></i> Complete Payment');
                }

                updatePriceDisplay($(this));
            });
        });
    </script>
@endpush
