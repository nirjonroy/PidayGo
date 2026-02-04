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

        <label>Live Selfie</label>
        <div style="margin-top:8px;">
            <video id="selfieVideo" autoplay playsinline style="width:100%; max-width:360px; border:1px solid #d1d5db; border-radius:6px;"></video>
            <canvas id="selfieCanvas" width="360" height="270" style="display:none;"></canvas>
        </div>
        <button type="button" id="selfieCapture">Capture Selfie</button>
        <input type="hidden" name="selfie_data" id="selfieData">
        <div class="muted" id="selfieStatus">Camera not started.</div>
        @error('selfie_data')
            <div class="error">{{ $message }}</div>
        @enderror

        <label for="notes">Notes (optional)</label>
        <textarea id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>

        <button type="submit">Submit KYC</button>
    </form>

    <script>
        const video = document.getElementById('selfieVideo');
        const canvas = document.getElementById('selfieCanvas');
        const captureBtn = document.getElementById('selfieCapture');
        const selfieData = document.getElementById('selfieData');
        const status = document.getElementById('selfieStatus');

        async function startCamera() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
                video.srcObject = stream;
                status.textContent = 'Camera ready.';
            } catch (err) {
                status.textContent = 'Camera access denied or unavailable.';
            }
        }

        captureBtn.addEventListener('click', () => {
            const ctx = canvas.getContext('2d');
            canvas.width = video.videoWidth || 360;
            canvas.height = video.videoHeight || 270;
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
            selfieData.value = dataUrl;
            status.textContent = 'Selfie captured.';
        });

        startCamera();
    </script>
@endsection
