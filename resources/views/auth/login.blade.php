@extends('layouts.app')

@section('content')
    <h1>Login</h1>
    <form method="POST" action="{{ route('login.store') }}">
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

    <p class="muted">
        <a href="{{ route('password.request') }}">Forgot your password?</a>
    </p>
@endsection
