@extends('layouts.app')
@section('title', __('Referral'))

@section('sidebar')
    @include('profile.sidebar')
@stop

@section('content')
    <div class="container">
        <div class="card border-0">
            <div class="card-body">
                <a href="{{ route('profile.tickets') }}" class="btn btn-secondary mb-3">Back to Tickets</a>

                <h4>Ticket #{{ $ticket_id }}</h4>

                @foreach($messages as $msg)
                    <div class="card mb-2 @if($msg->type=='player') text-start @else text-end @endif">
                        <div class="card-body">
                            <strong>{{ $msg->type=='player' ? 'You' : 'Admin' }}:</strong>
                            <p>{!! $msg->message !!}</p>
                            <small class="text-muted">{{ $msg->created_at->format('Y-m-d H:i') }}</small>
                        </div>
                    </div>
                @endforeach

                @if($messages->first()->status)
                    <form action="{{ route('profile.ticket.send') }}" method="POST" class="mt-3">
                        @csrf
                        <input type="hidden" name="ticket_id" value="{{ $ticket_id }}">
                        <input type="hidden" name="category" value="{{ $messages->first()->category }}">
                        <div class="mb-3">
                            <textarea name="message" id="summernote" class="form-control" placeholder="Write your reply..." rows="3" required></textarea>
                        </div>
                        <button class="btn btn-primary">Send Reply</button>
                    </form>
                @else
                    <div class="alert alert-warning mt-3">This ticket is closed.</div>
                @endif
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
