@extends('layouts.admin-panel')

@section('page-title', 'Blog Posts')

@section('content')
  <div class="mb-3">
    <a href="{{ route('admin.blog-posts.create') }}" class="btn btn-primary">Add Post</a>
  </div>

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped mb-0">
          <thead>
            <tr>
              <th>Image</th>
              <th>Title</th>
              <th>Category</th>
              <th>Published</th>
              <th>Status</th>
              <th>Updated</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @forelse ($posts as $post)
              @php
                $image = $post->image_path ? asset('storage/' . $post->image_path) : asset('frontend/images/news/news-b1.jpg');
              @endphp
              <tr>
                <td><img src="{{ $image }}" alt="Post" style="height:48px;" class="rounded"></td>
                <td>{{ $post->title }}</td>
                <td>{{ $post->category ?? '-' }}</td>
                <td>{{ $post->published_at?->format('Y-m-d') ?? '-' }}</td>
                <td>
                  @if ($post->is_active)
                    <span class="badge bg-success">Active</span>
                  @else
                    <span class="badge bg-secondary">Inactive</span>
                  @endif
                </td>
                <td>{{ $post->updated_at }}</td>
                <td class="text-end">
                  <a href="{{ route('admin.blog-posts.edit', $post) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                  <form method="POST" action="{{ route('admin.blog-posts.toggle', $post) }}" class="d-inline">
                    @csrf
                    <button class="btn btn-sm btn-outline-secondary" type="submit">
                      {{ $post->is_active ? 'Disable' : 'Enable' }}
                    </button>
                  </form>
                  <form method="POST" action="{{ route('admin.blog-posts.delete', $post) }}" class="d-inline" onsubmit="return confirm('Delete this post?')">
                    @csrf
                    <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center text-muted">No blog posts yet.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection
