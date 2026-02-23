@extends('layouts.admin-panel')

@section('content')
    @section('page-title', 'Levels')

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="mb-3">
        <a class="btn btn-primary" href="{{ route('admin.levels.create') }}">Create Level</a>
    </div>

    <div class="card">
        <div class="card-body">
            @if ($levels->isEmpty())
                <p class="text-muted">No levels configured.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Reservation Range</th>
                                <th>Income % Range</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($levels as $level)
                                <tr>
                                    <td>{{ $level->code }}</td>
                                    <td>{{ $level->min_reservation }} - {{ $level->max_reservation }}</td>
                                    <td>{{ $level->income_min_percent }}% - {{ $level->income_max_percent }}%</td>
                                    <td>
                                        @if ($level->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.levels.edit', $level) }}">Edit</a>
                                        <form method="POST" action="{{ route('admin.levels.toggle', $level) }}" style="display:inline;">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-secondary" type="submit">
                                                {{ $level->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
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
