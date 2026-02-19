<form method="POST" action="{{ route('admin.settings.update') }}">
    @csrf

    <div class="mb-3">
        <label class="form-check">
            <input class="form-check-input" type="checkbox" id="server_info_enabled" {{ json_decode($data['server_info'] ?? '{"enabled":false,"data":[]}', true)['enabled'] ?? false ? 'checked' : '' }}>
            <span class="form-check-label">{{ __('Enable Server Info') }}</span>
        </label>
    </div>

    <div class="table-responsive_">
        <table class="table table-striped" id="serverInfoTable">
            <thead>
                <tr>
                    <th scope="col">{{ __('Icon') }}</th>
                    <th scope="col">{{ __('Name') }}</th>
                    <th scope="col">{{ __('Value') }}</th>
                    <th scope="col">{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse(json_decode($data['server_info'] ?? '{"enabled":false,"data":[]}', true)['data'] ?? [] as $index => $row)
                    <tr>
                        <td><input type="text" class="form-control" value="{{ $row['icon'] ?? '' }}" data-key="icon"></td>
                        <td><input type="text" class="form-control" value="{{ $row['name'] ?? '' }}" data-key="name"></td>
                        <td><input type="text" class="form-control" value="{{ $row['value'] ?? '' }}" data-key="value"></td>
                        <td><button type="button" class="btn btn-danger" onclick="removeRow(this)">{{ __('Remove') }}</button></td>
                    </tr>
                @empty
                    <tr>
                        <td><input type="text" class="form-control" data-key="icon"></td>
                        <td><input type="text" class="form-control" data-key="name"></td>
                        <td><input type="text" class="form-control" data-key="value"></td>
                        <td><button type="button" class="btn btn-danger" onclick="removeRow(this)">{{ __('Remove') }}</button></td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <button type="button" class="btn btn-secondary" onclick="addRow()">{{ __('+ Add Row') }}</button>
    <button type="submit" class="btn btn-primary" onclick="serializeServerInfo()">
        {{ __('Save Changes') }}
    </button>

    <input type="hidden" id="server_info" name="server_info">
</form>

<script>
function addRow() {
    const tbody = document.getElementById('serverInfoTable').querySelector('tbody');
    const row = tbody.insertRow();
    row.innerHTML = `
        <td><input type="text" class="form-control" data-key="icon"></td>
        <td><input type="text" class="form-control" data-key="name"></td>
        <td><input type="text" class="form-control" data-key="value"></td>
        <td><button type="button" class="btn btn-danger" onclick="removeRow(this)">{{ __('Remove') }}</button></td>
    `;
}

function removeRow(btn) {
    btn.closest('tr').remove();
}

function serializeServerInfo() {
    const rows = Array.from(document.querySelectorAll('#serverInfoTable tbody tr')).map(tr => ({
        icon: tr.querySelector('[data-key="icon"]').value,
        name: tr.querySelector('[data-key="name"]').value,
        value: tr.querySelector('[data-key="value"]').value
    })).filter(row => row.icon || row.name || row.value);
    
    document.getElementById('server_info').value = JSON.stringify({
        enabled: document.getElementById('server_info_enabled').checked,
        data: rows
    });
}
</script>
