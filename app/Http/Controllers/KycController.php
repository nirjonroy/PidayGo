<?php

namespace App\Http\Controllers;

use App\Models\KycRequest;
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

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'document_front' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'document_back' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'selfie_data' => ['required', 'string'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = $request->user();

        $selfiePath = $this->storeSelfieData($validated['selfie_data']);

        $kyc = KycRequest::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'submitted_at' => now(),
            'notes' => $validated['notes'] ?? null,
            'document_front_path' => $validated['document_front']->store('kyc'),
            'document_back_path' => $validated['document_back']->store('kyc'),
            'selfie_path' => $selfiePath,
        ]);

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
