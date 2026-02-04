@extends('layouts.app')

@section('content')
    <h1>Set Up Two-Factor Authentication</h1>
    <p class="muted">Scan the QR code with Google Authenticator or enter the secret manually.</p>

    <div>
        <img alt="QR Code" src="{!! $qrInline !!}">
    </div>

    <p><strong>Secret:</strong> {{ $secret }}</p>

    <form method="POST" action="{{ route('two-factor.store') }}">
        @csrf
        <label for="one_time_password">Authentication Code</label>
        <input id="one_time_password" type="text" name="one_time_password" required autofocus>
        @error('one_time_password')
            <div class="error">{{ $message }}</div>
        @enderror
        <button type="submit">Confirm 2FA</button>
    </form>
@endsection
