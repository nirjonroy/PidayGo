@extends('layouts.frontend')

@section('content')
    @include('frontend.partials.page-banner', [
        'title' => 'Forgot Password',
        'subtitle' => 'Enter your account email and we will send you a reset link.',
    ])

    <section aria-label="section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-7">
                    <form method="POST" action="{{ route('password.email') }}" class="form-border">
                        @csrf

                        <h3>Reset your password</h3>
                        <p class="text-muted">Use the email address linked to your account.</p>

                        @if (session('status'))
                            <div class="alert alert-success">{{ session('status') }}</div>
                        @endif

                        <div class="field-set">
                            <label for="email">Email Address</label>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                class="form-control"
                                value="{{ old('email') }}"
                                placeholder="Enter your email"
                                required
                                autofocus
                            >
                            @error('email')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="submit">
                            <button type="submit" class="btn btn-main color-2">Send Reset Link</button>
                            <div class="spacer-single"></div>
                            <a href="{{ route('login') }}">Back to login</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
