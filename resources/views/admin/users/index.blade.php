@extends('layouts.admin-panel')

@section('content')
    @section('page-title', 'Users')

    <div class="card">
        <div class="card-body">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Email Verified</th>
                        <th>KYC</th>
                        <th>2FA</th>
                        <th>Joined</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->email_verified_at ? 'Yes' : 'No' }}</td>
                            <td>{{ $user->isKycApproved() ? 'Approved' : 'Pending' }}</td>
                            <td>{{ $user->hasTwoFactorEnabled() ? 'Enabled' : 'Disabled' }}</td>
                            <td>{{ $user->created_at?->format('Y-m-d') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $users->links() }}
        </div>
    </div>
@endsection
