@php
    $reserveConfirmedAt = $activeReserve->confirmed_at?->copy()->timezone('Asia/Dhaka')->format('M d, Y h:i A');
    $reserveSellAvailableAt = $activeReserve->sell_available_at?->copy()->timezone('Asia/Dhaka')->format('M d, Y h:i A');
    $selectedSellItem = $sellItems->first();
    $selectedSellItemImage = $selectedSellItem
        ? (\Illuminate\Support\Str::startsWith($selectedSellItem->image_path, 'frontend/')
            ? asset($selectedSellItem->image_path)
            : asset('storage/' . $selectedSellItem->image_path))
        : '';
@endphp

<div class="reserve-modal" id="reserve-sell-modal" aria-hidden="true">
    <div class="reserve-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="reserve-sell-modal-title">
        <button type="button" class="reserve-modal__close" aria-label="Close sell PI popup" data-close-reserve-modal>&times;</button>

        <div class="reserve-modal__head">
            <div class="reserve-modal__icon">
                <img src="{{ asset('frontend/images/icon.png') }}" alt="PI">
            </div>
            <div>
                <h3 class="reserve-modal__title" id="reserve-sell-modal-title">Sell PI</h3>
                <p class="reserve-modal__copy">
                    @if ($activeReserve->isSellUnlocked())
                        Your reserve is unlocked. Complete the sell and your locked reserve amount plus profit will be credited back to your wallet.
                    @else
                        Your reserve amount is locked. Sell PI will unlock at {{ $reserveSellAvailableAt }}.
                    @endif
                </p>
            </div>
        </div>

        <div class="reserve-modal__grid">
            <div class="reserve-modal__panel">
                <h5>PI Reservation Details</h5>
                <div class="reserve-modal__meta">
                    <div>Level: {{ $activeReserve->level?->code ?? '-' }}</div>
                    <div>Reserve Criteria Range: {{ $activeReserve->meta['range_label'] ?? '-' }}</div>
                    <div>Reserve Percentage: {{ isset($activeReserve->meta['reserve_percentage']) ? number_format((float) $activeReserve->meta['reserve_percentage'], 3) . '%' : '-' }}</div>
                    <div>Reserve Amount: {{ number_format((float) ($activeReserve->amount ?? 0), 8) }} USDT</div>
                    <div>Profit Range: {{ $activeReserve->plan?->profit_min_percent }}% - {{ $activeReserve->plan?->profit_max_percent }}%</div>
                    <div>Max Sells Per Reserve: {{ $activeReserve->plan?->max_sells ?? 'Unlimited' }}</div>
                    <div>Daily Limit: {{ $activeReserve->plan?->max_sells_per_day ?? 'Unlimited' }}</div>
                    <div>Reserved At: {{ $reserveConfirmedAt }}</div>
                    <div>Sell Unlocks At: {{ $reserveSellAvailableAt ?? 'Now' }}</div>
                </div>
            </div>

            <div class="reserve-modal__panel">
                @if (!$activeReserve->isSellUnlocked())
                    <h5>Sell PI Locked</h5>
                    <div class="text-muted">This reserve stays locked until {{ $reserveSellAvailableAt }}. After that time, Sell PI will be available here.</div>
                @elseif (!$nftEnabled)
                    <h5>Sell PI</h5>
                    <div class="text-muted">PI selling is currently disabled.</div>
                @endif

                @if ($activeReserve->isSellUnlocked() && $nftEnabled && $sellItems->isEmpty())
                    <h5>Sell PI</h5>
                    <div class="text-muted">No PI items are available right now.</div>
                @endif

                @if ($activeReserve->isSellUnlocked() && $nftEnabled && $sellItems->isNotEmpty())
                    <h5>Complete the PI Sell</h5>
                    <form method="POST" action="{{ route('reserve.sell.submit') }}" class="form-border reserve-sell-form" id="sell-pi-form" data-loader-title="Selling PI" data-loader-copy="Please wait while your PI sell and profit are being processed.">
                        @csrf
                        <input type="hidden" name="nft_item_id" value="{{ $selectedSellItem?->id }}">
                        <div class="reserve-modal__selected">
                            <img
                                id="reserve-selected-nft-image"
                                src="{{ $selectedSellItemImage }}"
                                alt="Selected PI"
                            >
                            <div>
                                <div id="reserve-selected-nft-title" class="fw-semibold">{{ $selectedSellItem?->title }}</div>
                                <div class="small text-muted">Profit: {{ $activeReserve->plan?->profit_min_percent }}% - {{ $activeReserve->plan?->profit_max_percent }}%</div>
                                <div class="small text-muted" id="reserve-selected-nft-price">Reserve Amount: {{ number_format((float) ($activeReserve->amount ?? 0), 8) }} USDT</div>
                            </div>
                        </div>

                        <div class="reserve-modal__nft-grid">
                            @if ($selectedSellItem)
                                <div class="reserve-modal__nft">
                                    <div class="fw-semibold">{{ $selectedSellItem->title }}</div>
                                    <img src="{{ $selectedSellItemImage }}" alt="{{ $selectedSellItem->title }}">
                                    <div class="small text-muted mt-2">Reserve Amount: {{ number_format((float) ($activeReserve->amount ?? 0), 8) }} USDT</div>
                                </div>
                            @endif
                        </div>

                        <div class="mt-3">
                            <label class="form-label">Reserve Amount</label>
                            <input id="reserve-sale-amount" type="number" step="0.00000001" name="sale_amount" class="form-control" value="{{ number_format((float) ($activeReserve->amount ?? 0), 8, '.', '') }}" readonly>
                        </div>

                        <div class="reserve-modal__actions">
                            <button type="submit" class="btn-main">Sell PI</button>
                            <button type="button" class="btn-border" data-close-reserve-modal>Close</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

