<?php

namespace App\Http\Controllers;

use App\Models\KycRequest;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class KycController extends Controller
{
    public function create(Request $request)
    {
        return view('kyc.form', [
            'latestKyc' => $request->user()->latestKycRequest,
        ]);
    }

    public function store(Request $request, NotificationService $notifications): RedirectResponse
    {
        $validated = $request->validate([
            'document_front' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'document_back' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'selfie_data' => ['required', 'string'],
            'document_type' => ['nullable', 'in:nid,passport,driving_license'],
            'document_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = $request->user();

        $selfiePath = $this->storeSelfieData($validated['selfie_data']);

        $kyc = KycRequest::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'submitted_at' => now(),
            'document_type' => $validated['document_type'] ?? null,
            'document_number' => $validated['document_number'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'document_front_path' => $validated['document_front']->store('kyc'),
            'document_back_path' => $validated['document_back']->store('kyc'),
            'selfie_path' => $selfiePath,
        ]);

        $notifications->notifyUser(
            $user->id,
            'kyc_submitted',
            'KYC submitted',
            'Your KYC submission has been received and is under review.',
            'info',
            ['kyc_request_id' => $kyc->id]
        );

        $notifications->notifyAdminsByRoleOrPermission(
            'kyc.review',
            'kyc_submitted',
            'New KYC request',
            'A new KYC request was submitted.',
            'warning',
            ['kyc_request_id' => $kyc->id]
        );

        return redirect()->route('kyc.status')->with('status', 'KYC submitted.');
    }

    private function storeSelfieData(string $dataUrl): string
    {
        if (!str_starts_with($dataUrl, 'data:image/')) {
            abort(422, 'Invalid selfie data.');
        }

        [$meta, $content] = explode(',', $dataUrl, 2);
        $decoded = base64_decode($content, true);

        if ($decoded === false) {
            abort(422, 'Invalid selfie data.');
        }

        $extension = str_contains($meta, 'image/png') ? 'png' : 'jpg';
        $filename = 'kyc/selfie_'.uniqid().'.'.$extension;

        \Illuminate\Support\Facades\Storage::put($filename, $decoded);

        return $filename;
    }

    public function status(Request $request)
    {
        return view('kyc.status', [
            'latestKyc' => $request->user()->latestKycRequest,
        ]);
    }
}
