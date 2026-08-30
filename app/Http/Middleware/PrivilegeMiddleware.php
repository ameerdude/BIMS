<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PrivilegeMiddleware
{
    public function handle(Request $request, Closure $next, string $minLevel = '1'): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (auth()->user()->privilege_level < (int) $minLevel) {
            abort(403, 'Insufficient privilege level. Required: Level ' . $minLevel . ' or above.');
        }

        return $next($request);
    }
}
