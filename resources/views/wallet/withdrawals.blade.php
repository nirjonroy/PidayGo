@extends('layouts.frontend')

@push('styles')
    @include('wallet.partials.transaction-styles')
@endpush

@section('content')
@include('frontend.partials.page-banner', ['title' => 'Withdrawals'])

@php
    $pendingCount = $withdrawals->where('status', 'pending')->count();
    $approvedCount = $withdrawals->where('status', 'approved')->count();
@endphp

<section class="transaction-shell" aria-label="Withdrawal page">
    <div class="container">
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <div class="transaction-stack">
            <div class="transaction-overview">
                <div class="transaction-panel">
                    <div class="transaction-section-head">
                        <div>
                            <div class="transaction-meta-label">Payouts</div>
                            <h2 class="transaction-section-title">Withdraw From Wallet</h2>
                            <p class="transaction-section-copy">Request a withdrawal from your available balance. Submitted requests go into manual review before approval.</p>
                        </div>
                        <span class="transaction-icon" aria-hidden="true"><i class="fa fa-arrow-circle-up"></i></span>
                    </div>

                    <div class="transaction-summary-grid">
                        <div class="transaction-summary-card">
                            <div class="transaction-meta-label">Available Balance</div>
                            <p class="transaction-summary-value">{{ number_format($balance, 4) }} USDT</p>
                            <div class="transaction-subcopy">This is the current spendable amount in your wallet.</div>
                        </div>
                        <div class="transaction-summary-card">
                            <div class="transaction-meta-label">Review Window</div>
                            <p class="transaction-summary-value">{{ $reviewHours }}-96h</p>
                            <div class="transaction-subcopy">Most requests are reviewed during this time window.</div>
                        </div>
                        <div class="transaction-summary-card">
                            <div class="transaction-meta-label">Pending Requests</div>
                            <p class="transaction-summary-value">{{ $pendingCount }}</p>
                            <div class="transaction-subcopy">Requests currently waiting for manual review.</div>
                        </div>
                        <div class="transaction-summary-card">
                            <div class="transaction-meta-label">Approved</div>
                            <p class="transaction-summary-value">{{ $approvedCount }}</p>
                            <div class="transaction-subcopy">Approved requests from your recent withdrawal history.</div>
                        </div>
                    </div>
                </div>

                <div class="transaction-panel">
                    <div class="transaction-section-head">
                        <div>
                            <div class="transaction-meta-label">Request Form</div>
                            <h2 class="transaction-section-title">Submit Withdrawal</h2>
                            <p class="transaction-section-copy">Enter the amount you want to move out from your wallet. The amount is reserved immediately while the request is under review.</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('withdrawals.store') }}" class="transaction-form">
                        @csrf
                        <div>
                            <label for="withdraw_amount">Amount (USDT)</label>
                            <input id="withdraw_amount" name="amount" type="number" step="0.0001" min="0.0001" class="form-control" placeholder="Enter withdrawal amount" value="{{ old('amount') }}" required>
                            @error('amount')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn-main">Submit Withdrawal</button>
                    </form>
                </div>
            </div>

            <div class="transaction-form-grid">
                <div class="transaction-panel transaction-panel--compact">
                    <div class="transaction-section-head">
                        <div>
                            <div class="transaction-meta-label">Before You Request</div>
                            <h2 class="transaction-section-title">Withdrawal Notes</h2>
                            <p class="transaction-section-copy">Keep these points in mind so your request stays clear and easy to review.</p>
                        </div>
                    </div>

                    <ul class="transaction-note-list">
                        <li>Withdrawals are submitted for manual review.</li>
                        <li>The requested amount is debited from your wallet immediately after submission.</li>
                        <li>If a request is rejected later, that amount is credited back to your wallet.</li>
                        <li>Approved requests are usually completed after the review window.</li>
                    </ul>
                </div>

                <div class="transaction-panel transaction-panel--compact">
                    <div class="transaction-section-head">
                        <div>
                            <div class="transaction-meta-label">Review Flow</div>
                            <h2 class="transaction-section-title">What Happens Next</h2>
                            <p class="transaction-section-copy">A quick overview of how the withdrawal request moves after you submit it.</p>
                        </div>
                    </div>

                    <ol class="transaction-step-list">
                        <li>You submit the amount from this page.</li>
                        <li>The request enters pending review and the amount is held from wallet balance.</li>
                        <li>Admin reviews and either approves or rejects the request.</li>
                        <li>If approved, the payout is processed. If rejected, the balance is restored.</li>
                    </ol>
                </div>
            </div>

            <div class="transaction-panel">
                <div class="transaction-section-head">
                    <div>
                        <div class="transaction-meta-label">History</div>
                        <h2 class="transaction-section-title">Withdrawal History</h2>
                        <p class="transaction-section-copy">Review the latest request amounts, statuses, and when each request becomes eligible for completion.</p>
                    </div>
                </div>

                @if ($withdrawals->isEmpty())
                    <div class="transaction-empty">No withdrawals have been requested yet.</div>
                @else
                    <div class="transaction-ledger-wrap">
                        <table class="table transaction-ledger-table align-middle">
                            <thead>
                                <tr>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Requested</th>
                                    <th>Eligible At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($withdrawals as $withdrawal)
                                    @php
                                        $status = strtolower((string) $withdrawal->status);
                                        $statusClass = match ($status) {
                                            'pending' => 'is-pending',
                                            'approved' => 'is-approved',
                                            'rejected' => 'is-rejected',
                                            'expired' => 'is-expired',
                                            default => 'is-default',
                                        };
                                    @endphp
                                    <tr>
                                        <td><span class="transaction-ledger-amount is-debit">-{{ number_format((float) $withdrawal->amount, 4) }} USDT</span></td>
                                        <td><span class="transaction-status-badge {{ $statusClass }}">{{ ucfirst($status) }}</span></td>
                                        <td><span class="transaction-ledger-subtext">{{ optional($withdrawal->requested_at)->format('M d, Y h:i A') }}</span></td>
                                        <td><span class="transaction-ledger-subtext">{{ optional($withdrawal->eligible_at)->format('M d, Y h:i A') }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="transaction-ledger-mobile">
                        @foreach ($withdrawals as $withdrawal)
                            @php
                                $status = strtolower((string) $withdrawal->status);
                                $statusClass = match ($status) {
                                    'pending' => 'is-pending',
                                    'approved' => 'is-approved',
                                    'rejected' => 'is-rejected',
                                    'expired' => 'is-expired',
                                    default => 'is-default',
                                };
                            @endphp
                            <div class="transaction-ledger-mobile-card">
                                <div class="transaction-mobile-top">
                                    <div>
                                        <div class="transaction-ledger-type">{{ number_format((float) $withdrawal->amount, 4) }} USDT</div>
                                        <div class="transaction-ledger-subtext">Withdrawal request</div>
                                    </div>
                                    <span class="transaction-status-badge {{ $statusClass }}">{{ ucfirst($status) }}</span>
                                </div>
                                <div class="transaction-mobile-meta">
                                    <span>Requested</span>
                                    <strong>{{ optional($withdrawal->requested_at)->format('M d, Y h:i A') }}</strong>
                                </div>
                                <div class="transaction-mobile-meta">
                                    <span>Eligible At</span>
                                    <strong>{{ optional($withdrawal->eligible_at)->format('M d, Y h:i A') }}</strong>
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
