@extends('layouts.admin-panel')

@section('content')
    @section('page-title', 'Chain Bonus Settings')

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="mb-3">
        <a class="btn btn-primary" href="{{ route('admin.chain-bonuses.create') }}">Add Chain Bonus</a>
    </div>

    <div class="card">
        <div class="card-body">
            @if ($bonuses->isEmpty())
                <p class="text-muted">No chain bonus settings configured.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Chain</th>
                                <th>Depth</th>
                                <th>Percent</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bonuses as $bonus)
                                @php
                                    $label = match((int) $bonus->depth) {
                                        1 => 'A',
                                        2 => 'B',
                                        3 => 'C',
                                        default => 'Level ' . $bonus->depth,
                                    };
                                @endphp
                                <tr>
                                    <td>{{ $label }}</td>
                                    <td>{{ $bonus->depth }}</td>
                                    <td>{{ $bonus->percent }}%</td>
                                    <td>
                                        @if ($bonus->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.chain-bonuses.edit', $bonus) }}">Edit</a>
                                        <form method="POST" action="{{ route('admin.chain-bonuses.toggle', $bonus) }}" style="display:inline;">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-secondary" type="submit">
                                                {{ $bonus->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.chain-bonuses.delete', $bonus) }}" style="display:inline;">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-danger" type="submit" onclick="return confirm('Delete this chain bonus?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
