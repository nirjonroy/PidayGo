@extends('layouts.admin-panel')

@section('content')
    @section('page-title', $role->exists ? 'Edit Role' : 'Create Role')

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ $role->exists ? route('admin.roles.update', $role) : route('admin.roles.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label" for="name">Role Name</label>
                    <input id="name" name="name" class="form-control" value="{{ old('name', $role->name) }}" required>
                    @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Permissions</label>
                    <div class="row">
                        @foreach ($permissions as $permission)
                            <div class="col-md-4">
                                <label class="form-check">
                                    <input
                                        type="checkbox"
                                        class="form-check-input"
                                        name="permissions[]"
                                        value="{{ $permission->name }}"
                                        {{ in_array($permission->name, old('permissions', $role->permissions->pluck('name')->toArray())) ? 'checked' : '' }}
                                    >
                                    <span class="form-check-label">{{ $permission->name }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <button type="submit" class="btn btn-success">Save</button>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection
