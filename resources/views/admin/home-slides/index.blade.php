@extends('layouts.admin-panel')

@section('page-title', 'Home Slides')

@section('content')
  <div class="mb-3">
    <a href="{{ route('admin.home-slides.create') }}" class="btn btn-primary">Add Slide</a>
  </div>

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped mb-0">
          <thead>
            <tr>
              <th>Image</th>
              <th>Title</th>
              <th>Button</th>
              <th>Sort</th>
              <th>Status</th>
              <th>Updated</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @forelse ($slides as $slide)
              @php
                $image = $slide->image_path
                    ? (\Illuminate\Support\Str::startsWith($slide->image_path, 'slides/')
                        ? asset('storage/' . $slide->image_path)
                        : asset($slide->image_path))
                    : asset('frontend/images/carousel/crs-12.jpg');
              @endphp
              <tr>
                <td>
                  <img src="{{ $image }}" alt="Slide" style="height:48px;" class="rounded">
                </td>
                <td>{{ $slide->title }}</td>
                <td>
                  {{ $slide->button_text ?? '-' }}
                  @if ($slide->button_url)
                    <div class="small text-muted">{{ $slide->button_url }}</div>
                  @endif
                </td>
                <td>{{ $slide->sort_order }}</td>
                <td>
                  @if ($slide->is_active)
                    <span class="badge bg-success">Active</span>
                  @else
                    <span class="badge bg-secondary">Inactive</span>
                  @endif
                </td>
                <td>{{ $slide->updated_at }}</td>
                <td class="text-end">
                  <a href="{{ route('admin.home-slides.edit', $slide) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                  <form method="POST" action="{{ route('admin.home-slides.toggle', $slide) }}" class="d-inline">
                    @csrf
                    <button class="btn btn-sm btn-outline-secondary" type="submit">
                      {{ $slide->is_active ? 'Disable' : 'Enable' }}
                    </button>
                  </form>
                  <form method="POST" action="{{ route('admin.home-slides.delete', $slide) }}" class="d-inline" onsubmit="return confirm('Delete this slide?')">
                    @csrf
                    <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center text-muted">No slides yet.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection
