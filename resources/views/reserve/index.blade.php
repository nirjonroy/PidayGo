@extends('layouts.frontend')

@push('styles')
    @include('reserve.partials.index-styles')
@endpush

@section('content')
@include('frontend.partials.page-banner', ['title' => 'Reserve PI'])

@php
    $activeReserveAmount = (float) ($activeReserve->amount ?? 0);
    $sellIncomeTotal = (float) $recentReserveSales->sum('profit_amount');
    $reserveStartedCount = $recentReserveLedgers->where('reason', 'reserve_started')->count();
    $recentSellCount = $recentReserveSales->count();
    $activeLevelId = optional($reserveOptions->firstWhere('is_active_option', true))->getAttribute('level_id');
    $availableLevelId = optional($reserveOptions->firstWhere('can_reserve', true))->getAttribute('level_id');
    $initialLevelId = $activeLevelId ?? $availableLevelId ?? optional($reserveOptions->first())->getAttribute('level_id');
    $levelGroups = $reserveOptions->groupBy('level_id');
    $activeReserveConfirmedAt = !empty($activeReserve) ? optional($activeReserve->confirmed_at)->format('M d, Y h:i A') : null;
    $planPayload = $reserveOptions->map(function ($option) {
        return [
            'id' => (int) $option->id,
            'planId' => (int) $option->reserve_plan_id,
            'levelId' => (int) $option->getAttribute('level_id'),
            'levelLabel' => $option->getAttribute('level_label'),
            'profitRange' => $option->plan->profit_min_percent . '% - ' . $option->plan->profit_max_percent . '%',
            'rangeLabel' => $option->getAttribute('range_label'),
            'reserveAmountLabel' => $option->getAttribute('computed_reserve_label'),
            'dailyLimit' => is_null($option->plan->max_sells_per_day) ? 'Unlimited' : (string) $option->plan->max_sells_per_day,
            'remainingToday' => is_null($option->daily_remaining) ? 'Unlimited' : (string) $option->daily_remaining,
            'canReserve' => (bool) $option->can_reserve,
            'isActiveOption' => (bool) $option->is_active_option,
            'note' => $option->availability_note,
            'actionLabel' => $option->action_label,
        ];
    })->values();
@endphp

<section class="reserve-shell" aria-label="Reserve overview">
    <div class="container">
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        @if (!empty($activeReserve))
            <div class="alert alert-warning d-flex justify-content-between align-items-center gap-3">
                <span>You already have an active reserve. Open the Sell PI flow and complete the sell to return reserve amount with profit to wallet.</span>
                <button type="button" class="btn btn-sm btn-primary" data-open-reserve-modal>Sell PI Now</button>
            </div>
        @elseif ($reserveOptions->isNotEmpty() && $availableOptionCount === 0)
            <div class="alert alert-warning">No reserve option is available right now. Use the selector below to review which reserve levels are locked or already exhausted for today.</div>
        @endif

        <div class="reserve-stack">
            <div class="reserve-overview">
                <div class="reserve-panel reserve-panel--hero">
                    <div class="reserve-section-head reserve-section-head--hero">
                        <div>
                            <div class="reserve-meta-label">Reserve Hub</div>
                            <h2 class="reserve-section-title">Reserve And Sell PI</h2>
                            <p class="reserve-section-copy">Reserve follows your unlocked level, then lets you complete the PI sell flow so the reserve amount and profit are credited back to your wallet.</p>
                        </div>
                        <span class="reserve-icon" aria-hidden="true"><i class="fa fa-shield"></i></span>
                    </div>

                    <div class="reserve-balance-grid">
                        <div class="reserve-summary-card">
                            <div>
                                <div class="reserve-meta-label">Wallet Balance</div>
                                <p class="reserve-summary-card__value">{{ number_format($walletBalance, 4) }}</p>
                                <div class="reserve-card-copy">USDT currently available in your main wallet.</div>
                            </div>
                            <span class="reserve-summary-card__icon" aria-hidden="true"><i class="fa fa-wallet"></i></span>
                        </div>
                        <div class="reserve-summary-card">
                            <div>
                                <div class="reserve-meta-label">Reserve Account</div>
                                <p class="reserve-summary-card__value">{{ number_format($reserveAccountBalance, 4) }}</p>
                                <div class="reserve-card-copy">Held inside reserve until the PI sell is completed.</div>
                            </div>
                            <span class="reserve-summary-card__icon is-alt" aria-hidden="true"><i class="fa fa-lock"></i></span>
                        </div>
                    </div>

                    <div class="reserve-glance-grid">
                        <div class="reserve-card">
                            <div class="reserve-meta-label">Current Level</div>
                            <div class="reserve-card__value">{{ $level?->code ?? 'Not set' }}</div>
                            <div class="reserve-card-copy">{{ $level ? 'Resolved from your deposit and chain qualification.' : 'No active level could be resolved yet.' }}</div>
                        </div>
                        <div class="reserve-card">
                            <div class="reserve-meta-label">Available Right Now</div>
                            <div class="reserve-card__value">{{ $availableOptionCount }}</div>
                            <div class="reserve-card-copy">{{ $unlockedOptionCount }} unlocked and {{ $reserveOptions->count() }} visible reserve options.</div>
                        </div>
                        <div class="reserve-card">
                            <div class="reserve-meta-label">Current Reserve</div>
                            <div class="reserve-card__value">{{ !empty($activeReserve) ? number_format($activeReserveAmount, 4) : '0.0000' }}</div>
                            <div class="reserve-card-copy">{{ !empty($activeReserve) ? 'An active reserve is ready for Sell PI.' : 'No reserve is waiting for sell right now.' }}</div>
                        </div>
                        <div class="reserve-card">
                            <div class="reserve-meta-label">Recent Profit</div>
                            <div class="reserve-card__value">{{ number_format($sellIncomeTotal, 4) }}</div>
                            <div class="reserve-card-copy">{{ $recentSellCount }} recent PI sell records are included in this total.</div>
                        </div>
                    </div>

                    <div class="reserve-action-grid">
                        <a href="{{ route('wallet.index') }}" class="btn-main reserve-action-btn"><i class="fa fa-wallet" aria-hidden="true"></i><span>Wallet</span></a>
                        <a href="{{ route('wallet.deposit') }}" class="btn-main btn-light reserve-action-btn"><i class="fa fa-arrow-circle-down" aria-hidden="true"></i><span>Deposit</span></a>
                        <a href="{{ route('wallet.withdrawals') }}" class="btn-main btn-light reserve-action-btn"><i class="fa fa-arrow-circle-up" aria-hidden="true"></i><span>Withdraw</span></a>
                        <a href="{{ route('stake.index') }}" class="btn-main reserve-action-btn"><i class="fa fa-line-chart" aria-hidden="true"></i><span>Stake</span></a>
                    </div>

                    @if (!empty($activeReserve))
                        <div class="reserve-live-card">
                            <div>
                                <div class="reserve-meta-label">Sell PI Ready</div>
                                <div class="reserve-live-card__value">{{ number_format($activeReserveAmount, 4) }} USDT</div>
                                <div class="reserve-card-copy">
                                    Active reserve confirmed{{ $activeReserveConfirmedAt ? ' on ' . $activeReserveConfirmedAt : '' }}.
                                    Open Sell PI to complete the flow and credit reserve amount plus profit back to wallet.
                                </div>
                            </div>
                            <button type="button" class="btn-main" data-open-reserve-modal>Open Sell PI</button>
                        </div>
                    @endif
                </div>

                <div class="reserve-panel">
                    <div class="reserve-section-head">
                        <div>
                            <div class="reserve-meta-label">Reservation Options</div>
                            <h2 class="reserve-section-title">Choose Your Reserve Criteria</h2>
                            <p class="reserve-section-copy">Pick the unlocked level and reserve range you want to use, then confirm reserve or continue directly to Sell PI if one is already active.</p>
                        </div>
                        <span class="reserve-icon is-income" aria-hidden="true"><i class="fa fa-sliders"></i></span>
                    </div>

                    @if ($reserveOptions->isEmpty())
                        <div class="reserve-empty">No reserve plans are configured yet.</div>
                    @else
                        <div class="reserve-selector" data-initial-level="{{ $initialLevelId }}">
                            <div class="reserve-selector__row">
                                <div class="reserve-selector__field">
                                    <label for="reserve-level-select">Level</label>
                                    <select id="reserve-level-select" class="reserve-selector__select">
                                        @foreach ($levelGroups as $levelId => $levelPlans)
                                            @php($firstPlan = $levelPlans->first())
                                            <option value="{{ $levelId }}" @selected((string) $initialLevelId === (string) $levelId)>{{ $firstPlan->getAttribute('level_label') }}</option>
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
                                    <strong id="reserve-selected-status" class="reserve-status-badge is-blocked">-</strong>
                                </div>
                                <div class="reserve-selector__stat">
                                    <span>Reserve Amount</span>
                                    <strong id="reserve-selected-amount">-</strong>
                                </div>
                                <div class="reserve-selector__stat">
                                    <span>Profit</span>
                                    <strong id="reserve-selected-profit">-</strong>
                                </div>
                                <div class="reserve-selector__stat">
                                    <span>Remaining</span>
                                    <strong id="reserve-selected-remaining">-</strong>
                                </div>
                            </div>

                            <div class="reserve-selector__glance">
                                <div class="reserve-pill-card">
                                    <span>Daily Limit</span>
                                    <strong id="reserve-selected-daily-limit">-</strong>
                                </div>
                                <div class="reserve-pill-card">
                                    <span>Recent PI Sells</span>
                                    <strong>{{ $recentSellCount }}</strong>
                                </div>
                                <div class="reserve-pill-card">
                                    <span>Recent Profit</span>
                                    <strong>{{ number_format($sellIncomeTotal, 4) }}</strong>
                                </div>
                            </div>

                            <div class="reserve-selector__note" id="reserve-selected-note">Select a reserve option to continue.</div>

                            <div class="reserve-selector__actions">
                                <form method="POST" action="{{ route('reserve.confirm') }}" class="reserve-start-form" id="reserve-plan-form" data-loader-title="Preparing Buy PI" data-loader-copy="Please wait while your reserve is being created.">
                                    @csrf
                                    <input type="hidden" name="reserve_plan_id" id="reserve_plan_id">
                                    <input type="hidden" name="reserve_plan_range_id" id="reserve_plan_range_id">
                                    <button type="submit" class="btn-main" id="reserve-plan-submit">Confirm Reserve</button>
                                </form>
                                <button type="button" class="btn-border" id="reserve-go-buy-pi" style="display:none;" data-open-reserve-modal>Sell PI Now</button>
                            </div>
                        </div>

                        <script type="application/json" id="reserve-plan-data">{!! $planPayload->toJson(JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
                    @endif
                </div>
            </div>

            <div class="reserve-summary-grid">
                <div class="reserve-stat-card">
                    <div>
                        <div class="reserve-meta-label">Reserve Starts</div>
                        <p class="reserve-stat-value">{{ $reserveStartedCount }}</p>
                        <div class="reserve-subcopy">Recent reserve start entries visible in your reserve ledger.</div>
                    </div>
                </div>
                <div class="reserve-stat-card">
                    <div>
                        <div class="reserve-meta-label">Recent PI Sells</div>
                        <p class="reserve-stat-value">{{ $recentSellCount }}</p>
                        <div class="reserve-subcopy">Visible PI sell records tied to reserve activity.</div>
                    </div>
                </div>
                <div class="reserve-stat-card">
                    <div>
                        <div class="reserve-meta-label">Recent Profit</div>
                        <p class="reserve-stat-value">{{ number_format($sellIncomeTotal, 4) }}</p>
                        <div class="reserve-subcopy">Profit from the visible recent PI sell income records.</div>
                    </div>
                </div>
                <div class="reserve-stat-card">
                    <div>
                        <div class="reserve-meta-label">Reserve Enabled</div>
                        <p class="reserve-stat-value">{{ $reserveEnabled ? 'Yes' : 'No' }}</p>
                        <div class="reserve-subcopy">{{ $reserveEnabled ? 'Reserve and sell actions are currently enabled.' : 'Reserve actions are disabled right now.' }}</div>
                    </div>
                </div>
            </div>

            <div class="reserve-history-grid">
                <div class="reserve-panel">
                    <div class="reserve-section-head">
                        <div>
                            <div class="reserve-meta-label">Reserve Ledger</div>
                            <h2 class="reserve-section-title">Recent Reserve Ledger</h2>
                            <p class="reserve-section-copy">Track reserve adds, starts, releases, and completion-related movements in one place.</p>
                        </div>
                    </div>

                    @if ($recentReserveLedgers->isEmpty())
                        <div class="reserve-empty">No reserve ledger entries found yet.</div>
                    @else
                        <div class="reserve-history-wrap">
                            <table class="table reserve-table align-middle">
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
                                        @php($change = (float) $ledger->change)
                                        @php($referenceLabel = $ledger->ref_type && $ledger->ref_id ? \Illuminate\Support\Str::headline(str_replace('_', ' ', $ledger->ref_type)) . ' #' . $ledger->ref_id : '-')
                                        <tr>
                                            <td><span class="reserve-amount {{ $change < 0 ? 'is-debit' : 'is-credit' }}">{{ $change >= 0 ? '+' : '' }}{{ number_format($change, 8) }}</span></td>
                                            <td><span class="reserve-table-title">{{ \Illuminate\Support\Str::headline(str_replace('_', ' ', $ledger->reason)) }}</span></td>
                                            <td><span class="reserve-table-subtext">{{ $referenceLabel }}</span></td>
                                            <td><span class="reserve-table-subtext">{{ optional($ledger->created_at)->format('M d, Y h:i A') }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="reserve-history-mobile">
                            @foreach ($recentReserveLedgers as $ledger)
                                @php($change = (float) $ledger->change)
                                @php($referenceLabel = $ledger->ref_type && $ledger->ref_id ? \Illuminate\Support\Str::headline(str_replace('_', ' ', $ledger->ref_type)) . ' #' . $ledger->ref_id : '-')
                                <div class="reserve-mobile-card">
                                    <div class="reserve-mobile-top">
                                        <div>
                                            <div class="reserve-table-title">{{ \Illuminate\Support\Str::headline(str_replace('_', ' ', $ledger->reason)) }}</div>
                                            <div class="reserve-table-subtext">Reserve ledger entry</div>
                                        </div>
                                        <span class="reserve-amount {{ $change < 0 ? 'is-debit' : 'is-credit' }}">{{ $change >= 0 ? '+' : '' }}{{ number_format($change, 8) }}</span>
                                    </div>
                                    <div class="reserve-mobile-meta"><span>Reference</span><strong>{{ $referenceLabel }}</strong></div>
                                    <div class="reserve-mobile-meta"><span>Date</span><strong>{{ optional($ledger->created_at)->format('M d, Y h:i A') }}</strong></div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="reserve-panel">
                    <div class="reserve-section-head">
                        <div>
                            <div class="reserve-meta-label">PI Sell History</div>
                            <h2 class="reserve-section-title">Recent PI Sell Income</h2>
                            <p class="reserve-section-copy">Review reserve sale amounts, selected PI items, profit percentages, and the credited profit from each sell.</p>
                        </div>
                        <span class="reserve-icon is-income" aria-hidden="true"><i class="fa fa-chart-line"></i></span>
                    </div>

                    @if ($recentReserveSales->isEmpty())
                        <div class="reserve-empty">No PI sell income found yet.</div>
                    @else
                        <div class="reserve-history-wrap">
                            <table class="table reserve-table align-middle">
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
                                            <td>
                                                <span class="reserve-table-title">{{ $sale->nftItem?->title ?: ('PI #' . $sale->nft_item_id) }}</span>
                                                <span class="reserve-table-subtext">NFT sale #{{ $sale->id }}</span>
                                            </td>
                                            <td>{{ number_format((float) $sale->sale_amount, 8) }}</td>
                                            <td>{{ number_format((float) $sale->profit_percent, 3) }}%</td>
                                            <td><span class="reserve-amount is-credit">+{{ number_format((float) $sale->profit_amount, 8) }}</span></td>
                                            <td><span class="reserve-table-subtext">{{ \Illuminate\Support\Str::headline((string) $sale->status) }}</span></td>
                                            <td><span class="reserve-table-subtext">{{ optional($sale->created_at)->format('M d, Y h:i A') }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="reserve-sales-mobile">
                            @foreach ($recentReserveSales as $sale)
                                <div class="reserve-mobile-card">
                                    <div class="reserve-mobile-top">
                                        <div>
                                            <div class="reserve-table-title">{{ $sale->nftItem?->title ?: ('PI #' . $sale->nft_item_id) }}</div>
                                            <div class="reserve-table-subtext">NFT sale #{{ $sale->id }}</div>
                                        </div>
                                        <span class="reserve-amount is-credit">+{{ number_format((float) $sale->profit_amount, 8) }}</span>
                                    </div>
                                    <div class="reserve-mobile-meta"><span>Reserve Amount</span><strong>{{ number_format((float) $sale->sale_amount, 8) }}</strong></div>
                                    <div class="reserve-mobile-meta"><span>Profit %</span><strong>{{ number_format((float) $sale->profit_percent, 3) }}%</strong></div>
                                    <div class="reserve-mobile-meta"><span>Status</span><strong>{{ \Illuminate\Support\Str::headline((string) $sale->status) }}</strong></div>
                                    <div class="reserve-mobile-meta"><span>Date</span><strong>{{ optional($sale->created_at)->format('M d, Y h:i A') }}</strong></div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

@if (!empty($activeReserve))
    @include('reserve.partials.sell-modal', ['activeReserve' => $activeReserve, 'sellItems' => $sellItems, 'nftEnabled' => $nftEnabled])
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
    @include('reserve.partials.index-script')
@endpush
