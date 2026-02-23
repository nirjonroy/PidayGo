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
                            @if ($profile && $profile->photo_path)
                                <img src="{{ asset('storage/' . $profile->photo_path) }}" alt="Profile Photo" style="width:140px;height:140px;object-fit:cover;border-radius:8px;">
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

    <div class="card">
        <div class="card-body">
            <h5 class="mb-3">Bank Accounts</h5>
            @if ($user->bankAccounts->isEmpty())
                <p class="text-muted">No bank accounts on file.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Bank</th>
                                <th>Account Name</th>
                                <th>Account Number</th>
                                <th>Branch</th>
                                <th>Routing</th>
                                <th>SWIFT</th>
                                <th>IFSC</th>
                                <th>Currency</th>
                                <th>Default</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($user->bankAccounts as $account)
                                <tr>
                                    <td>{{ $account->bank_name }}</td>
                                    <td>{{ $account->account_name }}</td>
                                    <td>{{ $account->account_number }}</td>
                                    <td>{{ $account->branch ?? '-' }}</td>
                                    <td>{{ $account->routing_number ?? '-' }}</td>
                                    <td>{{ $account->swift_code ?? '-' }}</td>
                                    <td>{{ $account->ifsc_code ?? '-' }}</td>
                                    <td>{{ $account->currency ?? '-' }}</td>
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
@endsection
