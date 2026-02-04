<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\KycRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KycReviewController extends Controller
{
    public function index()
    {
        $status = request('status', 'pending');

        $query = KycRequest::query()->with('user')->orderByDesc('submitted_at');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $pending = (clone $query)->where('status', 'pending')->count();
        $approved = (clone $query)->where('status', 'approved')->count();
        $rejected = (clone $query)->where('status', 'rejected')->count();

        $requests = $query->paginate(20)->withQueryString();

        return view('admin.kyc.index', [
            'requests' => $requests,
            'status' => $status,
            'counts' => [
                'pending' => $pending,
                'approved' => $approved,
                'rejected' => $rejected,
            ],
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

    public function file(Request $request, KycRequest $kycRequest, string $type)
    {
        $fieldMap = [
            'front' => 'document_front_path',
            'back' => 'document_back_path',
            'selfie' => 'selfie_path',
        ];

        if (!isset($fieldMap[$type])) {
            abort(404);
        }

        $path = $kycRequest->{$fieldMap[$type]};
        if (!$path || !Storage::disk('local')->exists($path)) {
            abort(404);
        }

        $filePath = Storage::disk('local')->path($path);

        return response()->file($filePath);
    }
}
