@extends('layouts.admin-panel')

@section('page-title', 'Bids')

@section('content')
  <div class="mb-3">
    <a href="{{ route('admin.bids.create') }}" class="btn btn-primary">Add Bid</a>
  </div>

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped mb-0">
          <thead>
            <tr>
              <th>Item</th>
              <th>Bidder</th>
              <th>Amount</th>
              <th>Status</th>
              <th>Created</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @forelse ($bids as $bid)
              <tr>
                <td>{{ $bid->item?->title ?? '-' }}</td>
                <td>{{ $bid->user?->name ?? $bid->bidder_name ?? 'Anonymous' }}</td>
                <td>{{ number_format($bid->amount, 4) }} USDT</td>
                <td>
                  @if ($bid->is_active)
                    <span class="badge bg-success">Active</span>
                  @else
                    <span class="badge bg-secondary">Inactive</span>
                  @endif
                </td>
                <td>{{ $bid->created_at }}</td>
                <td class="text-end">
                  <form method="POST" action="{{ route('admin.bids.toggle', $bid) }}" class="d-inline">
                    @csrf
                    <button class="btn btn-sm btn-outline-secondary" type="submit">
                      {{ $bid->is_active ? 'Disable' : 'Enable' }}
                    </button>
                  </form>
                  <form method="POST" action="{{ route('admin.bids.delete', $bid) }}" class="d-inline" onsubmit="return confirm('Delete this bid?')">
                    @csrf
                    <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center text-muted">No bids yet.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  @if ($bids->hasPages())
    <div class="mt-3">
      {{ $bids->links() }}
    </div>
  @endif
@endsection
