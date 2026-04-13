@extends('layouts.admin-panel')

@section('page-title', 'PI Items')

@section('content')
  <div class="mb-3">
    <a href="{{ route('admin.nft-items.create') }}" class="btn btn-primary">Add PI Item</a>
  </div>

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped mb-0">
          <thead>
            <tr>
              <th>Image</th>
              <th>Title</th>
              <th>Creator</th>
              <th>Owner</th>
              <th>Price</th>
              <th>Status</th>
              <th>Active</th>
              <th>Trending</th>
              <th>Updated</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @forelse ($items as $item)
              @php
                $image = $item->image_path
                    ? (\Illuminate\Support\Str::startsWith($item->image_path, 'nfts/')
                        ? asset('storage/' . $item->image_path)
                        : asset($item->image_path))
                    : asset('frontend/images/items/static-21.jpg');
              @endphp
              <tr>
                <td><img src="{{ $image }}" alt="Item" style="height:48px;" class="rounded"></td>
                <td>{{ $item->title }}</td>
                <td>{{ $item->creatorSeller?->name ?? '-' }}</td>
                <td>{{ $item->ownerSeller?->name ?? '-' }}</td>
                <td>{{ $item->price ? number_format($item->price, 4) : '-' }}</td>
                <td>{{ ucfirst($item->status) }}</td>
                <td>
                  @if ($item->is_active)
                    <span class="badge bg-success">Active</span>
                  @else
                    <span class="badge bg-secondary">Inactive</span>
                  @endif
                </td>
                <td>
                  @if ($item->is_trending)
                    <span class="badge bg-success">Yes</span>
                  @else
                    <span class="badge bg-secondary">No</span>
                  @endif
                </td>
                <td>{{ $item->updated_at }}</td>
                <td class="text-end">
                  <a href="{{ route('admin.nft-items.edit', $item) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                  <form method="POST" action="{{ route('admin.nft-items.toggle', $item) }}" class="d-inline">
                    @csrf
                    <button class="btn btn-sm btn-outline-secondary" type="submit">
                      {{ $item->is_active ? 'Disable' : 'Enable' }}
                    </button>
                  </form>
                  <form method="POST" action="{{ route('admin.nft-items.delete', $item) }}" class="d-inline" onsubmit="return confirm('Delete this item?')">
                    @csrf
                    <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="10" class="text-center text-muted">No PI items yet.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection

