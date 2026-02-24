@extends('layouts.admin-panel')

@section('page-title', $slide->exists ? 'Edit Slide' : 'Create Slide')

@section('content')
  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ $slide->exists ? route('admin.home-slides.update', $slide) : route('admin.home-slides.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
          <label class="form-label" for="title">Title</label>
          <input id="title" name="title" class="form-control" value="{{ old('title', $slide->title) }}" required>
          @error('title') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
          <label class="form-label" for="subtitle">Subtitle</label>
          <textarea id="subtitle" name="subtitle" class="form-control" rows="2">{{ old('subtitle', $slide->subtitle) }}</textarea>
          @error('subtitle') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label" for="button_text">Button Text</label>
            <input id="button_text" name="button_text" class="form-control" value="{{ old('button_text', $slide->button_text) }}">
            @error('button_text') <div class="text-danger">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label" for="button_url">Button URL</label>
            <input id="button_url" name="button_url" class="form-control" value="{{ old('button_url', $slide->button_url) }}">
            @error('button_url') <div class="text-danger">{{ $message }}</div> @enderror
          </div>
        </div>

        <div class="row">
          <div class="col-md-4 mb-3">
            <label class="form-label" for="sort_order">Sort Order</label>
            <input id="sort_order" name="sort_order" type="number" min="0" class="form-control" value="{{ old('sort_order', $slide->sort_order ?? 0) }}">
            @error('sort_order') <div class="text-danger">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-4 mb-3 d-flex align-items-end">
            <div class="form-check">
              <input type="hidden" name="is_active" value="0">
              <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $slide->is_active) ? 'checked' : '' }}>
              <label class="form-check-label" for="is_active">Active</label>
            </div>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label" for="image">Slide Image</label>
          <input id="image" name="image" type="file" class="form-control" {{ $slide->exists ? '' : 'required' }}>
          @error('image') <div class="text-danger">{{ $message }}</div> @enderror
          @if ($slide->image_path)
            @php
              $image = \Illuminate\Support\Str::startsWith($slide->image_path, 'slides/')
                  ? asset('storage/' . $slide->image_path)
                  : asset($slide->image_path);
            @endphp
            <div class="mt-2">
              <img src="{{ $image }}" alt="Slide" style="height:80px;" class="rounded">
            </div>
          @endif
        </div>

        <button type="submit" class="btn btn-success">Save</button>
        <a href="{{ route('admin.home-slides.index') }}" class="btn btn-secondary">Cancel</a>
      </form>
    </div>
  </div>
@endsection
