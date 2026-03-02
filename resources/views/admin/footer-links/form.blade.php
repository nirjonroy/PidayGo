@extends('layouts.admin-panel')

@section('page-title', $link->exists ? 'Edit Footer Link' : 'Create Footer Link')

@section('content')
  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ $link->exists ? route('admin.footer-links.update', $link) : route('admin.footer-links.store') }}">
        @csrf

        <div class="row">
          <div class="col-md-4 mb-3">
            <label class="form-label" for="section">Section</label>
            <select id="section" name="section" class="form-select" required>
              @foreach ($sections as $value => $label)
                <option value="{{ $value }}" @selected(old('section', $link->section) === $value)>{{ $label }}</option>
              @endforeach
            </select>
            @error('section') <div class="text-danger">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label" for="label">Label</label>
            <input id="label" name="label" class="form-control" value="{{ old('label', $link->label) }}" required>
            @error('label') <div class="text-danger">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label" for="sort_order">Sort Order</label>
            <input id="sort_order" name="sort_order" type="number" min="0" class="form-control" value="{{ old('sort_order', $link->sort_order ?? 0) }}">
            @error('sort_order') <div class="text-danger">{{ $message }}</div> @enderror
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label" for="url">URL</label>
          <input id="url" name="url" class="form-control" value="{{ old('url', $link->url) }}" required>
          @error('url') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
          <div class="form-check">
            <input type="hidden" name="is_active" value="0">
            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $link->is_active) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">Active</label>
          </div>
        </div>

        <button type="submit" class="btn btn-success">Save</button>
        <a href="{{ route('admin.footer-links.index') }}" class="btn btn-secondary">Cancel</a>
      </form>
    </div>
  </div>
@endsection
