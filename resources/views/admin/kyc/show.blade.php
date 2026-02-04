@extends('layouts.admin-panel')

@section('content')
    @section('page-title', 'KYC Review')

    <p><strong>User:</strong> {{ $kyc->user->name }} ({{ $kyc->user->email }})</p>
    <p><strong>Status:</strong> {{ ucfirst($kyc->status) }}</p>
    @if ($kyc->notes)
        <p><strong>Notes:</strong> {{ $kyc->notes }}</p>
    @endif

    @php
        $frontUrl = $kyc->document_front_path ? route('admin.kyc.file', [$kyc, 'front']) : null;
        $backUrl = $kyc->document_back_path ? route('admin.kyc.file', [$kyc, 'back']) : null;
        $selfieUrl = $kyc->selfie_path ? route('admin.kyc.file', [$kyc, 'selfie']) : null;

        $isImage = function ($path) {
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            return in_array($ext, ['jpg', 'jpeg', 'png', 'webp']);
        };
    @endphp

    <style>
        .kyc-thumb { cursor: zoom-in; transition: transform 0.15s ease; }
        .kyc-thumb:hover { transform: scale(1.02); }
        .kyc-lightbox {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.85);
            align-items: center;
            justify-content: center;
            z-index: 1055;
        }
        .kyc-lightbox img {
            max-width: 95vw;
            max-height: 95vh;
            box-shadow: 0 12px 30px rgba(0,0,0,0.4);
            border-radius: 8px;
            cursor: zoom-out;
        }
    </style>

    <div class="row">
        <div class="col-md-4 mb-3">
            <strong>Document front</strong>
            @if ($frontUrl)
                @if ($isImage($kyc->document_front_path))
                    <div class="mt-2">
                        <img src="{{ $frontUrl }}" alt="Document front" class="img-fluid rounded kyc-thumb" data-zoom="{{ $frontUrl }}">
                    </div>
                @else
                    <div class="mt-2">
                        <a href="{{ $frontUrl }}" target="_blank">View document</a>
                    </div>
                @endif
            @else
                <div class="text-secondary">Not provided</div>
            @endif
        </div>
        <div class="col-md-4 mb-3">
            <strong>Document back</strong>
            @if ($backUrl)
                @if ($isImage($kyc->document_back_path))
                    <div class="mt-2">
                        <img src="{{ $backUrl }}" alt="Document back" class="img-fluid rounded kyc-thumb" data-zoom="{{ $backUrl }}">
                    </div>
                @else
                    <div class="mt-2">
                        <a href="{{ $backUrl }}" target="_blank">View document</a>
                    </div>
                @endif
            @else
                <div class="text-secondary">Not provided</div>
            @endif
        </div>
        <div class="col-md-4 mb-3">
            <strong>Selfie</strong>
            @if ($selfieUrl)
                @if ($isImage($kyc->selfie_path))
                    <div class="mt-2">
                        <img src="{{ $selfieUrl }}" alt="Selfie" class="img-fluid rounded kyc-thumb" data-zoom="{{ $selfieUrl }}">
                    </div>
                @else
                    <div class="mt-2">
                        <a href="{{ $selfieUrl }}" target="_blank">View document</a>
                    </div>
                @endif
            @else
                <div class="text-secondary">Not provided</div>
            @endif
        </div>
    </div>

    <div class="kyc-lightbox" id="kycLightbox">
        <img src="" alt="Zoomed KYC" id="kycLightboxImg">
    </div>

    <script>
        const lightbox = document.getElementById('kycLightbox');
        const lightboxImg = document.getElementById('kycLightboxImg');

        document.querySelectorAll('.kyc-thumb').forEach((img) => {
            img.addEventListener('click', () => {
                lightboxImg.src = img.dataset.zoom || img.src;
                lightbox.style.display = 'flex';
            });
        });

        lightbox.addEventListener('click', () => {
            lightbox.style.display = 'none';
            lightboxImg.src = '';
        });
    </script>

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
