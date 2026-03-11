@extends('layouts.frontend')

@section('content')
@include('frontend.partials.page-banner', ['title' => 'Stake'])

<section aria-label="section">
    <div class="container">
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif
        <div class="row">
            <div class="col-lg-12 mb30">
                <div class="nft__item s2">
                    <div class="nft__item_info">
                        <h4>Available Balance</h4>
                        <div class="nft__item_price">{{ number_format((float) $balance, 4) }} USDT</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <h2>Active Plans</h2>
            </div>
            @forelse ($plans as $plan)
                <div class="col-lg-4 col-md-6 mb30">
                    <div class="nft__item s2">
                        <div class="nft__item_info">
                            <h4>{{ $plan->name }}</h4>
                            <div class="nft__item_price">Min: {{ $plan->min_amount ?? 0 }} / Max: {{ $plan->max_amount ?? 'Unlimited' }}</div>
                            <div class="nft__item_price">Daily Rate: {{ $plan->daily_rate }}%</div>
                            <div class="nft__item_price">Duration: {{ $plan->duration_days }} days</div>
                            @if ($plan->level_required)
                                <div class="nft__item_price">Level Required: {{ $plan->level_required }}</div>
                            @endif
                            <div class="mt-3">
                                <form id="stake-form-{{ $plan->id }}" method="POST" action="{{ route('stake.store') }}">
                                    @csrf
                                    <input type="hidden" name="stake_plan_id" value="{{ $plan->id }}">
                                    <div class="d-flex gap-2">
                                        <input name="amount" type="number" step="0.0001" min="0.0001" class="form-control" placeholder="Amount" required value="{{ old('amount') }}">
                                        <button type="submit" class="btn-main btn-light">Stake</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-md-12">
                    <p class="text-muted">No active plans right now.</p>
                </div>
            @endforelse
        </div>

        <div class="spacer-30"></div>

        <div class="row">
            <div class="col-lg-12">
                <h2>Your Active Stakes</h2>
            </div>
            <div class="col-lg-12">
                <div class="table-responsive">
                    <table class="table table-borderless table-striped align-middle">
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
                            @forelse ($stakes as $stake)
                                <tr>
                                    <td>{{ $stake->stakePlan->name ?? '-' }}</td>
                                    <td>{{ number_format($stake->principal_amount, 4) }}</td>
                                    <td>{{ $stake->stakePlan->daily_rate ?? '-' }}</td>
                                    <td>{{ $stake->started_at }}</td>
                                    <td>{{ number_format($stake->total_reward_paid, 4) }}</td>
                                    <td>{{ ucfirst($stake->status) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No active stakes yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="spacer-30"></div>

        <div class="row">
            <div class="col-lg-12">
                <h2>Daily Stake Income</h2>
            </div>
            <div class="col-lg-12">
                @if ($recentStakeIncome->isEmpty())
                    <div class="text-muted">No daily stake income credited yet.</div>
                @else
                    <div class="table-responsive reserve-table-card">
                        <table class="table table-borderless table-striped align-middle mb-0">
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
                                        <td>{{ $stakeRef?->stakePlan?->name ?: ('Stake #' . $ledger->reference_id) }}</td>
                                        <td>{{ $stakeRef ? number_format((float) $stakeRef->principal_amount, 4) : '-' }}</td>
                                        <td>{{ $meta['day'] ?? '-' }}</td>
                                        <td>{{ isset($meta['rate']) ? number_format((float) $meta['rate'], 6) . '%' : '-' }}</td>
                                        <td class="text-success">{{ number_format((float) $ledger->amount, 8) }}</td>
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
</section>
@endsection
