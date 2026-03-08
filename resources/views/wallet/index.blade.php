@extends('layouts.frontend')

@section('content')
@include('frontend.partials.page-banner', ['title' => 'Wallet'])

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
            <div class="col-lg-3 col-md-6 mb30">
                <div class="nft__item s2">
                    <div class="nft__item_info">
                        <h4>Wallet Balance</h4>
                        <div class="nft__item_price">{{ number_format($balance, 4) }} USDT</div>
                        <div class="mt-3">
                            <a href="{{ route('wallet.deposit') }}" class="btn-main btn-light btn-sm">Deposit</a>
                            <a href="{{ route('wallet.withdrawals') }}" class="btn-main btn-sm">Withdraw</a>
                            <a href="{{ route('reserve.index') }}" class="btn-main btn-sm">Reserve</a>
                            @if ($canSell)
                                <a href="{{ route('sell.index') }}" class="btn-main btn-sm btn-light">Sell</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb30" id="reservation">
                <div class="nft__item s2">
                    <div class="nft__item_info">
                        <h4>Reservation Balance</h4>
                        <div class="nft__item_price">{{ number_format($reservedBalance, 4) }} USDT</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb30">
                <div class="nft__item s2">
                    <div class="nft__item_info">
                        <h4>Today Earnings</h4>
                        <div class="nft__item_price">{{ number_format($todayEarnings, 4) }} USDT</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb30">
                <div class="nft__item s2">
                    <div class="nft__item_info">
                        <h4>Cumulative Income</h4>
                        <div class="nft__item_price">{{ number_format($cumulativeIncome, 4) }} USDT</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 mb30">
                <div class="nft__item s2">
                    <div class="nft__item_info">
                        <h4>Current Level</h4>
                        @if ($level)
                            <div class="nft__item_price">{{ $level->code }}</div>
                            <div class="text-muted">Income: {{ $level->income_min_percent }}% - {{ $level->income_max_percent }}%</div>
                            <div class="text-muted">Reservation: {{ $level->min_reservation }} - {{ $level->max_reservation }}</div>
                        @else
                            <div class="text-muted">No levels configured</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <h2>Staking Plans</h2>
            </div>
            @forelse ($plans as $plan)
                <div class="col-lg-4 col-md-6 mb30">
                    <div class="nft__item s2">
                        <div class="nft__item_info">
                            <h4>{{ $plan->name }}</h4>
                            <div class="nft__item_price">Daily Rate: {{ $plan->daily_rate }}%</div>
                            <div class="nft__item_price">Min: {{ $plan->min_amount ?? 0 }} / Max: {{ $plan->max_amount ?? 'Unlimited' }}</div>
                            <div class="nft__item_price">Duration: {{ $plan->duration_days }} days</div>
                            <div class="mt-3">
                                <form method="POST" action="{{ route('staking.store') }}">
                                    @csrf
                                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                    <div class="d-flex gap-2">
                                        <input name="amount" type="number" step="0.0001" class="form-control" placeholder="Amount" required>
                                        <button type="submit" class="btn-main btn-light">Stake</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-md-12">
                    <p class="text-muted">No active plans.</p>
                </div>
            @endforelse
        </div>

        <div class="spacer-30"></div>

        <div class="row">
            <div class="col-lg-12">
                <h2>Your Stakes</h2>
            </div>
            <div class="col-lg-12">
                <div class="table-responsive">
                    <table class="table table-borderless table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Plan</th>
                                <th>Principal</th>
                                <th>Status</th>
                                <th>Ends At</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($stakes as $stake)
                                <tr>
                                    <td>{{ $stake->stakePlan->name ?? '-' }}</td>
                                    <td>{{ number_format($stake->principal_amount, 4) }}</td>
                                    <td>{{ ucfirst($stake->status) }}</td>
                                    <td>{{ $stake->ends_at }}</td>
                                    <td>
                                        @if ($stake->status === 'active' && now()->gte($stake->ends_at))
                                            <form method="POST" action="{{ route('staking.unstake', $stake) }}">
                                                @csrf
                                                <button type="submit" class="btn-main btn-sm">Unstake</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No stakes yet.</td>
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
