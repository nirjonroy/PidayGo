@extends('layouts.admin-panel')

@section('page-title', 'Footer Links')

@section('content')
  <div class="mb-3">
    <a href="{{ route('admin.footer-links.create') }}" class="btn btn-primary">Add Footer Link</a>
  </div>

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped mb-0">
          <thead>
            <tr>
              <th>Section</th>
              <th>Label</th>
              <th>URL</th>
              <th>Sort</th>
              <th>Status</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @forelse ($links as $link)
              <tr>
                <td>{{ $sections[$link->section] ?? $link->section }}</td>
                <td>{{ $link->label }}</td>
                <td class="text-truncate" style="max-width: 280px;">{{ $link->url }}</td>
                <td>{{ $link->sort_order }}</td>
                <td>
                  @if ($link->is_active)
                    <span class="badge bg-success">Active</span>
                  @else
                    <span class="badge bg-secondary">Inactive</span>
                  @endif
                </td>
                <td class="text-end">
                  <a href="{{ route('admin.footer-links.edit', $link) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                  <form method="POST" action="{{ route('admin.footer-links.toggle', $link) }}" class="d-inline">
                    @csrf
                    <button class="btn btn-sm btn-outline-secondary" type="submit">
                      {{ $link->is_active ? 'Disable' : 'Enable' }}
                    </button>
                  </form>
                  <form method="POST" action="{{ route('admin.footer-links.delete', $link) }}" class="d-inline" onsubmit="return confirm('Delete this link?')">
                    @csrf
                    <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center text-muted">No footer links yet.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection
