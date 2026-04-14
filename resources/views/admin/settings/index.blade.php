@extends('admin.layouts.app')
@section('title', __('Settings'))

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Settings</h1>

            <div class="btn-toolbar mb-2 mb-md-0">
                <div class="btn-group me-2">
                    <form action="{{ route('admin.settings.clear-cache') }}" method="POST" onsubmit="return confirm('Are you sure you want to clear all caches?')">
                        @csrf
                        <button type="submit" class="btn btn-danger">
                            Clear All Cache
                        </button>
                    </form>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

    </div>
@endsection
