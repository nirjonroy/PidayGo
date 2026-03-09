@extends('layouts.frontend')

@section('content')
    @include('frontend.partials.page-banner', [
        'title' => 'KYC Status',
        'subtitle' => 'Track the review progress of your identity verification request.',
    ])

    <style>
        .kyc-status-layout .nft__item.s2 {
            border-radius: 20px;
        }
        .kyc-status-card {
            padding: 28px;
        }
        .kyc-status-title {
            font-size: 20px;
            margin-bottom: 10px;
        }
        .kyc-status-copy {
            color: #6b7280;
            margin-bottom: 0;
        }
        .kyc-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 14px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
        }
        .kyc-status-pill--pending {
            background: rgba(251, 191, 36, 0.16);
            color: #92400e;
        }
        .kyc-status-pill--approved {
            background: rgba(34, 197, 94, 0.16);
            color: #166534;
        }
        .kyc-status-pill--rejected {
            background: rgba(239, 68, 68, 0.14);
            color: #991b1b;
        }
        .kyc-status-meta {
            padding: 16px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.55);
            border: 1px solid rgba(17, 24, 39, 0.08);
            margin-bottom: 16px;
        }
        .kyc-status-meta strong {
            display: block;
            margin-bottom: 6px;
        }
        .dark-scheme .kyc-status-copy {
            color: rgba(242, 245, 249, 0.8);
        }
        .dark-scheme .kyc-status-meta {
            background: rgba(255, 255, 255, 0.04);
            border-color: rgba(255, 255, 255, 0.08);
        }
        .dark-scheme .kyc-status-pill--pending {
            background: rgba(251, 191, 36, 0.18);
            color: #fcd34d;
        }
        .dark-scheme .kyc-status-pill--approved {
            background: rgba(34, 197, 94, 0.18);
            color: #86efac;
        }
        .dark-scheme .kyc-status-pill--rejected {
            background: rgba(239, 68, 68, 0.18);
            color: #fca5a5;
        }
        @media (max-width: 991.98px) {
            .kyc-status-card {
                padding: 20px;
            }
        }
    </style>

    <section aria-label="section" class="kyc-status-layout">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="nft__item s2 kyc-status-card">
                        <div class="nft__item_info">
                            @if (!$latestKyc)
                                <h3 class="kyc-status-title">No submission found</h3>
                                <p class="kyc-status-copy mb-4">
                                    Your account does not have a KYC request yet. Submit your documents to start verification.
                                </p>
                                <a href="{{ route('kyc.form') }}" class="btn-main">Submit KYC</a>
                            @else
                                @php
                                    $statusClass = match ($latestKyc->status) {
                                        'approved' => 'kyc-status-pill--approved',
                                        'rejected' => 'kyc-status-pill--rejected',
                                        default => 'kyc-status-pill--pending',
                                    };
                                @endphp

                                <h3 class="kyc-status-title">Current verification status</h3>
                                <p class="mb-4">
                                    <span class="kyc-status-pill {{ $statusClass }}">
                                        <i class="fa fa-shield" aria-hidden="true"></i>
                                        {{ ucfirst($latestKyc->status) }}
                                    </span>
                                </p>

                                <div class="kyc-status-meta">
                                    <strong>Submitted</strong>
                                    <span>{{ optional($latestKyc->submitted_at)->format('M d, Y h:i A') ?? 'N/A' }}</span>
                                </div>

                                <div class="kyc-status-meta">
                                    <strong>Document Type</strong>
                                    <span>{{ $latestKyc->document_type ? strtoupper(str_replace('_', ' ', $latestKyc->document_type)) : 'Not specified' }}</span>
                                </div>

                                <div class="kyc-status-meta">
                                    <strong>Document Number</strong>
                                    <span>{{ $latestKyc->document_number ?: 'Not provided' }}</span>
                                </div>

                                @if ($latestKyc->status === 'rejected')
                                    <div class="alert alert-danger">
                                        <strong>Submission rejected.</strong>
                                        {{ $latestKyc->notes ?: 'Please review your files and submit a clearer set of documents.' }}
                                    </div>
                                    <a href="{{ route('kyc.form') }}" class="btn-main">Resubmit KYC</a>
                                @elseif ($latestKyc->status === 'approved')
                                    <div class="alert alert-success mb-0">
                                        Your KYC is approved. Wallet, reserve, and other protected finance actions are now available.
                                    </div>
                                @else
                                    <div class="alert alert-warning mb-0">
                                        Your request is still under manual review. No action is needed from you right now.
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="nft__item s2 kyc-status-card">
                        <div class="nft__item_info">
                            <h3 class="kyc-status-title">Review guidance</h3>
                            <p class="kyc-status-copy mb-3">
                                Reviews usually focus on clarity, consistency, and whether the selfie matches the uploaded document.
                            </p>

                            <div class="kyc-status-meta">
                                <strong>Accepted files</strong>
                                <span>JPG, JPEG, PNG, PDF up to 5 MB each.</span>
                            </div>

                            <div class="kyc-status-meta">
                                <strong>Best results</strong>
                                <span>Use bright lighting, avoid blur, and make sure all document edges are visible.</span>
                            </div>

                            <div class="kyc-status-meta mb-0">
                                <strong>Need changes?</strong>
                                <span>If rejected, use the resubmit button with cleaner files and updated notes.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
