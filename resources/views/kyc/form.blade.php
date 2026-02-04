@extends('layouts.app')

@section('content')
    <h1>KYC Submission</h1>

    @if ($latestKyc && $latestKyc->status === 'pending')
        <p class="muted">Your last submission is still under review.</p>
        <p><a href="{{ route('kyc.status') }}">View status</a></p>
    @endif

    <form method="POST" action="{{ route('kyc.submit') }}" enctype="multipart/form-data">
        @csrf
        <label for="document_front">Document Front (jpg, png, pdf)</label>
        <input id="document_front" type="file" name="document_front" required>
        @error('document_front')
            <div class="error">{{ $message }}</div>
        @enderror

        <label for="document_back">Document Back (jpg, png, pdf)</label>
        <input id="document_back" type="file" name="document_back" required>
        @error('document_back')
            <div class="error">{{ $message }}</div>
        @enderror

        <label for="selfie">Selfie (jpg, png)</label>
        <input id="selfie" type="file" name="selfie" required>
        @error('selfie')
            <div class="error">{{ $message }}</div>
        @enderror

        <label for="notes">Notes (optional)</label>
        <textarea id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>

        <button type="submit">Submit KYC</button>
    </form>
@endsection
