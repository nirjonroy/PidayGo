@extends('layouts.admin-panel')

@section('content')
    @section('page-title', 'Users')

    <div class="card">
        <div class="card-body">
            <form method="GET" class="row g-2 mb-3">
                <div class="col-md-2">
                    <input type="text" name="user_code" class="form-control" placeholder="User ID"
                           value="{{ $filters['user_code'] ?? '' }}">
                </div>
                <div class="col-md-3">
                    <input type="text" name="email" class="form-control" placeholder="Email"
                           value="{{ $filters['email'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <select name="kyc_status" class="form-select">
                        <option value="">KYC: All</option>
                        <option value="approved" @selected(($filters['kyc_status'] ?? '') === 'approved')>Approved</option>
                        <option value="pending" @selected(($filters['kyc_status'] ?? '') === 'pending')>Pending</option>
                        <option value="rejected" @selected(($filters['kyc_status'] ?? '') === 'rejected')>Rejected</option>
                        <option value="none" @selected(($filters['kyc_status'] ?? '') === 'none')>Not Submitted</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_from" class="form-control"
                           value="{{ $filters['date_from'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_to" class="form-control"
                           value="{{ $filters['date_to'] ?? '' }}">
                </div>
                <div class="col-md-3">
                    <input type="text" name="q" class="form-control" placeholder="Search name/email/user id"
                           value="{{ $filters['q'] ?? '' }}">
                </div>
                <div class="col-md-2 d-grid">
                    <button class="btn btn-primary">Filter</button>
                </div>
                <div class="col-md-2 d-grid">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>

            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Email Verified</th>
                        <th>KYC</th>
                        <th>2FA</th>
                        <th>Joined</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->user_code }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->email_verified_at ? 'Yes' : 'No' }}</td>
                            <td>{{ $user->latestKycRequest?->status ? ucfirst($user->latestKycRequest->status) : 'Not Submitted' }}</td>
                            <td>{{ $user->hasTwoFactorEnabled() ? 'Enabled' : 'Disabled' }}</td>
                            <td>{{ $user->created_at?->format('Y-m-d') }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-primary">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $users->links() }}
        </div>
    </div>
@endsection
