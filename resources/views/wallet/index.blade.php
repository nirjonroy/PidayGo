@extends('layouts.frontend')

@push('styles')
<style>
    .wallet-shell {
        padding: 24px 0 54px;
        background:
            radial-gradient(circle at top left, rgba(var(--secondary-color-rgb), 0.18), transparent 34%),
            linear-gradient(180deg, rgba(var(--primary-color-rgb), 0.06), rgba(255, 255, 255, 0) 320px);
    }
    .wallet-stack,
    .wallet-summary-grid,
    .wallet-ledger-mobile {
        display: grid;
        gap: 24px;
    }
    .wallet-overview {
        display: grid;
        gap: 24px;
        grid-template-columns: minmax(0, 1.04fr) minmax(0, 0.96fr);
        align-items: start;
    }
    .wallet-panel {
        position: relative;
        padding: 28px;
        border-radius: 28px;
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid rgba(15, 23, 42, 0.08);
        box-shadow: 0 24px 52px rgba(15, 23, 42, 0.08);
        overflow: hidden;
    }
    .wallet-panel::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(var(--secondary-color-rgb), 0.08), rgba(var(--primary-color-rgb), 0.04));
        pointer-events: none;
    }
    .wallet-panel > * {
        position: relative;
        z-index: 1;
    }
    .wallet-meta-label {
        margin-bottom: 8px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #64748b;
    }
    .wallet-section-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 20px;
    }
    .wallet-section-title {
        margin: 0;
        font-size: clamp(24px, 3vw, 30px);
        line-height: 1.1;
        font-weight: 800;
        color: #0f172a;
    }
    .wallet-section-copy {
        margin: 8px 0 0;
        color: #64748b;
    }
    .wallet-balance-card,
    .wallet-summary-card,
    .wallet-ledger-mobile-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
    }
    .wallet-balance-value,
    .wallet-summary-card__value {
        margin: 0;
        line-height: 1.04;
        font-weight: 800;
        color: #0f172a;
        word-break: break-word;
    }
    .wallet-balance-value {
        font-size: clamp(34px, 4vw, 48px);
    }
    .wallet-summary-card__value {
        font-size: clamp(24px, 3vw, 32px);
    }
    .wallet-amount-line {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    .wallet-usdt-icon {
        min-width: 42px;
        height: 42px;
        padding: 0 10px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #ffffff;
        background: linear-gradient(135deg, #16a34a, #0f766e);
        box-shadow: 0 14px 22px rgba(15, 118, 110, 0.18);
    }
    .wallet-amount-line.is-hero .wallet-usdt-icon {
        min-width: 52px;
        height: 52px;
        font-size: 11px;
    }
    .wallet-amount-line.is-table {
        gap: 8px;
    }
    .wallet-amount-line.is-table .wallet-usdt-icon {
        min-width: 34px;
        height: 34px;
        padding: 0 8px;
        font-size: 9px;
        box-shadow: none;
    }
    .wallet-balance-copy,
    .wallet-summary-card__copy,
    .wallet-level-copy,
    .wallet-card-copy,
    .wallet-ledger-subtext,
    .wallet-table-subtext,
    .wallet-ledger-mobile-meta span,
    .wallet-stake-mobile-meta span {
        font-size: 13px;
        color: #64748b;
    }
    .wallet-balance-copy {
        margin-top: 10px;
        font-size: 15px;
        line-height: 1.7;
    }
    .wallet-balance-icon,
    .wallet-summary-card__icon {
        width: 52px;
        height: 52px;
        flex: 0 0 52px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        color: #ffffff;
        background: linear-gradient(135deg, #f0a83a, #6f33cc);
        box-shadow: 0 14px 26px rgba(111, 51, 204, 0.2);
    }
    .wallet-summary-card__icon.is-alt {
        background: linear-gradient(135deg, #0ea5e9, #2563eb);
    }
    .wallet-summary-card__icon.is-income {
        background: linear-gradient(135deg, #10b981, #0f766e);
    }
    .wallet-card,
    .wallet-summary-card,
    .wallet-level-card,
    .wallet-ledger-wrap,
    .wallet-table-wrap,
    .wallet-ledger-mobile-card,
    .wallet-empty {
        border-radius: 22px;
        background: rgba(255, 255, 255, 0.76);
        border: 1px solid rgba(15, 23, 42, 0.08);
    }
    .wallet-card,
    .wallet-level-card,
    .wallet-ledger-mobile-card {
        padding: 18px;
    }
    .wallet-card__value,
    .wallet-level-card__value,
    .wallet-plan-meta__value,
    .wallet-table-title,
    .wallet-ledger-type,
    .wallet-ledger-source {
        color: #0f172a;
        font-weight: 800;
        word-break: break-word;
    }
    .wallet-card__value {
        font-size: 24px;
        line-height: 1.08;
    }
    .wallet-level-card__value {
        font-size: 20px;
        line-height: 1.15;
    }
    .wallet-level-copy {
        margin-top: 16px;
        font-size: 14px;
    }
    .wallet-ledger-wrap,
    .wallet-table-wrap {
        overflow: hidden;
    }
    .wallet-ledger-table,
    .wallet-table {
        margin-bottom: 0;
    }
    .wallet-ledger-table thead th,
    .wallet-table thead th {
        padding: 16px 18px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #64748b;
        border-bottom: 1px solid rgba(15, 23, 42, 0.08);
        background: rgba(255, 255, 255, 0.3);
    }
    .wallet-ledger-table tbody td,
    .wallet-table tbody td {
        padding: 18px;
        vertical-align: middle;
        border-color: rgba(15, 23, 42, 0.08);
    }
    .wallet-ledger-source {
        display: block;
        margin-bottom: 4px;
        font-size: 14px;
    }
    .wallet-ledger-amount {
        font-size: 16px;
        font-weight: 800;
    }
    .wallet-ledger-amount.is-credit {
        color: #059669;
    }
    .wallet-ledger-amount.is-debit {
        color: #dc2626;
    }
    .wallet-ledger-mobile,
    {
        display: none;
        gap: 14px;
    }
    .wallet-ledger-mobile-meta {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        padding-top: 10px;
        margin-top: 10px;
        border-top: 1px solid rgba(15, 23, 42, 0.08);
        font-size: 14px;
    }
    .wallet-status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
    }
    .wallet-status-badge {
        padding: 7px 11px;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }
    .wallet-status-badge.is-active {
        background: rgba(16, 185, 129, 0.12);
        color: #047857;
    }
    .wallet-status-badge.is-completed {
        background: rgba(59, 130, 246, 0.12);
        color: #1d4ed8;
    }
    .wallet-status-badge.is-cancelled,
    .wallet-status-badge.is-default {
        background: rgba(148, 163, 184, 0.16);
        color: #475569;
    }
    .wallet-empty {
        padding: 34px 24px;
        text-align: center;
        color: #64748b;
        border-style: dashed;
        border-width: 1px;
        border-color: rgba(15, 23, 42, 0.14);
    }
    .dark-scheme .wallet-shell {
        background:
            radial-gradient(circle at top left, rgba(var(--secondary-color-rgb), 0.18), transparent 34%),
            linear-gradient(180deg, rgba(10, 14, 22, 0.8), rgba(10, 14, 22, 0) 360px);
    }
    .dark-scheme .wallet-panel {
        background: rgba(10, 14, 22, 0.86);
        border-color: rgba(255, 255, 255, 0.08);
        box-shadow: 0 26px 54px rgba(0, 0, 0, 0.34);
    }
    .dark-scheme .wallet-card,
    .dark-scheme .wallet-summary-card,
    .dark-scheme .wallet-level-card,
    .dark-scheme .wallet-ledger-wrap,
    .dark-scheme .wallet-table-wrap,
    .dark-scheme .wallet-ledger-mobile-card,
    .dark-scheme .wallet-empty {
        background: rgba(255, 255, 255, 0.04);
        border-color: rgba(255, 255, 255, 0.08);
    }
    .dark-scheme .wallet-meta-label,
    .dark-scheme .wallet-balance-copy,
    .dark-scheme .wallet-summary-card__copy,
    .dark-scheme .wallet-level-copy,
    .dark-scheme .wallet-card-copy,
    .dark-scheme .wallet-section-copy,
    .dark-scheme .wallet-ledger-subtext,
    .dark-scheme .wallet-table-subtext,
    .dark-scheme .wallet-ledger-mobile-meta span,
    .dark-scheme .wallet-stake-mobile-meta span,
    .dark-scheme .wallet-ledger-table thead th,
    .dark-scheme .wallet-table thead th {
        color: #94a3b8;
    }
    .dark-scheme .wallet-balance-value,
    .dark-scheme .wallet-summary-card__value,
    .dark-scheme .wallet-card__value,
    .dark-scheme .wallet-level-card__value,
    .dark-scheme .wallet-section-title,
    .dark-scheme .wallet-table-title,
    .dark-scheme .wallet-ledger-type,
    .dark-scheme .wallet-ledger-source,
    .dark-scheme .wallet-ledger-mobile-meta strong {
        color: #f8fafc;
    }
    .dark-scheme .wallet-ledger-table thead th,
    .dark-scheme .wallet-table thead th {
        background: rgba(255, 255, 255, 0.02);
        border-color: rgba(255, 255, 255, 0.08);
    }
    .dark-scheme .wallet-ledger-table tbody td,
    .dark-scheme .wallet-table tbody td,
    .dark-scheme .wallet-ledger-mobile-meta {
        border-color: rgba(255, 255, 255, 0.08);
    }
    .dark-scheme .wallet-status-badge.is-active {
        color: #86efac;
        background: rgba(16, 185, 129, 0.16);
    }
    .dark-scheme .wallet-status-badge.is-completed {
        color: #93c5fd;
        background: rgba(59, 130, 246, 0.18);
    }
    .dark-scheme .wallet-status-badge.is-cancelled,
    .dark-scheme .wallet-status-badge.is-default {
        color: #cbd5e1;
        background: rgba(148, 163, 184, 0.18);
    }
    @media (max-width: 991.98px) {
        .wallet-overview {
            grid-template-columns: 1fr;
        }
        .wallet-panel {
            padding: 24px;
            border-radius: 24px;
        }
    }
    @media (max-width: 767.98px) {
        .wallet-shell {
            padding-bottom: 106px;
        }
        .wallet-section-head {
            flex-direction: column;
            align-items: stretch;
        }
        .wallet-ledger-wrap,
        .wallet-table-wrap {
            display: none;
        }
        .wallet-ledger-mobile,
        {
            display: grid;
        }
    }
    @media (max-width: 575.98px) {
        .wallet-panel {
            padding: 20px;
            border-radius: 22px;
        }
        .wallet-balance-card,
        .wallet-summary-card,
        .wallet-ledger-mobile-top,
        .wallet-ledger-mobile-meta {
            flex-direction: column;
        }
    }
</style>
@endpush

@section('content')
@include('frontend.partials.page-banner', ['title' => 'Wallet'])

<section class="wallet-shell" aria-label="Wallet overview">
    <div class="container">
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <div class="wallet-stack">
            <div class="wallet-overview">
                <div class="wallet-panel">
                    <div class="wallet-balance-card">
                        <div>
                            <div class="wallet-meta-label">Wallet Balance</div>
                            <div class="wallet-amount-line is-hero">
                                <span class="wallet-usdt-icon" aria-hidden="true">USDT</span>
                                <p class="wallet-balance-value">{{ number_format($walletBalance, 4) }}</p>
                            </div>
                            <div class="wallet-balance-copy">Main wallet balance available for your account activity.</div>
                        </div>
                        <span class="wallet-balance-icon" aria-hidden="true"><i class="fa fa-wallet"></i></span>
                    </div>
                </div>

                <div class="wallet-panel">
                    <div class="wallet-summary-grid">
                        <div class="wallet-summary-card">
                            <div>
                                <div class="wallet-meta-label">Reserve Balance</div>
                                <div class="wallet-amount-line">
                                    <span class="wallet-usdt-icon" aria-hidden="true">USDT</span>
                                    <p class="wallet-summary-card__value">{{ number_format($reservedBalance, 4) }}</p>
                                </div>
                                <div class="wallet-summary-card__copy">Current reserve account balance available for reserve activity.</div>
                            </div>
                            <span class="wallet-summary-card__icon is-alt" aria-hidden="true"><i class="fa fa-lock"></i></span>
                        </div>
                        <div class="wallet-summary-card">
                            <div>
                                <div class="wallet-meta-label">Today Earnings</div>
                                <div class="wallet-amount-line">
                                    <span class="wallet-usdt-icon" aria-hidden="true">USDT</span>
                                    <p class="wallet-summary-card__value">{{ number_format($todayEarnings, 4) }}</p>
                                </div>
                                <div class="wallet-summary-card__copy">Profit credited to your wallet today from reserve, team, and stake sources.</div>
                            </div>
                            <span class="wallet-summary-card__icon" aria-hidden="true"><i class="fa fa-line-chart"></i></span>
                        </div>
                        <div class="wallet-summary-card">
                            <div>
                                <div class="wallet-meta-label">Cumulative Income</div>
                                <div class="wallet-amount-line">
                                    <span class="wallet-usdt-icon" aria-hidden="true">USDT</span>
                                    <p class="wallet-summary-card__value">{{ number_format($cumulativeIncome, 4) }}</p>
                                </div>
                                <div class="wallet-summary-card__copy">Your total credited earnings across all wallet income types.</div>
                            </div>
                            <span class="wallet-summary-card__icon is-income" aria-hidden="true"><i class="fa fa-table"></i></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="wallet-panel">
                <div class="wallet-section-head">
                    <div>
                        <div class="wallet-meta-label">Wallet Activity</div>
                        <h2 class="wallet-section-title">Recent Wallet Ledger</h2>
                        <p class="wallet-section-copy">Latest credits and debits from your account, including reserve profits, chain income, and staking rewards.</p>
                    </div>
                </div>

                @if ($recentWalletLedgers->isEmpty())
                    <div class="wallet-empty">No ledger entries found yet.</div>
                @else
                    <div class="wallet-ledger-wrap">
                        <table class="table wallet-ledger-table align-middle">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Reference</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentWalletLedgers as $ledger)
                                    @php
                                        $amount = (float) $ledger->amount;
                                        $referenceLabel = $ledger->reference_type && $ledger->reference_id ? class_basename($ledger->reference_type) . ' #' . $ledger->reference_id : '-';
                                        $chainSourceUser = $ledger->relationLoaded('chainSourceUser') ? $ledger->getRelation('chainSourceUser') : null;
                                        $chainSourceLabel = null;
                                        if ($ledger->type === 'chain_income') {
                                            $sourceUserId = (int) data_get($ledger->meta, 'source_user_id', 0);
                                            $chainSourceLabel = $chainSourceUser ? ($chainSourceUser->profile?->username ?: $chainSourceUser->name) : ($sourceUserId > 0 ? 'User #' . $sourceUserId : null);
                                        }
                                    @endphp
                                    <tr>
                                        <td>
                                            <span class="wallet-ledger-type">{{ \Illuminate\Support\Str::headline(str_replace('_', ' ', $ledger->type)) }}</span>
                                            @if (is_array($ledger->meta) && isset($ledger->meta['day']))
                                                <span class="wallet-ledger-subtext">Reward day {{ $ledger->meta['day'] }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="wallet-amount-line is-table wallet-ledger-amount {{ $amount < 0 ? 'is-debit' : 'is-credit' }}">
                                                <span class="wallet-usdt-icon" aria-hidden="true">USDT</span>
                                                <span>{{ $amount >= 0 ? '+' : '' }}{{ number_format($amount, 8) }}</span>
                                            </span>
                                        </td>
                                        <td>
                                            @if ($chainSourceLabel)
                                                <span class="wallet-ledger-source">{{ $chainSourceLabel }}</span>
                                            @endif
                                            <span class="wallet-ledger-subtext">{{ $referenceLabel }}</span>
                                        </td>
                                        <td><span class="wallet-ledger-subtext">{{ optional($ledger->created_at)->format('M d, Y h:i A') }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="wallet-ledger-mobile">
                        @foreach ($recentWalletLedgers as $ledger)
                            @php
                                $amount = (float) $ledger->amount;
                                $referenceLabel = $ledger->reference_type && $ledger->reference_id ? class_basename($ledger->reference_type) . ' #' . $ledger->reference_id : '-';
                                $chainSourceUser = $ledger->relationLoaded('chainSourceUser') ? $ledger->getRelation('chainSourceUser') : null;
                                $chainSourceLabel = null;
                                if ($ledger->type === 'chain_income') {
                                    $sourceUserId = (int) data_get($ledger->meta, 'source_user_id', 0);
                                    $chainSourceLabel = $chainSourceUser ? ($chainSourceUser->profile?->username ?: $chainSourceUser->name) : ($sourceUserId > 0 ? 'User #' . $sourceUserId : null);
                                }
                            @endphp
                            <div class="wallet-ledger-mobile-card">
                                <div class="wallet-ledger-mobile-top">
                                    <div>
                                        <div class="wallet-ledger-type">{{ \Illuminate\Support\Str::headline(str_replace('_', ' ', $ledger->type)) }}</div>
                                        @if (is_array($ledger->meta) && isset($ledger->meta['day']))
                                            <span class="wallet-ledger-subtext">Reward day {{ $ledger->meta['day'] }}</span>
                                        @endif
                                    </div>
                                    <span class="wallet-amount-line is-table wallet-ledger-amount {{ $amount < 0 ? 'is-debit' : 'is-credit' }}">
                                        <span class="wallet-usdt-icon" aria-hidden="true">USDT</span>
                                        <span>{{ $amount >= 0 ? '+' : '' }}{{ number_format($amount, 8) }}</span>
                                    </span>
                                </div>
                                <div class="wallet-ledger-mobile-meta"><span>Reference</span><strong>{{ $referenceLabel }}</strong></div>
                                @if ($chainSourceLabel)
                                    <div class="wallet-ledger-mobile-meta"><span>From</span><strong>{{ $chainSourceLabel }}</strong></div>
                                @endif
                                <div class="wallet-ledger-mobile-meta"><span>Date</span><strong>{{ optional($ledger->created_at)->format('M d, Y h:i A') }}</strong></div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
