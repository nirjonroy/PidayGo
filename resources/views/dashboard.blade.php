@extends('layouts.frontend')

@push('styles')
<style>
    .dashboard-shell {
        padding: 116px 0 48px;
        background:
            radial-gradient(circle at top left, rgba(var(--secondary-color-rgb), 0.18), transparent 36%),
            linear-gradient(180deg, rgba(var(--primary-color-rgb), 0.06), rgba(255, 255, 255, 0) 340px);
    }
    .dashboard-stack {
        display: grid;
        gap: 24px;
    }
    .dashboard-overview {
        display: grid;
        grid-template-columns: minmax(0, 1.08fr) minmax(0, 0.92fr);
        gap: 24px;
        align-items: stretch;
    }
    .dashboard-panel {
        position: relative;
        padding: 28px;
        border-radius: 28px;
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid rgba(15, 23, 42, 0.08);
        box-shadow: 0 24px 52px rgba(15, 23, 42, 0.08);
        overflow: hidden;
    }
    .dashboard-panel::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(var(--secondary-color-rgb), 0.08), rgba(var(--primary-color-rgb), 0.04));
        pointer-events: none;
    }
    .dashboard-panel > * {
        position: relative;
        z-index: 1;
    }
    .dashboard-profile {
        display: flex;
        align-items: center;
        gap: 18px;
    }
    .dashboard-avatar,
    .dashboard-avatar-fallback {
        width: 84px;
        height: 84px;
        flex: 0 0 84px;
        border-radius: 24px;
    }
    .dashboard-avatar {
        object-fit: cover;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.16);
    }
    .dashboard-avatar-fallback {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
        font-weight: 800;
        color: #ffffff;
        background: linear-gradient(135deg, #f0a83a, #6f33cc);
        box-shadow: 0 12px 28px rgba(111, 51, 204, 0.24);
    }
    .dashboard-meta-label {
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #64748b;
        margin-bottom: 6px;
    }
    .dashboard-name {
        margin: 0 0 10px;
        font-size: clamp(28px, 4vw, 38px);
        line-height: 1.05;
        font-weight: 800;
        word-break: break-word;
    }
    .dashboard-uid-row {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .dashboard-uid {
        display: inline-flex;
        align-items: center;
        padding: 10px 14px;
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.7);
        border: 1px solid rgba(15, 23, 42, 0.08);
        font-weight: 700;
        letter-spacing: 0.04em;
    }
    .dashboard-copy-btn {
        width: 40px;
        height: 40px;
        border: 0;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(var(--primary-color-rgb), 0.14);
        color: #111827;
        transition: transform 0.2s ease, background 0.2s ease;
    }
    .dashboard-copy-btn:hover,
    .dashboard-copy-btn:focus {
        transform: translateY(-1px);
        background: rgba(var(--primary-color-rgb), 0.2);
    }
    .dashboard-copy-feedback {
        display: none;
        font-size: 12px;
        font-weight: 700;
        color: #059669;
    }
    .dashboard-copy-feedback.is-visible {
        display: inline-flex;
    }
    .dashboard-chip-list {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 18px;
    }
    .dashboard-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 14px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.72);
        border: 1px solid rgba(15, 23, 42, 0.08);
        font-size: 13px;
        font-weight: 700;
        color: #334155;
    }
    .dashboard-chip.is-success {
        background: rgba(16, 185, 129, 0.12);
        border-color: rgba(16, 185, 129, 0.2);
        color: #047857;
    }
    .dashboard-summary {
        display: grid;
        gap: 16px;
    }
    .dashboard-summary-grid {
        display: grid;
        gap: 16px;
    }
    .dashboard-metric {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding: 18px 20px;
        border-radius: 22px;
        background: rgba(255, 255, 255, 0.76);
        border: 1px solid rgba(15, 23, 42, 0.08);
    }
    .dashboard-metric__label {
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #64748b;
        margin-bottom: 8px;
    }
    .dashboard-metric__value {
        margin: 0;
        font-size: clamp(26px, 3vw, 34px);
        line-height: 1.05;
        font-weight: 800;
        word-break: break-word;
    }
    .dashboard-metric__caption {
        margin-top: 8px;
        font-size: 13px;
        color: #64748b;
    }
    .dashboard-metric__icon {
        width: 48px;
        height: 48px;
        flex: 0 0 48px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: #ffffff;
        background: linear-gradient(135deg, #f0a83a, #6f33cc);
        box-shadow: 0 14px 26px rgba(111, 51, 204, 0.2);
    }
    .dashboard-metric__icon.is-alt {
        background: linear-gradient(135deg, #0ea5e9, #2563eb);
        box-shadow: 0 14px 26px rgba(37, 99, 235, 0.18);
    }
    .dashboard-metric__icon.is-income {
        background: linear-gradient(135deg, #10b981, #0f766e);
        box-shadow: 0 14px 26px rgba(16, 185, 129, 0.18);
    }
    .dashboard-income-card {
        padding: 18px 20px;
        border-radius: 22px;
        background: rgba(255, 255, 255, 0.76);
        border: 1px solid rgba(15, 23, 42, 0.08);
    }
    .dashboard-income-card__head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
    }
    .dashboard-income-card__head .dashboard-metric__value {
        font-size: clamp(22px, 3vw, 30px);
    }
    .dashboard-income-table {
        width: 100%;
        border-collapse: collapse;
    }
    .dashboard-income-table thead th {
        padding: 0 0 12px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #64748b;
        border-bottom: 1px solid rgba(15, 23, 42, 0.08);
    }
    .dashboard-income-table thead th:first-child,
    .dashboard-income-table tbody td:first-child {
        text-align: left;
    }
    .dashboard-income-table thead th:not(:first-child),
    .dashboard-income-table tbody td:not(:first-child) {
        text-align: right;
    }
    .dashboard-income-table tbody td {
        padding: 14px 0;
        border-bottom: 1px solid rgba(15, 23, 42, 0.08);
        vertical-align: middle;
    }
    .dashboard-income-table tbody tr:last-child td {
        border-bottom: 0;
        padding-bottom: 0;
    }
    .dashboard-income-table__label {
        font-weight: 700;
        color: #0f172a;
    }
    .dashboard-income-table__value {
        font-weight: 800;
        color: #0f172a;
        white-space: nowrap;
    }
    .dashboard-team-summary {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 18px;
    }
    .dashboard-team-stat {
        padding: 16px 18px;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.76);
        border: 1px solid rgba(15, 23, 42, 0.08);
    }
    .dashboard-team-stat__value {
        margin: 0;
        font-size: clamp(24px, 2.5vw, 30px);
        line-height: 1;
        font-weight: 800;
        color: #0f172a;
    }
    .dashboard-team-stat__label {
        margin-top: 8px;
        font-size: 13px;
        color: #64748b;
    }
    .dashboard-team-tabs {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 18px;
    }
    .dashboard-team-tab {
        width: 100%;
        padding: 12px 14px;
        border-radius: 16px;
        border: 1px solid rgba(15, 23, 42, 0.08);
        background: rgba(255, 255, 255, 0.76);
        color: #0f172a;
        font-weight: 800;
        transition: transform 0.2s ease, background 0.2s ease, color 0.2s ease;
    }
    .dashboard-team-tab:hover,
    .dashboard-team-tab:focus {
        transform: translateY(-1px);
    }
    .dashboard-team-tab.is-active {
        color: #ffffff;
        border-color: transparent;
        background: linear-gradient(135deg, #f0a83a, #6f33cc);
        box-shadow: 0 14px 26px rgba(111, 51, 204, 0.18);
    }
    .dashboard-team-panel {
        display: none;
    }
    .dashboard-team-panel.is-active {
        display: block;
    }
    .dashboard-team-list {
        display: grid;
        gap: 14px;
    }
    .dashboard-team-member {
        border-radius: 20px;
        border: 1px solid rgba(15, 23, 42, 0.08);
        background: rgba(255, 255, 255, 0.76);
        overflow: hidden;
    }
    .dashboard-team-member summary {
        list-style: none;
        cursor: pointer;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding: 18px;
    }
    .dashboard-team-member summary::-webkit-details-marker {
        display: none;
    }
    .dashboard-team-member summary::after {
        content: "\f107";
        font-family: FontAwesome;
        font-size: 18px;
        color: #64748b;
        transition: transform 0.2s ease;
    }
    .dashboard-team-member[open] summary::after {
        transform: rotate(180deg);
    }
    .dashboard-team-member__name {
        font-size: 18px;
        font-weight: 800;
        color: #0f172a;
    }
    .dashboard-team-member__meta {
        margin-top: 6px;
        font-size: 13px;
        color: #64748b;
        word-break: break-word;
    }
    .dashboard-team-member__earn {
        min-width: 130px;
        text-align: right;
    }
    .dashboard-team-member__earn-value {
        font-size: 20px;
        font-weight: 800;
        color: #059669;
        white-space: nowrap;
    }
    .dashboard-team-details {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        padding: 0 18px 18px;
    }
    .dashboard-team-detail {
        padding: 12px 14px;
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.72);
        border: 1px solid rgba(15, 23, 42, 0.08);
    }
    .dashboard-team-detail__label {
        display: block;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #64748b;
        margin-bottom: 6px;
    }
    .dashboard-team-detail__value {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        word-break: break-word;
    }
    .dashboard-section-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 20px;
    }
    .dashboard-section-title {
        margin: 0;
        font-size: clamp(24px, 3vw, 30px);
        line-height: 1.1;
        font-weight: 800;
    }
    .dashboard-section-copy {
        margin: 8px 0 0;
        color: #64748b;
    }
    .dashboard-ledger-wrap {
        border-radius: 22px;
        overflow: hidden;
        background: rgba(255, 255, 255, 0.76);
        border: 1px solid rgba(15, 23, 42, 0.08);
    }
    .dashboard-ledger-table {
        margin-bottom: 0;
    }
    .dashboard-ledger-table thead th {
        padding: 16px 18px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #64748b;
        border-bottom: 1px solid rgba(15, 23, 42, 0.08);
        background: rgba(255, 255, 255, 0.3);
    }
    .dashboard-ledger-table tbody td {
        padding: 18px;
        vertical-align: middle;
        border-color: rgba(15, 23, 42, 0.08);
    }
    .dashboard-ledger-type {
        font-weight: 700;
    }
    .dashboard-ledger-subtext {
        display: block;
        margin-top: 4px;
        font-size: 13px;
        color: #64748b;
    }
    .dashboard-ledger-amount {
        font-weight: 800;
        font-size: 16px;
    }
    .dashboard-ledger-amount.is-credit {
        color: #059669;
    }
    .dashboard-ledger-amount.is-debit {
        color: #dc2626;
    }
    .dashboard-ledger-mobile {
        display: none;
        gap: 14px;
    }
    .dashboard-ledger-mobile-card {
        padding: 18px;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.76);
        border: 1px solid rgba(15, 23, 42, 0.08);
    }
    .dashboard-ledger-mobile-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 14px;
    }
    .dashboard-ledger-mobile-meta {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        padding-top: 10px;
        margin-top: 10px;
        border-top: 1px solid rgba(15, 23, 42, 0.08);
        font-size: 14px;
    }
    .dashboard-ledger-mobile-meta span {
        color: #64748b;
    }
    .dashboard-empty {
        padding: 34px 24px;
        border-radius: 22px;
        text-align: center;
        color: #64748b;
        background: rgba(255, 255, 255, 0.58);
        border: 1px dashed rgba(15, 23, 42, 0.14);
    }
    .dark-scheme .dashboard-shell {
        background:
            radial-gradient(circle at top left, rgba(var(--secondary-color-rgb), 0.18), transparent 34%),
            linear-gradient(180deg, rgba(10, 14, 22, 0.8), rgba(10, 14, 22, 0) 360px);
    }
    .dark-scheme .dashboard-panel {
        background: rgba(10, 14, 22, 0.86);
        border-color: rgba(255, 255, 255, 0.08);
        box-shadow: 0 26px 54px rgba(0, 0, 0, 0.34);
    }
    .dark-scheme .dashboard-uid,
    .dark-scheme .dashboard-chip,
    .dark-scheme .dashboard-metric,
    .dark-scheme .dashboard-income-card,
    .dark-scheme .dashboard-team-stat,
    .dark-scheme .dashboard-team-tab,
    .dark-scheme .dashboard-team-member,
    .dark-scheme .dashboard-team-detail,
    .dark-scheme .dashboard-ledger-wrap,
    .dark-scheme .dashboard-ledger-mobile-card,
    .dark-scheme .dashboard-empty {
        background: rgba(255, 255, 255, 0.04);
        border-color: rgba(255, 255, 255, 0.08);
    }
    .dark-scheme .dashboard-copy-btn {
        color: #f8fafc;
        background: rgba(255, 255, 255, 0.1);
    }
    .dark-scheme .dashboard-copy-btn:hover,
    .dark-scheme .dashboard-copy-btn:focus {
        background: rgba(255, 255, 255, 0.16);
    }
    .dark-scheme .dashboard-meta-label,
    .dark-scheme .dashboard-metric__label,
    .dark-scheme .dashboard-metric__caption,
    .dark-scheme .dashboard-team-stat__label,
    .dark-scheme .dashboard-team-detail__label,
    .dark-scheme .dashboard-team-member summary::after,
    .dark-scheme .dashboard-team-member__meta,
    .dark-scheme .dashboard-section-copy,
    .dark-scheme .dashboard-ledger-subtext,
    .dark-scheme .dashboard-income-table thead th,
    .dark-scheme .dashboard-ledger-mobile-meta span {
        color: #94a3b8;
    }
    .dark-scheme .dashboard-income-table tbody td {
        border-color: rgba(255, 255, 255, 0.08);
    }
    .dark-scheme .dashboard-income-table__label,
    .dark-scheme .dashboard-income-table__value,
    .dark-scheme .dashboard-team-stat__value,
    .dark-scheme .dashboard-team-member__name,
    .dark-scheme .dashboard-team-detail__value {
        color: #f8fafc;
    }
    .dark-scheme .dashboard-chip {
        color: #e2e8f0;
    }
    .dark-scheme .dashboard-chip.is-success {
        color: #86efac;
        background: rgba(16, 185, 129, 0.14);
        border-color: rgba(16, 185, 129, 0.24);
    }
    .dark-scheme .dashboard-team-tab.is-active {
        color: #ffffff;
        background: linear-gradient(135deg, rgba(var(--secondary-color-rgb), 0.9), rgba(var(--primary-color-rgb), 0.9));
        border-color: transparent;
    }
    .dark-scheme .dashboard-team-member__earn-value {
        color: #86efac;
    }
    .dark-scheme .dashboard-ledger-table thead th,
    .dark-scheme .dashboard-ledger-mobile-meta {
        border-color: rgba(255, 255, 255, 0.08);
    }
    .dark-scheme .dashboard-ledger-table thead th {
        background: rgba(255, 255, 255, 0.02);
    }
    .dark-scheme .dashboard-ledger-table tbody td {
        border-color: rgba(255, 255, 255, 0.06);
    }
    @media (max-width: 991.98px) {
        .dashboard-shell {
            padding-top: 88px;
        }
        .dashboard-overview {
            grid-template-columns: 1fr;
        }
        .dashboard-panel {
            padding: 24px;
            border-radius: 24px;
        }
    }
    @media (max-width: 767.98px) {
        .dashboard-shell {
            padding-bottom: 106px;
        }
        .dashboard-profile {
            align-items: flex-start;
        }
        .dashboard-summary-grid,
        .dashboard-team-summary,
        .dashboard-team-tabs,
        .dashboard-team-details {
            grid-template-columns: 1fr;
        }
        .dashboard-section-head {
            flex-direction: column;
            align-items: stretch;
        }
        .dashboard-section-head .btn-main {
            width: 100%;
            text-align: center;
        }
        .dashboard-ledger-wrap {
            display: none;
        }
        .dashboard-ledger-mobile {
            display: grid;
        }
    }
    @media (max-width: 575.98px) {
        .dashboard-shell {
            padding-top: 82px;
        }
        .dashboard-panel {
            padding: 20px;
            border-radius: 22px;
        }
        .dashboard-profile {
            flex-direction: column;
        }
        .dashboard-avatar,
        .dashboard-avatar-fallback {
            width: 76px;
            height: 76px;
            flex-basis: 76px;
            border-radius: 22px;
        }
        .dashboard-avatar-fallback {
            font-size: 28px;
        }
        .dashboard-uid-row,
        .dashboard-team-member summary,
        .dashboard-ledger-mobile-top,
        .dashboard-ledger-mobile-meta {
            flex-direction: column;
        }
        .dashboard-copy-btn {
            width: 100%;
        }
        .dashboard-chip {
            width: 100%;
            justify-content: flex-start;
        }
        .dashboard-team-member__earn {
            width: 100%;
            text-align: left;
        }
        .dashboard-income-table thead th,
        .dashboard-income-table tbody td {
            font-size: 13px;
        }
    }
</style>
@endpush

@section('content')
<section class="dashboard-shell">
    <div class="container">
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <div class="dashboard-stack">
            <div class="dashboard-overview">
                <div class="dashboard-panel">
                    <div class="dashboard-profile">
                        @if ($avatarUrl)
                            <img src="{{ $avatarUrl }}" alt="{{ $displayName }}" class="dashboard-avatar">
                        @else
                            <span class="dashboard-avatar-fallback">
                                {{ strtoupper(\Illuminate\Support\Str::substr($displayName, 0, 1)) }}
                            </span>
                        @endif

                        <div>
                            <div class="dashboard-meta-label">Username</div>
                            <h1 class="dashboard-name">{{ $displayName }}</h1>

                            <div class="dashboard-meta-label">UID</div>
                            <div class="dashboard-uid-row">
                                <span class="dashboard-uid">{{ $user->user_code }}</span>
                                <button
                                    type="button"
                                    class="dashboard-copy-btn"
                                    data-copy-target="{{ $user->user_code }}"
                                    data-copy-feedback="dashboard-uid-feedback"
                                    aria-label="Copy UID"
                                >
                                    <i class="fa fa-copy" aria-hidden="true"></i>
                                </button>
                                <span id="dashboard-uid-feedback" class="dashboard-copy-feedback" aria-live="polite">Copied</span>
                            </div>

                            <div class="dashboard-chip-list">
                                <span class="dashboard-chip">
                                    <i class="fa fa-trophy" aria-hidden="true"></i>
                                    Current Level: {{ $level?->code ?? 'Not set' }}
                                </span>
                                <span class="dashboard-chip {{ $emailVerified ? 'is-success' : '' }}">
                                    <i class="fa fa-envelope-o" aria-hidden="true"></i>
                                    {{ $emailVerified ? 'Email verified' : 'Email pending' }}
                                </span>
                                <span class="dashboard-chip {{ $twoFactorEnabled ? 'is-success' : '' }}">
                                    <i class="fa fa-shield" aria-hidden="true"></i>
                                    {{ $twoFactorEnabled ? '2FA enabled' : '2FA disabled' }}
                                </span>
                                <span class="dashboard-chip {{ $kycStatus === 'approved' ? 'is-success' : '' }}">
                                    <i class="fa fa-id-card-o" aria-hidden="true"></i>
                                    KYC: {{ $kycStatus ? ucfirst($kycStatus) : 'Not submitted' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dashboard-panel">
                    <div class="dashboard-summary">
                        <div class="dashboard-summary-grid">
                            <div class="dashboard-metric">
                                <div>
                                    <div class="dashboard-metric__label">Wallet Balance</div>
                                    <p class="dashboard-metric__value">{{ number_format($walletBalance, 4) }}</p>
                                    <div class="dashboard-metric__caption">USDT available in your main wallet</div>
                                </div>
                                <span class="dashboard-metric__icon">
                                    <i class="fa fa-wallet" aria-hidden="true"></i>
                                </span>
                            </div>

                            <div class="dashboard-metric">
                                <div>
                                    <div class="dashboard-metric__label">Reserve Balance</div>
                                    <p class="dashboard-metric__value">{{ number_format($reserveBalance, 4) }}</p>
                                    <div class="dashboard-metric__caption">USDT currently locked in reserve</div>
                                </div>
                                <span class="dashboard-metric__icon is-alt">
                                    <i class="fa fa-lock" aria-hidden="true"></i>
                                </span>
                            </div>
                        </div>

                        <div class="dashboard-income-card">
                            <div class="dashboard-income-card__head">
                                <div>
                                    <div class="dashboard-metric__label">Income Breakdown</div>
                                    <p class="dashboard-metric__value">{{ number_format($totalIncome, 4) }}</p>
                                    <div class="dashboard-metric__caption">Daily vs total income by source</div>
                                </div>
                                <span class="dashboard-metric__icon is-income">
                                    <i class="fa fa-table" aria-hidden="true"></i>
                                </span>
                            </div>

                            <table class="dashboard-income-table">
                                <thead>
                                    <tr>
                                        <th>Income</th>
                                        <th>Daily Income</th>
                                        <th>Total Income</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($incomeBreakdown as $row)
                                        <tr>
                                            <td>
                                                <span class="dashboard-income-table__label">{{ $row['label'] }}</span>
                                            </td>
                                            <td>
                                                <span class="dashboard-income-table__value">{{ number_format((float) $row['daily'], 4) }}</span>
                                            </td>
                                            <td>
                                                <span class="dashboard-income-table__value">{{ number_format((float) $row['total'], 4) }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="dashboard-panel">
                <div class="dashboard-section-head">
                    <div>
                        <div class="dashboard-meta-label">Community</div>
                        <h2 class="dashboard-section-title">My Team</h2>
                        <p class="dashboard-section-copy">Browse your A, B, and C branches and open a member to see what they generated for you.</p>
                    </div>
                </div>

                <div class="dashboard-team-summary">
                    <div class="dashboard-team-stat">
                        <p class="dashboard-team-stat__value">{{ $teamSummary['total_members'] }}</p>
                        <div class="dashboard-team-stat__label">Total Members</div>
                    </div>
                    <div class="dashboard-team-stat">
                        <p class="dashboard-team-stat__value">{{ $teamSummary['a_members'] }}</p>
                        <div class="dashboard-team-stat__label">A Members</div>
                    </div>
                    <div class="dashboard-team-stat">
                        <p class="dashboard-team-stat__value">{{ $teamSummary['b_members'] }}</p>
                        <div class="dashboard-team-stat__label">B Members</div>
                    </div>
                    <div class="dashboard-team-stat">
                        <p class="dashboard-team-stat__value">{{ $teamSummary['c_members'] }}</p>
                        <div class="dashboard-team-stat__label">C Members</div>
                    </div>
                </div>

                <div class="dashboard-team-tabs" role="tablist" aria-label="Team branches">
                    @foreach ($teamBranches as $branch)
                        <button
                            type="button"
                            class="dashboard-team-tab {{ $teamDefaultBranch === $branch['slot'] ? 'is-active' : '' }}"
                            data-team-tab="{{ $branch['slot'] }}"
                        >
                            {{ $branch['label'] }} ({{ $branch['count'] }})
                        </button>
                    @endforeach
                </div>

                @foreach ($teamBranches as $branch)
                    <div
                        class="dashboard-team-panel {{ $teamDefaultBranch === $branch['slot'] ? 'is-active' : '' }}"
                        data-team-panel="{{ $branch['slot'] }}"
                    >
                        @if ($branch['members']->isEmpty())
                            <div class="dashboard-empty">No members found in {{ $branch['label'] }} yet.</div>
                        @else
                            <div class="dashboard-team-list">
                                @foreach ($branch['members'] as $member)
                                    <details class="dashboard-team-member">
                                        <summary>
                                            <div>
                                                <div class="dashboard-team-member__name">{{ $member['display_name'] }}</div>
                                                <div class="dashboard-team-member__meta">
                                                    {{ $member['uid'] }} | {{ \Illuminate\Support\Str::limit($member['email'] ?: 'No email', 28) }}
                                                </div>
                                            </div>
                                            <div class="dashboard-team-member__earn">
                                                <div class="dashboard-metric__label">You Earned</div>
                                                <div class="dashboard-team-member__earn-value">{{ number_format((float) $member['total_earned'], 4) }}</div>
                                            </div>
                                        </summary>

                                        <div class="dashboard-team-details">
                                            <div class="dashboard-team-detail">
                                                <span class="dashboard-team-detail__label">Email</span>
                                                <span class="dashboard-team-detail__value">{{ $member['email'] ?: 'N/A' }}</span>
                                            </div>
                                            <div class="dashboard-team-detail">
                                                <span class="dashboard-team-detail__label">Phone</span>
                                                <span class="dashboard-team-detail__value">{{ $member['phone'] ?: 'N/A' }}</span>
                                            </div>
                                            <div class="dashboard-team-detail">
                                                <span class="dashboard-team-detail__label">UID</span>
                                                <span class="dashboard-team-detail__value">{{ $member['uid'] }}</span>
                                            </div>
                                            <div class="dashboard-team-detail">
                                                <span class="dashboard-team-detail__label">Referral Code</span>
                                                <span class="dashboard-team-detail__value">{{ $member['ref_code'] }}</span>
                                            </div>
                                            <div class="dashboard-team-detail">
                                                <span class="dashboard-team-detail__label">Joined</span>
                                                <span class="dashboard-team-detail__value">{{ $member['joined_at'] ?: 'N/A' }}</span>
                                            </div>
                                            <div class="dashboard-team-detail">
                                                <span class="dashboard-team-detail__label">Today's Earn From Member</span>
                                                <span class="dashboard-team-detail__value">{{ number_format((float) $member['daily_earned'], 4) }}</span>
                                            </div>
                                            <div class="dashboard-team-detail">
                                                <span class="dashboard-team-detail__label">Commission Events</span>
                                                <span class="dashboard-team-detail__value">{{ $member['commission_count'] }}</span>
                                            </div>
                                            <div class="dashboard-team-detail">
                                                <span class="dashboard-team-detail__label">Reserve Sales</span>
                                                <span class="dashboard-team-detail__value">{{ $member['sales_count'] }}</span>
                                            </div>
                                            <div class="dashboard-team-detail">
                                                <span class="dashboard-team-detail__label">Sales Volume</span>
                                                <span class="dashboard-team-detail__value">{{ number_format((float) $member['sales_amount'], 4) }}</span>
                                            </div>
                                            <div class="dashboard-team-detail">
                                                <span class="dashboard-team-detail__label">Member Profit</span>
                                                <span class="dashboard-team-detail__value">{{ number_format((float) $member['profit_amount'], 4) }}</span>
                                            </div>
                                            <div class="dashboard-team-detail">
                                                <span class="dashboard-team-detail__label">Last Activity</span>
                                                <span class="dashboard-team-detail__value">{{ $member['last_activity'] ?: 'No activity yet' }}</span>
                                            </div>
                                        </div>
                                    </details>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="dashboard-panel">
                <div class="dashboard-section-head">
                    <div>
                        <div class="dashboard-meta-label">Wallet Activity</div>
                        <h2 class="dashboard-section-title">Recent Wallet Ledger</h2>
                        <p class="dashboard-section-copy">Latest credits and debits from your account.</p>
                    </div>
                    <a href="{{ route('wallet.index') }}" class="btn-main btn-light">View Wallet</a>
                </div>

                @if ($recentWalletLedgers->isEmpty())
                    <div class="dashboard-empty">No ledger entries found yet.</div>
                @else
                    <div class="dashboard-ledger-wrap">
                        <table class="table dashboard-ledger-table align-middle">
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
                                        $referenceLabel = $ledger->reference_type && $ledger->reference_id
                                            ? class_basename($ledger->reference_type) . ' #' . $ledger->reference_id
                                            : '-';
                                    @endphp
                                    <tr>
                                        <td>
                                            <span class="dashboard-ledger-type">
                                                {{ \Illuminate\Support\Str::headline(str_replace('_', ' ', $ledger->type)) }}
                                            </span>
                                            @if (is_array($ledger->meta) && isset($ledger->meta['day']))
                                                <span class="dashboard-ledger-subtext">Reward day {{ $ledger->meta['day'] }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="dashboard-ledger-amount {{ $amount < 0 ? 'is-debit' : 'is-credit' }}">
                                                {{ $amount >= 0 ? '+' : '' }}{{ number_format($amount, 8) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="dashboard-ledger-subtext">{{ $referenceLabel }}</span>
                                        </td>
                                        <td>
                                            <span class="dashboard-ledger-subtext">{{ optional($ledger->created_at)->format('M d, Y h:i A') }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="dashboard-ledger-mobile">
                        @foreach ($recentWalletLedgers as $ledger)
                            @php
                                $amount = (float) $ledger->amount;
                                $referenceLabel = $ledger->reference_type && $ledger->reference_id
                                    ? class_basename($ledger->reference_type) . ' #' . $ledger->reference_id
                                    : '-';
                            @endphp
                            <div class="dashboard-ledger-mobile-card">
                                <div class="dashboard-ledger-mobile-top">
                                    <div>
                                        <div class="dashboard-ledger-type">
                                            {{ \Illuminate\Support\Str::headline(str_replace('_', ' ', $ledger->type)) }}
                                        </div>
                                        @if (is_array($ledger->meta) && isset($ledger->meta['day']))
                                            <span class="dashboard-ledger-subtext">Reward day {{ $ledger->meta['day'] }}</span>
                                        @endif
                                    </div>
                                    <span class="dashboard-ledger-amount {{ $amount < 0 ? 'is-debit' : 'is-credit' }}">
                                        {{ $amount >= 0 ? '+' : '' }}{{ number_format($amount, 8) }}
                                    </span>
                                </div>

                                <div class="dashboard-ledger-mobile-meta">
                                    <span>Reference</span>
                                    <strong>{{ $referenceLabel }}</strong>
                                </div>
                                <div class="dashboard-ledger-mobile-meta">
                                    <span>Date</span>
                                    <strong>{{ optional($ledger->created_at)->format('M d, Y h:i A') }}</strong>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-copy-target]').forEach(function (button) {
            button.addEventListener('click', function () {
                var value = button.getAttribute('data-copy-target') || '';
                var feedbackId = button.getAttribute('data-copy-feedback');
                var feedback = feedbackId ? document.getElementById(feedbackId) : null;

                if (!value || !navigator.clipboard) {
                    return;
                }

                navigator.clipboard.writeText(value).then(function () {
                    if (!feedback) {
                        return;
                    }

                    feedback.classList.add('is-visible');
                    window.clearTimeout(button.copyTimer);
                    button.copyTimer = window.setTimeout(function () {
                        feedback.classList.remove('is-visible');
                    }, 1600);
                });
            });
        });

        var teamTabs = document.querySelectorAll('[data-team-tab]');
        var teamPanels = document.querySelectorAll('[data-team-panel]');
        var activateTeamBranch = function (slot) {
            teamTabs.forEach(function (tab) {
                tab.classList.toggle('is-active', tab.getAttribute('data-team-tab') === slot);
            });

            teamPanels.forEach(function (panel) {
                panel.classList.toggle('is-active', panel.getAttribute('data-team-panel') === slot);
            });
        };

        teamTabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                activateTeamBranch(tab.getAttribute('data-team-tab'));
            });
        });
    });
</script>
@endpush
