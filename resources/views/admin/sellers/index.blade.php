@extends('layouts.admin-panel')

@section('page-title', 'Sellers')

@section('content')
  <div class="mb-3">
    <a href="{{ route('admin.sellers.create') }}" class="btn btn-primary">Add Seller</a>
  </div>

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped mb-0">
          <thead>
            <tr>
              <th>Avatar</th>
              <th>Name</th>
              <th>Username</th>
              <th>Volume</th>
              <th>Verified</th>
              <th>Status</th>
              <th>Updated</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @forelse ($sellers as $seller)
              @php
                $avatar = $seller->avatar_path
                    ? (\Illuminate\Support\Str::startsWith($seller->avatar_path, 'sellers/')
                        ? asset('storage/' . $seller->avatar_path)
                        : asset($seller->avatar_path))
                    : asset('frontend/images/author/author-1.jpg');
              @endphp
              <tr>
                <td><img src="{{ $avatar }}" alt="Avatar" style="height:40px;width:40px;object-fit:cover;border-radius:50%;"></td>
                <td>{{ $seller->name }}</td>
                <td>{{ '@' . $seller->username }}</td>
                <td>{{ number_format($seller->volume, 4) }} USDT</td>
                <td>
                  @if ($seller->is_verified)
                    <span class="badge bg-success">Yes</span>
                  @else
                    <span class="badge bg-secondary">No</span>
                  @endif
                </td>
                <td>
                  @if ($seller->is_active)
                    <span class="badge bg-success">Active</span>
                  @else
                    <span class="badge bg-secondary">Inactive</span>
                  @endif
                </td>
                <td>{{ $seller->updated_at }}</td>
                <td class="text-end">
                  <a href="{{ route('admin.sellers.edit', $seller) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                  <form method="POST" action="{{ route('admin.sellers.toggle', $seller) }}" class="d-inline">
                    @csrf
                    <button class="btn btn-sm btn-outline-secondary" type="submit">
                      {{ $seller->is_active ? 'Disable' : 'Enable' }}
                    </button>
                  </form>
                  <form method="POST" action="{{ route('admin.sellers.delete', $seller) }}" class="d-inline" onsubmit="return confirm('Delete this seller?')">
                    @csrf
                    <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="text-center text-muted">No sellers yet.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection
