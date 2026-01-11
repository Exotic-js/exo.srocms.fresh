@extends('admin.layouts.app')
@section('title', __('Show Ticket'))

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Tickets</h1>

            <div class="btn-toolbar mb-2 mb-md-0">
                <div class="btn-group me-2">
                    <a href="{{ route('admin.tickets.index') }}" class="btn btn-sm btn-outline-secondary">Back to Tickets</a>
                </div>
            </div>
        </div>

        <h4>Ticket #{{ $ticket->id }} - {{ $ticket->subject }}</h4>

        @foreach($data as $row)
            <div class="card mb-2 {{ $row->type == 'player' ? 'text-end' : 'text-start' }}">
                <div class="card-body">
                    <strong>{{ $row->type == 'player' ? $row->user->username : 'Admin' }}:</strong>
                    <p>{!! $row->message !!}</p>
                    <small class="text-muted">{{ $row->created_at->format('Y-m-d H:i') }}</small>
                </div>
            </div>
        @endforeach

        @if($ticket->status)
            <form action="{{ route('admin.ticket.reply', $ticket) }}" method="POST" class="mt-3">
                @csrf
                <div class="mb-3">
                    <textarea name="message" id="summernote" class="form-control" placeholder="Write your reply..." rows="3" required></textarea>
                </div>
                <button class="btn btn-primary">Send Reply</button>
            </form>
        @else
            <div class="alert alert-warning mt-3">This ticket is closed.</div>
        @endif
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
