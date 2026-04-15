@extends('layouts.frontend')

@push('styles')
<style>
    .stake-shell {
        padding: 24px 0 54px;
        background:
            radial-gradient(circle at top left, rgba(var(--secondary-color-rgb), 0.18), transparent 34%),
            linear-gradient(180deg, rgba(var(--primary-color-rgb), 0.06), rgba(255, 255, 255, 0) 320px);
    }
    .stake-stack,
    .stake-summary-grid,
    .stake-plan-grid,
    .stake-income-mobile,
    .stake-active-mobile {
        display: grid;
        gap: 24px;
    }
    .stake-overview {
        display: grid;
        gap: 24px;
        grid-template-columns: minmax(0, 1.02fr) minmax(0, 0.98fr);
    }
    .stake-panel,
    .stake-card,
    .stake-summary-card,
    .stake-plan-card,
    .stake-table-wrap,
    .stake-mobile-card,
    .stake-empty {
        position: relative;
        overflow: hidden;
        border-radius: 28px;
        border: 1px solid rgba(15, 23, 42, 0.08);
        background: rgba(255, 255, 255, 0.9);
        box-shadow: 0 24px 52px rgba(15, 23, 42, 0.08);
    }
    .stake-panel::before,
    .stake-card::before,
    .stake-summary-card::before,
    .stake-plan-card::before,
    .stake-table-wrap::before,
    .stake-mobile-card::before,
    .stake-empty::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(var(--secondary-color-rgb), 0.08), rgba(var(--primary-color-rgb), 0.04));
        pointer-events: none;
    }
    .stake-panel > *,
    .stake-card > *,
    .stake-summary-card > *,
    .stake-plan-card > *,
    .stake-table-wrap > *,
    .stake-mobile-card > *,
    .stake-empty > * {
        position: relative;
        z-index: 1;
    }
    .stake-panel,
    .stake-empty {
        padding: 28px;
    }
    .stake-card,
    .stake-summary-card,
    .stake-plan-card,
    .stake-mobile-card {
        padding: 18px;
    }
    .stake-meta-label {
        margin-bottom: 8px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #64748b;
    }
    .stake-section-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 20px;
    }
    .stake-section-title {
        margin: 0;
        font-size: clamp(24px, 3vw, 30px);
        line-height: 1.1;
        font-weight: 800;
        color: #0f172a;
    }
    .stake-section-copy,
    .stake-card-copy,
    .stake-subcopy,
    .stake-table-subtext,
    .stake-mobile-meta span {
        color: #64748b;
    }
    .stake-section-copy {
        margin: 8px 0 0;
        font-size: 15px;
        line-height: 1.7;
    }
    .stake-highlight,
    .stake-summary-value,
    .stake-card__value,
    .stake-plan-title,
    .stake-table-title,
    .stake-mobile-meta strong {
        color: #0f172a;
        font-weight: 800;
        word-break: break-word;
    }
    .stake-highlight {
        margin: 0;
        font-size: clamp(34px, 4vw, 48px);
        line-height: 1.04;
    }
    .stake-summary-value {
        margin: 0;
        font-size: clamp(24px, 3vw, 32px);
        line-height: 1.08;
    }
    .stake-card__value {
        font-size: 24px;
        line-height: 1.08;
    }
    .stake-icon {
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
    .stake-icon.is-alt {
        background: linear-gradient(135deg, #0ea5e9, #2563eb);
    }
    .stake-icon.is-income {
        background: linear-gradient(135deg, #10b981, #0f766e);
    }
    .stake-balance-card,
    .stake-summary-card,
    .stake-mobile-top,
    .stake-mobile-meta {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
    }
    .stake-balance-copy {
        margin-top: 10px;
        font-size: 15px;
        line-height: 1.7;
        color: #64748b;
    }
    .stake-action-grid,
    .stake-glance-grid,
    .stake-plan-meta,
    .stake-plan-form {
        display: grid;
        gap: 12px;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    .stake-action-grid {
        margin: 22px 0;
    }
    .stake-action-btn {
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
    .stake-plan-grid {
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 18px;
    }
    .stake-plan-top {
        margin-bottom: 16px;
    }
    .stake-plan-title {
        margin: 0;
        font-size: 20px;
        line-height: 1.15;
    }
    .stake-plan-lines {
        display: grid;
        gap: 10px;
        margin-bottom: 18px;
    }
    .stake-plan-line {
        font-size: 16px;
        line-height: 1.45;
        color: #0f172a;
        font-weight: 700;
        word-break: break-word;
    }
    .stake-status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
    }
    .stake-plan-form {
        grid-template-columns: minmax(0, 1fr) auto;
        align-items: stretch;
        margin-top: 0;
    }
    .stake-plan-form .form-control {
        min-height: 50px;
        border-radius: 14px;
        border-color: rgba(15, 23, 42, 0.12);
        background: rgba(255, 255, 255, 0.88);
        box-shadow: none;
    }
    .stake-plan-form .btn-main {
        min-width: 114px;
        border-radius: 14px;
    }
    .stake-status-badge {
        min-height: 32px;
        padding: 7px 11px;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }
    .stake-status-badge.is-active {
        background: rgba(16, 185, 129, 0.12);
        color: #047857;
    }
    .stake-status-badge.is-default {
        background: rgba(148, 163, 184, 0.16);
        color: #475569;
    }
    .stake-table-wrap {
        padding: 0;
        overflow: hidden;
    }
    .stake-table {
        margin-bottom: 0;
    }
    .stake-table thead th {
        padding: 16px 18px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #64748b;
        border-bottom: 1px solid rgba(15, 23, 42, 0.08);
        background: rgba(255, 255, 255, 0.3);
    }
    .stake-table tbody td {
        padding: 18px;
        vertical-align: middle;
        border-color: rgba(15, 23, 42, 0.08);
    }
    .stake-income-amount {
        font-size: 16px;
        font-weight: 800;
        color: #059669;
    }
    .stake-active-mobile,
    .stake-income-mobile {
        display: none;
        gap: 14px;
    }
    .stake-mobile-meta {
        padding-top: 10px;
        margin-top: 10px;
        border-top: 1px solid rgba(15, 23, 42, 0.08);
        font-size: 14px;
    }
    .stake-empty {
        text-align: center;
        color: #64748b;
        border-style: dashed;
    }
    .dark-scheme .stake-shell {
        background:
            radial-gradient(circle at top left, rgba(var(--secondary-color-rgb), 0.18), transparent 34%),
            linear-gradient(180deg, rgba(10, 14, 22, 0.8), rgba(10, 14, 22, 0) 360px);
    }
    .dark-scheme .stake-panel,
    .dark-scheme .stake-card,
    .dark-scheme .stake-summary-card,
    .dark-scheme .stake-plan-card,
    .dark-scheme .stake-table-wrap,
    .dark-scheme .stake-mobile-card,
    .dark-scheme .stake-plan-meta__item,
    .dark-scheme .stake-empty {
        background: rgba(10, 14, 22, 0.86);
        border-color: rgba(255, 255, 255, 0.08);
        box-shadow: 0 26px 54px rgba(0, 0, 0, 0.34);
    }
    .dark-scheme .stake-meta-label,
    .dark-scheme .stake-section-copy,
    .dark-scheme .stake-card-copy,
    .dark-scheme .stake-subcopy,
    .dark-scheme .stake-table-subtext,
    .dark-scheme .stake-mobile-meta span,
    .dark-scheme .stake-balance-copy,
    .dark-scheme .stake-table thead th {
        color: #94a3b8;
    }
    .dark-scheme .stake-highlight,
    .dark-scheme .stake-summary-value,
    .dark-scheme .stake-card__value,
    .dark-scheme .stake-section-title,
    .dark-scheme .stake-plan-title,
    .dark-scheme .stake-plan-line,
    .dark-scheme .stake-table-title,
    .dark-scheme .stake-mobile-meta strong {
        color: #f8fafc;
    }
    .dark-scheme .stake-plan-form .form-control {
        color: #f8fafc;
        border-color: rgba(255, 255, 255, 0.1);
        background: rgba(255, 255, 255, 0.06);
    }
    .dark-scheme .stake-table thead th {
        background: rgba(255, 255, 255, 0.02);
        border-color: rgba(255, 255, 255, 0.08);
    }
    .dark-scheme .stake-table tbody td,
    .dark-scheme .stake-mobile-meta {
        border-color: rgba(255, 255, 255, 0.08);
    }
    .dark-scheme .stake-status-badge.is-active {
        color: #86efac;
        background: rgba(16, 185, 129, 0.16);
    }
    .dark-scheme .stake-status-badge.is-default {
        color: #cbd5e1;
        background: rgba(148, 163, 184, 0.18);
    }
    @media (max-width: 991.98px) {
        .stake-overview {
            grid-template-columns: 1fr;
        }
        .stake-panel,
        .stake-empty {
            padding: 24px;
            border-radius: 24px;
        }
    }
    @media (max-width: 767.98px) {
        .stake-shell {
            padding-top: 16px;
            padding-bottom: 106px;
        }
        .stake-stack {
            gap: 18px;
        }
        .stake-panel--plans {
            order: 1;
        }
        .stake-overview {
            order: 2;
            gap: 18px;
        }
        .stake-panel--active {
            order: 3;
        }
        .stake-panel--income {
            order: 4;
        }
        .stake-action-grid,
        .stake-glance-grid,
        .stake-summary-grid,
        .stake-plan-form {
            grid-template-columns: 1fr;
        }
        .stake-plan-grid {
            gap: 14px;
        }
        .stake-plan-card {
            padding: 16px;
        }
        .stake-plan-top {
            margin-bottom: 14px;
        }
        .stake-plan-title {
            font-size: 15px;
        }
        .stake-plan-lines {
            gap: 8px;
            margin-bottom: 14px;
        }
        .stake-plan-line {
            font-size: 14px;
        }
        .stake-plan-form {
            grid-template-columns: minmax(0, 1fr) 104px;
            gap: 10px;
        }
        .stake-plan-form .form-control,
        .stake-plan-form .btn-main {
            min-height: 46px;
        }
        .stake-section-head,
        .stake-balance-card,
        .stake-summary-card,
        .stake-mobile-top,
        .stake-mobile-meta {
            flex-direction: column;
        }
        .stake-table-wrap {
            display: none;
        }
        .stake-active-mobile,
        .stake-income-mobile {
            display: grid;
        }
    }
    @media (max-width: 575.98px) {
        .stake-panel,
        .stake-empty {
            padding: 20px;
            border-radius: 22px;
        }
    }
    @media (max-width: 399.98px) {
        .stake-plan-form {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
@include('frontend.partials.page-banner', ['title' => 'Stake'])

<section class="stake-shell" aria-label="Stake overview">
    <div class="container">
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <div class="stake-stack">
            <div class="stake-overview">
                <div class="stake-panel">
                    <div class="stake-balance-card">
                        <div>
                            <div class="stake-meta-label">Stake Hub</div>
                            <p class="stake-highlight">{{ number_format((float) $balance, 4) }}</p>
                            <div class="stake-balance-copy">Use your wallet balance to activate stake plans, track active positions, and review recent stake income from one place.</div>
                        </div>
                        <span class="stake-icon" aria-hidden="true"><i class="fa fa-line-chart"></i></span>
                    </div>

                    <div class="stake-glance-grid">
                        <div class="stake-card">
                            <div class="stake-meta-label">Current Level</div>
                            <div class="stake-card__value">{{ $currentLevel?->code ?? 'Not set' }}</div>
                            <div class="stake-card-copy">Your current level controls whether a plan is available for staking.</div>
                        </div>
                        <div class="stake-card">
                            <div class="stake-meta-label">Active Stakes</div>
                            <div class="stake-card__value">{{ $activeStakeCount }}</div>
                            <div class="stake-card-copy">Number of active staking positions currently running on your account.</div>
                        </div>
                    </div>
                </div>

                <div class="stake-panel">
                    <div class="stake-summary-grid">
                        <div class="stake-summary-card">
                            <div>
                                <div class="stake-meta-label">Active Principal</div>
                                <p class="stake-summary-value">{{ number_format((float) $activeStakePrincipal, 4) }}</p>
                                <div class="stake-subcopy">Total principal currently locked across your active stakes.</div>
                            </div>
                            <span class="stake-icon is-alt" aria-hidden="true"><i class="fa fa-lock"></i></span>
                        </div>
                        <div class="stake-summary-card">
                            <div>
                                <div class="stake-meta-label">Today Stake Income</div>
                                <p class="stake-summary-value">{{ number_format((float) $todayStakeIncome, 4) }}</p>
                                <div class="stake-subcopy">Reward credit added today from all eligible active stakes.</div>
                            </div>
                            <span class="stake-icon" aria-hidden="true"><i class="fa fa-line-chart"></i></span>
                        </div>
                        <div class="stake-summary-card">
                            <div>
                                <div class="stake-meta-label">Total Reward Paid</div>
                                <p class="stake-summary-value">{{ number_format((float) $totalRewardPaid, 4) }}</p>
                                <div class="stake-subcopy">Lifetime staking rewards credited to your account so far.</div>
                            </div>
                            <span class="stake-icon is-income" aria-hidden="true"><i class="fa fa-table"></i></span>
                        </div>
                        <div class="stake-summary-card">
                            <div>
                                <div class="stake-meta-label">Active Plans</div>
                                <p class="stake-summary-value">{{ $plans->count() }}</p>
                                <div class="stake-subcopy">Total active plans currently available for you to review on this page.</div>
                            </div>
                            <span class="stake-icon is-alt" aria-hidden="true"><i class="fa fa-trophy"></i></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="stake-panel stake-panel--plans">
                <div class="stake-section-head">
                    <div>
                        <div class="stake-meta-label">Plans</div>
                        <h2 class="stake-section-title">Active Stake Plans</h2>
                        <p class="stake-section-copy">Choose a plan, check the required level and amount range, then stake directly from this page without leaving the flow.</p>
                    </div>
                </div>

                @if ($plans->isEmpty())
                    <div class="stake-empty">No active staking plans are available right now.</div>
                @else
                    <div class="stake-plan-grid">
                        @foreach ($plans as $plan)
                            <div class="stake-plan-card">
                                <div class="stake-plan-top">
                                    <h3 class="stake-plan-title">{{ $plan->name }}</h3>
                                </div>

                                <div class="stake-plan-lines">
                                    <div class="stake-plan-line">Min: {{ number_format((float) ($plan->min_amount ?? 0), 4) }} / Max: {{ $plan->max_amount ? number_format((float) $plan->max_amount, 4) : 'Unlimited' }}</div>
                                    <div class="stake-plan-line">Daily Rate: {{ number_format((float) $plan->daily_rate, 6) }}%</div>
                                    <div class="stake-plan-line">Duration: {{ $plan->duration_days }} days</div>
                                    <div class="stake-plan-line">Level Required: {{ $plan->requiredLevel?->code ?? 'All levels' }}</div>
                                </div>

                                <form method="POST" action="{{ route('stake.store') }}" class="stake-plan-form">
                                    @csrf
                                    <input type="hidden" name="stake_plan_id" value="{{ $plan->id }}">
                                    <input id="stake-amount-{{ $plan->id }}" name="amount" type="number" step="0.0001" min="0.0001" class="form-control" placeholder="Enter amount" required>
                                    <button type="submit" class="btn-main btn-light">Stake</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="stake-panel stake-panel--active">
                <div class="stake-section-head">
                    <div>
                        <div class="stake-meta-label">Portfolio</div>
                        <h2 class="stake-section-title">Your Active Stakes</h2>
                        <p class="stake-section-copy">Track the plan, principal, reward progress, and start time of each active position in a cleaner layout.</p>
                    </div>
                </div>

                @if ($stakes->isEmpty())
                    <div class="stake-empty">No active stakes yet.</div>
                @else
                    <div class="stake-table-wrap">
                        <table class="table stake-table align-middle">
                            <thead>
                                <tr>
                                    <th>Plan</th>
                                    <th>Principal</th>
                                    <th>Daily Rate</th>
                                    <th>Started</th>
                                    <th>Total Reward Paid</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($stakes as $stake)
                                    @php
                                        $status = strtolower((string) $stake->status);
                                        $statusClass = $status === 'active' ? 'is-active' : 'is-default';
                                    @endphp
                                    <tr>
                                        <td>
                                            <span class="stake-table-title">{{ $stake->stakePlan->name ?? '-' }}</span>
                                            @if ($stake->stakePlan?->requiredLevel?->code)
                                                <span class="stake-table-subtext">Required {{ $stake->stakePlan->requiredLevel->code }}</span>
                                            @endif
                                        </td>
                                        <td>{{ number_format((float) $stake->principal_amount, 4) }}</td>
                                        <td>{{ number_format((float) ($stake->stakePlan->daily_rate ?? 0), 6) }}%</td>
                                        <td>{{ optional($stake->started_at)->format('M d, Y h:i A') }}</td>
                                        <td>{{ number_format((float) $stake->total_reward_paid, 4) }}</td>
                                        <td><span class="stake-status-badge {{ $statusClass }}">{{ ucfirst($status) }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="stake-active-mobile">
                        @foreach ($stakes as $stake)
                            @php
                                $status = strtolower((string) $stake->status);
                                $statusClass = $status === 'active' ? 'is-active' : 'is-default';
                            @endphp
                            <div class="stake-mobile-card">
                                <div class="stake-mobile-top">
                                    <div>
                                        <div class="stake-table-title">{{ $stake->stakePlan->name ?? '-' }}</div>
                                        @if ($stake->stakePlan?->requiredLevel?->code)
                                            <div class="stake-table-subtext">Required {{ $stake->stakePlan->requiredLevel->code }}</div>
                                        @endif
                                    </div>
                                    <span class="stake-status-badge {{ $statusClass }}">{{ ucfirst($status) }}</span>
                                </div>
                                <div class="stake-mobile-meta"><span>Principal</span><strong>{{ number_format((float) $stake->principal_amount, 4) }}</strong></div>
                                <div class="stake-mobile-meta"><span>Daily Rate</span><strong>{{ number_format((float) ($stake->stakePlan->daily_rate ?? 0), 6) }}%</strong></div>
                                <div class="stake-mobile-meta"><span>Started</span><strong>{{ optional($stake->started_at)->format('M d, Y h:i A') }}</strong></div>
                                <div class="stake-mobile-meta"><span>Total Reward Paid</span><strong>{{ number_format((float) $stake->total_reward_paid, 4) }}</strong></div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="stake-panel stake-panel--income">
                <div class="stake-section-head">
                    <div>
                        <div class="stake-meta-label">Income History</div>
                        <h2 class="stake-section-title">Daily Stake Income</h2>
                        <p class="stake-section-copy">Review the reward day, rate, and credited amount for the latest stake-income ledger entries.</p>
                    </div>
                </div>

                @if ($recentStakeIncome->isEmpty())
                    <div class="stake-empty">No daily stake income has been credited yet.</div>
                @else
                    <div class="stake-table-wrap">
                        <table class="table stake-table align-middle">
                            <thead>
                                <tr>
                                    <th>Plan</th>
                                    <th>Principal</th>
                                    <th>Reward Day</th>
                                    <th>Rate</th>
                                    <th>Income</th>
                                    <th>Credited At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentStakeIncome as $ledger)
                                    @php
                                        $stakeRef = $stakeReferences->get($ledger->reference_id);
                                        $meta = is_array($ledger->meta) ? $ledger->meta : [];
                                    @endphp
                                    <tr>
                                        <td><span class="stake-table-title">{{ $stakeRef?->stakePlan?->name ?: ('Stake #' . $ledger->reference_id) }}</span></td>
                                        <td>{{ $stakeRef ? number_format((float) $stakeRef->principal_amount, 4) : '-' }}</td>
                                        <td>{{ $meta['day'] ?? '-' }}</td>
                                        <td>{{ isset($meta['rate']) ? number_format((float) $meta['rate'], 6) . '%' : '-' }}</td>
                                        <td><span class="stake-income-amount">+{{ number_format((float) $ledger->amount, 8) }}</span></td>
                                        <td>{{ optional($ledger->created_at)->format('M d, Y h:i A') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="stake-income-mobile">
                        @foreach ($recentStakeIncome as $ledger)
                            @php
                                $stakeRef = $stakeReferences->get($ledger->reference_id);
                                $meta = is_array($ledger->meta) ? $ledger->meta : [];
                            @endphp
                            <div class="stake-mobile-card">
                                <div class="stake-mobile-top">
                                    <div>
                                        <div class="stake-table-title">{{ $stakeRef?->stakePlan?->name ?: ('Stake #' . $ledger->reference_id) }}</div>
                                        <div class="stake-table-subtext">Stake reward credit</div>
                                    </div>
                                    <span class="stake-income-amount">+{{ number_format((float) $ledger->amount, 8) }}</span>
                                </div>
                                <div class="stake-mobile-meta"><span>Principal</span><strong>{{ $stakeRef ? number_format((float) $stakeRef->principal_amount, 4) : '-' }}</strong></div>
                                <div class="stake-mobile-meta"><span>Reward Day</span><strong>{{ $meta['day'] ?? '-' }}</strong></div>
                                <div class="stake-mobile-meta"><span>Rate</span><strong>{{ isset($meta['rate']) ? number_format((float) $meta['rate'], 6) . '%' : '-' }}</strong></div>
                                <div class="stake-mobile-meta"><span>Credited At</span><strong>{{ optional($ledger->created_at)->format('M d, Y h:i A') }}</strong></div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
