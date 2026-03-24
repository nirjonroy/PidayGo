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
    .wallet-plan-grid,
    .wallet-ledger-mobile,
    .wallet-stake-mobile {
        display: grid;
        gap: 24px;
    }
    .wallet-overview,
    .wallet-level-grid {
        display: grid;
        gap: 24px;
        grid-template-columns: minmax(0, 1.04fr) minmax(0, 0.96fr);
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
    .wallet-action-grid,
    .wallet-glance-grid,
    .wallet-plan-meta,
    .wallet-plan-form {
        display: grid;
        gap: 12px;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    .wallet-action-grid {
        margin: 22px 0;
    }
    .wallet-action-btn {
        width: 100%;
        min-height: 52px;
        padding: 12px 16px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        font-size: 14px;
        font-weight: 800;
        text-align: center;
    }
    .wallet-card,
    .wallet-summary-card,
    .wallet-level-card,
    .wallet-plan-card,
    .wallet-ledger-wrap,
    .wallet-table-wrap,
    .wallet-ledger-mobile-card,
    .wallet-stake-mobile-card,
    .wallet-empty {
        border-radius: 22px;
        background: rgba(255, 255, 255, 0.76);
        border: 1px solid rgba(15, 23, 42, 0.08);
    }
    .wallet-card,
    .wallet-level-card,
    .wallet-plan-card,
    .wallet-ledger-mobile-card,
    .wallet-stake-mobile-card {
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
    .wallet-level-grid {
        grid-template-columns: repeat(4, minmax(0, 1fr));
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
    .wallet-stake-mobile {
        display: none;
        gap: 14px;
    }
    .wallet-ledger-mobile-meta,
    .wallet-stake-mobile-meta {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        padding-top: 10px;
        margin-top: 10px;
        border-top: 1px solid rgba(15, 23, 42, 0.08);
        font-size: 14px;
    }
    .wallet-plan-grid {
        grid-template-columns: repeat(auto-fit, minmax(270px, 1fr));
        gap: 18px;
    }
    .wallet-plan-card__top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
    }
    .wallet-plan-card__title {
        margin: 0;
        font-size: 22px;
        line-height: 1.15;
        font-weight: 800;
        color: #0f172a;
    }
    .wallet-plan-card__badge,
    .wallet-status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
    }
    .wallet-plan-card__badge {
        gap: 8px;
        margin-top: 8px;
        padding: 7px 12px;
        background: rgba(var(--primary-color-rgb), 0.12);
        color: #334155;
    }
    .wallet-plan-card__rate {
        font-size: 15px;
        font-weight: 800;
        color: #059669;
        white-space: nowrap;
    }
    .wallet-plan-meta__item {
        padding: 14px 16px;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.58);
        border: 1px solid rgba(15, 23, 42, 0.08);
    }
    .wallet-plan-meta__value {
        margin-top: 8px;
        font-size: 16px;
        line-height: 1.3;
    }
    .wallet-plan-form {
        grid-template-columns: minmax(0, 1fr) auto;
        align-items: stretch;
        margin-top: 18px;
    }
    .wallet-plan-form .form-control {
        min-height: 50px;
        border-radius: 14px;
        border-color: rgba(15, 23, 42, 0.12);
        background: rgba(255, 255, 255, 0.88);
        box-shadow: none;
    }
    .wallet-plan-form .btn-main {
        min-width: 114px;
        border-radius: 14px;
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
    .dark-scheme .wallet-plan-card,
    .dark-scheme .wallet-plan-meta__item,
    .dark-scheme .wallet-ledger-wrap,
    .dark-scheme .wallet-table-wrap,
    .dark-scheme .wallet-ledger-mobile-card,
    .dark-scheme .wallet-stake-mobile-card,
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
    .dark-scheme .wallet-plan-card__title,
    .dark-scheme .wallet-plan-meta__value,
    .dark-scheme .wallet-table-title,
    .dark-scheme .wallet-ledger-type,
    .dark-scheme .wallet-ledger-source,
    .dark-scheme .wallet-ledger-mobile-meta strong,
    .dark-scheme .wallet-stake-mobile-meta strong {
        color: #f8fafc;
    }
    .dark-scheme .wallet-plan-card__badge {
        color: #e2e8f0;
        background: rgba(255, 255, 255, 0.08);
    }
    .dark-scheme .wallet-ledger-table thead th,
    .dark-scheme .wallet-table thead th {
        background: rgba(255, 255, 255, 0.02);
        border-color: rgba(255, 255, 255, 0.08);
    }
    .dark-scheme .wallet-ledger-table tbody td,
    .dark-scheme .wallet-table tbody td,
    .dark-scheme .wallet-ledger-mobile-meta,
    .dark-scheme .wallet-stake-mobile-meta {
        border-color: rgba(255, 255, 255, 0.08);
    }
    .dark-scheme .wallet-plan-form .form-control {
        color: #f8fafc;
        border-color: rgba(255, 255, 255, 0.1);
        background: rgba(255, 255, 255, 0.06);
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
        .wallet-level-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
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
        .wallet-action-grid,
        .wallet-glance-grid,
        .wallet-plan-meta,
        .wallet-plan-form,
        .wallet-level-grid {
            grid-template-columns: 1fr;
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
        .wallet-stake-mobile {
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
        .wallet-ledger-mobile-meta,
        .wallet-stake-mobile-meta {
            flex-direction: column;
        }
        .wallet-plan-card__top {
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
                            <div class="wallet-meta-label">Wallet Hub</div>
                            <p class="wallet-balance-value">{{ number_format($walletBalance, 4) }}</p>
                            <div class="wallet-balance-copy">Use your wallet to deposit, withdraw, reserve, and manage active profit actions from one place.</div>
                        </div>
                        <span class="wallet-balance-icon" aria-hidden="true"><i class="fa fa-wallet"></i></span>
                    </div>

                    <div class="wallet-action-grid">
                        <a href="{{ route('wallet.deposit') }}" class="btn-main wallet-action-btn"><i class="fa fa-arrow-circle-down" aria-hidden="true"></i><span>Deposit</span></a>
                        <a href="{{ route('wallet.withdrawals') }}" class="btn-main btn-light wallet-action-btn"><i class="fa fa-arrow-circle-up" aria-hidden="true"></i><span>Withdraw</span></a>
                        <a href="{{ route('reserve.index') }}" class="btn-main wallet-action-btn"><i class="fa fa-lock" aria-hidden="true"></i><span>Reserve</span></a>
                        @if ($canSell)
                            <a href="{{ route('sell.index') }}" class="btn-main btn-light wallet-action-btn"><i class="fa fa-line-chart" aria-hidden="true"></i><span>Sell PI</span></a>
                        @else
                            <a href="{{ route('stake.index') }}" class="btn-main btn-light wallet-action-btn"><i class="fa fa-line-chart" aria-hidden="true"></i><span>Stake</span></a>
                        @endif
                    </div>

                    <div class="wallet-glance-grid">
                        <div class="wallet-card">
                            <div class="wallet-meta-label">Current Level</div>
                            <div class="wallet-card__value">{{ $level?->code ?? 'Not set' }}</div>
                            <div class="wallet-card-copy">Resolved from your deposit and chain qualification.</div>
                        </div>
                        <div class="wallet-card">
                            <div class="wallet-meta-label">Sell Status</div>
                            <div class="wallet-card__value">{{ $canSell ? 'Ready' : 'Idle' }}</div>
                            <div class="wallet-card-copy">{{ $canSell ? 'You have an active reserve ready to sell.' : 'No active reserve is waiting for sell right now.' }}</div>
                        </div>
                    </div>
                </div>

                <div class="wallet-panel">
                    <div class="wallet-summary-grid">
                        <div class="wallet-summary-card">
                            <div>
                                <div class="wallet-meta-label">Reserve Balance</div>
                                <p class="wallet-summary-card__value">{{ number_format($reservedBalance, 4) }}</p>
                                <div class="wallet-summary-card__copy">Current reserve account balance available for reserve activity.</div>
                            </div>
                            <span class="wallet-summary-card__icon is-alt" aria-hidden="true"><i class="fa fa-lock"></i></span>
                        </div>
                        <div class="wallet-summary-card">
                            <div>
                                <div class="wallet-meta-label">Today Earnings</div>
                                <p class="wallet-summary-card__value">{{ number_format($todayEarnings, 4) }}</p>
                                <div class="wallet-summary-card__copy">Profit credited to your wallet today from reserve, team, and stake sources.</div>
                            </div>
                            <span class="wallet-summary-card__icon" aria-hidden="true"><i class="fa fa-line-chart"></i></span>
                        </div>
                        <div class="wallet-summary-card">
                            <div>
                                <div class="wallet-meta-label">Cumulative Income</div>
                                <p class="wallet-summary-card__value">{{ number_format($cumulativeIncome, 4) }}</p>
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
                        <div class="wallet-meta-label">Level Snapshot</div>
                        <h2 class="wallet-section-title">Current Level Info</h2>
                        <p class="wallet-section-copy">Your active level controls the deposit band, reservation range, and qualification rules used across the system.</p>
                    </div>
                </div>

                @if ($level)
                    <div class="wallet-level-grid">
                        <div class="wallet-level-card">
                            <div class="wallet-meta-label">Level</div>
                            <div class="wallet-level-card__value">{{ $level->code }}</div>
                        </div>
                        <div class="wallet-level-card">
                            <div class="wallet-meta-label">Deposit Range</div>
                            <div class="wallet-level-card__value">{{ number_format((float) $level->min_deposit, 4) }} - {{ number_format((float) $level->max_deposit, 4) }}</div>
                        </div>
                        <div class="wallet-level-card">
                            <div class="wallet-meta-label">Income Range</div>
                            <div class="wallet-level-card__value">{{ number_format((float) $level->income_min_percent, 3) }}% - {{ number_format((float) $level->income_max_percent, 3) }}%</div>
                        </div>
                        <div class="wallet-level-card">
                            <div class="wallet-meta-label">Reservation Range</div>
                            <div class="wallet-level-card__value">{{ number_format((float) $level->min_reservation, 4) }} - {{ number_format((float) $level->max_reservation, 4) }}</div>
                        </div>
                    </div>
                    <div class="wallet-level-copy">
                        Chain requirements: A {{ (int) $level->req_chain_a }}, B {{ (int) $level->req_chain_b }}, C {{ (int) $level->req_chain_c }}.
                        <br>
                        Chain income %: A {{ $level->chain_income_a_percent !== null ? number_format((float) $level->chain_income_a_percent, 3) . '%' : 'Default' }},
                        B {{ $level->chain_income_b_percent !== null ? number_format((float) $level->chain_income_b_percent, 3) . '%' : 'Default' }},
                        C {{ $level->chain_income_c_percent !== null ? number_format((float) $level->chain_income_c_percent, 3) . '%' : 'Default' }}.
                    </div>
                @else
                    <div class="wallet-empty">No active level could be resolved for your account yet.</div>
                @endif
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
                                        <td><span class="wallet-ledger-amount {{ $amount < 0 ? 'is-debit' : 'is-credit' }}">{{ $amount >= 0 ? '+' : '' }}{{ number_format($amount, 8) }}</span></td>
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
                                    <span class="wallet-ledger-amount {{ $amount < 0 ? 'is-debit' : 'is-credit' }}">{{ $amount >= 0 ? '+' : '' }}{{ number_format($amount, 8) }}</span>
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

            <div class="wallet-panel">
                <div class="wallet-section-head">
                    <div>
                        <div class="wallet-meta-label">Staking</div>
                        <h2 class="wallet-section-title">Staking Plans</h2>
                        <p class="wallet-section-copy">Choose an active plan, enter an amount, and start staking directly from your wallet page.</p>
                    </div>
                </div>

                @if ($plans->isEmpty())
                    <div class="wallet-empty">No active staking plans are available right now.</div>
                @else
                    <div class="wallet-plan-grid">
                        @foreach ($plans as $plan)
                            <div class="wallet-plan-card">
                                <div class="wallet-plan-card__top">
                                    <div>
                                        <h3 class="wallet-plan-card__title">{{ $plan->name }}</h3>
                                        <span class="wallet-plan-card__badge"><i class="fa fa-trophy" aria-hidden="true"></i><span>{{ $plan->requiredLevel?->code ?? 'All levels' }}</span></span>
                                    </div>
                                    <span class="wallet-plan-card__rate">{{ number_format((float) $plan->daily_rate, 6) }}%</span>
                                </div>
                                <div class="wallet-plan-meta">
                                    <div class="wallet-plan-meta__item">
                                        <div class="wallet-meta-label">Amount Range</div>
                                        <div class="wallet-plan-meta__value">{{ number_format((float) ($plan->min_amount ?? 0), 4) }} - {{ $plan->max_amount ? number_format((float) $plan->max_amount, 4) : 'Unlimited' }}</div>
                                    </div>
                                    <div class="wallet-plan-meta__item">
                                        <div class="wallet-meta-label">Duration</div>
                                        <div class="wallet-plan-meta__value">{{ $plan->duration_days }} days</div>
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('staking.store') }}" class="wallet-plan-form">
                                    @csrf
                                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                    <input name="amount" type="number" step="0.0001" class="form-control" placeholder="Enter amount" required>
                                    <button type="submit" class="btn-main btn-light">Stake</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="wallet-panel">
                <div class="wallet-section-head">
                    <div>
                        <div class="wallet-meta-label">Portfolio</div>
                        <h2 class="wallet-section-title">Your Stakes</h2>
                        <p class="wallet-section-copy">Track each active or completed stake, its reward status, and when it ends.</p>
                    </div>
                </div>

                @if ($stakes->isEmpty())
                    <div class="wallet-empty">No stakes found yet.</div>
                @else
                    <div class="wallet-table-wrap">
                        <table class="table wallet-table align-middle">
                            <thead>
                                <tr>
                                    <th>Plan</th>
                                    <th>Principal</th>
                                    <th>Rewards Paid</th>
                                    <th>Status</th>
                                    <th>Ends At</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($stakes as $stake)
                                    @php
                                        $status = strtolower((string) $stake->status);
                                        $statusClass = match ($status) {
                                            'active' => 'is-active',
                                            'completed' => 'is-completed',
                                            'cancelled' => 'is-cancelled',
                                            default => 'is-default',
                                        };
                                    @endphp
                                    <tr>
                                        <td>
                                            <span class="wallet-table-title">{{ $stake->stakePlan->name ?? '-' }}</span>
                                            @if ($stake->stakePlan?->requiredLevel?->code)
                                                <span class="wallet-table-subtext">Required {{ $stake->stakePlan->requiredLevel->code }}</span>
                                            @endif
                                        </td>
                                        <td>{{ number_format((float) $stake->principal_amount, 4) }}</td>
                                        <td>{{ number_format((float) $stake->total_reward_paid, 4) }}</td>
                                        <td><span class="wallet-status-badge {{ $statusClass }}">{{ ucfirst($status) }}</span></td>
                                        <td>{{ optional($stake->ends_at)->format('M d, Y h:i A') }}</td>
                                        <td>
                                            @if ($stake->status === 'active' && $stake->ends_at && now()->gte($stake->ends_at))
                                                <form method="POST" action="{{ route('staking.unstake', $stake) }}">
                                                    @csrf
                                                    <button type="submit" class="btn-main btn-sm">Unstake</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="wallet-stake-mobile">
                        @foreach ($stakes as $stake)
                            @php
                                $status = strtolower((string) $stake->status);
                                $statusClass = match ($status) {
                                    'active' => 'is-active',
                                    'completed' => 'is-completed',
                                    'cancelled' => 'is-cancelled',
                                    default => 'is-default',
                                };
                            @endphp
                            <div class="wallet-stake-mobile-card">
                                <div class="wallet-ledger-mobile-top">
                                    <div>
                                        <div class="wallet-table-title">{{ $stake->stakePlan->name ?? '-' }}</div>
                                        @if ($stake->stakePlan?->requiredLevel?->code)
                                            <span class="wallet-table-subtext">Required {{ $stake->stakePlan->requiredLevel->code }}</span>
                                        @endif
                                    </div>
                                    <span class="wallet-status-badge {{ $statusClass }}">{{ ucfirst($status) }}</span>
                                </div>
                                <div class="wallet-stake-mobile-meta"><span>Principal</span><strong>{{ number_format((float) $stake->principal_amount, 4) }}</strong></div>
                                <div class="wallet-stake-mobile-meta"><span>Rewards Paid</span><strong>{{ number_format((float) $stake->total_reward_paid, 4) }}</strong></div>
                                <div class="wallet-stake-mobile-meta"><span>Ends At</span><strong>{{ optional($stake->ends_at)->format('M d, Y h:i A') }}</strong></div>
                                @if ($stake->status === 'active' && $stake->ends_at && now()->gte($stake->ends_at))
                                    <div class="mt-3">
                                        <form method="POST" action="{{ route('staking.unstake', $stake) }}">
                                            @csrf
                                            <button type="submit" class="btn-main w-100">Unstake</button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
