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

                <button type="submit" class="btn btn-success">Save</button>
                <a href="{{ route('admin.site-settings.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection
