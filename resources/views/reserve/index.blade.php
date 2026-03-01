@extends('layouts.frontend')

@section('content')
@include('frontend.partials.page-banner', ['title' => 'Reserve'])

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
                <span>You already have an active reserve. Please continue to sell.</span>
                <a class="btn btn-sm btn-primary" href="{{ route('reserve.sell.form') }}">Go to Sell</a>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-6 mb30">
                <div class="nft__item s2">
                    <div class="nft__item_info">
                        <h4>Reserve Summary</h4>
                        <div class="nft__item_price">Wallet Balance: {{ number_format($walletBalance, 8) }} USDT</div>
                        <div class="nft__item_price">Reserved Balance: {{ number_format($reservedBalance, 8) }} USDT</div>
                        @if ($level)
                            <div class="nft__item_price">Level: {{ $level->code }}</div>
                            <div class="nft__item_price">Reserve Range: {{ $level->min_reservation }} - {{ $level->max_reservation }} USDT</div>
                            <div class="nft__item_price">Deposit Range: {{ $level->min_deposit }} - {{ $level->max_deposit }} USDT</div>
                        @else
                            <div class="text-muted">No active level found.</div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb30">
                <div class="nft__item s2">
                    <div class="nft__item_info">
                        <h4>Confirm Reserve</h4>
                        @if (!$reserveEnabled)
                            <div class="text-muted">Reserve is currently disabled.</div>
                        @elseif (!$level)
                            <div class="text-muted">You do not have an eligible level yet.</div>
                        @elseif ($plans->isEmpty())
                            <div class="text-muted">No reserve plans configured for this level.</div>
                        @elseif (!empty($activeReserve))
                            <div class="text-muted">You already have an active reserve. Use the Sell page.</div>
                            <a class="btn-main mt-2" href="{{ route('reserve.sell.form') }}">Go to Sell</a>
                        @else
                            <form method="POST" action="{{ route('reserve.confirm') }}" class="form-border">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Reserve Plan</label>
                                    <select name="reserve_plan_id" class="form-select" @if($plans->count() === 1) readonly @endif>
                                        @foreach ($plans as $plan)
                                            <option value="{{ $plan->id }}" @selected($selectedPlanId == $plan->id)>
                                                {{ number_format($plan->reserve_amount, 8) }} USDT ({{ $plan->profit_min_percent }}% - {{ $plan->profit_max_percent }}%)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn-main">Confirm</button>
                            </form>
                            <p class="text-muted mt-3">Your reserve amount is selected from your level plans.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
