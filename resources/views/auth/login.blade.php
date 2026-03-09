@extends('layouts.frontend')

@section('content')
    <style>
        .remember-option {
            margin-top: 4px;
            margin-bottom: 12px;
        }
        .remember-option input[type="checkbox"] {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }
        .remember-option label {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            user-select: none;
            color: #4b5563;
            font-weight: 600;
        }
        .remember-option__box {
            width: 20px;
            height: 20px;
            border-radius: 6px;
            border: 1px solid rgba(17, 24, 39, 0.2);
            background: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            flex: 0 0 20px;
        }
        .remember-option__box i {
            opacity: 0;
            transform: scale(0.8);
            color: #ffffff;
            font-size: 11px;
            transition: all 0.2s ease;
        }
        .remember-option input[type="checkbox"]:checked + label .remember-option__box {
            background: linear-gradient(90deg, #f2b54b, #7b2cbf);
            border-color: transparent;
            box-shadow: 0 8px 18px rgba(123, 44, 191, 0.18);
        }
        .remember-option input[type="checkbox"]:checked + label .remember-option__box i {
            opacity: 1;
            transform: scale(1);
        }
        .remember-option input[type="checkbox"]:focus + label .remember-option__box {
            box-shadow: 0 0 0 3px rgba(123, 44, 191, 0.18);
        }
        .dark-scheme .remember-option label {
            color: rgba(242, 245, 249, 0.88);
        }
        .dark-scheme .remember-option__box {
            background: rgba(255, 255, 255, 0.06);
            border-color: rgba(255, 255, 255, 0.18);
        }
    </style>

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
                            <div class="password-input">
                                <input type="password" name="password" class="form-control" required>
                                <button type="button" class="password-toggle" data-password-toggle aria-label="Show password">
                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-set remember-option">
                            <input type="checkbox" id="remember" name="remember" value="1" @checked(old('remember'))>
                            <label for="remember">
                                <span class="remember-option__box" aria-hidden="true">
                                    <i class="fa fa-check"></i>
                                </span>
                                <span>Remember me on this device</span>
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
