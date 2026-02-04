<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminIpAllowed
{
    public function handle(Request $request, Closure $next): Response
    {
        $allowed = config('admin.allowed_ips', []);
        $ip = $request->ip();

        if (!in_array($ip, $allowed, true)) {
            abort(403, 'Admin access is restricted for this IP address.');
        }

        return $next($request);
    }
}
