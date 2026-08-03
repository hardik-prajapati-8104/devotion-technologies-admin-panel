<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    /**
     * Baseline security headers for the whole admin panel. These are
     * defensive-in-depth — they don't replace input validation, CSRF
     * protection, or authorization checks, but they close off a few
     * classes of attack (clickjacking, MIME-sniffing, referrer leaks)
     * for near-zero cost.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        // Only set HSTS when the request actually arrived over HTTPS —
        // setting it on plain HTTP (e.g. local dev) can lock browsers
        // into HTTPS-only for the domain before you're ready for that.
        if ($request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
