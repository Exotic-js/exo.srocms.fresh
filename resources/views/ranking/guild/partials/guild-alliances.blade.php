<div class="table-responsive">
    <table class="table table-striped">
        <thead class="table-dark">
            <tr class="text-center">
                <th scope="col">{{ __('Alliances') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data->alliance as $row)
                <tr class="text-center">
                    <td>
                        <a href="{{ route('ranking.guild.view', ['name' => $row->Name]) }}" class="text-decoration-none">{{ $row->Name }}</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="1" class="text-center">{{ __('No Records Found!') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
