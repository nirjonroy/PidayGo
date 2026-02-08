@extends('layouts.admin-panel')

@section('content')
    @section('page-title', $admin->exists ? 'Edit Admin' : 'Create Admin')

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ $admin->exists ? route('admin.admins.update', $admin) : route('admin.admins.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label" for="name">Name</label>
                    <input id="name" name="name" class="form-control" value="{{ old('name', $admin->name) }}" required>
                    @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="email">Email</label>
                    <input id="email" name="email" type="email" class="form-control" value="{{ old('email', $admin->email) }}" required>
                    @error('email') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="password">Password {{ $admin->exists ? '(leave blank to keep current)' : '' }}</label>
                    <input id="password" name="password" type="password" class="form-control" {{ $admin->exists ? '' : 'required' }}>
                    @error('password') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="password_confirmation">Confirm Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" {{ $admin->exists ? '' : 'required' }}>
                </div>

                <div class="mb-3">
                    <label class="form-label">Roles</label>
                    <div class="row">
                        @foreach ($roles as $role)
                            <div class="col-md-4">
                                <label class="form-check">
                                    <input
                                        type="checkbox"
                                        class="form-check-input"
                                        name="roles[]"
                                        value="{{ $role->name }}"
                                        {{ in_array($role->name, old('roles', $admin->roles->pluck('name')->toArray())) ? 'checked' : '' }}
                                    >
                                    <span class="form-check-label">{{ $role->name }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <button type="submit" class="btn btn-success">Save</button>
                <a href="{{ route('admin.admins.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection
