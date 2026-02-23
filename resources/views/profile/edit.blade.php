@extends('layouts.app')

@section('content')
    <h1>Profile</h1>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label class="form-label">Profile Photo</label>
            <div>
                @if ($profile->photo_path)
                    <img src="{{ asset('storage/' . $profile->photo_path) }}" alt="Profile Photo" style="width:120px;height:120px;object-fit:cover;border-radius:8px;">
                @else
                    <div class="text-muted">No photo uploaded.</div>
                @endif
            </div>
            <input type="file" name="photo" class="form-control mt-2">
            @error('photo')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="row">
            <div class="col-md-6 mb-2">
                <label class="form-label">Username</label>
                <input name="username" class="form-control" value="{{ old('username', $profile->username) }}">
                @error('username')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-2">
                <label class="form-label">Phone</label>
                <input name="phone" class="form-control" value="{{ old('phone', $profile->phone) }}">
                @error('phone')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-2">
                <label class="form-label">Country</label>
                <input name="country" class="form-control" value="{{ old('country', $profile->country) }}">
            </div>
            <div class="col-md-6 mb-2">
                <label class="form-label">City</label>
                <input name="city" class="form-control" value="{{ old('city', $profile->city) }}">
            </div>
        </div>

        <div class="mb-2">
            <label class="form-label">Address</label>
            <textarea name="address" class="form-control" rows="2">{{ old('address', $profile->address) }}</textarea>
        </div>

        <div class="mb-2">
            <label class="form-label">Date of Birth</label>
            <input type="date" name="dob" class="form-control" value="{{ old('dob', optional($profile->dob)->format('Y-m-d')) }}">
        </div>

        <div class="mb-2">
            <label class="form-label">Bio</label>
            <textarea name="bio" class="form-control" rows="3">{{ old('bio', $profile->bio) }}</textarea>
        </div>

        <div class="row">
            <div class="col-md-4 mb-2">
                <label class="form-label">Twitter</label>
                <input name="social_twitter" class="form-control" value="{{ old('social_twitter', $profile->social_twitter) }}">
            </div>
            <div class="col-md-4 mb-2">
                <label class="form-label">Telegram</label>
                <input name="social_telegram" class="form-control" value="{{ old('social_telegram', $profile->social_telegram) }}">
            </div>
            <div class="col-md-4 mb-2">
                <label class="form-label">Discord</label>
                <input name="social_discord" class="form-control" value="{{ old('social_discord', $profile->social_discord) }}">
            </div>
        </div>

        <button class="btn btn-primary" type="submit">Save Profile</button>
        <a class="btn btn-outline-secondary" href="{{ route('profile.bank.index') }}">Manage Bank Accounts</a>
    </form>
@endsection
