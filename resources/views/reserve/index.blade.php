@extends('layouts.frontend')

@push('styles')
<style>
    .reserve-page {
        --reserve-panel-bg: linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(248, 245, 255, 0.96));
        --reserve-panel-border: rgba(15, 23, 42, 0.08);
        --reserve-panel-shadow: 0 18px 36px rgba(15, 23, 42, 0.08);
        --reserve-surface-bg: rgba(15, 23, 42, 0.04);
        --reserve-surface-border: rgba(15, 23, 42, 0.08);
        --reserve-title: #121826;
        --reserve-text: #253044;
        --reserve-muted: #617086;
        --reserve-select-bg: #ffffff;
        --reserve-select-border: rgba(15, 23, 42, 0.12);
        --reserve-select-text: #111827;
        --reserve-select-option-bg: #ffffff;
        --reserve-select-option-text: #111827;
        --reserve-select-option-disabled: #98a2b3;
        --reserve-note-bg: rgba(15, 23, 42, 0.04);
        --reserve-note-border: rgba(15, 23, 42, 0.08);
        --reserve-note-text: #334155;
    }
    .dark-scheme .reserve-page {
        --reserve-panel-bg: linear-gradient(180deg, rgba(28, 18, 47, 0.96), rgba(19, 13, 34, 0.96));
        --reserve-panel-border: rgba(255, 255, 255, 0.08);
        --reserve-panel-shadow: 0 24px 48px rgba(0, 0, 0, 0.34);
        --reserve-surface-bg: rgba(255, 255, 255, 0.04);
        --reserve-surface-border: rgba(255, 255, 255, 0.08);
        --reserve-title: #ffffff;
        --reserve-text: #edf2f7;
        --reserve-muted: #aeb7c4;
        --reserve-select-bg: rgba(255, 255, 255, 0.05);
        --reserve-select-border: rgba(255, 255, 255, 0.12);
        --reserve-select-text: #ffffff;
        --reserve-select-option-bg: #1a1327;
        --reserve-select-option-text: #f8fbff;
        --reserve-select-option-disabled: #7f8a9f;
        --reserve-note-bg: rgba(255, 255, 255, 0.04);
        --reserve-note-border: rgba(255, 255, 255, 0.08);
        --reserve-note-text: #d5dae3;
    }
    .reserve-page .nft__item.s2 {
        background: var(--reserve-panel-bg);
        border: 1px solid var(--reserve-panel-border);
        box-shadow: var(--reserve-panel-shadow);
        border-radius: 22px;
    }
    .reserve-page .nft__item_info h4,
    .reserve-page .nft__item_info .nft__item_price,
    .reserve-page .nft__item_info .text-muted,
    .reserve-page .nft__item_info p {
        color: var(--reserve-text);
    }
    .reserve-page .nft__item_info h4 {
        color: var(--reserve-title);
    }
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
    .reserve-option-card.is-disabled {
        opacity: 0.78;
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
    .reserve-option-status {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }
    .reserve-option-status.is-available {
        background: rgba(17, 185, 129, 0.16);
        color: #7cf0bd;
    }
    .reserve-option-status.is-blocked {
        background: rgba(255, 255, 255, 0.08);
        color: #d5dae3;
    }
    .reserve-option-note {
        min-height: 42px;
        color: #d5dae3;
        font-size: 14px;
        margin-bottom: 14px;
    }
    .reserve-option-form {
        margin: 0;
    }
    .reserve-selector {
        display: grid;
        gap: 18px;
    }
    .reserve-selector__row {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }
    .reserve-selector__field {
        display: grid;
        gap: 8px;
    }
    .reserve-selector__field label {
        color: var(--reserve-muted);
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }
    .reserve-selector__select {
        width: 100%;
        min-height: 52px;
        padding: 0 14px;
        border-radius: 16px;
        border: 1px solid var(--reserve-select-border);
        background-color: var(--reserve-select-bg);
        color: var(--reserve-select-text);
        font-weight: 700;
        appearance: auto;
        -webkit-appearance: menulist;
        -moz-appearance: auto;
        background-image: none !important;
        box-shadow: 0 10px 22px rgba(15, 23, 42, 0.06);
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
        color-scheme: light;
    }
    .dark-scheme .reserve-selector__select {
        box-shadow: none;
        color-scheme: dark;
    }
    .reserve-selector__select:focus {
        outline: none;
        border-color: rgba(var(--primary-color-rgb, 111, 51, 204), 0.45);
        box-shadow: 0 0 0 4px rgba(var(--primary-color-rgb, 111, 51, 204), 0.12);
    }
    .reserve-selector__select option {
        background: var(--reserve-select-option-bg);
        color: var(--reserve-select-option-text);
    }
    .reserve-selector__select option:disabled {
        color: var(--reserve-select-option-disabled);
    }
    .reserve-selector__summary {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
    }
    .reserve-selector__stat {
        padding: 14px 16px;
        border-radius: 16px;
        background: var(--reserve-surface-bg);
        border: 1px solid var(--reserve-surface-border);
    }
    .reserve-selector__stat span {
        display: block;
        margin-bottom: 6px;
        color: var(--reserve-muted);
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }
    .reserve-selector__stat strong {
        color: var(--reserve-title);
        font-size: 18px;
        font-weight: 800;
    }
    .reserve-selector__note {
        padding: 14px 16px;
        border-radius: 16px;
        background: var(--reserve-note-bg);
        border: 1px solid var(--reserve-note-border);
        color: var(--reserve-note-text);
    }
    .reserve-selector__actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }
    .reserve-selector__actions .btn-main {
        min-width: 220px;
        text-align: center;
    }
    .reserve-flow-note {
        color: var(--reserve-muted);
        font-size: 14px;
        margin-top: 12px;
        line-height: 1.85;
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
    .reserve-loader__bar::after {
        content: "";
        position: absolute;
        inset: 0;
        width: 40%;
        border-radius: inherit;
        background: linear-gradient(135deg, #f0a83a, #6f33cc);
        animation: reserve-loader-slide 1s linear infinite;
    }
    .reserve-modal {
        position: fixed;
        inset: 0;
        z-index: 2900;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(7, 8, 18, 0.82);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }
    .reserve-modal.is-visible {
        display: flex;
    }
    .reserve-modal__dialog {
        position: relative;
        width: min(860px, calc(100vw - 32px));
        max-height: calc(100vh - 40px);
        overflow-y: auto;
        padding: 24px;
        border-radius: 24px;
        background: var(--reserve-panel-bg);
        border: 1px solid var(--reserve-panel-border);
        box-shadow: var(--reserve-panel-shadow);
    }
    .reserve-modal__close {
        position: absolute;
        top: 14px;
        right: 14px;
        width: 42px;
        height: 42px;
        border: 0;
        border-radius: 999px;
        background: var(--reserve-surface-bg);
        border: 1px solid var(--reserve-surface-border);
        color: var(--reserve-title);
        font-size: 24px;
        line-height: 1;
    }
    .reserve-modal__head {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 20px;
        padding-right: 40px;
    }
    .reserve-modal__icon {
        width: 68px;
        height: 68px;
        border-radius: 22px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, rgba(240, 168, 58, 0.18), rgba(111, 51, 204, 0.24));
        border: 1px solid rgba(240, 168, 58, 0.22);
        box-shadow: 0 16px 28px rgba(111, 51, 204, 0.18);
    }
    .reserve-modal__icon img {
        width: 42px;
        height: 42px;
        object-fit: contain;
    }
    .reserve-modal__title {
        margin: 0 0 4px;
        color: var(--reserve-title);
        font-size: 28px;
        font-weight: 800;
    }
    .reserve-modal__copy {
        margin: 0;
        color: var(--reserve-muted);
        line-height: 1.7;
    }
    .reserve-modal__grid {
        display: grid;
        grid-template-columns: minmax(0, 280px) minmax(0, 1fr);
        gap: 18px;
        margin-bottom: 18px;
    }
    .reserve-modal__panel {
        padding: 18px;
        border-radius: 18px;
        background: var(--reserve-surface-bg);
        border: 1px solid var(--reserve-surface-border);
    }
    .reserve-modal__panel h5 {
        margin-bottom: 12px;
        color: var(--reserve-title);
        font-size: 18px;
        font-weight: 800;
    }
    .reserve-modal__meta {
        display: grid;
        gap: 8px;
        color: var(--reserve-text);
    }
    .reserve-modal__nft-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }
    .reserve-modal__nft {
        display: block;
        height: 100%;
        padding: 12px;
        border-radius: 18px;
        background: var(--reserve-surface-bg);
        border: 1px solid var(--reserve-surface-border);
    }
    .reserve-modal__nft img {
        width: 100%;
        max-height: 150px;
        object-fit: cover;
        border-radius: 14px;
        margin-top: 10px;
    }
    .reserve-modal__selected {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 12px;
        border-radius: 18px;
        background: var(--reserve-surface-bg);
        border: 1px solid var(--reserve-surface-border);
        margin-bottom: 16px;
    }
    .reserve-modal__selected img {
        width: 92px;
        height: 92px;
        object-fit: cover;
        border-radius: 16px;
        flex: 0 0 92px;
    }
    .reserve-modal__actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 18px;
    }
    .reserve-modal__actions .btn-main,
    .reserve-modal__actions .btn-border {
        min-width: 180px;
        text-align: center;
    }
    body.reserve-modal-open {
        overflow: hidden;
    }
    @keyframes reserve-loader-pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.08); }
    }
    @keyframes reserve-loader-slide {
        0% { transform: translateX(-120%); }
        100% { transform: translateX(260%); }
    }
    @media (max-width: 767px) {
        .reserve-selector__row,
        .reserve-selector__summary {
            grid-template-columns: 1fr;
        }
        .reserve-modal__grid,
        .reserve-modal__nft-grid {
            grid-template-columns: 1fr;
        }
        .reserve-modal__selected {
            align-items: flex-start;
        }
        .reserve-modal__actions .btn-main,
        .reserve-modal__actions .btn-border {
            width: 100%;
            min-width: 0;
        }
        .reserve-page .nft__item.s2 {
            border-radius: 18px;
        }
        .reserve-selector__actions .btn-main {
            width: 100%;
            min-width: 0;
        }
    }
</style>
@endpush

@section('content')
@include('frontend.partials.page-banner', ['title' => 'Reserve PI'])

<section aria-label="section" class="reserve-page">
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
                <button type="button" class="btn btn-sm btn-primary" data-open-reserve-modal>Sell PI Now</button>
            </div>
        @elseif ($reserveOptions->isNotEmpty() && $availableOptionCount === 0)
            <div class="alert alert-warning">
                No reserve option is available right now. Use the dropdowns below to review which plans are locked or already exhausted for today.
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
                            <div class="nft__item_price">Unlocked Reserve Options: {{ $unlockedOptionCount }}</div>
                        @else
                            <div class="text-muted">No active level found.</div>
                        @endif
                        <div class="nft__item_price">Visible Reserve Options: {{ $reserveOptions->count() }}</div>
                        <div class="nft__item_price">Available Right Now: {{ $availableOptionCount }}</div>
                        <p class="reserve-flow-note">
                            Reserve uses your level qualification from level rules. Each reserve plan then provides profit, sell limits, and selectable reserve criteria for that level.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-lg-7 mb30">
                <div class="nft__item s2">
                    <div class="nft__item_info">
                        <h4>Reservation Options</h4>
                        @if ($reserveOptions->isEmpty())
                            <div class="text-muted">No reserve plans are configured yet.</div>
                        @else
                            @php
                                $activeLevelId = optional($reserveOptions->firstWhere('is_active_option', true))->getAttribute('level_id');
                                $availableLevelId = optional($reserveOptions->firstWhere('can_reserve', true))->getAttribute('level_id');
                                $initialLevelId = $activeLevelId ?? $availableLevelId ?? optional($reserveOptions->first())->getAttribute('level_id');
                                $levelGroups = $reserveOptions->groupBy('level_id');
                                $planPayload = $reserveOptions->map(function ($option) {
                                    return [
                                        'id' => (int) $option->id,
                                        'planId' => (int) $option->reserve_plan_id,
                                        'levelId' => (int) $option->getAttribute('level_id'),
                                        'levelLabel' => $option->getAttribute('level_label'),
                                        'profitRange' => $option->plan->profit_min_percent . '% - ' . $option->plan->profit_max_percent . '%',
                                        'rangeLabel' => $option->getAttribute('range_label'),
                                        'reserveAmountLabel' => $option->getAttribute('computed_reserve_label'),
                                        'maxSells' => $option->plan->max_sells ? (string) $option->plan->max_sells : 'Unlimited',
                                        'dailyLimit' => is_null($option->plan->max_sells_per_day) ? 'Unlimited' : (string) $option->plan->max_sells_per_day,
                                        'usedToday' => (int) $option->used_today,
                                        'remainingToday' => is_null($option->daily_remaining) ? 'Unlimited' : (string) $option->daily_remaining,
                                        'canReserve' => (bool) $option->can_reserve,
                                        'isActiveOption' => (bool) $option->is_active_option,
                                        'note' => $option->availability_note,
                                        'actionLabel' => $option->action_label,
                                    ];
                                })->values();
                            @endphp

                            <div class="reserve-selector" data-initial-level="{{ $initialLevelId }}">
                                <div class="reserve-selector__row">
                                    <div class="reserve-selector__field">
                                        <label for="reserve-level-select">Level</label>
                                        <select id="reserve-level-select" class="reserve-selector__select">
                                            @foreach ($levelGroups as $levelId => $levelPlans)
                                                @php($firstPlan = $levelPlans->first())
                                                <option value="{{ $levelId }}" @selected((string) $initialLevelId === (string) $levelId)>
                                                    {{ $firstPlan->getAttribute('level_label') }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="reserve-selector__field">
                                        <label for="reserve-plan-select">Reserve Option</label>
                                        <select id="reserve-plan-select" class="reserve-selector__select"></select>
                                    </div>
                                </div>

                                <div class="reserve-selector__summary">
                                    <div class="reserve-selector__stat">
                                        <span>Status</span>
                                        <strong id="reserve-selected-status">-</strong>
                                    </div>
                                    <div class="reserve-selector__stat">
                                        <span>Profit</span>
                                        <strong id="reserve-selected-profit">-</strong>
                                    </div>
                                    <div class="reserve-selector__stat">
                                        <span>Daily Limit</span>
                                        <strong id="reserve-selected-daily-limit">-</strong>
                                    </div>
                                    <div class="reserve-selector__stat">
                                        <span>Remaining</span>
                                        <strong id="reserve-selected-remaining">-</strong>
                                    </div>
                                </div>

                                <div class="reserve-selector__note" id="reserve-selected-note">Select a reserve option to continue.</div>

                                <div class="reserve-selector__actions">
                                    <form method="POST" action="{{ route('reserve.confirm') }}" class="reserve-option-form reserve-start-form" id="reserve-plan-form" data-loader-title="Preparing Buy PI" data-loader-copy="Please wait while your reserve is being created.">
                                        @csrf
                                        <input type="hidden" name="reserve_plan_id" id="reserve_plan_id">
                                        <input type="hidden" name="reserve_plan_range_id" id="reserve_plan_range_id">
                                        <button type="submit" class="btn-main" id="reserve-plan-submit">Confirm Reserve</button>
                                    </form>

                                    <button type="button" class="btn-main" id="reserve-go-buy-pi" style="display:none;" data-open-reserve-modal>Sell PI Now</button>
                                </div>
                            </div>

                            <script type="application/json" id="reserve-plan-data">{!! $planPayload->toJson(JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
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

@if (!empty($activeReserve))
    @include('reserve.partials.sell-modal', [
        'activeReserve' => $activeReserve,
        'sellItems' => $sellItems,
        'nftEnabled' => $nftEnabled,
    ])
@endif

<div class="reserve-loader" id="reserve-loader" aria-hidden="true">
    <div class="reserve-loader__card">
        <img src="{{ asset('frontend/images/icon.png') }}" alt="Loading" class="reserve-loader__logo">
        <div class="reserve-loader__title" id="reserve-loader-title">Preparing Buy PI</div>
        <div class="reserve-loader__copy" id="reserve-loader-copy">Please wait while your reserve is being created.</div>
        <div class="reserve-loader__bar"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var reserveLoader = document.getElementById('reserve-loader');
        var reserveLoaderTitle = document.getElementById('reserve-loader-title');
        var reserveLoaderCopy = document.getElementById('reserve-loader-copy');
        var levelSelect = document.getElementById('reserve-level-select');
        var planSelect = document.getElementById('reserve-plan-select');
        var hiddenPlanId = document.getElementById('reserve_plan_id');
        var hiddenRangeId = document.getElementById('reserve_plan_range_id');
        var submitButton = document.getElementById('reserve-plan-submit');
        var buyPiButton = document.getElementById('reserve-go-buy-pi');
        var selectedStatus = document.getElementById('reserve-selected-status');
        var selectedProfit = document.getElementById('reserve-selected-profit');
        var selectedDailyLimit = document.getElementById('reserve-selected-daily-limit');
        var selectedRemaining = document.getElementById('reserve-selected-remaining');
        var selectedNote = document.getElementById('reserve-selected-note');
        var sellModal = document.getElementById('reserve-sell-modal');
        var openSellButtons = document.querySelectorAll('[data-open-reserve-modal]');
        var closeSellButtons = document.querySelectorAll('[data-close-reserve-modal]');
        var reserveSaleAmount = document.getElementById('reserve-sale-amount');
        var reserveSelectedImage = document.getElementById('reserve-selected-nft-image');
        var reserveSelectedTitle = document.getElementById('reserve-selected-nft-title');
        var reserveSelectedPrice = document.getElementById('reserve-selected-nft-price');
        var planDataElement = document.getElementById('reserve-plan-data');
        var plans = planDataElement ? JSON.parse(planDataElement.textContent || '[]') : [];
        var shouldAutoOpenSellModal = @json((bool) session('open_sell_modal'));

        function setLoaderCopy(title, copy) {
            if (reserveLoaderTitle && title) {
                reserveLoaderTitle.textContent = title;
            }
            if (reserveLoaderCopy && copy) {
                reserveLoaderCopy.textContent = copy;
            }
        }

        function showLoader(title, copy) {
            if (!reserveLoader) {
                return;
            }
            setLoaderCopy(title, copy);
            reserveLoader.classList.add('is-visible');
            reserveLoader.setAttribute('aria-hidden', 'false');
        }

        function openSellModal() {
            if (!sellModal) {
                return;
            }
            sellModal.classList.add('is-visible');
            sellModal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('reserve-modal-open');
        }

        function closeSellModal() {
            if (!sellModal) {
                return;
            }
            sellModal.classList.remove('is-visible');
            sellModal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('reserve-modal-open');
        }

        function groupPlansByLevel() {
            return plans.reduce(function (carry, plan) {
                if (!carry[plan.levelId]) {
                    carry[plan.levelId] = [];
                }
                carry[plan.levelId].push(plan);
                return carry;
            }, {});
        }

        function populatePlanOptions(levelId) {
            if (!planSelect) {
                return;
            }

            var groupedPlans = groupPlansByLevel();
            var levelPlans = groupedPlans[levelId] || [];

            planSelect.innerHTML = '';

            levelPlans.forEach(function (plan) {
                var option = document.createElement('option');
                option.value = String(plan.id);
                option.textContent = plan.rangeLabel;
                planSelect.appendChild(option);
            });

            if (levelPlans.length > 0) {
                var activePlan = levelPlans.find(function (plan) { return plan.isActiveOption; });
                var availablePlan = levelPlans.find(function (plan) { return plan.canReserve; });
                planSelect.value = String((activePlan || availablePlan || levelPlans[0]).id);
            }
        }

        function updatePlanDetails() {
            if (!planSelect) {
                return;
            }

            var selectedPlan = plans.find(function (plan) {
                return String(plan.id) === String(planSelect.value);
            });

            if (!selectedPlan) {
                return;
            }

            if (hiddenPlanId) {
                hiddenPlanId.value = selectedPlan.planId;
            }
            if (hiddenRangeId) {
                hiddenRangeId.value = selectedPlan.id;
            }
            if (selectedStatus) {
                selectedStatus.textContent = selectedPlan.isActiveOption ? 'In Progress' : (selectedPlan.canReserve ? 'Available' : 'Blocked');
            }
            if (selectedProfit) {
                selectedProfit.textContent = selectedPlan.profitRange;
            }
            if (selectedDailyLimit) {
                selectedDailyLimit.textContent = selectedPlan.dailyLimit;
            }
            if (selectedRemaining) {
                selectedRemaining.textContent = selectedPlan.remainingToday;
            }
            if (selectedNote) {
                selectedNote.textContent = 'Level amount range: ' + selectedPlan.rangeLabel + ' | Reserve amount: ' + selectedPlan.reserveAmountLabel + ' | Profit: ' + selectedPlan.profitRange + ' | ' + selectedPlan.note;
            }

            if (selectedPlan.isActiveOption) {
                if (submitButton) {
                    submitButton.style.display = 'none';
                }
                if (buyPiButton) {
                    buyPiButton.style.display = 'inline-flex';
                }
                return;
            }

            if (buyPiButton) {
                buyPiButton.style.display = 'none';
            }
            if (submitButton) {
                submitButton.style.display = 'inline-flex';
                submitButton.disabled = !selectedPlan.canReserve;
                submitButton.textContent = selectedPlan.actionLabel;
            }
        }

        if (levelSelect && planSelect && plans.length > 0) {
            populatePlanOptions(levelSelect.value);
            updatePlanDetails();

            levelSelect.addEventListener('change', function () {
                populatePlanOptions(levelSelect.value);
                updatePlanDetails();
            });

            planSelect.addEventListener('change', updatePlanDetails);
        }

        openSellButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                openSellModal();
            });
        });

        closeSellButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                closeSellModal();
            });
        });

        if (sellModal) {
            sellModal.addEventListener('click', function (event) {
                if (event.target === sellModal) {
                    closeSellModal();
                }
            });
        }

        document.querySelectorAll('.reserve-nft-select').forEach(function (radio) {
            radio.addEventListener('change', function () {
                if (reserveSelectedImage) {
                    reserveSelectedImage.src = this.dataset.image || '';
                }
                if (reserveSelectedTitle) {
                    reserveSelectedTitle.textContent = this.dataset.title || '';
                }
                if (reserveSelectedPrice && reserveSaleAmount) {
                    reserveSelectedPrice.textContent = 'Reserve Amount: ' + parseFloat(reserveSaleAmount.value || 0).toFixed(8) + ' USDT';
                }
            });
        });

        document.querySelectorAll('.reserve-start-form, .reserve-sell-form').forEach(function (form) {
            form.addEventListener('submit', function () {
                showLoader(
                    form.dataset.loaderTitle || 'Processing',
                    form.dataset.loaderCopy || 'Please wait while we complete your request.'
                );
            });
        });

        if (shouldAutoOpenSellModal && sellModal && !document.getElementById('notif-modal')) {
            openSellModal();
        }
    });
</script>
@endpush
