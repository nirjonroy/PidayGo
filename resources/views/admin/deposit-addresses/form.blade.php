@extends('layouts.admin-panel')

@section('page-title', $address->exists ? 'Edit Deposit Address' : 'Create Deposit Address')

@section('content')
  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ $address->exists ? route('admin.deposit-addresses.update', $address) : route('admin.deposit-addresses.store') }}">
        @csrf
        @if ($address->exists)
          @method('PUT')
        @endif

        <div class="mb-3">
          <label class="form-label" for="label">Label</label>
          <input id="label" name="label" class="form-control" value="{{ old('label', $address->label) }}">
          @error('label') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
          <label class="form-label" for="currency">Currency</label>
          <input id="currency" name="currency" class="form-control" value="{{ old('currency', $address->currency ?? 'USDT') }}" readonly>
          @error('currency') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
          <label class="form-label" for="chain">Chain</label>
          <input id="chain" name="chain" class="form-control" value="{{ old('chain', $address->chain ?? 'TRC20') }}" readonly>
          @error('chain') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
          <label class="form-label" for="address">Address</label>
          <input id="address" name="address" class="form-control" value="{{ old('address', $address->address) }}" required>
          @error('address') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
          <label class="form-label" for="qr_payload">QR Payload (optional)</label>
          <input id="qr_payload" name="qr_payload" class="form-control" value="{{ old('qr_payload', $address->qr_payload) }}">
          @error('qr_payload') <div class="text-danger">{{ $message }}</div> @enderror
          <div class="form-text">If empty, the address will be used as the QR payload.</div>
        </div>

        <div class="mb-3">
          <label class="form-label" for="notes">Notes</label>
          <textarea id="notes" name="notes" class="form-control" rows="3">{{ old('notes', $address->notes) }}</textarea>
          @error('notes') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <button class="btn btn-success" type="submit">Save</button>
        <a href="{{ route('admin.deposit-addresses.index') }}" class="btn btn-secondary">Cancel</a>
      </form>
    </div>
  </div>
@endsection
