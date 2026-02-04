@extends('layouts.admin-panel')

@section('content')
    @section('page-title', $permission->exists ? 'Edit Permission' : 'Create Permission')

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ $permission->exists ? route('admin.permissions.update', $permission) : route('admin.permissions.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label" for="name">Permission Name</label>
                    <input id="name" name="name" class="form-control" value="{{ old('name', $permission->name) }}" required>
                    @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-success">Save</button>
                <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection
