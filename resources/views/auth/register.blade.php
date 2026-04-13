@extends('layouts.frontend')

@section('content')
    @include('frontend.partials.page-banner', [
        'title' => 'Create Account',
        'subtitle' => 'Join PidayGo to start collecting PI.',
    ])

    <section aria-label="section">
        <div class="container">
            <div class="row">
                <div class="col-md-6 offset-md-3">
                    <form id="register_form" class="form-border" method="POST" action="{{ route('register.store') }}">
                        @csrf
                        <h3>Create your account</h3>

                        <div class="field-set">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-set">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-set">
                            <label>Referral Code</label>
                            <input type="text" name="ref_code" class="form-control" value="{{ old('ref_code', $ref ?? '') }}" placeholder="Enter referral code" required>
                            @error('ref_code')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-set">
                            <label>Password</label>
                            <div class="password-input">
                                <input type="password" name="password" class="form-control" minlength="8" required>
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
                            <label>Confirm Password</label>
                            <div class="password-input">
                                <input type="password" name="password_confirmation" class="form-control" minlength="8" required>
                                <button type="button" class="password-toggle" data-password-toggle aria-label="Show password">
                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>

                        <div id="submit">
                            <button type="submit" class="btn btn-main color-2">Register</button>
                            <div class="spacer-single"></div>
                            <a href="{{ route('login') }}">Already have an account? Login</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

