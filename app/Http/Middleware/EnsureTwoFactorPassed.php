<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorPassed
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->routeIs('two-factor.*')) {
            return $next($request);
        }

        $user = $request->user();

        if ($user && $user->hasTwoFactorEnabled() && !session('two_factor_passed')) {
            return redirect()->route('two-factor.challenge');
        }

        return $next($request);
    }
}
