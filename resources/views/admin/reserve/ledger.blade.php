@extends('layouts.admin-panel')

@section('page-title', 'Reserve Ledger')

@section('content')
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span>Reserve Ledger</span>
      <a href="{{ route('admin.reserve.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped mb-0">
          <thead>
            <tr>
              <th>#</th>
              <th>Amount</th>
              <th>Reason</th>
              <th>Admin</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($ledgers as $ledger)
              <tr>
                <td>{{ $ledger->id }}</td>
                <td class="{{ $ledger->amount < 0 ? 'text-danger' : 'text-success' }}">
                  {{ $ledger->amount }}
                </td>
                <td>{{ $ledger->reason }}</td>
                <td>{{ $ledger->createdByAdmin->name ?? '-' }}</td>
                <td>{{ $ledger->created_at }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-muted">No ledger entries.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    @if ($ledgers->hasPages())
      <div class="card-footer">
        {{ $ledgers->links() }}
      </div>
    @endif
  </div>
@endsection
