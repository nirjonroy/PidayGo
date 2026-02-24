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
        </div>
    </div>
</section>
@endsection
