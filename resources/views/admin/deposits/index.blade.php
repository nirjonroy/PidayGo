@extends('layouts.admin-panel')

@section('page-title', 'Deposits')

@section('content')
  <div class="card mb-3">
    <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-3">
      <div>
        <div class="fw-semibold">OxaPay API</div>
        <div class="small text-muted">{{ $oxapayConnection['message'] ?? 'Connection status unavailable.' }}</div>
      </div>
      @if (!empty($oxapayConnection['connected']))
        <span class="badge text-bg-success">Connected</span>
      @else
        <span class="badge text-bg-danger">Disconnected</span>
      @endif
    </div>
  </div>

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
              <th>Gateway Order ID</th>
              <th>OxaPay Track ID</th>
              <th>Blockchain TxID</th>
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
                <td>{{ $deposit->amount }} {{ $deposit->currency }}</td>
                <td style="max-width:180px; word-break:break-all;">{{ $deposit->gateway_order_id ?: '-' }}</td>
                <td style="max-width:160px; word-break:break-all;">{{ $deposit->gateway_track_id ?: '-' }}</td>
                <td style="max-width:180px; word-break:break-all;">{{ $deposit->txid ?: '-' }}</td>
                <td>{{ $deposit->gateway ? strtoupper($deposit->gateway) : 'Manual' }}</td>
                <td>{{ ucfirst($deposit->status) }}</td>
                <td>{{ $deposit->created_at }}</td>
                <td class="text-nowrap">
                  <a href="{{ route('admin.deposits.show', $deposit) }}" class="btn btn-sm btn-outline-primary">View</a>
                  @if ($deposit->gateway === 'oxapay' && $deposit->gateway_track_id && in_array($deposit->status, ['pending', 'expired'], true))
                    <form method="POST" action="{{ route('admin.deposits.sync-oxapay', $deposit) }}" class="d-inline">
                      @csrf
                      <button type="submit" class="btn btn-sm btn-outline-success">Check Status</button>
                    </form>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="10" class="text-center text-muted">No deposits found.</td>
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
