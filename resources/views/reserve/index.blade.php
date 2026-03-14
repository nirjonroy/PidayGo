@extends('layouts.frontend')

@push('styles')
<style>
    .reserve-options-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 18px;
    }
    .reserve-option-card {
        border-radius: 18px;
        padding: 20px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
        height: 100%;
    }
    .reserve-option-label {
        display: inline-flex;
        margin-bottom: 12px;
        padding: 6px 10px;
        border-radius: 999px;
        background: rgba(240, 168, 58, 0.14);
        color: #fbb040;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }
    .reserve-option-amount {
        font-size: 28px;
        font-weight: 800;
        line-height: 1.1;
        margin-bottom: 10px;
    }
    .reserve-option-meta {
        display: grid;
        gap: 8px;
        color: #aeb7c4;
        font-size: 14px;
        margin-bottom: 16px;
    }
    .reserve-option-form {
        margin: 0;
    }
    .reserve-flow-note {
        color: #aeb7c4;
        font-size: 14px;
        margin-top: 12px;
    }
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
        background: #11131f;
        border: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 24px 50px rgba(0, 0, 0, 0.35);
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
        color: #ffffff;
        margin-bottom: 8px;
    }
    .reserve-loader__copy {
        color: #aeb7c4;
        margin-bottom: 18px;
    }
    .reserve-loader__bar {
        position: relative;
        overflow: hidden;
        height: 8px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.08);
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
@include('frontend.partials.page-banner', ['title' => 'Reserve PI'])

<section aria-label="section">
    <div class="container">
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif
        @if (!empty($activeReserve))
            <div class="alert alert-warning d-flex justify-content-between align-items-center">
                <span>You already have an active reserve. Continue to the Buy PI page and complete the sell.</span>
                <a class="btn btn-sm btn-primary" href="{{ route('reserve.sell.form') }}">Go to Buy PI</a>
            </div>
        @elseif ($reservedToday)
            <div class="alert alert-warning">
                You already used today's reserve chance. Please come back tomorrow.
            </div>
        @endif

        <div class="row">
            <div class="col-lg-5 mb30">
                <div class="nft__item s2">
                    <div class="nft__item_info">
                        <h4>Reserve Summary</h4>
                        <div class="nft__item_price">Wallet Balance: {{ number_format($walletBalance, 8) }} USDT</div>
                        <div class="nft__item_price">Reserve Account Balance: {{ number_format($reserveAccountBalance, 8) }} USDT</div>
                        @if ($level)
                            <div class="nft__item_price">Current Level: {{ $level->code }}</div>
                            <div class="nft__item_price">Eligible Reserve Options: {{ $plans->count() }}</div>
                        @else
                            <div class="text-muted">No active level found.</div>
                        @endif
                        <p class="reserve-flow-note">
                            Reserve works once per day. No amount is locked when you reserve. After a successful PI sell, the reserve amount and profit are credited to your wallet.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-lg-7 mb30">
                <div class="nft__item s2">
                    <div class="nft__item_info">
                        <h4>Reservation Options</h4>
                        @if (!$reserveEnabled)
                            <div class="text-muted">Reserve is currently disabled.</div>
                        @elseif (!$level)
                            <div class="text-muted">You do not have an eligible level yet.</div>
                        @elseif ($plans->isEmpty())
                            <div class="text-muted">No reserve options are available for your current level yet.</div>
                        @elseif (!empty($activeReserve))
                            <div class="text-muted">Your current reserve is already ready. Open the Buy PI page to sell it.</div>
                            <a class="btn-main mt-2" href="{{ route('reserve.sell.form') }}">Go to Buy PI</a>
                        @elseif ($reservedToday)
                            <div class="text-muted">Today's reserve chance is already used.</div>
                        @else
                            <div class="reserve-options-grid">
                                @foreach ($plans as $plan)
                                    <div class="reserve-option-card">
                                        <span class="reserve-option-label">{{ $plan->level?->code ?? 'Reserve' }}</span>
                                        <div class="reserve-option-amount">{{ number_format((float) $plan->reserve_amount, 8) }} USDT</div>
                                        <div class="reserve-option-meta">
                                            <div>Profit: {{ $plan->profit_min_percent }}% - {{ $plan->profit_max_percent }}%</div>
                                            <div>Daily Reserve Limit: 1 time</div>
                                            <div>After reserve, you will continue to Buy PI and sell once.</div>
                                        </div>
                                        <form method="POST" action="{{ route('reserve.confirm') }}" class="reserve-option-form reserve-start-form">
                                            @csrf
                                            <input type="hidden" name="reserve_plan_id" value="{{ $plan->id }}">
                                            <button type="submit" class="btn-main w-100">Reserve Now</button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12 mb30">
                <div class="nft__item s2">
                    <div class="nft__item_info">
                        <h4>Recent Reserve Ledger</h4>
                        @if ($recentReserveLedgers->isEmpty())
                            <div class="text-muted">No reserve ledger entries found yet.</div>
                        @else
                            <div class="table-responsive reserve-table-card mb-0">
                                <table class="table table-borderless table-striped align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Change</th>
                                            <th>Reason</th>
                                            <th>Reference</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentReserveLedgers as $ledger)
                                            <tr>
                                                <td class="{{ (float) $ledger->change < 0 ? 'text-danger' : 'text-success' }}">
                                                    {{ number_format((float) $ledger->change, 8) }}
                                                </td>
                                                <td>{{ \Illuminate\Support\Str::headline(str_replace('_', ' ', $ledger->reason)) }}</td>
                                                <td>
                                                    @if ($ledger->ref_type && $ledger->ref_id)
                                                        {{ \Illuminate\Support\Str::headline(str_replace('_', ' ', $ledger->ref_type)) }} #{{ $ledger->ref_id }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>{{ optional($ledger->created_at)->format('M d, Y h:i A') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12 mb30">
                <div class="nft__item s2">
                    <div class="nft__item_info">
                        <h4>Recent PI Sell Income</h4>
                        @if ($recentReserveSales->isEmpty())
                            <div class="text-muted">No PI sell income found yet.</div>
                        @else
                            <div class="table-responsive reserve-table-card mb-0">
                                <table class="table table-borderless table-striped align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>PI Item</th>
                                            <th>Reserve Amount</th>
                                            <th>Profit %</th>
                                            <th>Income</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentReserveSales as $sale)
                                            <tr>
                                                <td>{{ $sale->nftItem?->title ?: ('PI #' . $sale->nft_item_id) }}</td>
                                                <td>{{ number_format((float) $sale->sale_amount, 8) }}</td>
                                                <td>{{ number_format((float) $sale->profit_percent, 3) }}%</td>
                                                <td class="text-success">{{ number_format((float) $sale->profit_amount, 8) }}</td>
                                                <td>{{ \Illuminate\Support\Str::headline((string) $sale->status) }}</td>
                                                <td>{{ optional($sale->created_at)->format('M d, Y h:i A') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
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
        <div class="reserve-loader__title">Preparing Buy PI</div>
        <div class="reserve-loader__copy">Please wait while your reserve is being created.</div>
        <div class="reserve-loader__bar"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var reserveLoader = document.getElementById('reserve-loader');

        document.querySelectorAll('.reserve-start-form').forEach(function (form) {
            form.addEventListener('submit', function () {
                if (reserveLoader) {
                    reserveLoader.classList.add('is-visible');
                    reserveLoader.setAttribute('aria-hidden', 'false');
                }
            });
        });
    });
</script>
@endpush
