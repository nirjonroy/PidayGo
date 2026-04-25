@extends('layouts.admin-panel')

@section('page-title', 'Deposits')

@section('content')
  <div class="card mb-3">
    <div class="card-body">
      <form method="GET" class="row g-2 align-items-end">
        <div class="col-auto">
          <label class="form-label">Status</label>
          <select name="status" class="form-select">
            @foreach (['pending', 'Completed', 'approved', 'rejected', 'expired', 'all'] as $option)
              <option value="{{ $option }}" @selected($status === $option)>{{ ucfirst($option) }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-auto">
          <button class="btn btn-primary" type="submit">Filter</button>
        </div>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped mb-0">
          <thead>
            <tr>
              <th>#</th>
              <th>User</th>
              <th>Amount</th>
              <th>Reference</th>
              <th>Gateway</th>
              <th>Status</th>
              <th>Submitted</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @forelse ($requests as $deposit)
              <tr>
                <td>{{ $deposit->id }}</td>
                <td>{{ $deposit->user->email ?? '-' }}</td>
                <td>{{ $deposit->amount }}</td>
                <td style="max-width:180px; word-break:break-all;">{{ $deposit->txid ?: ($deposit->gateway_track_id ?: '-') }}</td>
                <td>{{ $deposit->gateway ? strtoupper($deposit->gateway) : 'Manual' }}</td>
                <td>{{ ucfirst($deposit->status) }}</td>
                <td>{{ $deposit->created_at }}</td>
                <td>
                  <a href="{{ route('admin.deposits.show', $deposit) }}" class="btn btn-sm btn-outline-primary">View</a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="text-center text-muted">No deposits found.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    @if ($requests->hasPages())
      <div class="card-footer">
        {{ $requests->links() }}
      </div>
    @endif
  </div>
@endsection
