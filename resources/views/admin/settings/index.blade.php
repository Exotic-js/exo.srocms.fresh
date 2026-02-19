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
                <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab" aria-controls="general" aria-selected="true">
                    {{ __('General') }}
                </button>
            </li>
            <!--
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="server-info-tab" data-bs-toggle="tab" data-bs-target="#server-info" type="button" role="tab" aria-controls="server-info" aria-selected="false">
                    {{ __('Server Info') }}
                </button>
            </li>
            -->
        </ul>

        <div class="tab-content" id="settingsTabsContent">
            <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                @include('admin.settings.general', ['data' => $data])
            </div>

            <div class="tab-pane fade" id="server-info" role="tabpanel" aria-labelledby="server-info-tab">
                @include('admin.settings.server-info', ['data' => $data])
            </div>
        </div>
    </div>
@endsection
