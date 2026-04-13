@extends('layouts.admin-panel')

@section('content')
    @section('page-title', 'User Details')

    @php
        $profile = $user->profile;
        $kyc = $user->latestKycRequest;
        $defaultBank = $user->bankAccounts->firstWhere('is_default', true) ?? $user->bankAccounts->first();
    @endphp

    <div class="row">
        <div class="col-lg-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">Basic Info</h5>
                    <p><strong>User ID:</strong> {{ $user->user_code }}</p>
                    <p><strong>Name:</strong> {{ $user->name }}</p>
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                    <p><strong>Email Verified:</strong> {{ $user->email_verified_at ? 'Yes' : 'No' }}</p>
                    <p><strong>2FA:</strong> {{ $user->hasTwoFactorEnabled() ? 'Enabled' : 'Disabled' }}</p>
                    <p><strong>Joined:</strong> {{ $user->created_at }}</p>
                </div>
            </div>
        </div>

        <div class="col-lg-8 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">Profile</h5>
                    <div class="row">
                        <div class="col-md-4">
                            @if ($profile && $profile->photo_url)
                                <img src="{{ $profile->photo_url }}" alt="Profile Photo" style="width:140px;height:140px;object-fit:cover;border-radius:8px;">
                            @else
                                <div class="text-muted">No photo</div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <p><strong>Username:</strong> {{ $profile->username ?? '-' }}</p>
                            <p><strong>Phone:</strong> {{ $profile->phone ?? '-' }}</p>
                            <p><strong>Country:</strong> {{ $profile->country ?? '-' }}</p>
                            <p><strong>City:</strong> {{ $profile->city ?? '-' }}</p>
                            <p><strong>Address:</strong> {{ $profile->address ?? '-' }}</p>
                            <p><strong>DOB:</strong> {{ $profile?->dob?->format('Y-m-d') ?? '-' }}</p>
                            <p><strong>Bio:</strong> {{ $profile->bio ?? '-' }}</p>
                            <p><strong>Twitter:</strong> {{ $profile->social_twitter ?? '-' }}</p>
                            <p><strong>Telegram:</strong> {{ $profile->social_telegram ?? '-' }}</p>
                            <p><strong>Discord:</strong> {{ $profile->social_discord ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">KYC</h5>
            @if ($kyc)
                <p><strong>Status:</strong> {{ ucfirst($kyc->status) }}</p>
                <p><strong>Submitted:</strong> {{ $kyc->submitted_at }}</p>
                <p><strong>Document Type:</strong> {{ $kyc->document_type ? strtoupper($kyc->document_type) : '-' }}</p>
                <p><strong>Document Number:</strong> {{ $kyc->document_number ?? '-' }}</p>
                @if (auth('admin')->user()?->can('kyc.review'))
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.kyc.show', $kyc) }}">View KYC</a>
                @endif
            @else
                <p class="text-muted mb-0">No KYC submission yet.</p>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">Wallet Summary</h5>
                    <p><strong>Wallet Balance:</strong> {{ number_format($walletBalance, 8) }} USDT</p>
                    <p><strong>Reserve Balance:</strong> {{ number_format($reserveBalance, 8) }} USDT</p>
                    <p><strong>Active Stakes:</strong> {{ $user->stakes->where('status', 'active')->count() }}</p>
                    <p><strong>Total Stakes:</strong> {{ $user->stakes->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">Deposits & Withdrawals</h5>
                    <p><strong>Total Deposits:</strong> {{ $depositRequests->count() }}</p>
                    <p><strong>Pending Deposits:</strong> {{ $depositRequests->where('status', 'pending')->count() }}</p>
                    <p><strong>Total Withdrawals:</strong> {{ $withdrawals->count() }}</p>
                    <p><strong>Pending Withdrawals:</strong> {{ $withdrawals->where('status', 'pending')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="mb-3">Crypto Wallets</h5>
            @if ($user->bankAccounts->isEmpty())
                <p class="text-muted">No crypto wallets on file.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Network</th>
                                <th>Wallet Address</th>
                                <th>Label</th>
                                <th>Memo/Tag</th>
                                <th>Default</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($user->bankAccounts as $account)
                                <tr>
                                    <td>{{ $account->network ?? '-' }}</td>
                                    <td>
                                        @php
                                            $address = $account->wallet_address;
                                            $masked = $address ? substr($address, 0, 6) . '...' . substr($address, -4) : '-';
                                        @endphp
                                        {{ $masked }}
                                    </td>
                                    <td>{{ $account->address_label ?? '-' }}</td>
                                    <td>{{ $account->memo_tag ?? '-' }}</td>
                                    <td>
                                        @if ($account->is_default)
                                            <span class="badge bg-success">Default</span>
                                        @else
                                            <span class="badge bg-secondary">No</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <h5 class="mb-3">Stakes</h5>
            @if ($user->stakes->isEmpty())
                <p class="text-muted mb-0">No stakes found.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Plan</th>
                                <th>Principal</th>
                                <th>Status</th>
                                <th>Started</th>
                                <th>Ends</th>
                                <th>Reward Paid</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($user->stakes as $stake)
                                <tr>
                                    <td>{{ $stake->stakePlan?->name ?? '-' }}</td>
                                    <td>{{ number_format($stake->principal_amount, 8) }}</td>
                                    <td>{{ ucfirst($stake->status) }}</td>
                                    <td>{{ $stake->started_at }}</td>
                                    <td>{{ $stake->ends_at ?? '-' }}</td>
                                    <td>{{ number_format($stake->total_reward_paid, 8) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <h5 class="mb-3">Deposit Requests</h5>
            @if ($depositRequests->isEmpty())
                <p class="text-muted mb-0">No deposit requests found.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Amount</th>
                                <th>TxID</th>
                                <th>Status</th>
                                <th>Requested</th>
                                <th>Reviewed</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($depositRequests as $deposit)
                                <tr>
                                    <td>{{ number_format($deposit->amount, 8) }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($deposit->txid, 18) }}</td>
                                    <td>{{ ucfirst($deposit->status) }}</td>
                                    <td>{{ $deposit->created_at }}</td>
                                    <td>{{ $deposit->reviewed_at ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <h5 class="mb-3">Withdrawal Requests</h5>
            @if ($withdrawals->isEmpty())
                <p class="text-muted mb-0">No withdrawals found.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Requested</th>
                                <th>Eligible At</th>
                                <th>Reviewed</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($withdrawals as $withdrawal)
                                <tr>
                                    <td>{{ number_format($withdrawal->amount, 8) }}</td>
                                    <td>{{ ucfirst($withdrawal->status) }}</td>
                                    <td>{{ $withdrawal->requested_at }}</td>
                                    <td>{{ $withdrawal->eligible_at ?? '-' }}</td>
                                    <td>{{ $withdrawal->reviewed_at ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <h5 class="mb-3">Wallet Ledger (Credit/Debit)</h5>
            @if ($walletLedgers->isEmpty())
                <p class="text-muted mb-0">No wallet ledger entries.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Reference</th>
                                <th>Admin</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($walletLedgers as $ledger)
                                <tr>
                                    <td>{{ $ledger->type }}</td>
                                    <td class="{{ $ledger->amount < 0 ? 'text-danger' : 'text-success' }}">
                                        {{ number_format($ledger->amount, 8) }}
                                    </td>
                                    <td>{{ $ledger->reference_type ? class_basename($ledger->reference_type) . '#' . $ledger->reference_id : '-' }}</td>
                                    <td>{{ $ledger->createdByAdmin?->name ?? '-' }}</td>
                                    <td>{{ $ledger->created_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <h5 class="mb-3">Reserve Ledger</h5>
            @if ($reserveLedgers->isEmpty())
                <p class="text-muted mb-0">No reserve ledger entries.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Change</th>
                                <th>Reason</th>
                                <th>Reference</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reserveLedgers as $ledger)
                                <tr>
                                    <td class="{{ $ledger->change < 0 ? 'text-danger' : 'text-success' }}">
                                        {{ number_format($ledger->change, 8) }}
                                    </td>
                                    <td>{{ $ledger->reason }}</td>
                                    <td>{{ $ledger->ref_type ? $ledger->ref_type . '#' . $ledger->ref_id : '-' }}</td>
                                    <td>{{ $ledger->created_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
