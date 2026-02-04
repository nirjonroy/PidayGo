@extends('layouts.admin-panel')

@section('content')
    @section('page-title', 'KYC Review')

    <p><strong>User:</strong> {{ $kyc->user->name }} ({{ $kyc->user->email }})</p>
    <p><strong>Status:</strong> {{ ucfirst($kyc->status) }}</p>
    @if ($kyc->notes)
        <p><strong>Notes:</strong> {{ $kyc->notes }}</p>
    @endif

    <ul class="list-unstyled">
        <li>Document front: {{ $kyc->document_front_path }}</li>
        <li>Document back: {{ $kyc->document_back_path }}</li>
        <li>Selfie: {{ $kyc->selfie_path }}</li>
    </ul>

    <form method="POST" action="{{ route('admin.kyc.approve', $kyc) }}">
        @csrf
        <button type="submit" class="btn btn-success">Approve</button>
    </form>

    <form method="POST" action="{{ route('admin.kyc.reject', $kyc) }}">
        @csrf
        <label for="notes">Rejection Notes</label>
        <textarea id="notes" name="notes" rows="3"></textarea>
        <button type="submit" class="btn btn-danger">Reject</button>
    </form>
@endsection
