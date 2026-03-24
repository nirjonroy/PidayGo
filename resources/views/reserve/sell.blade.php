@extends('layouts.frontend')

@push('styles')
<style>
    .reserve-loader {
        position: fixed;
        inset: 0;
        z-index: 3000;
        display: none;
        align-items: center;
        justify-content: center;
        background: rgba(7, 8, 18, 0.88);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }
    .reserve-loader.is-visible {
        display: flex;
    }
    .reserve-loader__card {
        width: min(320px, calc(100vw - 32px));
        padding: 28px 24px;
        border-radius: 24px;
        text-align: center;
        background: #ffffff;
        border: 1px solid rgba(15, 23, 42, 0.08);
        box-shadow: 0 24px 50px rgba(15, 23, 42, 0.18);
    }
    .reserve-loader__logo {
        width: 76px;
        height: 76px;
        object-fit: contain;
        margin-bottom: 18px;
        animation: reserve-loader-pulse 1.2s ease-in-out infinite;
    }
    .reserve-loader__title {
        font-size: 22px;
        font-weight: 800;
        color: #101828;
        margin-bottom: 8px;
    }
    .reserve-loader__copy {
        color: #617086;
        margin-bottom: 18px;
    }
    .reserve-loader__bar {
        position: relative;
        overflow: hidden;
        height: 8px;
        border-radius: 999px;
        background: rgba(15, 23, 42, 0.08);
    }
    .reserve-loader__bar::after {
        content: "";
        position: absolute;
        inset: 0;
        width: 40%;
        border-radius: inherit;
        background: linear-gradient(135deg, #f0a83a, #6f33cc);
        animation: reserve-loader-slide 1s linear infinite;
    }
    .dark-scheme .reserve-loader__card {
        background: #11131f;
        border: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 24px 50px rgba(0, 0, 0, 0.35);
    }
    .dark-scheme .reserve-loader__title {
        color: #ffffff;
    }
    .dark-scheme .reserve-loader__copy {
        color: #aeb7c4;
    }
    .dark-scheme .reserve-loader__bar {
        background: rgba(255, 255, 255, 0.08);
    }
    @keyframes reserve-loader-pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.08); }
    }
    @keyframes reserve-loader-slide {
        0% { transform: translateX(-120%); }
        100% { transform: translateX(260%); }
    }
</style>
@endpush

@section('content')
@include('frontend.partials.page-banner', ['title' => 'Buy PI'])

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
                        <h4>PI Reservation Details</h4>
                        <div class="nft__item_price">Level: {{ $level?->code ?? '-' }}</div>
                        <div class="nft__item_price">Reserve Criteria Range: {{ $reserve->meta['range_label'] ?? '-' }}</div>
                        <div class="nft__item_price">Reserve Percentage: {{ isset($reserve->meta['reserve_percentage']) ? number_format((float) $reserve->meta['reserve_percentage'], 3) . '%' : '-' }}</div>
                        <div class="nft__item_price">Reserve Amount: {{ number_format((float) ($reserve->amount ?? 0), 8) }} USDT</div>
                        <div class="nft__item_price">Profit Range: {{ $plan?->profit_min_percent }}% - {{ $plan?->profit_max_percent }}%</div>
                        <div class="nft__item_price">Max Sells Per Reserve: {{ $plan?->max_sells ?? 'Unlimited' }}</div>
                        <div class="nft__item_price">Daily Limit: {{ $plan?->max_sells_per_day ?? 'Unlimited' }}</div>
                        <div class="nft__item_price">Reserved At: {{ optional($reserve->confirmed_at)->format('M d, Y h:i A') }}</div>
                        <div class="nft__item_price">Sell Unlocks At: {{ optional($reserve->sell_available_at)->format('M d, Y h:i A') ?? 'Now' }}</div>
                        <p class="text-muted mt-3">The reserve amount was debited from wallet and moved to reserve balance when you confirmed this plan. After 6:00 AM, Sell PI unlocks. After a successful PI sell, that locked reserve amount and the profit will be returned to wallet.</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb30">
                <div class="nft__item s2">
                    <div class="nft__item_info">
                        <h4>Sell PI</h4>
                        @if (!$reserve->isSellUnlocked())
                            <div class="text-muted">Sell PI is locked until {{ optional($reserve->sell_available_at)->format('M d, Y h:i A') }}.</div>
                        @elseif (!$nftEnabled)
                            <div class="text-muted">PI selling is currently disabled.</div>
                        @elseif ($items->isEmpty())
                            <div class="text-muted">No PI items are available right now.</div>
                        @else
                            <form method="POST" action="{{ route('reserve.sell.submit') }}" class="form-border reserve-sell-form" id="sell-pi-form" data-loader-title="Selling PI" data-loader-copy="Please wait while your PI sell and profit are being processed.">
                                @csrf
                                @php
                                    $firstItem = $items->first();
                                    $firstImage = $firstItem
                                        ? (\Illuminate\Support\Str::startsWith($firstItem->image_path, 'frontend/')
                                            ? asset($firstItem->image_path)
                                            : asset('storage/' . $firstItem->image_path))
                                        : '';
                                    $reserveAmount = number_format((float) ($reserve->amount ?? 0), 8, '.', '');
                                @endphp
                                <div class="mb-3">
                                    <label class="form-label">Selected PI</label>
                                    <div class="d-flex align-items-center gap-3 p-2 border rounded">
                                        <img id="selected-nft-image" src="{{ $firstImage }}" alt="Selected PI" style="width:90px; height:90px; object-fit:cover; border-radius:10px;">
                                        <div>
                                            <div id="selected-nft-title" class="fw-semibold">{{ $firstItem?->title }}</div>
                                            <div class="small text-muted">Profit: {{ $plan?->profit_min_percent }}% - {{ $plan?->profit_max_percent }}%</div>
                                            <div class="small text-muted" id="selected-nft-price">Reserve Amount: {{ number_format((float) ($reserve->amount ?? 0), 8) }} USDT</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Choose PI Item</label>
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
                                                        <input class="form-check-input nft-select" type="radio" name="nft_item_id" value="{{ $item->id }}" data-title="{{ $item->title }}" data-image="{{ $imagePath }}" @checked($loop->first)>
                                                        <div class="fw-semibold">{{ $item->title }}</div>
                                                    </div>
                                                    <div class="mt-2">
                                                        <img src="{{ $imagePath }}" alt="{{ $item->title }}" class="img-fluid rounded" style="max-height:140px; width:100%; object-fit:cover;">
                                                    </div>
                                                    <div class="small text-muted mt-2">Reserve Amount: {{ number_format((float) ($reserve->amount ?? 0), 8) }} USDT</div>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Reserve Amount</label>
                                    <input id="sale_amount" type="number" step="0.00000001" name="sale_amount" class="form-control" value="{{ $reserveAmount }}" readonly>
                                </div>
                                <button type="submit" class="btn-main">Sell PI</button>
                            </form>
                            <p class="text-muted mt-2">Complete this sell to add the reserve amount and profit to your wallet.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="reserve-loader" id="reserve-loader" aria-hidden="true">
    <div class="reserve-loader__card">
        <img src="{{ asset('frontend/images/icon.png') }}" alt="Loading" class="reserve-loader__logo">
        <div class="reserve-loader__title" id="reserve-loader-title">Selling PI</div>
        <div class="reserve-loader__copy" id="reserve-loader-copy">Please wait while your PI sell and profit are being processed.</div>
        <div class="reserve-loader__bar"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const amountInput = document.getElementById('sale_amount');
    const selectedImage = document.getElementById('selected-nft-image');
    const selectedTitle = document.getElementById('selected-nft-title');
    const selectedPrice = document.getElementById('selected-nft-price');
    const reserveLoader = document.getElementById('reserve-loader');
    const reserveLoaderTitle = document.getElementById('reserve-loader-title');
    const reserveLoaderCopy = document.getElementById('reserve-loader-copy');

    document.querySelectorAll('.nft-select').forEach(function (radio) {
      radio.addEventListener('change', function () {
        if (selectedImage) {
          selectedImage.src = this.dataset.image || '';
        }
        if (selectedTitle) {
          selectedTitle.textContent = this.dataset.title || '';
        }
        if (selectedPrice && amountInput) {
          selectedPrice.textContent = `Reserve Amount: ${parseFloat(amountInput.value || 0).toFixed(8)} USDT`;
        }
      });
    });

    const preselected = document.querySelector('.nft-select:checked');
    if (preselected && selectedPrice && amountInput) {
      selectedPrice.textContent = `Reserve Amount: ${parseFloat(amountInput.value || 0).toFixed(8)} USDT`;
    }

    document.querySelectorAll('.reserve-sell-form').forEach(function (form) {
      form.addEventListener('submit', function () {
        if (reserveLoaderTitle) {
          reserveLoaderTitle.textContent = form.dataset.loaderTitle || 'Selling PI';
        }
        if (reserveLoaderCopy) {
          reserveLoaderCopy.textContent = form.dataset.loaderCopy || 'Please wait while your PI sell is being processed.';
        }
        if (reserveLoader) {
          reserveLoader.classList.add('is-visible');
          reserveLoader.setAttribute('aria-hidden', 'false');
        }
      });
    });
  });
</script>
@endpush
