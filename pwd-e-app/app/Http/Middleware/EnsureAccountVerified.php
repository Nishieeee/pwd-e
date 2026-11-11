<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        $user = $request->user();

        if ($user->hasRole('admin')) {
            return $next($request);
        }

        if ($user->status === 'suspended') {
            Auth::logout();

            return redirect()->route('login')->withErrors(['email' => 'Your account has been suspended.']);
        }

        if (! $user->isVerified()) {
            if ($request->expectsJson()) {
                abort(403, 'Your account is pending verification.');
            }

            return redirect()->route('verification.notice');
        }

        return $next($request);
    }
}
