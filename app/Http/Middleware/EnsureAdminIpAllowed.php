<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\IpUtils;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminIpAllowed
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!config('admin.enforce_ip', true)) {
            return $next($request);
        }

        $allowed = config('admin.allowed_ips', []);
        $ip = $request->ip();

        if (empty($allowed)) {
            return $next($request);
        }

        if (in_array('*', $allowed, true)) {
            return $next($request);
        }

        if (!IpUtils::checkIp($ip, $allowed)) {
            abort(403, 'Admin access is restricted for this IP address.');
        }

        return $next($request);
    }
}
