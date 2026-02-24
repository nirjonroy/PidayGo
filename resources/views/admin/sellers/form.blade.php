@extends('layouts.admin-panel')

@section('page-title', $seller->exists ? 'Edit Seller' : 'Create Seller')

@section('content')
  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ $seller->exists ? route('admin.sellers.update', $seller) : route('admin.sellers.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
          <label class="form-label" for="name">Name</label>
          <input id="name" name="name" class="form-control" value="{{ old('name', $seller->name) }}" required>
          @error('name') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
          <label class="form-label" for="username">Username</label>
          <input id="username" name="username" class="form-control" value="{{ old('username', $seller->username) }}" required>
          @error('username') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
          <label class="form-label" for="volume">Volume</label>
          <input id="volume" name="volume" type="number" step="0.0001" class="form-control" value="{{ old('volume', $seller->volume ?? 0) }}">
          @error('volume') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
          <label class="form-label" for="avatar">Avatar</label>
          <input id="avatar" name="avatar" type="file" class="form-control">
          @error('avatar') <div class="text-danger">{{ $message }}</div> @enderror
          @if ($seller->avatar_path)
            @php
              $avatar = \Illuminate\Support\Str::startsWith($seller->avatar_path, 'sellers/')
                  ? asset('storage/' . $seller->avatar_path)
                  : asset($seller->avatar_path);
            @endphp
            <div class="mt-2">
              <img src="{{ $avatar }}" alt="Avatar" style="height:80px;width:80px;object-fit:cover;border-radius:50%;">
            </div>
          @endif
        </div>

        <div class="row">
          <div class="col-md-4 mb-3">
            <div class="form-check">
              <input type="hidden" name="is_verified" value="0">
              <input class="form-check-input" type="checkbox" name="is_verified" id="is_verified" value="1" {{ old('is_verified', $seller->is_verified) ? 'checked' : '' }}>
              <label class="form-check-label" for="is_verified">Verified</label>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="form-check">
              <input type="hidden" name="is_active" value="0">
              <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $seller->is_active ?? true) ? 'checked' : '' }}>
              <label class="form-check-label" for="is_active">Active</label>
            </div>
          </div>
        </div>

        <button type="submit" class="btn btn-success">Save</button>
        <a href="{{ route('admin.sellers.index') }}" class="btn btn-secondary">Cancel</a>
      </form>
    </div>
  </div>
@endsection
