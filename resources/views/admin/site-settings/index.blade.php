@extends('layouts.admin-panel')

@section('content')
    @section('page-title', 'Site Settings')

    @if (!$setting)
        <div class="alert alert-warning">No settings found. Create the first site settings record.</div>
        <a href="{{ route('admin.site-settings.create') }}" class="btn btn-primary">Create Settings</a>
    @else
        <div class="card">
            <div class="card-body">
                <div class="mb-3">
                    <strong>Site Name:</strong> {{ $setting->site_name }}
                </div>
                <div class="mb-3">
                    <strong>Logo:</strong>
                    @if ($setting->logo_path)
                        <div>
                            <img src="{{ asset('storage/' . $setting->logo_path) }}" alt="Logo" style="height:60px;">
                        </div>
                    @else
                        <span class="text-secondary">Not set</span>
                    @endif
                </div>
                <div class="mb-3"><strong>Mobile:</strong> {{ $setting->mobile ?? '-' }}</div>
                <div class="mb-3"><strong>Email:</strong> {{ $setting->email ?? '-' }}</div>
                <div class="mb-3"><strong>Address:</strong> {{ $setting->address ?? '-' }}</div>
                <div class="mb-3"><strong>Description:</strong> {{ $setting->description ?? '-' }}</div>

                <a href="{{ route('admin.site-settings.edit') }}" class="btn btn-primary">Edit Settings</a>
            </div>
        </div>
    @endif
@endsection
