@extends('layouts.frontend')

@section('content')
@include('frontend.partials.page-banner', ['title' => 'Sell'])

<section aria-label="section">
    <div class="container">
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <div class="row">
            <div class="col-lg-6 mb30">
                <div class="nft__item s2">
                    <div class="nft__item_info">
                        <h4>Reserve Details</h4>
                        <div class="nft__item_price">Level: {{ $level?->code ?? '-' }}</div>
                        <div class="nft__item_price">Reserved Amount: {{ number_format($reserve->amount ?? $reserve->reserved_balance, 8) }} USDT</div>
                        <div class="nft__item_price">Profit Range: {{ $plan?->profit_min_percent }}% - {{ $plan?->profit_max_percent }}%</div>
                        <div class="nft__item_price">
                            Sell Limit:
                            @if ($sellLimit === null)
                                Unlimited
                            @else
                                {{ $sellCount }} / {{ $sellLimit }} used ({{ $sellsRemaining }} left)
                            @endif
                        </div>
                        <div class="nft__item_price">
                            Unlock Policy: {{ ucfirst(str_replace('_', ' ', $plan?->unlock_policy ?? 'never')) }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb30">
                <div class="nft__item s2">
                    <div class="nft__item_info">
                        <h4>Sell Art / NFT</h4>
                        @if (!$nftEnabled)
                            <div class="text-muted">Selling is currently disabled.</div>
                        @elseif ($items->isEmpty())
                            <div class="text-muted">No NFTs available to sell right now.</div>
                        @elseif ($sellLimit !== null && $sellsRemaining <= 0)
                            <div class="text-muted">You have reached the sell limit for this reserve.</div>
                        @else
                            <form method="POST" action="{{ route('reserve.sell.submit') }}" class="form-border">
                                @csrf
                                @php
                                    $firstItem = $items->first();
                                    $firstImage = $firstItem
                                        ? (\Illuminate\Support\Str::startsWith($firstItem->image_path, 'frontend/')
                                            ? asset($firstItem->image_path)
                                            : asset('storage/' . $firstItem->image_path))
                                        : '';
                                @endphp
                                <div class="mb-3">
                                    <label class="form-label">Selected Art</label>
                                    <div class="d-flex align-items-center gap-3 p-2 border rounded">
                                        <img id="selected-nft-image" src="{{ $firstImage }}" alt="Selected NFT" style="width:90px; height:90px; object-fit:cover; border-radius:10px;">
                                        <div>
                                            <div id="selected-nft-title" class="fw-semibold">{{ $firstItem?->title }}</div>
                                            <div class="small text-muted">Profit: {{ $plan?->profit_min_percent }}% - {{ $plan?->profit_max_percent }}%</div>
                                            <div class="small text-muted" id="selected-nft-price">Sale Amount: {{ number_format((float) ($reserve->amount ?? $reserve->reserved_balance), 8) }} USDT</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Select NFT</label>
                                    <div class="row g-3">
                                        @foreach ($items as $item)
                                            @php
                                                $imagePath = \Illuminate\Support\Str::startsWith($item->image_path, 'frontend/')
                                                    ? asset($item->image_path)
                                                    : asset('storage/' . $item->image_path);
                                            @endphp
                                            <div class="col-md-6">
                                                <label class="d-block border rounded p-2 h-100">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <input class="form-check-input nft-select" type="radio" name="nft_item_id" value="{{ $item->id }}" data-price="{{ $item->price ?? '' }}" data-title="{{ $item->title }}" data-image="{{ $imagePath }}" @checked($loop->first)>
                                                        <div class="fw-semibold">{{ $item->title }}</div>
                                                    </div>
                                                    <div class="mt-2">
                                                        <img src="{{ $imagePath }}" alt="{{ $item->title }}" class="img-fluid rounded" style="max-height:140px; width:100%; object-fit:cover;">
                                                    </div>
                                                    <div class="small text-muted mt-2">Sale Amount: {{ number_format((float) ($reserve->amount ?? $reserve->reserved_balance), 8) }} USDT</div>
                                                    <div class="small text-muted">Profit: {{ $plan?->profit_min_percent }}% - {{ $plan?->profit_max_percent }}%</div>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Sale Amount (Locked)</label>
                                    <input id="sale_amount" type="number" step="0.00000001" name="sale_amount" class="form-control" value="{{ number_format((float) ($reserve->amount ?? $reserve->reserved_balance), 8, '.', '') }}" readonly>
                                </div>
                                <button type="submit" class="btn-main">Sell</button>
                            </form>
                            <p class="text-muted mt-2">Pick an NFT, then confirm the sale to credit profit to your wallet.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const amountInput = document.getElementById('sale_amount');
    const selectedImage = document.getElementById('selected-nft-image');
    const selectedTitle = document.getElementById('selected-nft-title');
    const selectedPrice = document.getElementById('selected-nft-price');
    document.querySelectorAll('.nft-select').forEach(function (radio) {
      radio.addEventListener('change', function () {
        if (amountInput) {
          amountInput.value = amountInput.value || '';
        }
        if (selectedImage) {
          selectedImage.src = this.dataset.image || '';
        }
        if (selectedTitle) {
          selectedTitle.textContent = this.dataset.title || '';
        }
        if (selectedPrice) {
          selectedPrice.textContent = `Sale Amount: ${parseFloat(amountInput.value || 0).toFixed(8)} USDT`;
        }
      });
    });

    const preselected = document.querySelector('.nft-select:checked');
    if (preselected && selectedPrice) {
      selectedPrice.textContent = `Sale Amount: ${parseFloat(amountInput.value || 0).toFixed(8)} USDT`;
    }
  });
</script>
@endpush
