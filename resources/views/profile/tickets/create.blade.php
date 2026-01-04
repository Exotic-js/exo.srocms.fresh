@extends('layouts.app')
@section('title', __('New Ticket'))

@section('sidebar')
    @include('profile.sidebar')
@stop

@section('content')
    <div class="container">
        <div class="card border-0">
            <div class="card-body">
                <h3>New Ticket</h3>

                <form action="{{ route('profile.ticket.send') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select name="category" id="category" class="form-control" required>
                            @foreach($categories as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea name="message" id="summernote" class="form-control" rows="4" required></textarea>
                    </div>

                    <button class="btn btn-primary">Create Ticket</button>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('styles')

@endpush
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

    <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-bs5.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-bs5.min.js"></script>

    <script>
        $('#summernote').summernote({
            placeholder: 'Hello iSRO-CMS v2',
            tabsize: 2,
            height: 200,
            codeviewFilter: false, // allows raw HTML
            codeviewIframeFilter: true
        });
    </script>
@endpush
