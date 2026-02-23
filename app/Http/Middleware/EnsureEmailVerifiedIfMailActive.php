<?php

namespace App\Http\Middleware;

use App\Services\MailSettingsService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailVerifiedIfMailActive
{
    public function __construct(private MailSettingsService $mailSettings)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        if (!$this->mailSettings->isActive()) {
            return $next($request);
        }

        $user = $request->user();
        if ($user && !$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        return $next($request);
    }
}
