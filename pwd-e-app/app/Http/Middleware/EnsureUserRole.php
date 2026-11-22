<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        foreach ($roles as $role) {
            if ($role === 'admin' && $user->isAdmin()) {
                return $next($request);
            }
            if ($role === 'candidate' && ($user->isCandidate() || $user->isAdmin())) {
                return $next($request);
            }
            if ($role === 'employer' && ($user->isEmployer() || $user->isAdmin())) {
                return $next($request);
            }
            if ($user->role === $role || $user->hasRole($role)) {
                return $next($request);
            }
        }

        abort(403, 'Unauthorized access for your account role.');
    }
}
