@extends('layouts.app')

@section('content')
    <h1>KYC Status</h1>

    @if (!$latestKyc)
        <p class="muted">No KYC submission found.</p>
        <p><a href="{{ route('kyc.form') }}">Submit KYC</a></p>
    @else
        <p>Status: <strong>{{ ucfirst($latestKyc->status) }}</strong></p>
        <p class="muted">Submitted at: {{ $latestKyc->submitted_at }}</p>
        @if ($latestKyc->status === 'rejected')
            <p class="error">Rejected. {{ $latestKyc->notes }}</p>
            <p><a href="{{ route('kyc.form') }}">Resubmit KYC</a></p>
        @endif
    @endif
@endsection
