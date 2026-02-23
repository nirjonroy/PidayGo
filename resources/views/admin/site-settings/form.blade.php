@extends('layouts.admin-panel')

@section('content')
    @section('page-title', $setting->exists ? 'Edit Site Settings' : 'Create Site Settings')

    <div class="card">
        <div class="card-body">
            <form
                method="POST"
                action="{{ $setting->exists ? route('admin.site-settings.update') : route('admin.site-settings.store') }}"
                enctype="multipart/form-data"
            >
                @csrf

                <div class="mb-3">
                    <label class="form-label" for="site_name">Site Name</label>
                    <input id="site_name" name="site_name" class="form-control" value="{{ old('site_name', $setting->site_name) }}" required>
                    @error('site_name') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="logo">Logo</label>
                    <input id="logo" name="logo" type="file" class="form-control">
                    @error('logo') <div class="text-danger">{{ $message }}</div> @enderror
                    @if ($setting->logo_path)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $setting->logo_path) }}" alt="Logo" style="height:60px;">
                        </div>
                    @endif
                </div>

                <div class="mb-3">
                    <label class="form-label" for="mobile">Mobile</label>
                    <input id="mobile" name="mobile" class="form-control" value="{{ old('mobile', $setting->mobile) }}">
                    @error('mobile') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="email">Email</label>
                    <input id="email" name="email" type="email" class="form-control" value="{{ old('email', $setting->email) }}">
                    @error('email') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="address">Address</label>
                    <input id="address" name="address" class="form-control" value="{{ old('address', $setting->address) }}">
                    @error('address') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="description">Description</label>
                    <textarea id="description" name="description" class="form-control" rows="4">{{ old('description', $setting->description) }}</textarea>
                    @error('description') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="usdt_trc20_address">USDT TRC20 Address</label>
                    <input id="usdt_trc20_address" name="usdt_trc20_address" class="form-control" value="{{ old('usdt_trc20_address', $setting->usdt_trc20_address) }}">
                    @error('usdt_trc20_address') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="min_deposit_usdt">Minimum Deposit (USDT)</label>
                    <input id="min_deposit_usdt" name="min_deposit_usdt" type="number" step="0.0001" class="form-control" value="{{ old('min_deposit_usdt', $setting->min_deposit_usdt ?? 50) }}" required>
                    @error('min_deposit_usdt') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="deposit_review_hours">Deposit Review Hours</label>
                    <input id="deposit_review_hours" name="deposit_review_hours" type="number" min="1" max="168" class="form-control" value="{{ old('deposit_review_hours', $setting->deposit_review_hours ?? 24) }}" required>
                    @error('deposit_review_hours') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-success">Save</button>
                <a href="{{ route('admin.site-settings.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection
