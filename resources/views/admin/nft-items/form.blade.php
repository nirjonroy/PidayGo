@extends('layouts.admin-panel')

@section('page-title', $item->exists ? 'Edit NFT Item' : 'Create NFT Item')

@section('content')
  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ $item->exists ? route('admin.nft-items.update', $item) : route('admin.nft-items.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
          <label class="form-label" for="title">Title</label>
          <input id="title" name="title" class="form-control" value="{{ old('title', $item->title) }}" required>
          @error('title') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
          <label class="form-label" for="description">Description</label>
          <textarea id="description" name="description" class="form-control" rows="3">{{ old('description', $item->description) }}</textarea>
          @error('description') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label" for="creator_seller_id">Creator Seller</label>
            <select id="creator_seller_id" name="creator_seller_id" class="form-select">
              <option value="">-- Select --</option>
              @foreach ($sellers as $seller)
                <option value="{{ $seller->id }}" @selected(old('creator_seller_id', $item->creator_seller_id) == $seller->id)>
                  {{ $seller->name }} ({{ '@' . $seller->username }})
                </option>
              @endforeach
            </select>
            @error('creator_seller_id') <div class="text-danger">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label" for="owner_seller_id">Owner Seller</label>
            <select id="owner_seller_id" name="owner_seller_id" class="form-select">
              <option value="">-- Select --</option>
              @foreach ($sellers as $seller)
                <option value="{{ $seller->id }}" @selected(old('owner_seller_id', $item->owner_seller_id) == $seller->id)>
                  {{ $seller->name }} ({{ '@' . $seller->username }})
                </option>
              @endforeach
            </select>
            @error('owner_seller_id') <div class="text-danger">{{ $message }}</div> @enderror
          </div>
        </div>

        <div class="row">
          <div class="col-md-4 mb-3">
            <label class="form-label" for="price">Price (USDT)</label>
            <input id="price" name="price" type="number" step="0.0001" class="form-control" value="{{ old('price', $item->price) }}">
            @error('price') <div class="text-danger">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label" for="auction_end_at">Auction End</label>
            <input id="auction_end_at" name="auction_end_at" type="datetime-local" class="form-control" value="{{ old('auction_end_at', $item->auction_end_at ? $item->auction_end_at->format('Y-m-d\TH:i') : '') }}">
            @error('auction_end_at') <div class="text-danger">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label" for="status">Status</label>
            <select id="status" name="status" class="form-select" required>
              @foreach (['draft' => 'Draft', 'published' => 'Published'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $item->status ?? 'published') === $value)>{{ $label }}</option>
              @endforeach
            </select>
            @error('status') <div class="text-danger">{{ $message }}</div> @enderror
          </div>
        </div>

        <div class="row">
          <div class="col-md-3 mb-3">
            <label class="form-label" for="likes_count">Likes</label>
            <input id="likes_count" name="likes_count" type="number" min="0" class="form-control" value="{{ old('likes_count', $item->likes_count ?? 0) }}">
            @error('likes_count') <div class="text-danger">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-3 mb-3">
            <label class="form-label" for="views_count">Views</label>
            <input id="views_count" name="views_count" type="number" min="0" class="form-control" value="{{ old('views_count', $item->views_count ?? 0) }}">
            @error('views_count') <div class="text-danger">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-3 mb-3 d-flex align-items-end">
            <div class="form-check">
              <input type="hidden" name="is_trending" value="0">
              <input class="form-check-input" type="checkbox" name="is_trending" id="is_trending" value="1" {{ old('is_trending', $item->is_trending) ? 'checked' : '' }}>
              <label class="form-check-label" for="is_trending">Trending</label>
            </div>
          </div>
          <div class="col-md-3 mb-3 d-flex align-items-end">
            <div class="form-check">
              <input type="hidden" name="is_featured" value="0">
              <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured', $item->is_featured) ? 'checked' : '' }}>
              <label class="form-check-label" for="is_featured">Featured</label>
            </div>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label" for="image">Image</label>
          <input id="image" name="image" type="file" class="form-control" {{ $item->exists ? '' : 'required' }}>
          @error('image') <div class="text-danger">{{ $message }}</div> @enderror
          @if ($item->image_path)
            @php
              $image = \Illuminate\Support\Str::startsWith($item->image_path, 'nfts/')
                  ? asset('storage/' . $item->image_path)
                  : asset($item->image_path);
            @endphp
            <div class="mt-2">
              <img src="{{ $image }}" alt="Item" style="height:100px;" class="rounded">
            </div>
          @endif
        </div>

        <button type="submit" class="btn btn-success">Save</button>
        <a href="{{ route('admin.nft-items.index') }}" class="btn btn-secondary">Cancel</a>
      </form>
    </div>
  </div>
@endsection
