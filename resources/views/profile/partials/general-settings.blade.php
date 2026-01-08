@if(Auth::user()->tbUser)
<div class="card">
    <div class="card-header">{{ __('General Settings') }}</div>

    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('profile.settings.update') }}">
            @csrf

            <div class="row mb-3">
                <label for="item_stats" class="col-md-4 col-form-label text-md-end">{{ __('Hide Item Stats') }}</label>

                <div class="col-md-6">
                    <div class="form-check">
                        <input type="hidden" name="item_stats_jid_{{ $user->tbUser->JID }}" value="0">
                        <input class="form-check-input" type="checkbox" name="item_stats_jid_{{ $user->tbUser->JID }}" value="1" id="item_stats_jid_{{ $user->tbUser->JID }}" {{ config("settings.item_stats_jid_{$user->tbUser->JID}") ? 'checked' : '' }}>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <label for="job_name" class="col-md-4 col-form-label text-md-end">{{ __('Hide Job Name') }}</label>

                <div class="col-md-6">
                    <div class="form-check">
                        <input type="hidden" name="job_name_jid_{{ $user->tbUser->JID }}" value="0">
                        <input class="form-check-input" type="checkbox" name="job_name_jid_{{ $user->tbUser->JID }}" value="1" id="job_name_jid_{{ $user->tbUser->JID }}" {{ config("settings.job_name_jid_{$user->tbUser->JID}") ? 'checked' : '' }}>
                    </div>
                </div>
            </div>

            <div class="row mb-0">
                <div class="col-md-6 offset-md-4">
                    <button type="submit" class="btn btn-primary">
                        {{ __('Save') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif
