@extends('layouts.admin-panel')

@section('page-title', 'Create Bid')

@section('content')
  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('admin.bids.store') }}">
        @csrf

        <div class="mb-3">
          <label class="form-label" for="nft_item_id">NFT Item</label>
          <select id="nft_item_id" name="nft_item_id" class="form-select" required>
            <option value="">-- Select Item --</option>
            @foreach ($items as $item)
              <option value="{{ $item->id }}" @selected(old('nft_item_id') == $item->id)>{{ $item->title }}</option>
            @endforeach
          </select>
          @error('nft_item_id') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
          <label class="form-label" for="user_id">User (optional)</label>
          <select id="user_id" name="user_id" class="form-select">
            <option value="">-- None --</option>
            @foreach ($users as $user)
              <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>{{ $user->name }} ({{ $user->email }})</option>
            @endforeach
          </select>
          @error('user_id') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
          <label class="form-label" for="bidder_name">Bidder Name (if no user)</label>
          <input id="bidder_name" name="bidder_name" class="form-control" value="{{ old('bidder_name') }}">
          @error('bidder_name') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
          <label class="form-label" for="amount">Amount</label>
          <input id="amount" name="amount" type="number" step="0.0001" class="form-control" value="{{ old('amount') }}" required>
          @error('amount') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn btn-success">Save</button>
        <a href="{{ route('admin.bids.index') }}" class="btn btn-secondary">Cancel</a>
      </form>
    </div>
  </div>
@endsection
