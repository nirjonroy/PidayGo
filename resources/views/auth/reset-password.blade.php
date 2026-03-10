@extends('layouts.frontend')

@section('content')
    @include('frontend.partials.page-banner', [
        'title' => 'Reset Password',
        'subtitle' => 'Choose a new password with at least 8 characters.',
    ])

    <section aria-label="section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-7">
                    <form method="POST" action="{{ route('password.update') }}" class="form-border">
                        @csrf
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

                        <h3>Create a new password</h3>
                        <p class="text-muted">Your reset link is valid for a limited time.</p>

                        <div class="field-set">
                            <label for="email">Email Address</label>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                class="form-control"
                                value="{{ old('email', $request->email) }}"
                                placeholder="Enter your email"
                                required
                                autofocus
                            >
                            @error('email')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-set">
                            <label for="password">New Password</label>
                            <div class="password-input">
                                <input id="password" type="password" name="password" class="form-control" minlength="8" required>
                                <button type="button" class="password-toggle" data-password-toggle aria-label="Show password">
                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                </button>
                            </div>
                            <small class="text-muted d-block mt-1">Use at least 8 characters.</small>
                            @error('password')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-set">
                            <label for="password_confirmation">Confirm Password</label>
                            <div class="password-input">
                                <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" minlength="8" required>
                                <button type="button" class="password-toggle" data-password-toggle aria-label="Show password">
                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>

                        <div id="submit">
                            <button type="submit" class="btn btn-main color-2">Reset Password</button>
                            <div class="spacer-single"></div>
                            <a href="{{ route('login') }}">Back to login</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
