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
            'selfie' => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = $request->user();

        $kyc = KycRequest::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'submitted_at' => now(),
            'notes' => $validated['notes'] ?? null,
            'document_front_path' => $validated['document_front']->store('kyc'),
            'document_back_path' => $validated['document_back']->store('kyc'),
            'selfie_path' => $validated['selfie']->store('kyc'),
        ]);

        return redirect()->route('kyc.status')->with('status', 'KYC submitted.');
    }

    public function status(Request $request)
    {
        return view('kyc.status', [
            'latestKyc' => $request->user()->latestKycRequest,
        ]);
    }
}
