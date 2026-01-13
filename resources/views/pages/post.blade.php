@extends('layouts.full')
@section('title', $data->title)

@section('content')
    <div class="container">
        <div class="card border-0">
            <div class="card-body">
                {!! $data->content !!}
            </div>
        </div>
    </div>
@endsection
