@extends('layouts.app')

@section('content')
    <h1>Verify Your Email</h1>
    <p class="muted">Please verify your email address to continue. We have sent a verification link to your inbox.</p>

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit">Resend verification email</button>
    </form>

    <p class="muted">
        Once verified, continue to <a href="{{ route('two-factor.setup') }}">set up 2FA</a>.
    </p>
@endsection
