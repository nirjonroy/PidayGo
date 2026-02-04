<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\KycRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class KycReviewController extends Controller
{
    public function index()
    {
        $pending = KycRequest::where('status', 'pending')
            ->with('user')
            ->orderByDesc('submitted_at')
            ->paginate(20);

        return view('admin.kyc.index', [
            'pending' => $pending,
        ]);
    }

    public function show(KycRequest $kycRequest)
    {
        $kycRequest->load('user', 'reviewer');

        return view('admin.kyc.show', [
            'kyc' => $kycRequest,
        ]);
    }

    public function approve(Request $request, KycRequest $kycRequest): RedirectResponse
    {
        $kycRequest->forceFill([
            'status' => 'approved',
            'reviewed_at' => now(),
            'reviewed_by' => $request->user('admin')->id,
        ])->save();

        ActivityLog::record('kyc.approved', $request->user('admin'), $kycRequest);

        return redirect()->route('admin.kyc.index')->with('status', 'KYC approved.');
    }

    public function reject(Request $request, KycRequest $kycRequest): RedirectResponse
    {
        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $kycRequest->forceFill([
            'status' => 'rejected',
            'reviewed_at' => now(),
            'reviewed_by' => $request->user('admin')->id,
            'notes' => $validated['notes'] ?? null,
        ])->save();

        ActivityLog::record('kyc.rejected', $request->user('admin'), $kycRequest, [
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('admin.kyc.index')->with('status', 'KYC rejected.');
    }
}
