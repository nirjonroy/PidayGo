@extends('layouts.frontend')

@section('content')
<section id="subheader" class="text-light" data-bgimage="url({{ asset('frontend/images/background/subheader.jpg') }}) top">
    <div class="center-y relative text-center">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h1>Stake</h1>
                </div>
            </div>
        </div>
    </div>
</section>

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
    </div>
</section>
@endsection
