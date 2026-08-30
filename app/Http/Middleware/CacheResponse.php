<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CacheResponse
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Cache Livewire page GETs for 60 seconds (browser cache)
        if ($request->isMethod('GET') && !$request->expectsJson()) {
            $response->headers->set('Cache-Control', 'public, max-age=300, s-maxage=300, stale-while-revalidate=600');
            $response->headers->set('X-Content-Type-Options', 'nosniff');
        }

        return $response;
    }
}
