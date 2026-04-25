@extends('layouts.admin-panel')

@section('page-title', 'Deposit Review')

@section('content')
  <div class="card mb-3">
    <div class="card-body">
      <div class="mb-2"><strong>User:</strong> {{ $deposit->user->name ?? '-' }} ({{ $deposit->user->email ?? '-' }})</div>
      <div class="mb-2"><strong>Status:</strong> {{ ucfirst($deposit->status) }}</div>
      <div class="mb-2"><strong>Amount:</strong> {{ $deposit->amount }} {{ $deposit->currency }}</div>
      @if ($deposit->pay_amount)
        <div class="mb-2"><strong>Pay Amount:</strong> {{ $deposit->pay_amount }} {{ $deposit->pay_currency }}</div>
      @endif
      <div class="mb-2"><strong>Chain:</strong> {{ $deposit->chain }}</div>
      <div class="mb-2"><strong>To Address:</strong> <span style="word-break:break-all;">{{ $deposit->to_address }}</span></div>
      <div class="mb-2"><strong>TxID:</strong> <span style="word-break:break-all;">{{ $deposit->txid ?: '-' }}</span></div>
      @if ($deposit->gateway)
        <div class="mb-2"><strong>Gateway:</strong> {{ strtoupper($deposit->gateway) }}</div>
        <div class="mb-2"><strong>Gateway Order:</strong> <span style="word-break:break-all;">{{ $deposit->gateway_order_id ?: '-' }}</span></div>
        <div class="mb-2"><strong>Gateway Track:</strong> <span style="word-break:break-all;">{{ $deposit->gateway_track_id ?: '-' }}</span></div>
      @endif
      <div class="mb-2"><strong>Submitted:</strong> {{ $deposit->created_at }}</div>
      <div class="mb-2"><strong>Expires At:</strong> {{ $deposit->expires_at }}</div>
      <div class="mb-2"><strong>Reviewed By:</strong> {{ $deposit->reviewedBy->name ?? '-' }}</div>
      <div class="mb-2"><strong>Reviewed At:</strong> {{ $deposit->reviewed_at ?? '-' }}</div>
      <div class="mb-2"><strong>Admin Note:</strong> {{ $deposit->admin_note ?? '-' }}</div>
    </div>
  </div>

  @if ($deposit->status === 'pending')
    <div class="card mb-3">
      <div class="card-body d-flex gap-2 flex-wrap">
        <form method="POST" action="{{ route('admin.deposits.approve', $deposit) }}">
          @csrf
          <button class="btn btn-success" type="submit">Approve</button>
        </form>
        <form method="POST" action="{{ route('admin.deposits.reject', $deposit) }}" class="d-flex gap-2">
          @csrf
          <input type="text" name="admin_note" class="form-control" placeholder="Rejection note" required>
          <button class="btn btn-danger" type="submit">Reject</button>
        </form>
        <form method="POST" action="{{ route('admin.deposits.expire', $deposit) }}">
          @csrf
          <button class="btn btn-outline-secondary" type="submit">Mark Expired</button>
        </form>
      </div>
      @error('admin_note')<div class="text-danger mt-2">{{ $message }}</div>@enderror
      @error('deposit')<div class="text-danger mt-2">{{ $message }}</div>@enderror
    </div>
  @endif

  <a href="{{ route('admin.deposits.index') }}" class="btn btn-secondary">Back to list</a>
@endsection
