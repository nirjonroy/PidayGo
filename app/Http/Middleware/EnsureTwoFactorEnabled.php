<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->routeIs('two-factor.*')) {
            return $next($request);
        }

        $user = $request->user();

        if ($user && !$user->hasTwoFactorEnabled()) {
            return redirect()->route('two-factor.setup');
        }

        return $next($request);
    }
}
