@extends('layouts.frontend')

@section('content')
    @include('frontend.partials.page-banner', [
        'title' => 'KYC Verification',
        'subtitle' => 'Upload your identity documents and capture a live selfie for manual review.',
    ])

    <style>
        .kyc-card {
            padding: 28px;
            border-radius: 20px;
        }
        .kyc-section-title {
            font-size: 20px;
            margin-bottom: 10px;
        }
        .kyc-section-copy {
            color: #6b7280;
            margin-bottom: 20px;
        }
        .kyc-upload-card {
            padding: 16px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.55);
            border: 1px solid rgba(17, 24, 39, 0.08);
            height: 100%;
        }
        .kyc-upload-card label,
        .kyc-meta-card label {
            display: block;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .kyc-upload-note {
            color: #6b7280;
            font-size: 13px;
            margin-top: 8px;
            margin-bottom: 0;
        }
        .kyc-camera-frame {
            position: relative;
            overflow: hidden;
            border-radius: 20px;
            background: linear-gradient(145deg, rgba(15, 23, 42, 0.96), rgba(30, 41, 59, 0.88));
            min-height: 320px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .kyc-camera-frame video,
        .kyc-camera-frame img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .kyc-camera-placeholder {
            padding: 24px;
            text-align: center;
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
            max-width: 260px;
        }
        .kyc-camera-placeholder i {
            display: inline-flex;
            width: 60px;
            height: 60px;
            margin-bottom: 14px;
            border-radius: 999px;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            background: rgba(255, 255, 255, 0.12);
        }
        .kyc-selfie-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 18px;
        }
        .kyc-selfie-status {
            margin: 0;
            font-size: 14px;
            color: #6b7280;
        }
        .kyc-checklist {
            margin: 0;
            padding: 0;
            list-style: none;
            display: grid;
            gap: 12px;
        }
        .kyc-checklist li {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            color: #4b5563;
        }
        .kyc-checklist i {
            color: #7c3aed;
            margin-top: 3px;
        }
        .kyc-status-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
        }
        .kyc-status-chip--pending {
            background: rgba(251, 191, 36, 0.16);
            color: #92400e;
        }
        .kyc-meta-card {
            padding: 16px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.55);
            border: 1px solid rgba(17, 24, 39, 0.08);
            margin-bottom: 18px;
        }
        .dark-scheme .kyc-upload-card,
        .dark-scheme .kyc-meta-card {
            background: rgba(255, 255, 255, 0.04);
            border-color: rgba(255, 255, 255, 0.08);
        }
        .dark-scheme .kyc-section-copy,
        .dark-scheme .kyc-upload-note,
        .dark-scheme .kyc-selfie-status,
        .dark-scheme .kyc-checklist li {
            color: rgba(242, 245, 249, 0.8);
        }
        .dark-scheme .kyc-status-chip--pending {
            background: rgba(251, 191, 36, 0.18);
            color: #fcd34d;
        }
        @media (max-width: 991.98px) {
            .kyc-card {
                padding: 20px;
            }
            .kyc-camera-frame {
                min-height: 240px;
            }
        }
    </style>

    <section aria-label="section">
        <div class="container">
            @if ($errors->has('kyc'))
                <div class="alert alert-danger">{{ $errors->first('kyc') }}</div>
            @endif

            @if ($latestKyc && $latestKyc->status === 'pending')
                <div class="alert alert-warning d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div>
                        <strong>Submission under review.</strong>
                        Your latest KYC request was submitted on {{ optional($latestKyc->submitted_at)->format('M d, Y h:i A') ?? 'recently' }}.
                    </div>
                    <a href="{{ route('kyc.status') }}" class="btn-main btn-light">View Status</a>
                </div>
            @endif

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="nft__item s2 kyc-card">
                        <div class="nft__item_info">
                            <h3 class="kyc-section-title">Submit documents</h3>
                            <p class="kyc-section-copy">
                                Use clear, readable files. JPG, PNG, and PDF are accepted for document uploads. A live selfie is required to complete the request.
                            </p>

                            <form method="POST" action="{{ route('kyc.submit') }}" enctype="multipart/form-data">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="kyc-meta-card">
                                            <label for="document_type">Document Type</label>
                                            <select id="document_type" name="document_type" class="form-control">
                                                <option value="">Select type</option>
                                                <option value="nid" @selected(old('document_type') === 'nid')>National ID</option>
                                                <option value="passport" @selected(old('document_type') === 'passport')>Passport</option>
                                                <option value="driving_license" @selected(old('document_type') === 'driving_license')>Driving License</option>
                                            </select>
                                            @error('document_type')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <div class="kyc-meta-card">
                                            <label for="document_number">Document Number</label>
                                            <input
                                                id="document_number"
                                                type="text"
                                                name="document_number"
                                                value="{{ old('document_number') }}"
                                                class="form-control"
                                                placeholder="Optional reference number"
                                            >
                                            @error('document_number')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="kyc-upload-card">
                                            <label for="document_front">Document Front</label>
                                            <input id="document_front" type="file" name="document_front" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                                            <p class="kyc-upload-note">Upload the front side of your selected document.</p>
                                            @error('document_front')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <div class="kyc-upload-card">
                                            <label for="document_back">Document Back</label>
                                            <input id="document_back" type="file" name="document_back" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                                            <p class="kyc-upload-note">Upload the back side. If your document has one side only, upload the same file again.</p>
                                            @error('document_back')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Live Selfie</label>
                                    <div class="kyc-camera-frame">
                                        <video id="selfieVideo" autoplay playsinline muted></video>
                                        <img id="selfiePreview" alt="Captured selfie preview" style="display:none;">
                                        <div id="selfiePlaceholder" class="kyc-camera-placeholder">
                                            <i class="fa fa-camera" aria-hidden="true"></i>
                                            <div>Allow camera access, then capture a straight-on selfie with good lighting.</div>
                                        </div>
                                    </div>
                                    <canvas id="selfieCanvas" width="360" height="270" style="display:none;"></canvas>
                                    <input type="hidden" name="selfie_data" id="selfieData" value="{{ old('selfie_data') }}">

                                    <div class="kyc-selfie-toolbar">
                                        <button type="button" id="selfieCapture" class="btn-main">
                                            <i class="fa fa-camera menu-icon" aria-hidden="true"></i>Capture Selfie
                                        </button>
                                        <p class="kyc-selfie-status" id="selfieStatus">Initializing camera...</p>
                                    </div>
                                    @error('selfie_data')
                                        <div class="text-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="notes" class="form-label fw-bold">Notes</label>
                                    <textarea id="notes" name="notes" rows="4" class="form-control" placeholder="Optional note for the reviewer">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="text-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-flex flex-wrap gap-3 align-items-center">
                                    <button type="submit" class="btn-main">Submit KYC</button>
                                    <a href="{{ route('kyc.status') }}" class="btn-main btn-light">View Current Status</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="nft__item s2 kyc-card mb30">
                        <div class="nft__item_info">
                            <h3 class="kyc-section-title">What reviewers check</h3>
                            <ul class="kyc-checklist">
                                <li><i class="fa fa-check-circle" aria-hidden="true"></i><span>Document image is sharp and fully visible.</span></li>
                                <li><i class="fa fa-check-circle" aria-hidden="true"></i><span>Name and details match your account information.</span></li>
                                <li><i class="fa fa-check-circle" aria-hidden="true"></i><span>Selfie is recent, clear, and taken by the submitting user.</span></li>
                                <li><i class="fa fa-check-circle" aria-hidden="true"></i><span>No edited, cropped, or obscured files.</span></li>
                            </ul>
                        </div>
                    </div>

                    <div class="nft__item s2 kyc-card">
                        <div class="nft__item_info">
                            <h3 class="kyc-section-title">Submission status</h3>

                            @if (!$latestKyc)
                                <p class="kyc-section-copy mb-0">No KYC request found yet. Submit the form to start account verification.</p>
                            @else
                                <p>
                                    <span class="kyc-status-chip {{ $latestKyc->status === 'pending' ? 'kyc-status-chip--pending' : '' }}">
                                        <i class="fa fa-shield" aria-hidden="true"></i>
                                        {{ ucfirst($latestKyc->status) }}
                                    </span>
                                </p>
                                <div class="kyc-meta-card">
                                    <label>Submitted</label>
                                    <div>{{ optional($latestKyc->submitted_at)->format('M d, Y h:i A') ?? 'N/A' }}</div>
                                </div>
                                <div class="kyc-meta-card">
                                    <label>Document Type</label>
                                    <div>{{ $latestKyc->document_type ? strtoupper(str_replace('_', ' ', $latestKyc->document_type)) : 'Not specified' }}</div>
                                </div>
                                @if ($latestKyc->document_number)
                                    <div class="kyc-meta-card">
                                        <label>Document Number</label>
                                        <div>{{ $latestKyc->document_number }}</div>
                                    </div>
                                @endif
                                @if ($latestKyc->status === 'rejected' && $latestKyc->notes)
                                    <div class="alert alert-danger mb-0">
                                        <strong>Reviewer note:</strong> {{ $latestKyc->notes }}
                                    </div>
                                @elseif ($latestKyc->status === 'approved')
                                    <div class="alert alert-success mb-0">
                                        Your identity has been verified. Finance actions are now unlocked for this account.
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const video = document.getElementById('selfieVideo');
            const preview = document.getElementById('selfiePreview');
            const placeholder = document.getElementById('selfiePlaceholder');
            const canvas = document.getElementById('selfieCanvas');
            const captureBtn = document.getElementById('selfieCapture');
            const selfieData = document.getElementById('selfieData');
            const status = document.getElementById('selfieStatus');

            let cameraStream = null;

            function showCapturedPreview(dataUrl) {
                preview.src = dataUrl;
                preview.style.display = 'block';
                video.style.display = 'none';
                placeholder.style.display = 'none';
            }

            async function startCamera() {
                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    status.textContent = 'Camera access is not supported on this device.';
                    captureBtn.disabled = true;
                    return;
                }

                try {
                    cameraStream = await navigator.mediaDevices.getUserMedia({
                        video: { facingMode: 'user' },
                        audio: false
                    });
                    video.srcObject = cameraStream;
                    video.style.display = 'block';
                    placeholder.style.display = 'none';
                    status.textContent = 'Camera ready. Center your face, then capture.';
                } catch (error) {
                    status.textContent = 'Camera access denied or unavailable. Please allow permissions and refresh.';
                    captureBtn.disabled = true;
                }
            }

            captureBtn.addEventListener('click', function () {
                const ctx = canvas.getContext('2d');

                canvas.width = video.videoWidth || 720;
                canvas.height = video.videoHeight || 540;
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
                selfieData.value = dataUrl;
                showCapturedPreview(dataUrl);
                status.textContent = 'Selfie captured and attached to your submission.';
            });

            if (selfieData.value) {
                showCapturedPreview(selfieData.value);
                status.textContent = 'Previously captured selfie ready to submit.';
            } else {
                preview.style.display = 'none';
                video.style.display = 'none';
                placeholder.style.display = 'block';
                startCamera();
            }
        });
    </script>
@endsection
