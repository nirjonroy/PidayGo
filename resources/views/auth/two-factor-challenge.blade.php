@extends('layouts.frontend')

@section('content')
    @include('frontend.partials.page-banner', ['title' => 'Two-Factor Challenge', 'subtitle' => 'Enter the code from your authenticator app.'])

    <section aria-label="section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="nft__item s2">
                        <div class="nft__item_info">
                            <h4 class="mb-3">Authentication Code</h4>
                            <form method="POST" action="{{ route('two-factor.verify') }}" class="form-border">
                                @csrf
                                <div class="mb-3">
                                    <label for="one_time_password" class="form-label">Code</label>
                                    <input id="one_time_password" type="text" name="one_time_password" class="form-control" required autofocus>
                                    @error('one_time_password')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn-main">Verify</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
