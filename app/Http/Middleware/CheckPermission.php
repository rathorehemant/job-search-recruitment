<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(
        Request $request,
        Closure $next,
        string $permission
    ): Response {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $hasPermission = $user->role
            && $user->role->permissions()
                ->where('slug', $permission)
                ->exists();

        if (!$hasPermission) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}