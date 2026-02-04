@extends('layouts.app')

@section('content')
    <h1>Two-Factor Challenge</h1>
    <p class="muted">Enter the code from your authenticator app.</p>

    <form method="POST" action="{{ route('two-factor.verify') }}">
        @csrf
        <label for="one_time_password">Authentication Code</label>
        <input id="one_time_password" type="text" name="one_time_password" required autofocus>
        @error('one_time_password')
            <div class="error">{{ $message }}</div>
        @enderror
        <button type="submit">Verify</button>
    </form>
@endsection
