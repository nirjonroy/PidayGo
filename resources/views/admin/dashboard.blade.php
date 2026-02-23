@extends('layouts.admin-panel')

@section('page-title', 'Dashboard')

@section('content')
    @php
        $pendingKyc = \App\Models\KycRequest::where('status', 'pending')->count();
        $pendingWithdrawals = \App\Models\WithdrawalRequest::where('status', 'pending')->count();
        $pendingDeposits = \App\Models\DepositRequest::where('status', 'pending')->count();
    @endphp

    <div class="row">
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Pending KYC</h5>
                    <p class="card-text">Review user submissions.</p>
                    <div class="mb-2"><strong>{{ $pendingKyc }}</strong> pending</div>
                    <a href="{{ route('admin.kyc.index') }}" class="btn btn-primary">View KYC</a>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Pending Withdrawals</h5>
                    <p class="card-text">Review withdrawal requests.</p>
                    <div class="mb-2"><strong>{{ $pendingWithdrawals }}</strong> pending</div>
                    <a href="{{ route('admin.withdrawals.index') }}" class="btn btn-primary">View Withdrawals</a>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Pending Deposits</h5>
                    <p class="card-text">Review deposit requests.</p>
                    <div class="mb-2"><strong>{{ $pendingDeposits }}</strong> pending</div>
                    <a href="{{ route('admin.deposits.index') }}" class="btn btn-primary">View Deposits</a>
                </div>
            </div>
        </div>
    </div>
@endsection
