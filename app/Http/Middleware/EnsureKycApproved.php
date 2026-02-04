<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureKycApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->routeIs('kyc.*')) {
            return $next($request);
        }

        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        $latestKyc = $user->latestKycRequest;

        if (!$latestKyc) {
            return redirect()->route('kyc.form');
        }

        if ($latestKyc->status === 'pending') {
            return redirect()->route('kyc.status');
        }

        if ($latestKyc->status === 'rejected') {
            return redirect()->route('kyc.form')->withErrors([
                'kyc' => 'Your previous KYC submission was rejected. Please resubmit.',
            ]);
        }

        return $next($request);
    }
}
