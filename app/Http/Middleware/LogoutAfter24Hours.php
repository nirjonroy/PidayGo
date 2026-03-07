<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LogoutAfter24Hours
{
    private const MAX_SESSION_SECONDS = 86400;

    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('web')->check()) {
            $loginAt = $request->session()->get('login_at');
            $now = time();

            if (!$loginAt) {
                $request->session()->put('login_at', $now);
            } elseif (($now - $loginAt) >= self::MAX_SESSION_SECONDS) {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Unauthenticated.'], 401);
                }

                return redirect()->route('login');
            }
        }

        return $next($request);
    }
}
