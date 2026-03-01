@extends('layouts.frontend')

@section('content')
@include('frontend.partials.page-banner', ['title' => 'Sell'])

<section aria-label="section">
    <div class="container">
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <div class="row">
            <div class="col-lg-6 mb30">
                <div class="nft__item s2">
                    <div class="nft__item_info">
                        <h4>Sell Summary</h4>
                        <div class="nft__item_price">Level: {{ $level->code }}</div>
                        <div class="nft__item_price">Reserved: {{ number_format($reservedBalance, 8) }} USDT</div>
                        <div class="nft__item_price">Income Range: {{ $level->income_min_percent }}% - {{ $level->income_max_percent }}%</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb30">
                <div class="nft__item s2">
                    <div class="nft__item_info">
                        <h4>Simulate Sell</h4>
                        <form method="POST" action="{{ route('sell.store') }}" class="form-border">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Sale Amount</label>
                                <input type="number" step="0.00000001" name="sale_amount" class="form-control" required>
                            </div>
                            <button type="submit" class="btn-main">Sell</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
