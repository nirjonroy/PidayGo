@extends('layouts.frontend')

@section('content')
@include('frontend.partials.page-banner', ['title' => 'Dashboard'])

<section aria-label="section">
    <div class="container">
        @php
            $user = auth()->user();
            $emailVerified = !is_null($user->email_verified_at);
            $twoFactorEnabled = $user->hasTwoFactorEnabled();
            $kycStatus = $user->latestKycRequest?->status;
            $recentWalletLedgers = $user->walletLedgers()
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();
        @endphp
        <div class="row">
            <div class="col-lg-12 mb-4">
                <div class="nft__item s2">
                    <div class="nft__item_info">
                        <h4>Welcome back</h4>
                        <p class="text-muted mb-0">
                            Email: {{ $emailVerified ? 'Verified' : 'Pending' }},
                            2FA: {{ $twoFactorEnabled ? 'Enabled' : 'Disabled' }},
                            KYC: {{ $kycStatus ? ucfirst($kycStatus) : 'Not submitted' }}.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb30">
                <div class="nft__item s2">
                    <div class="nft__item_info">
                        <h4>Email Verification</h4>
                        <div class="nft__item_price">{{ auth()->user()->email_verified_at ? 'Verified' : 'Pending' }}</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb30">
                <div class="nft__item s2">
                    <div class="nft__item_info">
                        <h4>Two-Factor Auth</h4>
                        <div class="nft__item_price">{{ auth()->user()->two_factor_secret ? 'Enabled' : 'Disabled' }}</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb30">
                <div class="nft__item s2">
                    <div class="nft__item_info">
                        <h4>KYC Status</h4>
                        <div class="nft__item_price">
                            {{ optional(auth()->user()->latestKycRequest)->status ? ucfirst(auth()->user()->latestKycRequest->status) : 'Not submitted' }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="nft__item s2">
                    <div class="nft__item_info">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                            <h4 class="mb-0">Recent Wallet Ledger</h4>
                            <a href="{{ route('wallet.index') }}" class="btn-main btn-light">View Wallet</a>
                        </div>

                        @if ($recentWalletLedgers->isEmpty())
                            <p class="text-muted mb-0">No ledger entries found yet.</p>
                        @else
                            <div class="table-responsive reserve-table-card mb-0">
                                <table class="table table-striped align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Amount</th>
                                            <th>Reference</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentWalletLedgers as $ledger)
                                            <tr>
                                                <td>{{ \Illuminate\Support\Str::headline(str_replace('_', ' ', $ledger->type)) }}</td>
                                                <td class="{{ (float) $ledger->amount < 0 ? 'text-danger' : 'text-success' }}">
                                                    {{ number_format((float) $ledger->amount, 8) }}
                                                </td>
                                                <td>
                                                    @if ($ledger->reference_type && $ledger->reference_id)
                                                        {{ class_basename($ledger->reference_type) }} #{{ $ledger->reference_id }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
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
        </div>
    </div>
</section>
@endsection
