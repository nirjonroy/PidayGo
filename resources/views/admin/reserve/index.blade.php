@extends('layouts.admin-panel')

@section('page-title', 'Reserve')

@section('content')
  <div class="row">
    <div class="col-lg-6">
      <div class="card mb-3">
        <div class="card-body">
          <h5 class="card-title">Current Reserve (USDT)</h5>
          <p class="display-6 mb-0">{{ number_format((float) $reserve->balance, 4) }}</p>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card mb-3">
        <div class="card-body">
          <h5 class="card-title">Ledger</h5>
          <a href="{{ route('admin.reserve.ledger') }}" class="btn btn-outline-primary">View Ledger</a>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-6">
      <div class="card">
        <div class="card-header">Add Reserve</div>
        <div class="card-body">
          <form method="POST" action="{{ route('admin.reserve.add') }}">
            @csrf
            <div class="mb-3">
              <label class="form-label">Amount</label>
              <input type="number" step="0.0001" min="0.0001" name="amount" class="form-control" required>
              @error('amount')<div class="text-danger mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
              <label class="form-label">Reason</label>
              <input type="text" name="reason" class="form-control" required>
              @error('reason')<div class="text-danger mt-1">{{ $message }}</div>@enderror
            </div>
            <button class="btn btn-success" type="submit">Add</button>
          </form>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card">
        <div class="card-header">Deduct Reserve</div>
        <div class="card-body">
          <form method="POST" action="{{ route('admin.reserve.deduct') }}">
            @csrf
            <div class="mb-3">
              <label class="form-label">Amount</label>
              <input type="number" step="0.0001" min="0.0001" name="amount" class="form-control" required>
              @error('amount')<div class="text-danger mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
              <label class="form-label">Reason</label>
              <input type="text" name="reason" class="form-control" required>
              @error('reason')<div class="text-danger mt-1">{{ $message }}</div>@enderror
            </div>
            <button class="btn btn-danger" type="submit">Deduct</button>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
