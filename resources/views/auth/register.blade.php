@extends('layouts.frontend')

@section('content')
    <section id="subheader" class="text-light" data-bgimage="url({{ asset('frontend/images/background/subheader.jpg') }}) top">
        <div class="center-y relative text-center">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <h1>Create Account</h1>
                        <p>Join PidayGo to start collecting NFTs.</p>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </section>

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
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                            @error('password')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-set">
                            <label>Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
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
