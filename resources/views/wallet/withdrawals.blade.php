@extends('layouts.frontend')

@section('content')
@include('frontend.partials.page-banner', ['title' => 'Withdrawals'])

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
                    <div class="nft__item_info d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <h4 class="mb-1">Available Balance</h4>
                            <div class="nft__item_price">{{ number_format($balance, 4) }} USDT</div>
                            <div class="text-muted mt-2">Withdrawal approvals are usually reviewed within {{ $reviewHours }}-96 hours.</div>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('wallet.index') }}" class="btn-main btn-light btn-sm">Back to Wallet</a>
                            <a href="{{ route('wallet.deposit') }}" class="btn-main btn-sm">Deposit Page</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 mb30">
                <form method="POST" action="{{ route('withdrawals.store') }}" class="form-border">
                    @csrf
                    <div class="field-set">
                        <h5>Request Withdrawal</h5>
                        <p class="text-muted mb-3">Enter the amount you want to withdraw from your wallet balance.</p>
                        <input id="withdraw_amount" name="amount" type="number" step="0.0001" class="form-control" placeholder="Amount (USDT)" required>
                        @error('amount') <div class="text-danger mt-2">{{ $message }}</div> @enderror

                        <div class="spacer-20"></div>

                        <button type="submit" class="btn-main">Submit Withdrawal</button>
                    </div>
                </form>
            </div>

            <div class="col-lg-7 mb30">
                <div class="nft__item s2 h-100">
                    <div class="nft__item_info">
                        <h4>Withdrawal Notes</h4>
                        <ul class="mb-0">
                            <li>Withdrawals are submitted for manual review.</li>
                            <li>Approved requests are usually completed after the waiting period.</li>
                            <li>Rejected requests are credited back to the wallet.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <h2>Withdrawal History</h2>
                <div class="table-responsive reserve-table-card">
                    <table class="table table-borderless table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Requested</th>
                                <th>Eligible At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($withdrawals as $withdrawal)
                                <tr>
                                    <td>{{ number_format($withdrawal->amount, 4) }} USDT</td>
                                    <td>{{ ucfirst($withdrawal->status) }}</td>
                                    <td>{{ $withdrawal->requested_at }}</td>
                                    <td>{{ $withdrawal->eligible_at }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No withdrawals yet.</td>
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
