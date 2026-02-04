@extends('layouts.admin')

@section('content')
    <h1>Admin Login</h1>
    <form method="POST" action="{{ route('admin.login.store') }}">
        @csrf
        <label for="email">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
        @error('email')
            <div class="error">{{ $message }}</div>
        @enderror

        <label for="password">Password</label>
        <input id="password" type="password" name="password" required>
        @error('password')
            <div class="error">{{ $message }}</div>
        @enderror

        <label>
            <input type="checkbox" name="remember"> Remember me
        </label>

        <button type="submit">Login</button>
    </form>
@endsection
