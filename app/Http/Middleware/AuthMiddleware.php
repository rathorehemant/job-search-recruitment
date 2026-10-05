<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {

            session()->put('url.intended', $request->fullUrl());

            return redirect()
                ->route('login')
                ->with('error', 'Please login to access this page.');
        }

        return $next($request);
    }
}
