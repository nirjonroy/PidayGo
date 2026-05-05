@extends('layouts.frontend')

@push('styles')
    @include('wallet.partials.transaction-styles')
@endpush

@section('content')
@include('frontend.partials.page-banner', ['title' => 'Deposit'])

@php
    $pendingCount = $history->where('status', 'pending')->count();
    $approvedCount = $history->whereIn('status', ['approved', 'Completed'])->count();
    $activeAmount = $activeDeposit?->pay_amount ?: $activeDeposit?->amount;
    $activeCurrency = $activeDeposit?->pay_currency ?: $activeDeposit?->currency;
@endphp

<section class="transaction-shell" aria-label="Deposit page">
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
                            <div class="transaction-meta-label">Deposit Address</div>
                            <h2 class="transaction-section-title">Scan Or Copy</h2>
                            <p class="transaction-section-copy">Choose the view that is easier for you on mobile or desktop before sending the transfer.</p>
                        </div>
                        <span class="transaction-icon" aria-hidden="true"><i class="fa fa-qrcode"></i></span>
                    </div>

                    @if (empty($address))
                        <div class="transaction-empty">
                            Enter an amount below to create a fresh deposit address.
                        </div>
                    @else
                        <div class="transaction-toggle-group">
                            <button type="button" id="btn-show-qr" class="transaction-toggle-btn is-active">Show QR</button>
                            <button type="button" id="btn-show-address" class="transaction-toggle-btn">Show Address</button>
                        </div>

                        <div id="qr-box" class="transaction-surface transaction-qr-card">
                            @if (!empty($activeDeposit->gateway_qr_code))
                                <img src="{{ $activeDeposit->gateway_qr_code }}" alt="Deposit QR code">
                            @else
                                {!! QrCode::size(220)->margin(1)->generate($qrPayload ?? $address) !!}
                            @endif
                        </div>

                        <div class="transaction-summary-grid mt-3">
                            <div class="transaction-summary-card">
                                <div class="transaction-meta-label">Deposit Amount</div>
                                <p class="transaction-summary-value">{{ number_format((float) $activeDeposit->amount, 4) }} USDT</p>
                            </div>
                            <div class="transaction-summary-card">
                                <div class="transaction-meta-label">Send Exactly</div>
                                <p class="transaction-summary-value">{{ number_format((float) $activeAmount, 8) }} {{ $activeCurrency }}</p>
                            </div>
                        </div>

                        <div id="address-box" class="transaction-surface" style="display:none;">
                            <div class="transaction-meta-label">Wallet Address</div>
                            <div class="transaction-address-row">
                                <input type="text" id="deposit-address" class="form-control" value="{{ $address }}" readonly>
                                <button type="button" id="copy-address" class="btn-main btn-light">Copy</button>
                            </div>
                            <div id="copy-toast" class="transaction-helper mt-3" style="display:none;">Address copied.</div>
                            <div class="transaction-subcopy mt-3">Send only {{ $activeCurrency }} on {{ $chain }}. This address expires {{ optional($activeDeposit->expires_at)->diffForHumans() }}.</div>
                        </div>
                    @endif
                </div>

                <div class="transaction-panel">
                    <div class="transaction-section-head">
                        <div>
                            <div class="transaction-meta-label">Confirmation</div>
                            <h2 class="transaction-section-title">Create Deposit</h2>
                            <p class="transaction-section-copy">Enter the amount first. A payment address and QR code will be created for that exact deposit.</p>
                        </div>
                    </div>

                    @if (!$gatewayReady)
                        <div class="transaction-empty">The deposit gateway is not active yet. Please contact support.</div>
                    @else
                        <form method="POST" action="{{ route('wallet.deposit.store') }}" class="transaction-form">
                            @csrf
                            <div>
                                <label for="amount">Amount (USDT)</label>
                                <input id="amount" name="amount" type="number" step="0.0001" min="{{ $minDeposit }}" class="form-control" placeholder="Enter deposit amount" value="{{ old('amount') }}" required>
                                @error('amount')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn-main">Create Deposit</button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="transaction-form-grid">
                <div id="deposit-guide-section" class="transaction-panel transaction-panel--compact">
                    <div class="transaction-section-head">
                        <div>
                            <div class="transaction-meta-label">Guide</div>
                            <h2 class="transaction-section-title">How To Deposit</h2>
                            <p class="transaction-section-copy">Follow the same steps each time so your transfer can be matched automatically.</p>
                        </div>
                    </div>

                    <ol class="transaction-step-list">
                        <li>Enter the amount you want to deposit and create a payment request.</li>
                        <li>Copy the address or scan the QR code from this page.</li>
                        <li>Open your exchange or wallet app and choose {{ $currency }} on {{ $chain }}.</li>
                        <li>Send the exact amount shown before the address expires.</li>
                        <li>Wait for confirmation; your balance updates automatically after payment is marked paid.</li>
                    </ol>

                    <ul class="transaction-note-list">
                        <li>Only send {{ $currency }} on {{ $chain }} to this address.</li>
                        <li>Payment callbacks cannot reach localhost; use a public HTTPS URL in production.</li>
                        <li>Expired payments must be created again with a new address.</li>
                    </ul>
                </div>

                <div id="deposit-funding-section" class="transaction-panel">
                    <div class="transaction-section-head">
                        <div>
                            <div class="transaction-meta-label">Funding</div>
                            <h2 class="transaction-section-title">USDT Deposit</h2>
                            <p class="transaction-section-copy">Create a payment address for the amount you want to deposit, then send the exact payment before it expires.</p>
                        </div>
                        <span class="transaction-icon" aria-hidden="true"><i class="fa fa-arrow-circle-down"></i></span>
                    </div>

                    <div class="transaction-summary-grid">
                        <div class="transaction-summary-card">
                            <div class="transaction-meta-label">Wallet Balance</div>
                            <p class="transaction-summary-value">{{ number_format($walletBalance, 4) }} USDT</p>
                            <div class="transaction-subcopy">Current available amount in your main wallet.</div>
                        </div>
                        <div class="transaction-summary-card">
                            <div class="transaction-meta-label">Minimum Deposit</div>
                            <p class="transaction-summary-value">{{ number_format($minDeposit, 4) }} USDT</p>
                            <div class="transaction-subcopy">Requests below this amount are not accepted.</div>
                        </div>
                        <div class="transaction-summary-card">
                            <div class="transaction-meta-label">Active Request</div>
                            <p class="transaction-summary-value">{{ $activeDeposit ? number_format((float) $activeDeposit->amount, 4) . ' USDT' : 'None' }}</p>
                            <div class="transaction-subcopy">{{ $activeDeposit ? 'Latest pending deposit.' : 'Create one from the form below.' }}</div>
                        </div>
                        <div class="transaction-summary-card">
                            <div class="transaction-meta-label">Payment Window</div>
                            <p class="transaction-summary-value">{{ $activeDeposit?->expires_at ? $activeDeposit->expires_at->diffForHumans(null, true) : 'After Create' }}</p>
                            <div class="transaction-subcopy">{{ $pendingCount }} pending and {{ $approvedCount }} approved requests in your recent history.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="transaction-panel">
                <div class="transaction-section-head">
                    <div>
                        <div class="transaction-meta-label">History</div>
                        <h2 class="transaction-section-title">Recent Deposits</h2>
                        <p class="transaction-section-copy">Track your submitted transaction hashes and their current review status from one place.</p>
                    </div>
                </div>

                @if ($history->isEmpty())
                    <div class="transaction-empty">No deposits have been submitted yet.</div>
                @else
                    <div class="transaction-ledger-wrap">
                        <table class="table transaction-ledger-table align-middle">
                            <thead>
                                <tr>
                                    <th>Amount</th>
                                    <th>Reference</th>
                                    <th>Status</th>
                                    <th>Submitted</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($history as $item)
                                    @php
                                        $status = strtolower((string) $item->status);
                                        $statusClass = match ($status) {
                                            'pending' => 'is-pending',
                                            'approved' => 'is-approved',
                                            'completed' => 'is-completed',
                                            'rejected' => 'is-rejected',
                                            'expired' => 'is-expired',
                                            default => 'is-default',
                                        };
                                        $reference = $item->txid ?: ($item->gateway_track_id ?: $item->gateway_order_id);
                                    @endphp
                                    <tr>
                                        <td><span class="transaction-ledger-amount is-credit">{{ number_format((float) $item->amount, 4) }} USDT</span></td>
                                        <td>
                                            <span class="transaction-table-title">{{ $reference ? \Illuminate\Support\Str::limit($reference, 24, '...') : '-' }}</span>
                                            <div class="transaction-ledger-subtext">{{ $item->gateway ? strtoupper($item->gateway) : 'Manual' }}</div>
                                        </td>
                                        <td><span class="transaction-status-badge {{ $statusClass }}">{{ ucfirst($status) }}</span></td>
                                        <td><span class="transaction-ledger-subtext">{{ optional($item->created_at)->format('M d, Y h:i A') }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="transaction-ledger-mobile">
                        @foreach ($history as $item)
                            @php
                                $status = strtolower((string) $item->status);
                                $statusClass = match ($status) {
                                    'pending' => 'is-pending',
                                    'approved' => 'is-approved',
                                    'completed' => 'is-completed',
                                    'rejected' => 'is-rejected',
                                    'expired' => 'is-expired',
                                    default => 'is-default',
                                };
                                $reference = $item->txid ?: ($item->gateway_track_id ?: $item->gateway_order_id);
                            @endphp
                            <div class="transaction-ledger-mobile-card">
                                <div class="transaction-mobile-top">
                                    <div>
                                        <div class="transaction-ledger-type">{{ number_format((float) $item->amount, 4) }} USDT</div>
                                        <div class="transaction-ledger-subtext">Deposit request</div>
                                    </div>
                                    <span class="transaction-status-badge {{ $statusClass }}">{{ ucfirst($status) }}</span>
                                </div>
                                <div class="transaction-mobile-meta">
                                    <span>Reference</span>
                                    <strong>{{ $reference ? \Illuminate\Support\Str::limit($reference, 24, '...') : '-' }}</strong>
                                </div>
                                <div class="transaction-mobile-meta">
                                    <span>Submitted</span>
                                    <strong>{{ optional($item->created_at)->format('M d, Y h:i A') }}</strong>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<div id="deposit-guide-modal" class="transaction-guide-modal" role="dialog" aria-modal="true" aria-labelledby="deposit-guide-title" aria-hidden="true">
    <div class="transaction-guide-dialog">
        <button type="button" class="transaction-guide-close" data-deposit-guide-close aria-label="Close deposit guide">
            <i class="fa fa-times" aria-hidden="true"></i>
        </button>
        <div class="transaction-guide-head">
            <div class="transaction-meta-label">Guide</div>
            <h2 id="deposit-guide-title" class="transaction-section-title">How To Deposit</h2>
        </div>
        <p class="transaction-section-copy">Follow these steps before creating or paying a deposit request.</p>

        <ol class="transaction-step-list">
            <li>Enter the amount you want to deposit and create a payment request.</li>
            <li>Copy the address or scan the QR code from this page.</li>
            <li>Open your exchange or wallet app and choose {{ $currency }} on {{ $chain }}.</li>
            <li>Send the exact amount shown before the address expires.</li>
            <li>Wait for confirmation; your balance updates automatically after payment is marked paid.</li>
        </ol>

        <ul class="transaction-note-list">
            <li>Only send {{ $currency }} on {{ $chain }} to this address.</li>
            <li>Expired payments must be created again with a new address.</li>
        </ul>

        <div class="transaction-guide-actions">
            <button type="button" class="btn-main" data-deposit-guide-funding>Go To Funding</button>
            <button type="button" class="btn-main btn-light" data-deposit-guide-close>Close Guide</button>
        </div>
    </div>
</div>

<script>
    const qrBox = document.getElementById('qr-box');
    const addressBox = document.getElementById('address-box');
    const btnShowQr = document.getElementById('btn-show-qr');
    const btnShowAddress = document.getElementById('btn-show-address');
    const copyBtn = document.getElementById('copy-address');
    const toast = document.getElementById('copy-toast');
    const guideModal = document.getElementById('deposit-guide-modal');
    const fundingSection = document.getElementById('deposit-funding-section');
    const guideCloseButtons = document.querySelectorAll('[data-deposit-guide-close]');
    const guideFundingButton = document.querySelector('[data-deposit-guide-funding]');

    const closeDepositGuide = () => {
        if (!guideModal) {
            return;
        }

        guideModal.classList.remove('is-open');
        guideModal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    };

    if (guideModal) {
        window.addEventListener('load', () => {
            guideModal.classList.add('is-open');
            guideModal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        });

        guideModal.addEventListener('click', (event) => {
            if (event.target === guideModal) {
                closeDepositGuide();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeDepositGuide();
            }
        });
    }

    guideCloseButtons.forEach((button) => {
        button.addEventListener('click', closeDepositGuide);
    });

    if (guideFundingButton && fundingSection) {
        guideFundingButton.addEventListener('click', () => {
            closeDepositGuide();
            fundingSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    }

    if (btnShowQr && btnShowAddress && qrBox && addressBox) {
        btnShowQr.addEventListener('click', () => {
            qrBox.style.display = 'flex';
            addressBox.style.display = 'none';
            btnShowQr.classList.add('is-active');
            btnShowAddress.classList.remove('is-active');
        });

        btnShowAddress.addEventListener('click', () => {
            qrBox.style.display = 'none';
            addressBox.style.display = 'block';
            btnShowAddress.classList.add('is-active');
            btnShowQr.classList.remove('is-active');
        });
    }

    if (copyBtn && toast) {
        copyBtn.addEventListener('click', async () => {
            const input = document.getElementById('deposit-address');

            if (!input) {
                return;
            }

            try {
                await navigator.clipboard.writeText(input.value);
            } catch (error) {
                input.select();
                document.execCommand('copy');
            }

            toast.style.display = 'block';
            setTimeout(() => {
                toast.style.display = 'none';
            }, 1500);
        });
    }
</script>
@endsection
