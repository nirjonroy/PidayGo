@extends('layouts.admin-panel')

@section('page-title', $post->exists ? 'Edit Blog Post' : 'Create Blog Post')

@section('content')
  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ $post->exists ? route('admin.blog-posts.update', $post) : route('admin.blog-posts.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
          <label class="form-label" for="title">Title</label>
          <input id="title" name="title" class="form-control" value="{{ old('title', $post->title) }}" required>
          @error('title') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label" for="slug">Slug (optional)</label>
            <input id="slug" name="slug" class="form-control" value="{{ old('slug', $post->slug) }}">
            @error('slug') <div class="text-danger">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label" for="category">Category</label>
            <input id="category" name="category" class="form-control" value="{{ old('category', $post->category) }}">
            @error('category') <div class="text-danger">{{ $message }}</div> @enderror
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label" for="published_at">Published At</label>
            <input id="published_at" name="published_at" type="datetime-local" class="form-control" value="{{ old('published_at', optional($post->published_at)->format('Y-m-d\\TH:i')) }}">
            @error('published_at') <div class="text-danger">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-6 mb-3 d-flex align-items-end">
            <div class="form-check">
              <input type="hidden" name="is_active" value="0">
              <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $post->is_active) ? 'checked' : '' }}>
              <label class="form-check-label" for="is_active">Active</label>
            </div>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label" for="excerpt">Excerpt</label>
          <textarea id="excerpt" name="excerpt" class="form-control" rows="3">{{ old('excerpt', $post->excerpt) }}</textarea>
          @error('excerpt') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
          <label class="form-label" for="content">Content</label>
          <textarea id="content" name="content" class="form-control" rows="6">{{ old('content', $post->content) }}</textarea>
          @error('content') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
          <label class="form-label" for="image">Featured Image</label>
          <input id="image" name="image" type="file" class="form-control" {{ $post->exists ? '' : 'required' }}>
          @error('image') <div class="text-danger">{{ $message }}</div> @enderror
          @if ($post->image_path)
            <div class="mt-2">
              <img src="{{ asset('storage/' . $post->image_path) }}" alt="Post image" style="height:80px;" class="rounded">
            </div>
          @endif
        </div>

        <button type="submit" class="btn btn-success">Save</button>
        <a href="{{ route('admin.blog-posts.index') }}" class="btn btn-secondary">Cancel</a>
      </form>
    </div>
  </div>
@endsection
