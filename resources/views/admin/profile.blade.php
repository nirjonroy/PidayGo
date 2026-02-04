@extends('layouts.admin-panel')

@section('content')
    @section('page-title', 'Profile')

    <div class="card">
        <div class="card-body">
            <div class="mb-4">
                <div><strong>Name:</strong> {{ $admin->name }}</div>
                <div><strong>Email:</strong> {{ $admin->email }}</div>
            </div>

            <form method="POST" action="{{ route('admin.profile.password') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label" for="current_password">Current Password</label>
                    <input id="current_password" name="current_password" type="password" class="form-control" required>
                    @error('current_password') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="password">New Password</label>
                    <input id="password" name="password" type="password" class="form-control" required>
                    @error('password') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="password_confirmation">Confirm Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-success">Update Password</button>
            </form>
        </div>
    </div>
@endsection
