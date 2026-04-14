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

        <ul class="nav nav-tabs mb-3" id="settingsTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="ranking-tab" data-bs-toggle="tab" data-bs-target="#ranking" type="button" role="tab" aria-controls="ranking" aria-selected="true">
                    {{ __('Ranking') }}
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="character-tab" data-bs-toggle="tab" data-bs-target="#character" type="button" role="tab" aria-controls="character" aria-selected="false">
                    {{ __('Character') }}
                </button>
            </li>
        </ul>

        <div class="tab-content" id="settingsTabsContent">
            <div class="tab-pane fade show active" id="ranking" role="tabpanel" aria-labelledby="ranking-tab">

            </div>

            <div class="tab-pane fade" id="character" role="tabpanel" aria-labelledby="character-tab">

            </div>
        </div>
    </div>
@endsection
