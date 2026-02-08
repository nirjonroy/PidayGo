@extends('layouts.admin-panel')

@section('content')
    @section('page-title', 'Admins')

    <a href="{{ route('admin.admins.create') }}" class="btn btn-primary mb-3">Create Admin</a>

    <div class="card">
        <div class="card-body">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Roles</th>
                        <th>Created</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($admins as $admin)
                        <tr>
                            <td>{{ $admin->name }}</td>
                            <td>{{ $admin->email }}</td>
                            <td>{{ $admin->roles->pluck('name')->join(', ') ?: '-' }}</td>
                            <td>{{ $admin->created_at?->format('Y-m-d') }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.admins.edit', $admin) }}" class="btn btn-sm btn-secondary">Edit</a>
                                <form method="POST" action="{{ route('admin.admins.delete', $admin) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this admin?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $admins->links() }}
        </div>
    </div>
@endsection
