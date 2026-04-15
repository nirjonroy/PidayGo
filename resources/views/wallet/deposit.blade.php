@extends('layouts.frontend')

@push('styles')
    @include('wallet.partials.transaction-styles')
@endpush

@section('content')
@include('frontend.partials.page-banner', ['title' => 'Deposit'])

@php
    $pendingCount = $history->where('status', 'pending')->count();
    $approvedCount = $history->where('status', 'approved')->count();
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
                            <div class="transaction-meta-label">Funding</div>
                            <h2 class="transaction-section-title">USDT Deposit</h2>
                            <p class="transaction-section-copy">Send {{ $currency }} through the {{ $chain }} network, then submit the transaction hash for review from this page.</p>
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
                            <div class="transaction-meta-label">Deposit Chain</div>
                            <p class="transaction-summary-value">{{ $currency }}-{{ $chain }}</p>
                            <div class="transaction-subcopy">Use the exact network shown here before sending funds.</div>
                        </div>
                        <div class="transaction-summary-card">
                            <div class="transaction-meta-label">Review Window</div>
                            <p class="transaction-summary-value">Within {{ $reviewHours }}h</p>
                            <div class="transaction-subcopy">{{ $pendingCount }} pending and {{ $approvedCount }} approved requests in your recent history.</div>
                        </div>
                    </div>
                </div>

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
                            Deposit is temporarily unavailable because no active deposit address is configured. Please contact support.
                        </div>
                    @else
                        <div class="transaction-toggle-group">
                            <button type="button" id="btn-show-qr" class="transaction-toggle-btn is-active">Show QR</button>
                            <button type="button" id="btn-show-address" class="transaction-toggle-btn">Show Address</button>
                        </div>

                        <div id="qr-box" class="transaction-surface transaction-qr-card">
                            {!! QrCode::size(220)->margin(1)->generate($qrPayload ?? $address) !!}
                        </div>

                        <div id="address-box" class="transaction-surface" style="display:none;">
                            <div class="transaction-meta-label">Wallet Address</div>
                            <div class="transaction-address-row">
                                <input type="text" id="deposit-address" class="form-control" value="{{ $address }}" readonly>
                                <button type="button" id="copy-address" class="btn-main btn-light">Copy</button>
                            </div>
                            <div id="copy-toast" class="transaction-helper mt-3" style="display:none;">Address copied.</div>
                            <div class="transaction-subcopy mt-3">Double-check the network in your exchange wallet before you confirm the transfer.</div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="transaction-form-grid">
                <div class="transaction-panel">
                    <div class="transaction-section-head">
                        <div>
                            <div class="transaction-meta-label">Confirmation</div>
                            <h2 class="transaction-section-title">Submit Deposit</h2>
                            <p class="transaction-section-copy">After sending funds, enter the transfer amount and blockchain transaction hash below.</p>
                        </div>
                    </div>

                    @if (empty($address))
                        <div class="transaction-empty">The form is unavailable until an active deposit address is configured by admin.</div>
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

                            <div>
                                <label for="txid">Transaction ID</label>
                                <input id="txid" name="txid" type="text" class="form-control" placeholder="Paste your 64 character TxID" value="{{ old('txid') }}" required>
                                @error('txid')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            @error('address')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror

                            <button type="submit" class="btn-main">Submit Deposit</button>
                        </form>
                    @endif
                </div>

                <div class="transaction-panel transaction-panel--compact">
                    <div class="transaction-section-head">
                        <div>
                            <div class="transaction-meta-label">Guide</div>
                            <h2 class="transaction-section-title">How To Deposit</h2>
                            <p class="transaction-section-copy">Follow the same steps each time so your transfer gets matched quickly during manual review.</p>
                        </div>
                    </div>

                    <ol class="transaction-step-list">
                        <li>Copy the address or scan the QR code from this page.</li>
                        <li>Open your exchange or wallet app and choose {{ $currency }} on the {{ $chain }} network.</li>
                        <li>Send the amount you want to fund into your PidayGo account.</li>
                        <li>Copy the blockchain TxID after the transfer is confirmed.</li>
                        <li>Submit the amount and TxID here to place the request into review.</li>
                    </ol>

                    <ul class="transaction-note-list">
                        <li>Only send {{ $currency }} on {{ $chain }} to this address.</li>
                        <li>Deposit review usually finishes within {{ $reviewHours }} hours.</li>
                        <li>Each TxID can be submitted only once.</li>
                    </ul>
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
                                    <th>TxID</th>
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
                                            'rejected' => 'is-rejected',
                                            'expired' => 'is-expired',
                                            default => 'is-default',
                                        };
                                    @endphp
                                    <tr>
                                        <td><span class="transaction-ledger-amount is-credit">{{ number_format((float) $item->amount, 4) }} USDT</span></td>
                                        <td>
                                            <span class="transaction-table-title">{{ \Illuminate\Support\Str::limit($item->txid, 24, '...') }}</span>
                                            <div class="transaction-ledger-subtext">{{ $item->txid }}</div>
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
                                    'rejected' => 'is-rejected',
                                    'expired' => 'is-expired',
                                    default => 'is-default',
                                };
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
                                    <span>TxID</span>
                                    <strong>{{ \Illuminate\Support\Str::limit($item->txid, 24, '...') }}</strong>
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

<script>
    const qrBox = document.getElementById('qr-box');
    const addressBox = document.getElementById('address-box');
    const btnShowQr = document.getElementById('btn-show-qr');
    const btnShowAddress = document.getElementById('btn-show-address');
    const copyBtn = document.getElementById('copy-address');
    const toast = document.getElementById('copy-toast');

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
