@extends('layouts.app')
@section('title', __('Donate'))

@section('sidebar')
    @include('profile.sidebar')
@stop

@section('content')
    <div class="container">
        <div class="card border-0">
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="row justify-content-center">
                    <div class="col-12 mb-4 text-center">
                        <p>Select Payment Method</p>
                        <div class="d-flex justify-content-center flex-wrap">
                            @foreach($data as $key => $row)
                                @if($row['enabled'])
                                    <div class="card m-2 d-flex {{ $key == 0 ? 'selected' : '' }}" role="button" data-method="{{ $key }}" style="width: 120px;">
                                        <img src="{{ asset($row['image']) }}" class="card-img-top object-fit-contain p-2" height="50" alt="{{ $row['name'] }}">
                                        <div class="card-body text-center p-2">
                                            <strong>{{ $row['name'] }}</strong>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <div class="col-12 mb-4">
                        <p>Select Package</p>
                        <div id="content-donate">
                            @php $method = array_key_first(array_filter($data, fn($v) => $v['enabled'])); @endphp
                            @include('profile.donate.' . $method, ['data' => $data[$method]])
                        </div>
                    </div>
                    <div class="col-12 mb-4">
                        <p>Order Details</p>
                        <div id="content-donate-details">
                            <form action="{{ route('profile.donate.process', ['method' => $method]) }}" method="POST">
                                @csrf
                                <input type="hidden" name="price" value="0">
                                <hr>
                                <p class="package-name text-muted mb-0 mt-2">Select a package</p>
                                <p class="package-price mb-0">Total amount: 0 USD</p>
                                <hr>
                                <button type="submit" class="btn w-100 btn-primary" disabled>{{ __('Buy Now') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('styles')
    <style>
        .card[data-method].selected, .card[data-price].selected {
            border: 1px solid #0d6efd;
            box-shadow: 0 0 10px rgba(13, 110, 253, 0.4);
        }
        .card[data-method]:hover, #content-donate .card:hover {
            border: 1px solid #0d6efd;
            box-shadow: 0 0 8px rgba(13, 110, 253, 0.3);
            cursor: pointer;
        }
    </style>
@endpush
@push('scripts')
    <script>
        $(document).ready(function () {
            $('[data-method]').on('click', function (e) {
                //e.preventDefault();
                let method = $(this).data('method');
                if (location.protocol === 'https:' && method.startsWith('http:')) {
                    method = method.replace(/^http:/, 'https:');
                }

                $('[data-method]').removeClass('selected');
                $(this).addClass('selected');

                $('#content-donate-details form').attr('action', `/profile/donate/${method}/process`);

                $('#content-donate').html(`
                <div style="text-align: center; padding: 20px;">
                    <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                </div>
                `);

                $.get(`/profile/donate/${method}`, function (res) {
                    $('#content-donate').html(res);

                    $('input[name=price]').val(0);
                    $('#content-donate-details button[type=submit]').prop('disabled', true);
                    $('#content-donate-details .package-name').text('Select a package');
                    $('#content-donate-details .package-price').text('Total amount: 0 USD');
                }).fail(function () {
                    $('#content-donate').html('<div class="alert alert-danger">Failed to load package options.</div>');
                });

                if (['maxicard', 'hipocard'].includes(method)) {
                    $('#content-donate-details button[type=submit]').prop('disabled', true).text('Not Available');
                } else {
                    $('#content-donate-details button[type=submit]').prop('disabled', false).text('Buy Now');
                }
            });

            $(document).on('click', '#content-donate .card', function (e) {
                //e.preventDefault();

                const method = $('[data-method].selected').data('method');
                const price = $(this).data('price');
                const name = $(this).data('name');
                const currency = $(this).data('currency');

                $('#content-donate .card').removeClass('selected');
                $(this).addClass('selected');

                $('input[name=price]').val(price);

                if (['maxicard', 'hipocard'].includes(method)) {
                    $('#content-donate-details button[type=submit]').prop('disabled', true).text('Not Available');
                } else {
                    $('#content-donate-details button[type=submit]').prop('disabled', false);
                }

                $('#content-donate-details .package-name').text(`Package: ${name}`);
                $('#content-donate-details .package-price').text(`Total amount: ${price} ${currency}`);
            });
        });
    </script>
@endpush
