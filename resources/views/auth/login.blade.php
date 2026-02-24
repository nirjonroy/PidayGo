@extends('layouts.frontend')

@section('content')
    @include('frontend.partials.page-banner', [
        'title' => 'User Login',
        'subtitle' => 'Welcome back. Sign in to continue.',
    ])

    <section aria-label="section">
        <div class="container">
            <div class="row">
                <div class="col-md-6 offset-md-3">
                    <form id="login_form" class="form-border" method="POST" action="{{ route('login.store') }}">
                        @csrf
                        <h3>Login to your account</h3>

                        <div class="field-set">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
                            @error('email')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-set">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                            @error('password')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-set">
                            <label class="form-check-label">
                                <input type="checkbox" name="remember"> Remember me
                            </label>
                        </div>

                        <div id="submit">
                            <button type="submit" class="btn btn-main color-2">Login</button>
                            <div class="spacer-single"></div>
                            <a href="{{ route('password.request') }}">Forgot your password?</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
